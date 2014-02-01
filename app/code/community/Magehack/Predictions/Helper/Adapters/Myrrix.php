<?php
class Magehack_Predictions_Helper_Adapters_Myrrix implements Magehack_Predictions_Helper_Adapters_Interface {
    protected $sdk;

    public function __construct() {
        require_once(Mage::getBaseDir('lib') . '/Predictions/MyrrixSDK.php');
        $this->sdk = new MyrrixSDK(Mage::helper('predictions')->getMyrrixBaseUri());
    }

    /**
     * Notify Myrrix of the product view.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function view($user_identifier, $product_id) {
        $this->sdk->ingest($user_identifier . ',' . $product_id . ',' . Mage::helper('predictions')->getMyrrixViewPoints());
    }

    /**
     * Notify Myrrix of the add to cart.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToCart($user_identifier, $product_id) {
        $this->sdk->ingest($user_identifier . ',' . $product_id . ',' . Mage::helper('predictions')->getMyrrixAddToCartPoints());
    }

    /**
     * Notify Myrrix of the add to wishlist.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToWishlist($user_identifier, $product_id) {
        $this->sdk->ingest($user_identifier . ',' . $product_id . ',' . Mage::helper('predictions')->getMyrrixAddToWishlistPoints());
    }

    /**
     * Notify Myrrix of ordered product.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function order($user_identifier, $product_id) {
        $this->sdk->ingest($user_identifier . ',' . $product_id . ',' . Mage::helper('predictions')->getMyrrixOrderPoints());
    }

    /**
     * Notify Myrrix of a return.
     *
     * [todo] This method is never called, should add EE support.
     * [todo] Push this data to Myrrix
     *
     * @param int Magento customer id or cookie id.
     * @param int Magento product id.
     * @return null
     */
    public function productReturn($user_identifier, $product_id) {}
}