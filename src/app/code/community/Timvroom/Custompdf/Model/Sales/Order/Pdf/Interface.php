<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
interface Timvroom_Custompdf_Model_Sales_Order_Pdf_Interface {
    /**
     * Returns instance of self
     *
     * @return Timvroom_Custompdf_Model_Sales_Order_Pdf_Interface
     */
    public function getPdf();

    /**
     * This will return the actual output of the pdf in string format
     *
     * @return string
     */
    public function render();
}