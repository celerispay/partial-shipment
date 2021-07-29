<?php 

namespace Boostsales\PartialShipment\Block;

class CartPageCache extends \Magento\Framework\View\Element\Template {

    protected $_checkoutSession;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
        )
	{
		$this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
        $this->clearOrderPreference();
	}

    protected function clearOrderPreference(){
        $this->_checkoutSession->unsOrderPreference(); 
    }
}