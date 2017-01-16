<?php

namespace Heyloyalty\Services;

class ApiEndPointsServices
{
    /** Hook WordPress
     *	@return void
     */
    public function __construct(){
        add_filter('query_vars', array($this, 'add_query_vars'), 0);
        add_action('parse_request', array($this, 'validate_requests'), 0);
        add_action('init', array($this, 'add_endpoints'), 0);
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
        add_rewrite_rule('^api/webhooks/','index.php?__api=1','top');
    }

    /**	Sniff Requests
     *	This is where we hijack all API requests
     *	@return die if API request
     */
    public function validate_requests($wp_query){
        if(isset($wp_query->query_vars['__api'])){
            $this->handle_request();
            exit;
        }
    }

    /** Handle Requests
     *	@return void
     */
    protected function handle_request(){
        $stream = $this->detectRequestBody();
        $requestBody = stream_get_contents($stream);
        $bodyArray = json_decode($requestBody);
        $this->send_response($bodyArray,200);
    }

    /** Response Handler
     *	This sends a JSON response to the browser
     */
    protected function send_response($msg,$response_code){
        $response['message'] = $msg;
        header('content-type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($response)."\n";
        exit;
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
