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
    /**
     * @param Mage_Adminhtml_Controller_Action $controller
     *
     * @return $this
     */
    public function prepareLayout(Mage_Adminhtml_Controller_Action $controller)
    {
        $controller->loadLayout('pdf');

        $root = $controller->getLayout()->getBlock('root');
        if ($root) {
            $controllerClass = $controller->getFullActionName('-');
            $root->addBodyClass($controllerClass);
        }

        return $this;
    }
}