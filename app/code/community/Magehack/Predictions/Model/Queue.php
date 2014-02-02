<?php
class Magehack_Predictions_Model_Queue extends Mage_Core_Model_Abstract
{

    const EVENT_TYPE_VIEW = 'view';
    const EVENT_TYPE_ADD_TO_CART = 'cart';
    const EVENT_TYPE_ADD_TO_WISHLIST = 'wishlist';
    const EVENT_TYPE_ORDER = 'order';
    const EVENT_TYPE_PRODUCT_RETURN = 'return';

    protected $_engineInstance;

    protected function _construct()
    {
        $this->_init('predictions/queue', 'queue_id');
    }


}
