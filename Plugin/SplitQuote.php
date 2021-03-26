<?php

namespace Boostsales\PartialShipment\Plugin;

use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\ManagerInterface;
use Boostsales\PartialShipment\Api\QuoteHandlerInterface;


class SplitQuote
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var QuoteHandlerInterface
     */
    private $quoteHandler;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param ManagerInterface $eventManager
     * @param QuoteHandlerInterface $quoteHandler
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        ManagerInterface $eventManager,
        QuoteHandlerInterface $quoteHandler
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->quoteHandler = $quoteHandler;
    }

    /**
     * Places an order for a specified cart.
     *
     * @param QuoteManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param string $payment
     * @return mixed
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see \Magento\Quote\Api\CartManagementInterface
     */
    public function aroundPlaceOrder(QuoteManagement $subject, callable $proceed, $cartId, $payment = null)
    {
        $currentQuote = $this->quoteRepository->getActive($cartId);

        
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);

        if (empty($quotes)) {
            return $result = array_values([($proceed($cartId, $payment))]);
        }
        
        $addresses = $this->quoteHandler->collectAddressesData($currentQuote);

        $orders = [];
        $orderIds = [];
        foreach ($quotes as $items) {
            $split = $this->quoteFactory->create();
    
            $this->quoteHandler->setCustomerData($currentQuote, $split);
            $this->toSaveQuote($split);

            
            foreach ($items as $item) {
                
                $item->setId(null);
                $split->addItem($item);
            }
            $this->quoteHandler->populateQuote($quotes, $split, $items, $addresses, $payment);

            
            $this->eventManager->dispatch(
                'checkout_submit_before',
                ['quote' => $split]
            );

            $this->toSaveQuote($split);
            $order = $subject->submit($split);

            $orders[] = $order;
            $orderIds[$order->getId()] = $order->getIncrementId();

            if (null == $order) {
                throw new LocalizedException(__('Please try to place the order again.'));
            }
        }
        $currentQuote->setIsActive(false);
        $this->toSaveQuote($currentQuote);

        $this->quoteHandler->defineSessions($split, $order, $orderIds);

        $this->eventManager->dispatch(
            'checkout_submit_all_after',
            ['orders' => $orders, 'quote' => $currentQuote]
        );
        return $this->getOrderKeys($orderIds);
    }

    /**
     * Save quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Boostsales\PartialShipment\Plugin\SplitQuote
     */
    private function toSaveQuote($quote)
    {
        $this->quoteRepository->save($quote);

        return $this;
    }

    /**
     * @param array $orderIds
     * @return array
     */
    private function getOrderKeys($orderIds)
    {
        $orderValues = [];
        foreach (array_keys($orderIds) as $orderKey) {
            $orderValues[] = (string) $orderKey;
        }
        return array_values($orderValues);
    }
}
