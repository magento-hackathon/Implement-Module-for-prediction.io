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
        $queueCollection = Mage::getModel('predictions/queue')->getCollection();
        $recommendFor = array();
        foreach($queueCollection as $task) {
            // [todo] add this condition to the collection query
            // if the cookie has not been processed yet and/or there is a customer id...
            $customer_id = $task->getCustomerId();
            if(!$task->getCookieProcessed() || $customer_id) {
                $recommendFor[] = $task->getCookieId();
                if($customer_id) {
                    $recommendFor[] = $customer_id;
                }

                try {
                    $this->_processTask($task);
                } catch (Exception $e) {
                    Mage::log($e->getMessage());
                }
            }
        }

        // [todo] loop through $recommendFor and create recommendations in the db
    }

    protected function _processTask($task)
    {
        $predictionEngine = $this->getPredictionEngine();
        $eventMethod = $this->_getEventMethod($task->getEventType());


        if(!$task->getCookieProcessed()) {
            $predictionEngine->createItem($task->getProductId());
            $predictionEngine->createUser($task->getCookieId());
            //if the customer id isn't set we'll need to make the update. Otherwise its getting deleted anyways.
            if(!$task->getCustomerId()) {
                $task->setCookieProcessed(1);
                $task->save();
            }

            call_user_func(array($predictionEngine, $eventMethod), $task->getCookieId(), $task->getProductId());
        }

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