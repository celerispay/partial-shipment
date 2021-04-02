<?php

namespace Boostsales\PartialShipment\Model;

use Boostsales\PartialShipment\Api\ExtensionAttributesInterface;

class ExtensionAttributes implements ExtensionAttributesInterface
{

    public function loadValue($product)
    {
        $attributes = $product->getExtensionAttributes();
        if($attributes->getStockItem()->getQty() < 1){
            return '0';
        } else{
            return '1';
        }
    }
}
