<?php

class Magehack_Predictions_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PREDICTIONS_GENERAL_ENABLED = 'predictions/general/enabled';
    const XML_PATH_PREDICTIONS_GENERAL_RELATED_PRODUCTS = 'predictions/general/related_products';
    const XML_PATH_PREDICTIONS_ENGINE = 'predictions/general/engine';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_API_KEY = 'predictions/predictionio/api_key';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_BASE_URI = 'predictions/predictionio/base_uri';
    const XML_PATH_PREDICTIONS_MYRRIX_BASE_URI = 'predictions/myrrix/base_uri';
    const XML_PATH_PREDICTIONS_MYRRIX_VIEW_POINTS = 'predictions/myrrix/view_points';
    const XML_PATH_PREDICTIONS_MYRRIX_ADDTOCART_POINTS = 'predictions/myrrix/addtocart_points';
    const XML_PATH_PREDICTIONS_MYRRIX_ADDTOWISHLIST_POINTS = 'predictions/myrrix/addtowishlist_points';
    const XML_PATH_PREDICTIONS_MYRRIX_ORDER_POINTS = 'predictions/myrrix/order_points';

    //points for myrrix...

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_GENERAL_ENABLED);
    }

    public function getPredictionEngine() {
        $engineAdapterName = strtolower(Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_ENGINE));
        return Mage::helper('predictions/adapters_' . $engineAdapterName);
    }

    public function canOverrideRelatedProducts()
    {
        return $this->isEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_GENERAL_RELATED_PRODUCTS);
    }

    public function getPredictionIOApiKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_PREDICTIONIO_API_KEY);
    }

    public function getPredictionIOBaseUri()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_PREDICTIONIO_BASE_URI);
    }

    public function getMyrrixBaseUri()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_MYRRIX_BASE_URI);
    }

    public function getMyrrixViewPoints()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_MYRRIX_VIEW_POINTS);
    }

    public function getMyrrixAddToCartPoints()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_MYRRIX_ADDTOCART_POINTS);
    }

    public function getMyrrixAddToWishlistPoints()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_MYRRIX_ADDTOWISHLIST_POINTS);
    }

    public function getMyrrixOrderPoints()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_MYRRIX_ORDER_POINTS);
    }

    public function getCurrentUserUniqueId() {
        $uniqueId = Mage::getSingleton('core/cookie')->get('predictions_unid');
        if(!$uniqueId) {
            $uniqueId = $this->generateUniqueId();
            Mage::getSingleton('core/cookie')->set("predictions_unid", $uniqueId);
        }
        return $uniqueId;
    }

    private function generateUniqueId()
    {
        list($usec, $sec) = explode(" ", microtime());
        $uniqueCode = intval($usec * 100000) . rand(1, 1000000000);

        return $uniqueCode;
    }
}
