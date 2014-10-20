<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Item extends Timvroom_Custompdf_Block_Adminhtml_Template
{
    protected $_itemRenders = array();
    protected $_items;

    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'custompdf/adminthml_sales_item_renderer', 'timvroom/pdf/items/renderer/default.phtml');
    }

    public function getItems()
    {
        if ($this->hasObject() && $this->getObject()->getOrder()->getId() != Mage::registry('current_object')->getOrder()->getId()) {
            $this->_items = null;
            $this->unsObject();
        }
        if ($this->_items === null) {
            if (!$this->hasObject() && Mage::registry('current_object')) {
                $this->setObject(Mage::registry('current_object'));
            }

            if (is_callable(array($this->getObject()->getOrder(), 'getAllItems')) && $this->getObject()->getOrder()->getAllItems()) {
                $this->_items = $this->getObject()->getOrder()->getAllItems();
            } elseif (is_callable(array($this->getObject()->getOrder(), 'getItems')) && $this->getObject()->getOrder()->getItems()) {
                $this->_items = $this->getObject()->getOrder()->getItems();
            }
            if ($this->_items === null) {
                $this->_items = array();
            } else {
                foreach ($this->_items as $key => $item){
                    if ($item && $item->getParentItem()) {
                        unset($this->_items[$key]);
                    }
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get renderer information by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRendererInfo($type)
    {
        if (isset($this->_itemRenders[$type])) {
            return $this->_itemRenders[$type];
        }
        return $this->_itemRenders['default'];
    }

    /**
     * Get renderer block instance by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['blockInstance'])) {
            $this->_itemRenders[$type]['blockInstance'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                ->setTemplate($this->_itemRenders[$type]['template'])
                ->setRenderedBlock($this);
        }

        return $this->_itemRenders[$type]['blockInstance'];
    }

    /**
     * Add renderer for item product type
     *
     * @param   string $productType
     * @param   string $blockType
     * @param   string $template
     * @return  Mage_Checkout_Block_Cart_Abstract
     */
    public function addItemRender($productType, $blockType, $template)
    {
        $this->_itemRenders[$productType] = array(
            'block' => $blockType,
            'template' => $template,
            'blockInstance' => null
        );
        return $this;
    }

    /**
     * Get item row html
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  string
     */
    public function getItemHtml(Varien_Object $item)
    {
        if (!($item instanceof Mage_Sales_Model_Order_Item) && is_callable(array($item, 'getOrderItem')) && $item->getOrderItem()) {
            $item = $item->getOrderItem();
        }
        $renderer = $this->getItemRenderer($item->getProductType())->clear()->setItem($item);
        return $renderer->toHtml();
    }
}