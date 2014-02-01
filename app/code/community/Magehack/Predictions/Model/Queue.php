<?php
class Magehack_Predictions_Model_Queue extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('predictions/queue', 'queue_id');
    }
}