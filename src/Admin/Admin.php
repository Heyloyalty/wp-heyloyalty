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

    public function menu()
    {
        add_utility_page('wp-heyloyalty', 'wp-heyloyalty', 'manage_options', $this->plugin->slug(), array($this, 'show_front_page'), $this->plugin->url() . '/assets/img/menu-icon.png');

        $menu_items = array(
            array(__('Settings', 'wp-heyloyalty'), __('Settings', 'wp-heyloyalty'), 'hl-settings', array($this, 'show_settings_page')),
            array(__('Nappings', 'wp-heyloyalty'), __('Mappings', 'wp-heyloyalty'), 'hl-mappings', array($this, 'show_mapping_page'))
        );
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
    }

    public function show_front_page()
    {
        //todo
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
        $lists = $this->plugin['heyloyalty-services']->getLists();
        $mappings = $this->plugin['mappings'];
        require __DIR__ . '/views/mappings.php';
    }

    public function ajax_handler()
    {
        // get action
        $handle = $_POST['handle'];

        switch($handle)
        {
            case "getListForMapping":
                break;
        }

        // generate the response
        $response = json_encode(array('status' => true ));

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
        wp_enqueue_script('jquery-ui-draggable',false,array('jquery'));
        wp_enqueue_script('jquery-ui-droppable',false,array('jquery'));

        wp_localize_script('hl-ajax-request', 'HLajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    protected function save_hl_settings($settings)
    {
        update_option('hl_settings', $settings);
    }
}