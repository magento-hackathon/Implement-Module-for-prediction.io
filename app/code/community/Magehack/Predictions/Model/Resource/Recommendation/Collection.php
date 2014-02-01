<?php

class Magehack_Predictions_Model_Resource_Recommendation_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('predictions/recommendation');
    }

}

