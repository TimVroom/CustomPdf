<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'ShipmentController.php';

class Timvroom_Custompdf_Adminhtml_Sales_ShipmentController extends Mage_Adminhtml_Sales_ShipmentController
{
    public function pdfshipmentsAction()
    {
        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $paid = $this->getRequest()->getPost('paid', array());
            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            $validate  = array();
            foreach ($shipments as $shipment) {
                if (in_array($shipment->getOrder()->getPayment()->getMethod(), Mage::helper('custompdf')->getRemboursMethods($shipment->getStoreId()))) {
                    $validate[] = $shipment->getId();
                }
            }
            if (count($validate) > 0 && count($paid) != count($validate)) {
                Mage::register('alter_shipments', $validate);
                Mage::register('current_shipments', $shipmentIds);
                $this->loadLayout(array(
                    'default',
//                    strtolower($this->getFullActionName()),
                    'adminhtml_order_shipment_pdfshipments_rembours'
                ), false, false);
                // add default layout handles for this action
                $this->addActionLayoutHandles();
                $this->getLayout()->getUpdate()->removeHandle(strtolower($this->getFullActionName()));
                $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
                $this->_isLayoutLoaded = true;
                // Force the use of custom page
                $this->renderLayout();
            } else {
                parent::pdfshipmentsAction();
            }
        }
    }
}