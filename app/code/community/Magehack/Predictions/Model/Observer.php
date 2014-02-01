<?php
/**
 * Created by PhpStorm.
 * User: amacgregor
 * Date: 31/01/14
 * Time: 5:25 PM
 */

class Magehack_Predictions_Model_Observer extends Mage_Core_Model_Observer
{

    /**
     * Generic Create event function to be used by all the events
     *
     * @param $data
     * @param null $action
     * @throws
     * @internal param $eventData
     * @internal param $event
     * @internal param $observer
     */
    protected function _addEventToQueue($data, $action = null)
    {
        $queue = Mage::getModel('predictions/queue');

        try { // Parse EventData into the model and save
            if (isset($data)) {
                $queue->setData($data);

                if (!is_null($action)) {
                    $queue->setEventType($action);
                } else {
                    throw Mage::exception('Magehack_Predictions', Mage::helper('predictions')->__('Missing Action type.'));
                }

                $queue->save();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Triggered when a product page is viewed
     *
     * @param $observer
     */
    public function viewProduct($observer)
    {

        $predictionHelper = Mage::helper('predictions');

        $event = $observer->getEvent();
        $queueRecord = array();
        $queueRecord['cookie_id'] = $predictionHelper->getCurrentUserUniqueId();


        try {

            //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }

            // Grab product from the observer
            $product = $event->getProduct();

            $queueRecord['product_id'] = $product->getId();

            $this->_addEventToQueue($queueRecord, Magehack_Predictions_Model_Queue::EVENT_TYPE_VIEW);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Triggered on order place event
     *
     * @param $observer
     */
    public function createOrder($observer)
    {

        $predictionHelper = Mage::helper('predictions');

        $event = $observer->getEvent();
        $queueRecord = array();
        $queueRecord['cookie_id'] = $predictionHelper->getCurrentUserUniqueId();

        // Grab products from the observer
        try {

            //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }


            // Grab product list from the observer
            $order      = $event->getOrder();
            $orderItems = $order->getAllItems();

            foreach($orderItems as $item)
            {
                $queueRecord['product_id'] = $item->getProductId();
                $this->_addEventToQueue($queueRecord, Magehack_Predictions_Model_Queue::EVENT_TYPE_ORDER);
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }

    }

    /**
     * Triggered on product add to cart complete
     *
     * @param $observer
     */
    public function addToCart($observer)
    {

        $predictionHelper = Mage::helper('predictions');

        $event = $observer->getEvent();
        $queueRecord = array();
        $queueRecord['cookie_id'] = $predictionHelper->getCurrentUserUniqueId();

        try {
            // Grab product from the observer
            $product = $event->getProduct();

            $queueRecord['product_id'] = $product->getId();

            $this->_addEventToQueue($queueRecord, Magehack_Predictions_Model_Queue::EVENT_TYPE_ADD_TO_CART);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function mergeCustomerLogin($observer)
    {
        $predictionHelper = Mage::helper('predictions');

        // Get Customer from the observer
        $customer = $observer->getEvent()->getCustomer();

        // Get Unique User ID (by cookie, not Customer)
        $queueRecord['cookie_id'] = $predictionHelper->getCurrentUserUniqueId();

        // Get a Collection of events that that have the same cookie but not the customer id
        $queueCollection = Mage::getModel('predictions/queue')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('null' => true))
            ->addFieldToFilter('cookie_id', array('eq' => $queueRecord['cookie_id']));


        // Use an iterator for  updating the events with the customer id
        // [todo] - Test for performance
        Mage::getSingleton('core/resource_iterator')->walk(
            $queueCollection->getSelect(),
            array(array($this, 'queueCallback')),
            array('customer_id' => $customer->getId())
        );

    }

    public function queueCallback($args)
    {
        // [todo] - Test for correct implementation
        $_queueEvent = Mage::getModel('predictions/queue');

        $_queueEvent->setData($args['row']);
        $_queueEvent->setCustomerId($args['customer_id']);
        $_queueEvent->save();
    }

}
