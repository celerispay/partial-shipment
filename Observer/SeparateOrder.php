<?php

namespace Boostsales\PartialShipment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SeparateOrder implements ObserverInterface
{
    
    const COOKIE_NAME = 'checkout_data_backorder_preference';
    protected $_checkoutSession;
    protected $_cart;
    protected $_cookieManger;
    protected $_request;

    public function __construct(
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Checkout\Model\Cart $_cart,
        \Magento\Framework\Stdlib\CookieManagerInterface $_cookieManger,
        \Magento\Framework\App\Request\Http $_request
    ) {
        $this->_checkoutSession = $_checkoutSession;
        $this->_cart = $_cart;
        $this->_cookieManger = $_cookieManger;
        $this->_request = $_request;
	}
	

   public function execute(Observer $observer)
   {
        $backorder_preference = $this->_checkoutSession->getOrderPreference();
        if($backorder_preference == 'separate_order'){
            $quoteItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
            foreach($quoteItems as $item){
            $itemStockQty = $item->getProduct()->getExtensionAttributes()->getStockItem()->getQty();
            var_dump($itemStockQty); 
                if($itemStockQty == 0){
                    $itemId = $item->getItemId();
                    var_dump('stock id to delete:'.$itemId);
                    $this->_cart->removeItem($itemId);
                } 
            }
        }
    }
}
