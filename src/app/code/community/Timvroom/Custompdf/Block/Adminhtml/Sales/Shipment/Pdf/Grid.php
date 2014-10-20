<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Shipment_Pdf_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('custompdf_sales_shipment_pdf_grid');
        $this->setDefaultSort('increment_id');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addFieldToFilter('main_table.entity_id', array('in' => Mage::registry('alter_shipments')));
        $this->_addOrderToCollection($collection);
        $this->_addShippingAddressToCollection($collection);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Shipment_Collection $collection
     */
    protected function _addOrderToCollection($collection)
    {
        $collection->getSelect()->joinLeft(array('order' => $collection->getTable('sales/order')), 'main_table.order_id = order.entity_id',
            array(
                'order_increment_id' => 'increment_id',
                'base_grand_total', 'grand_total',
                'paid'               => new Zend_Db_Expr('ROUND(grand_total, 2)')
            )
        );
    }

    protected function _addShippingAddressToCollection($collection)
    {
        $collection->getSelect()->joinLeft(array('shipping_address' => $collection->getTable('sales/order_address')), 'main_table.shipping_address_id = shipping_address.entity_id',
            array(
                'customer_name'  => new Zend_Db_Expr('CONCAT_WS(" ",shipping_address.prefix, shipping_address.firstname, shipping_address.middlename, shipping_address.lastname, shipping_address.suffix )'),
                'customer_email' => 'shipping_address.email',
                'city', 'postcode', 'street', 'telephone'
            )
        );
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
//        $this->addColumn('entity_id[]', array(
//            'header' => Mage::helper('sales')->__('Entity_id'),
//            'index' => 'entity_id',
//            'type' => 'input',
//            'editable' => false,
//            'inline_css' => 'input_entity_id',
//            'width' => '0px',
//            'column_css_class'=>'no-display',//this sets a css class to the column row item
//            'header_css_class'=>'no-display',//this sets a css class to the column header
//        ));
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('ID'),
            'index'  => 'increment_id'
        ));

        $this->addColumn('order_id', array(
            'header' => Mage::helper('sales')->__('Order ID'),
            'index'  => 'order_increment_id'
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('sales')->__('Customer'),
            'index'  => 'customer_name'
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Email'),
            'index'  => 'customer_email'
        ));

        $this->addColumn('postcode', array(
            'header' => Mage::helper('sales')->__('Postcode'),
            'index'  => 'postcode'
        ));

        $this->addColumn('grand_total', array(
            'header'        => Mage::helper('sales')->__('Grand total'),
            'type'          => 'price',
            'width'         => '80px',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'index'         => 'grand_total',
        ));

        $this->addColumn('paid[]', array(
            'header'     => Mage::helper('sales')->__('Amount paid'),
            'width'      => '80px',
            'index'      => 'paid',
            'type'       => 'input',
            'inline_css' => 'input_paid',
            'editable'   => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowClass($item)
    {
        return 'id_' . $item->getId();
    }

    public function getRowClickCallback()
    {
        return false;
    }

    public function getAdditionalJavascript()
    {
        $data = json_encode(array('shipment_ids' => Mage::registry('current_shipments')));
        $js   = <<<JS
function submitForm(url) {
    var rows = $data;
    rows.paid = {};
    $$('.data tbody tr').each(function(element){
        var classes = element.className.split(' '), id;
        for (var i in classes) {
            if(classes[i].indexOf('id_') !== -1) {
                id = classes[i].substr(3);
                break;
            }
        }
        rows.paid['"'+id+'"'] = $(element).select('.input_paid')[0].value;
    });

    post(url, rows);
}
function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    if (FORM_KEY) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", 'form_key');
        hiddenField.setAttribute("value", FORM_KEY);
        form.appendChild(hiddenField);
    }

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            if (typeof params[key] == 'object') {
                for(var par in params[key]) {
                    if(params[key].hasOwnProperty(par)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key +'[' + par + ']');
                        hiddenField.setAttribute("value", params[key][par]);
                        form.appendChild(hiddenField);
                    }
                }
            } else {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", params[key]);
                form.appendChild(hiddenField);
            }
         }
    }

    document.body.appendChild(form);
    form.submit();
}
JS;
        return $js;
    }
}