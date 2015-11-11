<?php

namespace Heyloyalty;


final class Plugin extends PluginBase {

    protected function register_services()
    {
        $provider = new PluginServiceProvider();
        $provider->register($this);
    }

    public function load()
    {
        $container = $this;

        add_action('init', function() use( $container ) {
				$container['admin']->init();
			});
    }

}