<?php

class Magehack_Predictions_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PREDICTIONS_GENERAL_ENABLED = 'predictions/general/enabled';
    const XML_PATH_PREDICTIONS_GENERAL_RELATED_PRODUCTS = 'predictions/general/related_products';
    const XML_PATH_PREDICTIONS_ENGINE = 'predictions/general/engine';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_API_KEY = 'predictions/predictionio/api_key';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_BASE_URI = 'predictions/predictionio/base_uri';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_GENERAL_ENABLED);
    }

    public function getPredictionEngine() {
        $engineAdapterName = strtolower(Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_ENGINE));
        return Mage::helper('predictions/adapters/' . $engineAdapterName);
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

    public function getUniqueId() {
        return Mage::getSingleton('core/cookie')->get('predictions_unid');
    }
}
