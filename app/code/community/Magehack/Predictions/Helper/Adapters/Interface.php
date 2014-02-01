<?php
/**
 * [todo] add support for batch processing of training data on supported engines
 */
interface Magehack_Predictions_Helper_Adapters_Interface {
    /**
     * Called when a product is added to the cart in Magento.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function view($user_identifier, $product_id);

    /**
     * Called when a product is added to the cart in Magento.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToCart($user_identifier, $product_id);

    /**
     * Called when a product is added to a wishlist in Magento.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function addToWishlist($user_identifier, $product_id);

    /**
     * Called when a product is ordered in Magento.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function order($user_identifier, $product_id);

    /**
     * Called when a product is returned
     *
     * [todo] - This method is never called, should add EE returns support.
     *
     * @param int Magento customer id or cookie id
     * @param int Magento product id
     * @return null
     */
    public function productReturn($user_identifier, $product_id);
}