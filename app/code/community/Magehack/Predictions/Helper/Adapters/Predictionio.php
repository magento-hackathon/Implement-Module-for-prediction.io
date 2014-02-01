<?php
class Magehack_Predictions_Helper_Adapters_Predictionio implements Magehack_Predictions_Helper_Adapters_Interface {
    protected $sdk;

    public function __construct() {
        require_once(Mage::getBaseDir('lib') . '/Predictions/PredictionIOSDK.php');
        $this->sdk = new PredictionsIOSDK(Mage::helper('predictions')->getBaseUri(), Mage::helper('predictions')->getApiKey());
    }

    /**
     * Notify PredictionIO of the product view.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function view($user_identifier, $product_id) {
        $this->sdk->viewItem($user_identifier, $product_id);
    }

    /**
     * Notify PredictionIO of the add to cart.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToCart($user_identifier, $product_id) {
        $this->sdk->likeItem($user_identifier, $product_id);
    }

    /**
     * Notify PredictionIO of the add to wishlist.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToWishlist($user_identifier, $product_id) {
        $this->sdk->likeItem($user_identifier, $product_id);
    }

    /**
     * Process the array of orders and notify PredictionIO about each.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function order($user_identifier, $product_id) {
        $this->sdk->conversionItem($user_identifier, $product_id);
    }

    /**
     * Notify PredictionsIO of a return.
     *
     * @TODO: This method is never called, should add EE support.
     *
     * @param int Magento customer id or cookie id.
     * @param int Magento product id.
     * @return null
     */
    public function productReturn($user_identifier, $product_id) {
        $this->sdk->dislikeItem($user_identifier, $product_id);
    }
}