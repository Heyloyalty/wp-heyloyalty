<?php

namespace Heyloyalty\Admin;


use Heyloyalty\DI\Container;
use Heyloyalty\DI\ServiceProviderInterface;
use Heyloyalty\Services\HeyloyaltyServices;

class AdminServiceProvider implements ServiceProviderInterface {
    /**
     * Registers services on the given container.
     *
     * @param Container $container A Container instance
     */
    public function register(Container $container)
    {
        $container['heyloyalty-services'] = function($container) {
          return new HeyloyaltyServices();
        };
    }

}