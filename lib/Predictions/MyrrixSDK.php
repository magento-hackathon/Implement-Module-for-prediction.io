<?php
class MyrrixSDK {
    protected $serverLocation;

    /**
     * Save the server location and api key for requests.
     *
     */
    public function __construct($serverLocation) {
        $this->serverLocation = $serverLocation;
    }


    /**
     * Accepts one or more lines of data to learn from as CSV.
     *
     * @param string String of data to ingest (format: userid,itemid,value)
     * @return null
     */
    public function ingest($data) {
        $this->apiCall('ingest', 'POST', $data);
    }

    /**
     * Create an api request and inject the app token.
     *
     * @param string Path to the rest method being called
     * @param string Request method: GET, POST, PUT or DELETE
     * @param mixed An array of parameters or raw post data.  Raw data isn't accepted for GET.
     * @return null
     * @throws Exception Invalid or failed requests throw exceptions.
     */
    protected function apiCall($path, $requestMethod, $params = array()) {
        $requestMethod = strtoupper($requestMethod);
        switch($requestMethod) {
            case 'GET':
                $setParamMethod = 'setParameterGet';
                if(!is_array($params)) {
                    throw new Exception('GET parameters can\'t be provided as raw data.');
                }
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $setParamMethod = 'setParameterPost';
                break;
            default:
                throw new Exception('Invalid request method');
        }

        $client = new Varien_Http_Client($this->serverLocation . $path);
        $client->setMethod($requestMethod);

        if(is_array($params)) {
            foreach($params as $paramKey => $paramValue) {
                call_user_func(array($client, $setParamMethod), $paramKey, $paramValue);
            }
        } else {
            $client->setRawData($params);
        }

        $response = $client->request();
        if ($response->isSuccessful()) {
            return json_decode($response->getBody());
        } else {
            throw new Exception('Request failed');
        }

    }
}