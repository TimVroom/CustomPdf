<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */

class Timvroom_Custompdf_Model_System_Config_Source_Fontfamily
{
    public function toOptionArray()
    {
        $fonts = Mage::helper('custompdf/dompdf')->getFontFamilies();
        $options = array();
        foreach ($fonts as $font) {
            $options[] = array('value' => $font, 'label' => $font);
        }
    }
}