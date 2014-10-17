<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Helper_Render extends Mage_Core_Helper_Abstract
{
    static $_pageIterator = 1;
    /**
     * @param Mage_Adminhtml_Controller_Action $controller
     *
     * @return $this
     */
    public function prepareLayout(Mage_Adminhtml_Controller_Action $controller)
    {
        $controller->loadLayout('pdf');

        return $this;
    }

    public function getIterator()
    {
        return self::$_pageIterator;
    }

    public function incrementIterator($pageCount)
    {
        if (self::$_pageIterator+1 > $pageCount) {
            $this->setIterator(0);
        }
        self::$_pageIterator++;
        return $this;
    }

    public function setIterator($value)
    {
        self::$_pageIterator = $value;
        return $this;
    }

    public function getPageSize($pdf)
    {
        $cpdf = $pdf->get_cpdf();
        $pid = $cpdf->currentPage;
        $objects = $cpdf->objects;
        $content = $objects[$pid];
        if ($content['t'] == 'contents') {
            $content = $objects[$content['onPage']];
        }
        $c = 0;
        $found = false;
        foreach ($objects as $key => $obj) {
            if ($obj['t'] == 'page') {
                if ($obj['info']['newPage']) {
                    if ($found) {
                        break;
                    }
                    $c = 0;
                }
                $c++;
            }
            if ($key == $pid) {
                $found = true;
            }
        }
        return $c;
    }
}