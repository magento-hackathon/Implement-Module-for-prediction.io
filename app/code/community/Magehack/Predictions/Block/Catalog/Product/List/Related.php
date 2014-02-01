<?php

class Magehack_Predictions_Block_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
    protected function _prepareData()
    {
        // Default back to core functionality
        if (!Mage::helper('predictions')->canOverrideRelatedProducts()){
            return parent::_prepareData();
        }

        /* Retrieve related product ids here. */
        $predictionHelper   = Mage::helper('predictions');
        $cookieId           = $predictionHelper->getCurrentUserUniqueId();

        $recommendationCollection = Mage::getModel('predictions/recommendations')
            ->getCollection()
            ->addFieldToFilter('cookie_id', array('eq' => $cookieId));

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $recommendationCollection->addFieldToFilter('customer_id', array('eq' => $customerData->getId()));
        }


        $predictionioIds = array(16, 17, 18, 19, 20, 21, 22, 23, 30, 31, 32);

        // [review] - Review the logic for loading the product collection for this block
        $this->_itemCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id',  array('in' => $predictionioIds))
            ->addAttributeToSelect('required_options')
            ->addStoreFilter();

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }


        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
}

