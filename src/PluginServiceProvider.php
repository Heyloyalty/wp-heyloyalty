<?php

namespace Heyloyalty;


use Heyloyalty\Admin\Admin;
use Heyloyalty\DI\Container;
use Heyloyalty\DI\ServiceProviderInterface;

class PluginServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * @param Container $container A Container instance
     */
    public function register(Container $container)
    {
        $container['options'] = function ($app) {
            $defaults = array(
                'testmode' => 0
            );
            $options = (array)get_option('hl_settings', $defaults);
            $options = array_merge($options, $defaults);
            return $options;
        };

        $container['mappings'] = function ($app) {
            $mappings = (array)get_option('hl_mappings');
            return $mappings;
        };

        $container['woo'] = function ($app) {
            $woo = (array)get_option('hl_woo');
            return $woo;
        };

        $container['admin'] = function ($app) {
            return new Admin($app);
        };
    }

}