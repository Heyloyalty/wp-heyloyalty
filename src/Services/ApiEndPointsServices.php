<?php

namespace Heyloyalty\Services;

use Heyloyalty\Services\WpUserServices;

class ApiEndPointsServices
{
    private $apiToken = "heyloyaltyrocks";
    private $WpUserServices;

    public function __construct(){
        add_filter('query_vars', array($this, 'add_query_vars'), 0);
        add_action('parse_request', array($this, 'validate_requests'), 0);
        add_action('init', array($this, 'add_endpoints'), 0);
        $this->WpUserServices = new WpUserServices();
    }

    /** Add public query vars
     *	@param array $vars List of current public query vars
     *	@return array $vars
     */
    public function add_query_vars($vars){
        $vars[] = '__api';
        return $vars;
    }

    /** Add API Endpoint
     *	@return void
     */
    public function add_endpoints(){
        add_rewrite_rule('^api/webhooks/','index.php?__api=','top');
    }

    /**	Sniff Requests
     *	This is where we hijack all API requests
     *	@return die if API request
     */
    public function validate_requests($wp_query){
        if($this->authenticate($wp_query->query_vars['__api'])){
            $this->handle_request();
            exit;
        }
    }
    /**
     * Authenticate request.
     *
     */
    protected function authenticate($token)
    {
        $decoded = base64_decode($token);
        if ( $decoded === $this->apiToken )
            return true;

        return false;
    }

    /** Handle Requests
     *	@return void
     */
    protected function handle_request(){
        $stream = $this->detectRequestBody();
        $requestBody = stream_get_contents($stream);
        $bodyArray = json_decode($requestBody,true);
        $msg = '';
        $code = 500;
        switch ($bodyArray['type']) {
            case 'unsubscribe':
                $this->WpUserServices->unsubscribe($bodyArray);
                break;
            case 'update':
                $this->WpUserServices->upsert($bodyArray);
                break;
            default:
                break;
        }
    }
    /**
     * Detect request body.
     */
    protected function detectRequestBody() {
        $rawInput = fopen('php://input', 'r');
        $tempStream = fopen('php://temp', 'r+');
        stream_copy_to_stream($rawInput, $tempStream);
        rewind($tempStream);
        return $tempStream;
    }
}
