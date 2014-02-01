<?php

class Magehack_Predictions_Model_Adminhtml_System_Config_Source_Predictions_Engine
{

    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('predictions')->__('PredictionIO'),
                'value' => Magehack_Predictions_Model_Engine::ENGINE_PREDICTIONIO
            ),
            array(
                'label' => Mage::helper('predictions')->__('Myrrix'),
                'value' => Magehack_Predictions_Model_Engine::ENGINE_MYRRIX
            )
        );
    }

}
