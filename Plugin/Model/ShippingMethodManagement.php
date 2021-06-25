<?php
namespace Boostsales\PartialShipment\Plugin\Model;

class ShippingMethodManagement {

    protected $_checkoutSession;

	public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
        )
	{
		$this->_checkoutSession = $checkoutSession;
	}

    public function afterEstimateByAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    public function afterEstimateByExtendedAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    private function filterOutput($output)
    {
        $orderType = $this->_checkoutSession->getOrderPreference(); 
        $free = [];
        $other=[];
        foreach ($output as $shippingMethod) {
            if($orderType == 'separate_order'){
                if ($shippingMethod->getCarrierCode() == 'flatrate' && $shippingMethod->getMethodCode() == 'flatrate') {
                    $free[] = $shippingMethod;
                }
            }else{
                if ($shippingMethod->getCarrierCode() == 'flatrate' && $shippingMethod->getMethodCode() == 'flatrate') {
                    continue;
                }
                $other[] = $shippingMethod;
            }
        }
        if ($free) {
            return $free;
        }
        if($other){
            return $other;
        }
        return $output;
    }
}
