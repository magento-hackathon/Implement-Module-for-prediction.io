<?php
/**
 * Created by PhpStorm.
 * User: amacgregor
 * Date: 31/01/14
 * Time: 5:25 PM
 */

class Magehack_Predictions_Model_Observer extends Mage_Core_Model_Observer
{

   const XML_PATH_COOKIE_DOMAIN = 'web/cookie/cookie_domain';

    /**
     * Generic Create event function to be used by all the events
     *
     * @param $event
     * @internal param $observer
     */
    protected function _createEvent($event)
    {

    }

    /**
     * Triggered when a product page is viewed
     *
     * @param $observer
     */
    public function viewProduct($observer)
    {


    }

    /**
     * Triggered on order place event
     *
     * @param $observer
     */
    public function createOrder($observer)
    {

    }

    /**
     * Triggered on product add to cart complete
     *
     * @param $observer
     */
    public function addToCart($observer)
    {

    }

    /**
     * Triggered on http_response_send_before
     *
     * @param
     */
    public function createUniqueId()
    {

        $cookie = Mage::getSingleton('core/cookie');
        $cookieName = "predictions_unid";
        if(!$cookie->get($cookieName)) {
            list($usec, $sec) = explode(" ", microtime());
            $uniqueCode = intval($usec * 100000) . rand(1, 1000000000);
            $cookie->set($cookieName, $uniqueCode);

        }
    }



}
