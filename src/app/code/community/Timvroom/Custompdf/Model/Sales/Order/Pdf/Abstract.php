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
    /** @var DOMPDF $_pdf */
    protected $_pdf;
    /** @var  Timvroom_Custompdf_Helper_Dompdf */
    protected $_helper;
    /** @var  Mage_Adminhtml_Controller_Action $_controllerObject */
    protected $_controllerObject;

    /**
     * Initialize dompdf helper
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('custompdf/dompdf');
    }

    /**
     * Prepare the pdf settings and forces setup
     *
     * @return $this
     * @throws Mage_Adminhtml_Exception
     */
    protected function _preparePdf()
    {
        if ($this->_controllerObject === null) {
            // Controller is not given so misuse debug_backtrace
            $backtrace = debug_backtrace();
            foreach ($backtrace as $trace) {
                if (is_a($trace['object'], 'Mage_Core_Controller_Varien_Action')) {
                    $this->_controllerObject= $trace['object'];
                    break;
                }
            }
        }
        if (!is_a($this->_controllerObject, 'Mage_Core_Controller_Varien_Action')) {
            throw new Mage_Adminhtml_Exception("Cannot properly load the controller object. This is required");
        }
        $this->_pdf = $this->_helper->getDomPdf();
        $this->_setupLayout();

        return $this;
    }

    public function getPdf($objects = array())
    {
        Mage::register('pdf_objects', $objects);

        $this->_preparePdf();

        $this->_controllerObject->renderLayout();
        $this->_pdf->load_html($this->_controllerObject->getResponse()->getBody());

        $this->_controllerObject->getResponse()->setBody('');
        return $this;
    }

    public function render(){
        return $this->_pdf->output();
    }

    protected function _setupLayout()
    {
        Mage::helper('custompdf/render')->prepareLayout($this->_controllerObject);
    }
}