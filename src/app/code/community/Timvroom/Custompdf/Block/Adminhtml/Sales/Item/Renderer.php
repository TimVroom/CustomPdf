<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Item_Renderer extends Timvroom_Custompdf_Block_Adminhtml_Template
{
    protected $_productOptions;
    /**
     * @var Mage_Catalog_Model_Product $_product
     */
    protected $_product;
    /**
     * @var Mage_Catalog_Model_Product $_configurableParent
     */
    protected $_configurableParent;

    public function setItem(Varien_Object $item)
    {
        $this->setData('item', $item);
        return $this;
    }

    public function clear()
    {
        $this->_productOptions     = null;
        $this->_product            = null;
        $this->_configurableParent = null;
        $this->unsetData('item');
        return $this;
    }

    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Get current product options
     *
     * @return array|bool
     */
    public function getOptionList()
    {
        $options = false;
        if (Mage::getStoreConfig('SCP_options/cart/show_custom_options')) {
            $options = parent::getOptionList();
        }

        if (Mage::getStoreConfig('SCP_options/cart/show_config_product_options')) {
            if ($this->getConfigurableProductParentId()) {
                $attributes = $this->getConfigurableProductParent()->getTypeInstance()->getUsedProductAttributes();
                foreach ($attributes as $attribute) {
                    $options[] = array(
                        'label'     => $attribute->getFrontendLabel(),
                        'value'     => $this->getProduct()->getAttributeText($attribute->getAttributeCode()),
                        'option_id' => $attribute->getId(),
                    );
                }
            }
        }

        return $options;
    }

    protected function getConfigurableProductParentId()
    {
        if ($this->_productOptions === null) {
            if ($this->getItem()->hasProductOptions()) {
                try {
                    $this->_productOptions = $this->getItem()->getProductOptions();
                } catch (Exception $e) {

                }
            }
        }
        if ($this->_productOptions !== null) {
            if ($this->getItem()->getOptionByCode('cpid')) {
                return $this->getItem()->getOptionByCode('cpid')->getValue();
            }
            #No idea why in 1.5 the stuff in buyRequest isn't auto-decoded from info_buyRequest
            #but then it's Magento we're talking about, so I've not a clue what's *meant* to happen.
            try {
                $buyRequest = $this->_productOptions['info_buyRequest'];
                if (!empty($buyRequest['cpid'])) {
                    return $buyRequest['cpid'];
                }
            } catch (Exception $e) {
            }
        }

        return null;
    }

    protected function getConfigurableProductParent()
    {
        if (!$this->_configurableParent || $this->_configurableParent->getId() != $this->getConfigurableProductParentId()) {
            $this->_configurableParent = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getConfigurableProductParentId());
        }

        return $this->_configurableParent;
    }

    public function getProduct()
    {
        if (!$this->_product || $this->_product->getSku() != $this->getItem()->getSku()) {
            $this->_product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId());
            $this->_product->load($this->_product->getIdBySku($this->getItem()->getSku()));
        }

        return $this->_product;
    }
}

