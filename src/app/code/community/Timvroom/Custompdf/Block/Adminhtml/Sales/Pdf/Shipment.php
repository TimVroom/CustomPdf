<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Pdf_Shipment extends Timvroom_Custompdf_Block_Adminhtml_Sales_Pdf_Abstract
{

    public function getShipment()
    {
        return $this->getObject();
    }

    public function getShipmentItems(){

    }
}