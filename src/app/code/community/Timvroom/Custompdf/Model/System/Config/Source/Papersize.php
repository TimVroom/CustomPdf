<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Model_System_Config_Source_Papersize
{
    public function toOptionArray()
    {
        $sizes = Mage::helper('custompdf/dompdf')->getPaperSizes();
        $options = array();
        foreach ($sizes as $size){
            $options[] = array('value' => $size, 'label' => $size);
        }

        return $options;
    }
}