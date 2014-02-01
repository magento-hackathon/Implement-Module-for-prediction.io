<?php

class Magehack_Predictions_Model_Adminhtml_System_Config_Source_Predictions_Engine
{

    public function toArray()
    {
        return array(
            Magehack_Predictions_Model_Engine::ENGINE_PREDICTIONIO => Mage::helper('predictions')->__('PredictionIO'),
            Magehack_Predictions_Model_Engine::ENGINE_MYRRIX => Mage::helper('predictions')->__('Myrrix'),
        );
    }

    public function toOptionArray()
    {
        $retval = array();
        foreach ($this->toArray() as $value => $label)
            $retval[] = array('value' => $value, 'label' => $label);

        return $retval;
    }

}
