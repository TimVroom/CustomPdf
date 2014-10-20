<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Shipment_Pdf extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'custompdf';
        $this->_controller = 'adminhtml_sales_shipment_pdf';
        $this->_headerText = Mage::helper('sales')->__('Shipment PDF rembours info');
        parent::__construct();

        $this->_removeButton('add');
        $this->_addSubmitButton();
    }

    protected function _addSubmitButton()
    {
        $this->_addButton('submit', array(
            'label'     => $this->helper('sales')->__('Submit'),
            'onclick'   => 'submitForm(\'' . $this->getSubmitUrl() .'\')',
            'class'     => '',
        ));
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/*');
    }
}