<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Boostsales\PartialShipment\Observer\Logxstar;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $helper;
    protected $checkoutSession;
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        \Logxstar\Integration\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $this->checkoutSession->unsOrderPreference();
        $method = $observer->getOrder()->getShippingMethod();
        if(isset($method) && $method == "flatrate_flatrate"){
            return;
        }else {
            $this->helper->apiSendOrderToLogxstar($observer->getOrder());
        }
    }
}
