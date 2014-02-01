<?php
class PredictionIOSDK {
    protected $serverLocation, $apiKey;

    /**
     * Save the server location and api key for requests.
     *
     */
    public function __construct($serverLocation, $apiKey) {
        $this->serverLocation = $serverLocation;
        $this->apiKey = $apiKey;
    }

    /**
     * Create user in PredictionIO.
     *
     * @param string Unique identifier for the user being created
     * @return null
     */
    public function addUser($uid) {
        $this->apiCall('users.json', 'POST', array('pio_uid' => $uid));
    }

    /**
     * Create item in PredictionIO.
     *
     * @param string Unique identifier for the item being created
     * @param string Type of item
     * @return null
     */
    public function addItem($item_id, $item_type) {
        $this->apiCall('items.json', 'POST', array('pio_iid' => $item_id, 'pio_itypes' => $item_type));
    }

    /**
     * Delete item from PredictionIO.
     *
     * @param string Unique identifier for the item being deleted
     * @return null
     */
    public function deleteItem($item_id) {
        $this->apiCall('items/' . $item_id . '.json', 'DELETE');
    }

    /**
     * Record a like action.
     *
     * @param string Unique identifier for the user
     * @param string Unique identifier for the item
     * @return null
     */
    public function likeItem($user_id, $item_id) {
        $this->itemAction($user_id, $item_id, 'like');
    }

    /**
     * Record a dislike action.
     *
     * @param string Unique identifier for the user
     * @param string Unique identifier for the item
     * @return null
     */
    public function dislikeItem($user_id, $item_id) {
        $this->itemAction($user_id, $item_id, 'dislike');
    }

    /**
     * Record a view action.
     *
     * @param string Unique identifier for the user
     * @param string Unique identifier for the item
     * @return null
     */
    public function viewItem($user_id, $item_id) {
        $this->itemAction($user_id, $item_id, 'view');
    }

    /**
     * Record a conversion action.
     *
     * @param string Unique identifier for the user
     * @param string Unique identifier for the item
     * @return null
     */
    public function conversionItem($user_id, $item_id) {
        $this->itemAction($user_id, $item_id, 'conversion');
    }

    // [todo] add documentation
    public function getRecommendations($user_id,$engineName) {
        Mage::log('get rec uid: ' . $user_id);
        return $this->apiCall('engines/itemrec/' . $engineName . '/topn.json', 'GET', array('pio_uid' => $user_id, 'pio_n' => 50));
    }

    /**
     * Record an item action.
     *
     * @param string Unique identifier for the user
     * @param string Unique identifier for the item
     * @param string Internally recognized name of the action being taken
     * @return null
     */
    protected function itemAction($user_id, $item_id, $action) {
         $this->apiCall('actions/u2i.json', 'POST', array('pio_uid' => $user_id, 'pio_iid' => $item_id, 'pio_action' => $action));
    }

    /**
     * Create an api request and inject the app token.
     *
     * @param string Path to the rest method being called
     * @param string Request method: GET, POST, PUT or DELETE
     * @param array An array of parameters
     * @return null
     * @throws Exception Invalid or failed requests throw exceptions.
     */
    protected function apiCall($path, $requestMethod, $params = array()) {
        $requestMethod = strtoupper($requestMethod);
        switch($requestMethod) {
            case 'GET':
                $setParamMethod = 'setParameterGet';
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $setParamMethod = 'setParameterPost';
                break;
            default:
                throw new Exception('Invalid request method');
        }
        Mage::log($this->serverLocation . $path);
        $client = new Varien_Http_Client($this->serverLocation . $path);
        $client->setMethod($requestMethod);

        call_user_func(array($client, $setParamMethod), 'pio_appkey', $this->apiKey);

        foreach($params as $paramKey => $paramValue) {
            call_user_func(array($client, $setParamMethod), $paramKey, $paramValue);
        }

        $response = $client->request();
        if ($response->isSuccessful()) {
            return json_decode($response->getBody());
        } else {
            Mage::log($response);
            throw new Exception('Request failed');
        }

    }
}