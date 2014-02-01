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

        $event          = $observer->getEvent();
        $queueRecord    = array();
        $cookie_id      = $predictionHelper->getUniqueId();


        try {

            //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }

            // If there is a cookie present
            // [todo] - Refactor this code to be in a single function

            if (isset($cookie_id)) {
                $queueRecord['cookie_id'] = $cookie_id;
            } else {
                $cookie = Mage::getSingleton('core/cookie');
                $uniqueCode = $predictionHelper->generateUniqueId();

                $cookie->set("predictions_unid", $uniqueCode);

                $queueRecord['cookie_id'] = $uniqueCode;
            }

            // Grab product from the observer
            $product = $event->getProduct();

            $queueRecord['product_id'] = $product->getSku();

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

        $event          = $observer->getEvent();
        $queueRecord    = array();
        $cookie_id      = $predictionHelper->getUniqueId();

        // Grab products from the observer
        try {

            //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }

            // If there is a cookie present
            // [todo] - Refactor this code to be in a single function

            if (isset($cookie_id)) {
                $queueRecord['cookie_id'] = $cookie_id;
            } else {
                $cookie = Mage::getSingleton('core/cookie');
                $uniqueCode = $predictionHelper->generateUniqueId();

                $cookie->set("predictions_unid", $uniqueCode);

                $queueRecord['cookie_id'] = $uniqueCode;
            }

            // Grab product list from the observer
            $order      = $event->getOrder();
            $orderItems = $order->getAllItems();

            foreach($orderItems as $item)
            {
                $queueRecord['product_id'] = $item->getSku();
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

        $event          = $observer->getEvent();
        $queueRecord    = array();
        $cookie_id      = $predictionHelper->getUniqueId();

        // Grab product from the observer

        try {

            //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }

            // If there is a cookie present
            // [todo] - Refactor this code to be in a single function

            if (isset($cookie_id)) {
                $queueRecord['cookie_id'] = $cookie_id;
            } else {
                $cookie = Mage::getSingleton('core/cookie');
                $uniqueCode = $predictionHelper->generateUniqueId();

                $cookie->set("predictions_unid", $uniqueCode);

                $queueRecord['cookie_id'] = $uniqueCode;
            }

            // Grab product from the observer
            $product = $event->getProduct();

            $queueRecord['product_id'] = $product->getSku();

            $this->_addEventToQueue($queueRecord, Magehack_Predictions_Model_Queue::EVENT_TYPE_ADD_TO_CART);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function mergeCustomerLogin($observer)
    {
        $predictionHelper = Mage::helper('predictions');

        // Get Customer from the observer
        $customer   = $observer->getEvent()->getCustomer();
        $cookie_id  = $predictionHelper->getUniqueId();

        // Check if there is cookie id, if NOT set it
        // [todo] - Refactor this code to be in a single function
        if (isset($cookie_id)) {
            $queueRecord['cookie_id'] = $cookie_id;
        } else {
            $cookie = Mage::getSingleton('core/cookie');
            $uniqueCode = $predictionHelper->generateUniqueId();

            $cookie->set("predictions_unid", $uniqueCode);

            $queueRecord['cookie_id'] = $uniqueCode;
        }

        // Get a Collection of events that that have the same cookie but not the customer id

        $queueCollection = Mage::getModel('predictions/queue')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('null' => true))
            ->addFieldToFilter('cookie_id', array('eq' => $cookie_id));


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

        $_queueEvent->setData($args['rows']);
        $_queueEvent->setCustomerId($args['customer_id']);
        $_queueEvent->save();
    }

    /**
     * Triggered on http_response_send_before
     *
     * @param
     */
    public function createUniqueId()
    {
        $predictionHelper = Mage::helper('predictions');

        $cookie     = Mage::getSingleton('core/cookie');
        $cookieName = "predictions_unid";

        if(!$cookie->get($cookieName)) {
            $uniqueCode = $predictionHelper->generateUniqueId();
            $cookie->set($cookieName, $uniqueCode);
        }
    }
}
