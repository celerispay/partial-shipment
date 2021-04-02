<?php

namespace Boostsales\PartialShipment\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Boostsales\PartialShipment\Api\QuoteHandlerInterface;
use Boostsales\PartialShipment\Api\ExtensionAttributesInterface;


class QuoteHandler implements QuoteHandlerInterface
{

    private $checkoutSession;

    private $extensionAttributes;

    public function __construct(
        CheckoutSession $checkoutSession,
        ExtensionAttributesInterface $extensionAttributes
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->extensionAttributes = $extensionAttributes;
    }

    public function normalizeQuotes($quote)
    {
        $backorder_preference = $this->checkoutSession->getOrderPreference();
        if($backorder_preference == "separate_order"){
            $groups = [];

            foreach ($quote->getAllVisibleItems() as $item) {
                $product = $item->getProduct();
                
                $attribute = $this->getProductAttributes($product);
                
                if ($attribute === false) {
                    return false;
                }
                
                $groups[$attribute][] = $item;
            }   
            
            if (count($groups) > 1) {
                return $groups;
            }
        }
        return false;
    }


    public function getProductAttributes($product)
    {
        $extensionAttribute = $this->extensionAttributes->loadValue($product);
        if ($extensionAttribute !== false) {
            return $extensionAttribute;
        }
        return false;
    }

    public function collectAddressesData($quote)
    {
        $billing = $quote->getBillingAddress()->getData();
        unset($billing['id']);
        unset($billing['quote_id']);

        $shipping = $quote->getShippingAddress()->getData();
        unset($shipping['id']);
        unset($shipping['quote_id']);

        return [
            'payment' => $quote->getPayment()->getMethod(),
            'billing' => $billing,
            'shipping' => $shipping
        ];
    }

    public function setCustomerData($quote, $split)
    {
        $split->setStoreId($quote->getStoreId());
        $split->setCustomer($quote->getCustomer());
        $split->setCustomerIsGuest($quote->getCustomerIsGuest());

        if ($quote->getCheckoutMethod() === CartManagementInterface::METHOD_GUEST) {
            $split->setCustomerId(null);
            $split->setCustomerEmail($quote->getBillingAddress()->getEmail());
            $split->setCustomerIsGuest(true);
            $split->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
        }
        return $this;
    }

    public function populateQuote($quotes, $split, $items, $addresses, $payment)
    {
        $this->recollectTotal($quotes, $items, $split, $addresses);
        
        $this->setPaymentMethod($split, $addresses['payment'], $payment);

        return $this;
    }

    public function recollectTotal($quotes, $items, $quote, $addresses)
    {
        $tax = 0.0;
        $discount = 0.0;
        $finalPrice = 0.0;

        foreach ($items as $item) {
            
            $tax += $item->getData('tax_amount');
            $discount += $item->getData('discount_amount');

            $finalPrice += ($item->getPrice() * $item->getQty());
        }

        
        $quote->getBillingAddress()->setData($addresses['billing']);
        $quote->getShippingAddress()->setData($addresses['shipping']);

        
        $shipping = $this->shippingAmount($quotes, $quote);

        
        foreach ($quote->getAllAddresses() as $address) {
            
            $grandTotal = (($finalPrice + $shipping + $tax) - $discount);

            $address->setBaseSubtotal($finalPrice);
            $address->setSubtotal($finalPrice);
            $address->setDiscountAmount($discount);
            $address->setTaxAmount($tax);
            $address->setBaseTaxAmount($tax);
            $address->setBaseGrandTotal($grandTotal);
            $address->setGrandTotal($grandTotal);
        }
        return $this;
    }

    public function shippingAmount($quotes, $quote, $total = 0.0)
    {
        
        if ($quote->hasVirtualItems() === true) {
            return $total;
        }
        $shippingTotals = $quote->getShippingAddress()->getShippingAmount();

        if ($shippingTotals > 0) {
            
            $total = (float) $shippingTotals;
            $quote->getShippingAddress()->setShippingAmount($total);
        }
        return $total;
    }

    public function setPaymentMethod($split, $payment, $paymentMethod)
    {
        $split->getPayment()->setMethod($payment);

        if ($paymentMethod) {
            $split->getPayment()->setQuote($split);
            $data = $paymentMethod->getData();
            $split->getPayment()->importData($data);
        }
        return $this;
    }

    public function defineSessions($split, $order, $orderIds)
    {
        $this->checkoutSession->setLastQuoteId($split->getId());
        $this->checkoutSession->setLastSuccessQuoteId($split->getId());
        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($order->getStatus());
        $this->checkoutSession->setOrderIds($orderIds);

        return $this;
    }
}
