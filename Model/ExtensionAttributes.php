<?php

namespace Boostsales\PartialShipment\Model;

use Boostsales\PartialShipment\Api\ExtensionAttributesInterface;

class ExtensionAttributes implements ExtensionAttributesInterface
{

    public function loadValue($product)
    {
        $attributes = $product->getExtensionAttributes();
        if($attributes->getStockItem()->getQty() < 1){
            return "out";
        }elseif($attributes->getStockItem()->getQty() > 1){
            return "in";
        }

    }
}
