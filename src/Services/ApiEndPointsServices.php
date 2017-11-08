<?php

namespace Heyloyalty\Services;

use Heyloyalty\Services\WpUserServices;
use WP_REST_Controller;

class ApiEndPointsServices extends WP_REST_Controller
{
    public $wpUserService;
    public function __construct(){
        add_action('rest_api_init', array($this, 'add_endpoints'),0);
        $this->wpUserService = new WpUserServices();
    }

    /** Add API Endpoint
     *	@return void
     */
    public function add_endpoints(){
        $namespace = 'wp-heyloyalty';
        register_rest_route($namespace,'/member',array(
            'methods' => 'POST',
            'callback' => array($this,'member_handler'),
            'args' => array(),
        ));
    }

    public function member_handler($request)
    {
        $body = $request->get_params();
        if (!isset($body['data'])) {
            return 'No object';
        }
        $member = $body['data'];
        if ($member['type'] == 'unsubscribe') {
            return $this->wpUserService->unsubscribe($member);
        }
        return 'no member to unsubscribe';
    }
}
