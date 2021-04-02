<?php

namespace Boostsales\PartialShipment\Block\Checkout;

use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Framework\App\Http\Context as HttpContext;


class Success extends \Magento\Checkout\Block\Onepage\Success
{

    private $checkoutSession;

    protected $_orderDetail;

    protected $_item;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        Config $orderConfig,
        HttpContext $httpContext,
        \Magento\Sales\Model\Order $orderDetails,
        \Magento\Catalog\Model\ProductRepository $item,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );
        $this->_orderDetail = $orderDetails;
        $this->_item = $item;
        $this->checkoutSession = $checkoutSession;
    }
    
    public function getOrderArray()
    {
        $splitOrders = $this->checkoutSession->getOrderIds();
        $this->checkoutSession->unsOrderIds();
        $this->checkoutSession->unsOrderPreference();
        if (empty($splitOrders) || count($splitOrders) <= 1) {
            return false;
        }
        return $splitOrders;
    }

    public function getOrder($id) {
        $lastOId = $this->checkoutSession->getLastRealOrderId();
        if(is_array($id)){
            $BackorderId = array_diff($id, [$lastOId]);
            $this->_order = $this->_orderDetail->loadByIncrementId($BackorderId);
            return $this->_order;
        }else{
            return false;
        }
    }

    public function getItem($itemId) {
        return $this->_item->getById($itemId);
    }
}
