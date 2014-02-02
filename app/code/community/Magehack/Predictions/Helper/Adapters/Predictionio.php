<?php
class Magehack_Predictions_Helper_Adapters_Predictionio implements Magehack_Predictions_Helper_Adapters_Interface {
    protected $sdk;

    public function __construct() {
        require_once(Mage::getBaseDir('lib') . '/Predictions/PredictionIOSDK.php');
        $this->sdk = new PredictionIOSDK(Mage::helper('predictions')->getPredictionIOBaseUri(), Mage::helper('predictions')->getPredictionIOApiKey());
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
     * Notify PredictionIO of ordered product.
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
     * @param int Magento customer id or cookie id.
     * @param int Magento product id.
     * @return null
     */
    public function productReturn($user_identifier, $product_id) {
        $this->sdk->dislikeItem($user_identifier, $product_id);
    }


    public function createUser($user_identifier) {
        $this->sdk->addUser($user_identifier);
    }

    public function createItem($item_identifier) {
        $this->sdk->addItem($item_identifier, 'product');
    }

    public function getRecommendations($user_identifier) {
        // [todo] replace products with the engine name from config
        $recs = $this->sdk->getRecommendations($user_identifier, Mage::helper('predictions')->getPredictionIOEngineName());
        return $recs->pio_iids;
    }
}