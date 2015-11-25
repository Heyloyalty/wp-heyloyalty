<?php

namespace Heyloyalty\Admin;

use Heyloyalty\IPlugin;

class Admin
{

    /**
     * @var iPlugin @plugin
     */
    private $plugin;

    public function __construct(IPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function init()
    {
        $this->register_services();

        load_plugin_textdomain('wp-heyloyalty', null, $this->plugin->dir() . '/languages');

        $this->add_hooks();
        $this->add_ajax_hooks();
    }

    protected function register_services()
    {
        $provider = new AdminServiceProvider();
        $provider->register($this->plugin);
    }

    protected function add_hooks()
    {
        add_action('init', array($this, 'register'));
        add_action('admin_menu', array($this, 'menu'));
    }

    protected function add_ajax_hooks()
    {
        add_action('wp_ajax_nopriv_hl-ajax-submit', array($this, 'ajax_handler'));
        add_action('wp_ajax_hl-ajax-submit', array($this, 'ajax_handler'));
    }

    /**
     * Plugin menu.
     *
     *  Submenu settings: parent_slug, page_title, menu_title, capability, menu_slug, callback.
     */
    public function menu()
    {
        add_utility_page('wp-heyloyalty', 'wp-heyloyalty', 'manage_options', $this->plugin->slug(), array($this, 'show_front_page'), $this->plugin->url() . '/assets/img/menu-icon.png');

        $menu_items = array(
            array(__('Settings', 'wp-heyloyalty'), __('Settings', 'wp-heyloyalty'), 'hl-settings', array($this, 'show_settings_page')),
            array(__('Mappings', 'wp-heyloyalty'), __('Mappings', 'wp-heyloyalty'), 'hl-mappings', array($this, 'show_mapping_page'))

        );
        /**
         * Check if WooCommerce is active
         **/
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            array_push($menu_items, array(__('Woocommerce', 'wp-heyloyalty'), __('Woocommerce', 'wp-heyloyalty'), 'hl-woocommerce', array($this, 'show_woocommerce_page')));
        }
        $test = false;
        if($test)
        {
            array_push($menu_items, array(__('test-page', 'wp-heyloyalty'), __('Test ', 'wp-heyloyalty'), 'hl-test', array($this, 'show_test_page')));
        }
        foreach ($menu_items as $item) {
            $page = add_submenu_page('wp-heyloyalty/wp-heyloyalty.php', $item[0], $item[1], 'manage_options', $item[2], $item[3]);
            add_action('admin_print_styles-' . $page, array($this, 'load_assets'));
            add_action('admin_enqueue_scripts-' . $page, array($this, 'load_assets'));
        }

    }

    public function register()
    {
        register_setting('hl-settings', 'hl-settings');
        register_setting('hl-mappings', 'hl-mappings');
        register_setting('hl-woocommerce', 'hl-woocommerce');
    }

    public function show_front_page()
    {
        //todo should show hetloyalty feed and latest actions.
    }

    public function show_settings_page()
    {
        if (isset($_POST['hl_settings'])) {
            $this->save_hl_settings($_POST['hl_settings']);
        }

        $opts = $this->plugin['options'];
        require __DIR__ . '/views/settings.php';

    }

    public function show_mapping_page()
    {
        if($_POST['option_page'] == 'hl_mappings')
        {
            $str = $_POST['mapped'];
            preg_match_all("/([^,= ]+)=([^,= ]+)/", $str, $r);
            $result = array_combine($r[1], $r[2]);
            $mappings = get_option('hl_mappings');
            $mappings['fields'] = $result;
            update_option('hl_mappings',$mappings);
        }

        try {
            $lists = $this->plugin['heyloyalty-services']->getLists();
            $user = get_user_meta(1);
            unset(
                $user['rich_editing'],
                $user['comment_shortcuts'],
                $user['admin_color'],
                $user['use_ssl'],
                $user['show_admin_bar_front'],
                $user['wp_capabilities'],
                $user['wp_user_level'],
                $user['dismissed_wp_pointers'],
                $user['show_welcome_panel'],
                $user['session_tokens'],
                $user['wp_dashboard_quick_press_last_post_id']
            );
            $user['email'] = get_the_author_meta('user_email', 1);
            $user['website'] = get_the_author_meta('user_url', 1);
        } catch (\Exception $e) {
            //TODO handle exception.
        }
        /**
         * Check if WooCommerce is active
         **/
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $woo_fields = array(
                'billing_first_name' => '',
                'billing_last_name' => '',
                'billing_company' => '',
                'billing_address_1' =>'',
                'billing_address_2' => '',
                'billing_city' => '',
                'billing_postalcode' => '',
                'billing_country' => '',
                'billing_state' => '',
                'billing_phone' => '',
                'billing_email' => '',
                'shipping_first_name' => '',
                'shipping_last_name' => '',
                'shipping_company'=> '',
                'shipping_address_1' => '',
                'shipping_address_2' => '',
                'shipping_city' => '',
                'shipping_postalcode' => '',
                'shipping_country' => '',
                'shipping_state' => ''
            );

            foreach($woo_fields as $key => $value)
            {
                unset($user[$key]);
            }

            $user = array_merge($user,$woo_fields);
        }
        $mappings = $this->plugin['mappings'];
        require __DIR__ . '/views/mappings.php';
    }

    public function show_woocommerce_page()
    {
        if ($_POST['option_page'] === 'hl_woo') {
            $settings = (isset($_POST['hl_woo'])) ? $_POST['hl_woo'] : 'off';
            $this->save_hl_woo($settings);
        }
        $woo = $this->plugin['woo'];
        require __DIR__ . '/views/woocommerce.php';
    }
    public function show_test_page()
    {
        require __DIR__ . '/views/test.php';
    }

    public function ajax_handler()
    {
        // get action
        $handle = $_POST['handle'];

        switch ($handle) {
            case "getListForMapping":
                $response = $this->getListForMapping($_POST['listID']);
                break;
        }

        // response output
        header("Content-Type: application/json");
        echo $response;

        // IMPORTANT: don't forget to "exit"
        exit;
    }

    public function load_assets()
    {
        wp_register_style('hl-admin-css', $this->plugin->url() . '/assets/css/heyloyalty.css', array(), $this->plugin->version());
        wp_register_script('hl-admin-js', $this->plugin->url() . '/assets/js/heyloyalty.js', array('jquery'), $this->plugin->version(), true);
        wp_register_script('hl-ajax-request', $this->plugin->url() . '/assets/js/heyloyalty-ajax.js', array('jquery'));
        wp_enqueue_script('hl-ajax-request');
        wp_enqueue_script('hl-admin-js');
        wp_enqueue_style('hl-admin-css');
        wp_enqueue_script('jquery-ui-draggable', false, array('jquery'));
        wp_enqueue_script('jquery-ui-droppable', false, array('jquery'));

        wp_localize_script('hl-ajax-request', 'HLajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    protected function save_hl_settings($settings)
    {
        update_option('hl_settings', $settings);
    }
    protected function save_hl_woo($settings)
    {
        update_option('hl_woo',$settings);
    }

    protected function getListForMapping($list_id)
    {
        try {
            $mappings = array('first_name' => 'firstname', 'last_name' => 'lastname', 'email' => 'email', 'billing_phone' => 'mobile');
            $response = $this->plugin['heyloyalty-services']->getList($list_id);
            update_option('hl_mappings',array('list_id' => $list_id,'fields' => $mappings));
            $response = json_encode($response);


        } catch (\Exception $e) {
            $response = array('status' => false);
        }

        return $response;
    }
}