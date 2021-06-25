<?php
namespace Boostsales\PartialShipment\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
    protected $_checkoutSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
        )
	{
		$this->_checkoutSession = $checkoutSession;
		return parent::__construct($context);
	}

	public function execute()
	{
		if($_GET){
			if($_GET['order_type'] == 'complete'){
				$this->_checkoutSession->setOrderPreference('complete_order');
			}if($_GET['order_type'] == 'separate'){
				$this->_checkoutSession->setOrderPreference('separate_order');
			}
		}
		$url = rtrim($this->_url->getUrl('checkout/#shipping'),'/');
        $this->_redirect($url);
	}
}