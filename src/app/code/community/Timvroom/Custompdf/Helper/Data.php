<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PDF_TITLE = 'custompdf/pdf/title';
    const XML_PATH_PDF_DISCLAIMER = 'custompdf/pdf/disclaimer';

    public function getPdfTitle($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PDF_TITLE, $storeId);
    }

    public function getPdfDisclaimer($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PDF_DISCLAIMER, $storeId);
    }
}