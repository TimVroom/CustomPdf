<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Pdf_Abstract extends Timvroom_Custompdf_Block_Adminhtml_Template
{

    public function getObject()
    {
        return Mage::registry('current_object');
    }
    public function getOrder()
    {
        return $this->getObject()->getOrder();
    }

    public function getBillingAddress()
    {
        return $this->getorder()->getBillingAddress();
    }

    public function getShippingAddress()
    {
        return $this->getorder()->getShippingAddress();
    }
}