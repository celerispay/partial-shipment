<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Boostsales\PartialShipment\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveDataToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();

        if ($this->checkoutSession->getOrderPreference() == 'separate_order') {
            $order->setCanShipPartially(1);
            $order->setCanShipPartiallyItem(1);
            $order->setIsPartialDelivery(1);
        } else {
            $order->setIsPartialDelivery(0);
        }

        return $this;
    }
}
