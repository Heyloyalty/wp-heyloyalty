<?php

namespace Heyloyalty\Admin;

use Heyloyalty\IPlugin;
use Carbon\Carbon;

class Admin
{

    /**
     * @var iPlugin @plugin
     */
    private $plugin;
    public $tabs = array(
        // The assoc key represents the ID
        // It is NOT allowed to contain spaces
        'EXAMPLE' => array(
            'title'   => 'TEST ME!'
        ,'content' => 'FOO'
        )
    );

    public function __construct(IPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function init()
    {

        load_plugin_textdomain('wp-heyloyalty', null, $this->plugin->dir() . '/languages');

        $this->add_hooks();
        $this->add_ajax_hooks();
    }
    
    /**
     * action hooks
     */
    protected function add_hooks()
    {
        add_action('init', array($this, 'register'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('user_register', array($this, 'add_user_to_heyloyalty'));
        add_action('profile_update', array($this, 'update_user_in_heyloyalty'));
        add_action('show_user_profile', array($this, 'add_permission_field'));
        add_action('edit_user_profile', array($this, 'add_permission_field'));
        add_action('personal_option_update', array($this, 'save_permission'));
        add_action('edit_user_profile_update', array($this, 'save_permission'));
        add_action('wp_login', array($this, 'last_visit'),10,2);
        add_action('woocommerce_payment_complete', array($this, 'last_buy'),10,1);
        add_action( 'woocommerce_after_order_notes', array($this,'add_newsletter_checkbox'),10,1 );
        add_action( 'woocommerce_checkout_update_order_meta', 'save_newsletter_field',10,1 );
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
        add_menu_page('wp-heyloyalty', 'wp-heyloyalty', 'manage_options', $this->plugin->slug(), array($this, 'show_front_page'), $this->plugin->url() . '/assets/img/menu-icon.png');

        $menu_items = array(
            array(__('Settings', 'wp-heyloyalty'), __('Settings', 'wp-heyloyalty'), 'hl-settings', array($this, 'show_settings_page')),
            array(__('Mappings', 'wp-heyloyalty'), __('Mappings', 'wp-heyloyalty'), 'hl-mappings', array($this, 'show_mapping_page')),
            array(__('Tools', 'wp-heyloyalty'), __('Tools', 'wp-heyloyalty'), 'hl-tools', array($this, 'show_tools_page'))

        );
        /**
         * Check if WooCommerce is active
         **/
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            array_push($menu_items, array(__('Woocommerce', 'wp-heyloyalty'), __('Woocommerce', 'wp-heyloyalty'), 'hl-woocommerce', array($this, 'show_woocommerce_page')));
        }

        foreach ($menu_items as $item) {
            $page = add_submenu_page('wp-heyloyalty/wp-heyloyalty.php', $item[0], $item[1], 'manage_options', $item[2], $item[3]);
            add_action('admin_print_styles-' . $page, array($this, 'load_assets'));
            add_action('admin_enqueue_scripts-' . $page, array($this, 'load_assets'));
            add_action( "load-".$page, array( $this, 'add_tabs' ), 20 );
        }

    }
    /**
     * register hooks
     */
    public function register()
    {
        register_setting('hl-settings', 'hl-settings');
        register_setting('hl-mappings', 'hl-mappings');
        register_setting('hl-woocommerce', 'hl-woocommerce');
        register_setting('hl-tools','hl-tools');
    }

    /**
     * 
     */
    public function add_tabs()
    {
        require(__DIR__.'/views/help-screens/tools.php');
        require(__DIR__.'/views/help-screens/settings.php');
        require(__DIR__.'/views/help-screens/mappings.php');
        require(__DIR__.'/views/help-screens/woocommerce.php');
        $screen = get_current_screen();
        $tabs = null;
        switch ($screen->base)
        {
            case 'wp-heyloyalty_page_hl-tools':
                $tabs = $tools;
                break;
            case 'wp-heyloyalty_page_hl-mappings':
                $tabs = $mappings;
                break;
            case 'wp-heyloyalty_page_hl-settings':
                $tabs = $settings;
                break;
            case 'wp-heyloyalty_page_hl-woocommerce':
                $tabs = $woo;
                break;
        }

        foreach ($tabs as $tab) {
            $screen->add_help_tab(array(
                'id' => $tab['id'],
                'title' => $tab['title'],
                'content' => $tab['content']
            ));
        }
    }

    public function last_visit($user_login, $user)
    {
        update_user_meta($user->ID, 'hl_last_visit', Carbon::now()->toDateString());
    }

    public function last_buy($order_id)
    {
        $order = new \WC_Order($order_id);
        $user_id = $order->get_user_id();
        update_user_meta($user_id, 'hl_last_buy', Carbon::now()->toDateString());
    }

    public function add_user_to_heyloyalty($user_id)
    {
        update_user_meta($user_id, 'hl_permission', 'on');
        try {
            $response = $this->plugin['admin-services']->addHeyloyaltyMember($user_id);
        } catch (\Exception $e) {
            //register error to show on front page.
            $this->plugin['admin-services']->setError('error',$e->getMessage());
        }
    }

    public function update_user_in_heyloyalty($user_id)
    {
        try {
            $response = $this->plugin['admin-services']->updateHeyloyaltyMember($user_id);
        } catch (\Exception $e) {
            //register error to show on front page.
            $this->plugin['admin-services']->setError('error',$e->getMessage());
        }
    }
    public function delete_user_in_heyloyalty($user_id)
    {
        try {
            $response = $this->plugin['admin-services']->deleteHeyloyaltyMember($user_id);
        } catch (\Exception $e) {
            //register error to show on front page.
            $this->plugin['admin-services']->setError('error',$e->getMessage());
        }
    }

    public function show_front_page()
    {
        $status = get_option('status');
        $errors = get_option('errors');

        if(is_array($status) && is_array($errors))
            $status = array_merge($status,$errors);

        krsort($status);
        $status = array_slice($status,0,20);

        require __DIR__ . '/views/front.php';
    }

    public function show_settings_page()
    {
        $status = 'ok';
        if (isset($_POST['hl_settings'])) {
            $this->save_hl_settings($_POST['hl_settings']);

        }

        $opts = $this->plugin['options'];
        require __DIR__ . '/views/settings.php';

    }

    /**
     * Mapping page handler.
     */
    public function show_mapping_page()
    {
        if (isset($_POST['option_page']) && $_POST['option_page'] == 'hl_mappings') {
            $str = $_POST['mapped'];

            //get hl-key, hl-format and wp-key from string container.
            preg_match_all("/([^,= ]+)=([^,= ]+)=([^,= ]+)/", $str, $r);

            //combine wp-key and hl-key into a key/value pair array.
            $result = array_combine($r[1], $r[3]);

            //combine hl-key and hl-format info key/value pair array.
            $fieldsFormats = array_combine($r[3],$r[2]);

            $mappings = get_option('hl_mappings');
            $mappings['fields'] = $result;
            $mappings['formats'] = $fieldsFormats;

            //update mapping options
            update_option('hl_mappings', $mappings);
        }

        try {
            $lists = $this->plugin['heyloyalty-services']->getLists();
        } catch (\Exception $e) {

            //register error to show on front page.
            $this->plugin['admin-services']->setError('error',$e->getMessage());
        }
        $user_fields = $this->plugin['admin-services']->getUserFields();
        $mappings = $this->plugin['mappings'];

        require __DIR__ . '/views/mappings.php';
    }

    public function show_woocommerce_page()
    {
        if (isset($_POST['option_page']) && $_POST['option_page'] === 'hl_woo') {
            $settings = (isset($_POST['hl_woo'])) ? $_POST['hl_woo'] : 'off';
            $this->save_hl_woo($settings);
        }
        $woo = $this->plugin['woo'];
        require __DIR__ . '/views/woocommerce.php';
    }

    /**
     * Show tools page.
     */
    public function show_tools_page()
    {
        $users = get_users();
        $status = 'ok';
        if (isset($_POST['action']) && isset($_POST['user'])) {
            switch($_POST['action'])
            {
                case 'create':
                    $this->add_user_to_heyloyalty($_POST['user']);
                    $status = 'created';
                    break;
                case 'update':
                    $this->update_user_in_heyloyalty($_POST['user']);
                    $status = 'updated';
                    break;
                case 'delete':
                    $this->delete_user_in_heyloyalty($_POST['user']);
                    $status = 'deleted';
                    break;
            }
        }
        require __DIR__ . '/views/tools.php';
    }

    /**
     * Handler for wordpress ajax calls
     */
    public function ajax_handler()
    {
        // get handle
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

    /**
     * adds Heyloyalty permission field to user profil.
     * @param $user
     */
    public function add_permission_field($user)
    {
        $userID = $user->ID;
        require __DIR__ . '/partials/permission.php';
    }

    /**
     * save permisson on user profile page.
     * @param $user_id
     */
    public function save_permission($user_id)
    {
        if (current_user_can('edit_user', $user_id)) {
            update_user_meta($user_id, 'hl_permission', $_POST['hl_permission']);
        }
    }

    /**
     * adds newsletter checkbox to woocommerce checkout flow.
     */
    public function add_newsletter_checkbox( $checkout ) {
        global $current_user;
        get_current_user();
        $permission = get_user_meta($current_user->ID,'hl_permission',true);

        if($permission != 'on')
        {
        echo '<div id="news_permission">';

        woocommerce_form_field( 'newsletter_field', array(
            'type'          => 'checkbox',
            'class'         => array('my-field-class form-row-wide'),
            'label'         => __('Ja tak til nyhedsbrev'),
            'placeholder'   => __('Enter something'),
        ), $checkout->get_value( 'newsletter_field' ));

        echo '</div>';
        }

    }
    /**
     * validate newsletter field on woocommerce checkout flow
     */
    function validate_newsletter_field() {
        // Check if set, if its not set add an error.
        //if ( ! $_POST['newsletter_field'] )
        //  wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' );
    }

    /**
     * save newsletter value to hl permission on process order
     * if there is a customer loggin
     */
    function save_newsletter_field( $order_id ) {
        if ( ! empty( $_POST['newsletter_field'] ) ) {
            $current_user = wp_get_current_user();

            if(isset($current_user)) {
                update_user_meta($current_user->ID, 'hl_permission', 'on');
            }else{
                //TODO create new user and update info
                error_log($_POST);
            }
        }
    }


    protected function save_hl_settings($settings)
    {
        update_option('hl_settings', $settings);
    }

    protected function save_hl_woo($settings)
    {
        update_option('hl_woo', $settings);
    }

    protected function getListForMapping($list_id)
    {
        try {

            $response = $this->plugin['heyloyalty-services']->getList($list_id);
            $this->plugin['admin-services']->saveListFieldChoiceOptions($response['fields']);
            $mappings = get_option('hl_mappings');
            $mappings['list_id'] = $list_id;
            update_option('hl_mappings', $mappings);
            $response = json_encode($response);

        } catch (\Exception $e) {
            $response = array('status' => false);
        }

        return $response;
    }
}