<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
abstract class Timvroom_Custompdf_Model_Sales_Order_Pdf_Abstract extends Varien_Object implements Timvroom_Custompdf_Model_Sales_Order_Pdf_Interface
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

    public function getPdf($objects = array())
    {
        $this->_preparePdf();
        $layout = $this->_controllerObject->getLayout();
        if (!($root = $layout->getBlock('root'))) {
            Mage::throwException('Failed to load root block');
        }
        $root->setData('objects', $objects);
        $this->_controllerObject->renderLayout();
        $html = $this->_controllerObject->getResponse()->getBody();
        Mage::log($html,null,'pdf-html.log');
        $this->_pdf->load_html($html);
        $this->_controllerObject->getResponse()->setBody('');

        return $this;
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
                if ($trace['object'] instanceof Mage_Core_Controller_Varien_Action) {
                    $this->_controllerObject = $trace['object'];
                    break;
                }
            }
        }
        if (!($this->_controllerObject instanceof Mage_Core_Controller_Varien_Action)) {
            throw new Mage_Adminhtml_Exception("Cannot properly load the controller object. This is required");
        }
        $this->_pdf = $this->_helper->getDomPdf();
        $this->_setupLayout();

        return $this;
    }

    protected function _setupLayout()
    {
        Mage::helper('custompdf/render')->prepareLayout($this->_controllerObject);
    }

    public function render()
    {
        $this->_pdf->render();
        $this->addFooter();
        $output = $this->_pdf->output();
        return $output;
    }

    public function addFooter()
    {
        $canvas = $this->_pdf->get_canvas();
        $footer = $canvas->open_object();
        $canvas->page_script("
            \$helper = Mage::helper('custompdf/render');
            \$pageCount = \$helper->getPageSize(\$pdf);
            if (\$pageCount > 1){
                \$font = Font_Metrics::get_font('helvetica', 'normal');
                \$size = 9;
                \$y = \$pdf->get_height() - 24;
                \$x = \$pdf->get_width() - 15 - Font_Metrics::get_text_width('1/1', \$font, \$size);
                \$pdf->text(\$x, \$y, \$helper->getIterator().'/'. \$pageCount, \$font, \$size);
            }
            \$helper->incrementIterator(\$pageCount);
        ");
        $canvas->close_object();
        $canvas->add_object($footer, "all");
    }

}