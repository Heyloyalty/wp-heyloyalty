<?php

namespace Heyloyalty\Services;

use WP_REST_Controller;

class ApiEndPointsServices extends WP_REST_Controller
{
    public $wpUserService;
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'add_endpoints'), 0);
        $this->wpUserService = new WpUserServices();
    }

    /** Add API Endpoint
     */
    public function add_endpoints()
    {
        $namespace = 'wp-heyloyalty';
        register_rest_route($namespace, '/member', array(
            'methods' => 'POST',
            'callback' => array($this, 'member_handler'),
            'args' => array(),
        ));
    }

    public function member_handler($request)
    {
        try {
            $body = $request->get_params();
            if (!isset($body['data'])) {
                return 'No object';
            }
            $member = $body['data'];
            if (!is_array($member)) {
                $member = json_decode($member, true);
            }
            if ($member['type'] == 'unsubscribe') {
                return $this->wpUserService->unsubscribe($member);
            }
            if ($member['type'] == 'update') {
                $this->writelog($member['type']);
                return $this->wpUserService->upsert($member);
            }

            return 'no member to unsubscribe';
        } catch (Exception $e) {
            $this->writelog($e->getMessage());
            return null;
        }
    }

    public function writelog ( $log )
    {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}
