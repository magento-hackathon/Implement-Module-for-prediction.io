<?php
interface Magehack_Predictions_Helper_Adapters_Interface {

    /**
     * Called when a product is added to the cart in Magento.
     *
     * @param int Magento customer id.
     * @param int Magento product id.
     * @return null
     */
    public function view($customer_id, $product_id);

    /**
     * Called when a product is added to the cart in Magento.
     *
     * @param int Magento customer id.
     * @param int Magento product id.
     * @return null
     */
    public function addToCart($customer_id, $product_id);

    /**
     * Called when a product is added to a wishlist in Magento.
     *
     * @param int Magento customer id.
     * @param int Magento product id.
     * @return null
     */
    public function addToWishlist($customer_id, $product_id);

    /**
     * Called when a product is ordered in Magento.
     *
     * @param int Magento customer id.
     * @param int Magento product id.
     * @return null
     */
    public function order($customer_id, $product_id);

    /**
     * Called when a product is returned
     *
     * @TODO: This method is never called, should add EE returns support.
     *
     * @param int Magento customer id.
     * @param int Magento product id.
     * @return null
     */
    public function ProductReturn($customer_id, $product_id);
}