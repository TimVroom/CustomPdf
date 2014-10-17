<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Template
{

    /**
     * Force adminhtml environment with the templates
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _beforeToHtml()
    {
        if (!$this->hasArea()) {
            $this->setArea('adminhtml');
        }
        if (!$this->hasPackage()) {
            $this->setPackage('default');
        }
        return parent::_beforeToHtml();
    }

    /**
     * Get absolute path to template
     *
     * @return string
     */
    public function getTemplateFile()
    {
        $params = array('_relative' => true);
        $area   = $this->getArea();
        if ($area) {
            $params['_area'] = $area;
        }

        if ($this->hasPackage()) {
            $params['_package'] = $this->getPackage();
        }

        $templateName = Mage::getDesign()->getTemplateFilename($this->getTemplate(), $params);
        return $templateName;
    }
}