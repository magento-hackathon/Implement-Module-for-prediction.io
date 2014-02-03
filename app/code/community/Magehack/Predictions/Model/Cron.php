<?php
class Magehack_Predictions_Model_Cron
{

    const EVENT_TYPE_VIEW = 'view';
    const EVENT_TYPE_ADD_TO_CART = 'cart';
    const EVENT_TYPE_ADD_TO_WISHLIST = 'wishlist';
    const EVENT_TYPE_ORDER = 'order';
    const EVENT_TYPE_PRODUCT_RETURN = 'return';


    private $_engineInstance;

    protected function getPredictionEngine()
    {
        if (is_null($this->_engineInstance)) {
            $this->_engineInstance = Mage::helper('predictions')->getPredictionEngine();
        }
        return $this->_engineInstance;
    }


    public function run()
    {
        $queueCollection = Mage::getModel('predictions/queue')->getCollection()
            ->addFieldToFilter('cookie_processed', array('eq' => '0'));

        $recommendFor = array();
        foreach($queueCollection as $task) {
            $recommendFor[] = $task->getCookieId();
            if($task->getCustomerId()){
                $recommendFor[] = $task->getCustomerId();
            }

            try {
                $this->_processTask($task);
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }

        // [todo] loop through $recommendFor and create recommendations in the db
        $predictionEngine = $this->getPredictionEngine();

        foreach($recommendFor as $uid)
        {
            try {
                $recommendations = $predictionEngine->getRecommendations($uid);


                // [todo] - Refactor this asap, terrible code hacked together to make it work
                foreach($recommendations as $key => $value) {
                    $rec = Mage::getModel('predictions/recommendation');

                    $rec->setCookieId($uid);
                    $rec->setProductId($value);

                    $rec->save();
                }

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    protected function _processTask($task)
    {
        $predictionEngine = $this->getPredictionEngine();
        $eventMethod = $this->_getEventMethod($task->getEventType());

        $predictionEngine->createItem($task->getProductId());
        $predictionEngine->createUser($task->getCookieId());
        //if the customer id isn't set we'll need to make the update. Otherwise its getting deleted anyways.
        if(!$task->getCustomerId()) {
            $task->setCookieProcessed(1);
            $task->save();
        }

        call_user_func(array($predictionEngine, $eventMethod), $task->getCookieId(), $task->getProductId());

        if($task->getCustomerId()) {
            $predictionEngine->createUser($task->getCustomerId());
            call_user_func(array($predictionEngine, $eventMethod), $task->getCustomerId(), $task->getProductId());
            $task->delete();
        }
        return true;
    }

    protected function _getEventMethod($eventType) {
        switch($eventType) {
            case self::EVENT_TYPE_VIEW:
                $eventMethod = 'view';
                break;
            case self::EVENT_TYPE_ADD_TO_CART:
                $eventMethod = 'addToCart';
                break;
            case self::EVENT_TYPE_ADD_TO_WISHLIST:
                $eventMethod = 'addToWishlist';
                break;
            case self::EVENT_TYPE_ORDER:
                $eventMethod = 'order';
                break;
            case self::EVENT_TYPE_PRODUCT_RETURN:
                $eventMethod = 'productReturn';
                break;
            default:
                throw new Exception('Invalid event type encountered in predictions queue table.');
        }
        return $eventMethod;
    }

}