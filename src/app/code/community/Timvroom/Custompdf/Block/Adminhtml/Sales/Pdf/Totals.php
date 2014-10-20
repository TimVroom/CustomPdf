<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Block_Adminhtml_Sales_Pdf_Totals extends Mage_Sales_Block_Order_Totals
{
    use Timvroom_Custompdf_Block_Adminhtml_Trait_Template;

    /**
     * Default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    public function getObject()
    {
        return Mage::registry('current_object');
    }

    public function getOrder()
    {
        if ($this->_order == null) {
            $this->_order = $this->getObject()->getOrder();
        }
        return parent::getOrder();
    }

    public function reset()
    {
        $this->_order = null;
        return $this;
    }

    protected function _initTotals(){
//        parent::_initTotals();
        $source = $this->getSource();

        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'  => 'subtotal',
            'value' => $source->getSubtotal(),
            'label' => $this->__('Subtotal')
        ));

        /**
         * Add discount
         */
        if (((float)$this->getSource()->getDiscountAmount()) != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->__('Discount (%s)', $source->getDiscountDescription());
            } else {
                $discountLabel = $this->__('Discount');
            }
            $this->_totals['discount'] = new Varien_Object(array(
                'code'  => 'discount',
                'field' => 'discount_amount',
                'value' => $source->getDiscountAmount(),
                'label' => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'  => 'grand_total',
            'field'  => 'grand_total',
            'strong'=> true,
            'value' => $source->getGrandTotal(),
            'label' => $this->__('Grand Total')
        ));

        $order = $this->getOrder();
        $totals = $this->_getTotalsList();
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                foreach ($total->setAmountPrefix('')->getTotalsForDisplay() as $totalData) {
                    $amount = $this->_toFloat(array_pop(explode(' ', $totalData['amount'])));
                    $this->addTotal(new Varien_Object(array(
                        'code'  => $total->getSourceField(),
                        'field'  => $total->getSourceField(),
                        'value' => $amount,
                        'label' => $totalData['label']
                    )));
                }
            }
        }

        if (in_array($this->getOrder()->getPayment()->getMethod(), $this->helper('custompdf')->getRemboursMethods($this->getOrder()->getStore()->getId())))
        {
            $invoices = $this->getOrder()->getInvoiceCollection();
            $paid = $this->getRequest()->getPost('paid');
            $key = '"'.$this->getObject()->getId().'"';
            if ($invoices->getSize() > 1 || (isset($paid[$key]) && $paid[$key] != $this->getOrder()->getGrandTotal())){
                $this->_initRembours($paid[$key]);
            }
        }
        return $this;
    }

    /**
     * Sort totals list
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    protected function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    /**
     * Return total list
     *
     * @return array
     */
    protected function _getTotalsList()
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $index => $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    protected function _tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }

    protected function _initRembours($paid)
    {
        $this->addTotal(new Varien_Object(array(
            'code'  => 'paid',
            'field'  => 'paid',
            'value' => $paid,
            'label' => $this->__('Amount paid')
        )), 'last');

        $this->addTotal(new Varien_Object(array(
            'code'  => 'due',
            'field'  => 'due',
            'value' => $this->getOrder()->getGrandTotal() - (float)$paid,
            'label' => $this->__('Amount due'),
            'strong' => true
        )), 'last');
    }

}
