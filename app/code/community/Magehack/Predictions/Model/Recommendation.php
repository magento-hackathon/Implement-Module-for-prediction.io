<?php
class Magehack_Predictions_Model_Recommendation extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('predictions/recommendation', 'recommendation_id');
    }


}
