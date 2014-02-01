<?php

class Magehack_Predictions_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PREDICTIONS_GENERAL_ENABLED = 'predictions/general/enabled';
    const XML_PATH_PREDICTIONS_GENERAL_RELATED_PRODUCTS = 'predictions/general/related_products';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_API_KEY = 'predictions/predictionio/api_key';
    const XML_PATH_PREDICTIONS_PREDICTIONIO_BASE_URI = 'predictions/predictionio/base_uri';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_GENERAL_ENABLED);
    }

    public function canOverrideRelatedProducts()
    {
        return $this->isEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_PREDICTIONS_GENERAL_RELATED_PRODUCTS);
    }

    public function getApiKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_PREDICTIONIO_API_KEY);
    }

    public function getBaseUri()
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDICTIONS_PREDICTIONIO_BASE_URI);
    }
}
