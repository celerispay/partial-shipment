<?php

namespace Boostsales\PartialShipment\Api;


interface QuoteHandlerInterface
{

    public function normalizeQuotes($quote);

    public function getProductAttributes($product);

    public function collectAddressesData($quote);

    public function setCustomerData($quote, $split);

    public function populateQuote($quotes, $split, $items, $addresses, $payment);

    public function recollectTotal($quotes, $items, $quote, $addresses);

    public function shippingAmount($quotes, $quote, $total = 0.0);
    
    public function setPaymentMethod($paymentMethod, $split, $payment);

    public function defineSessions($split, $order, $orderIds);
}
