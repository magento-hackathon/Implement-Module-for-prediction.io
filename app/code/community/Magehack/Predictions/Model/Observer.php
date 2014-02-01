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


        try { //If the customer is logged in
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $queueRecord['customer_id'] = $customerData->getId();
            }

            // If there is a cookie present
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

        $event = $observer->getEvent();

        // Grab products from the observer
    }

    /**
     * Triggered on product add to cart complete
     *
     * @param $observer
     */
    public function addToCart($observer)
    {

        $event = $observer->getEvent();

        // Grab product from the observer
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
