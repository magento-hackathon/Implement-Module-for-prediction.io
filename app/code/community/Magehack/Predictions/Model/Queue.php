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

    protected function getPredictionEngine()
    {
        if (is_null($this->_engineInstance))
            $this->_engineInstance = Mage::helper('predictions')->getPredictionEngine();

        return $this->_engineInstance;
    }

    protected function _processTask($task)
    {
        $engine = $this->getPredictionEngine();

        $uids = array();

        if ($task->getCustomerId())
            $uids[] = $task->getCustomerId();

        if ( ! $task->getCookieProcessed())
            $uids[] = $task->getCookieId();

        $pid = $task->getProductId();

        foreach ($uids as $uid) {
            try {
                switch ($task->getEventType()) {
                case self::EVENT_TYPE_VIEW:
                    $engine->view($uid, $pid);
                    break;

                case self::EVENT_TYPE_ADD_TO_CART:
                    $engine->addToCart($uid, $pid);
                    break;

                case self::EVENT_TYPE_ADD_TO_WISHLIST:
                    $engine->addToWishlist($uid, $pid);
                    break;

                case self::EVENT_TYPE_ORDER:
                    $engine->order($uid, $pid);
                    break;

                case self::EVENT_TYPE_PRODUCT_RETURN:
                    $engine->productReturn($uid, $pid);
                    break;

                default:
                    Mage::throwException(Mage::helper('predictions')->__("Unknown prediction event type '%s'.",
                        $task->getEventType()));
                }
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                return false;
            }
        }

        if ($task->getCustomerId()) {
            $task->delete();

        } elseif ( ! $task->getCookieProcessed()) {
            $task->setCookieProcessed(1);
            $task->save();
        }

        return true;
    }

    public function process(Mage_Cron_Model_Schedule $schedule = null)
    {
        /* customer_id IS NULL AND matching record with customer_id IS NOT NULL found */
        $queueCollection = Mage::getModel('predictions/queue')->getCollection()
            ->addFieldToFilter('main_table.customer_id', array('null' => true))
            ->addFieldToFilter('b.customer_id', array('notnull' => true));
        $queueCollection->getSelect()
            ->join(array('b' => 'predictions_queue'), 'b.cookie_id = main_table.cookie_id',
                array('customer_id' => 'b.customer_id')); // make use of ZF field overwriting

        $collections[] = $queueCollection;

        /* customer_id IS NOT NULL */
        $collections[] = Mage::getModel('predictions/queue')->getCollection()
            ->addFieldToFilter('customer_id', array('notnull' => true));

        /* cookie_processed = 0 */
        $collections[] = Mage::getModel('predictions/queue')->getCollection()
            ->addFieldToFilter('cookie_processed', array('eq' => 0));

        foreach($collections as $coll)
            foreach ($coll as $task)
                $this->_processTask($task);
    }
}
