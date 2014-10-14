<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
abstract class Timvroom_Custompdf_Model_Sales_Order_Pdf_Abstract extends Mage_Sales_Model_Order_Pdf_Abstract
{
    protected $_pdf;
    /** @var  Timvroom_Custompdf_Helper_Dompdf */
    protected $_helper;

    /**
     * Initialize dompdf helper
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('custompdf/dompdf');
    }

    protected function _preparePdf()
    {
        $this->_pdf = $this->_helper->getDomPdf();
        $this->_setupLayout();
    }

    protected function _setupLayout()
    {

    }
}