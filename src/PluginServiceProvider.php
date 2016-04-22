<?php
/*
 * This file is part of wp-heyloyalty.
 *
 * Copyright (c) 2015 Heyloyalty.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Heyloyalty;


use Heyloyalty\Admin\Admin;
use Heyloyalty\DI\Container;
use Heyloyalty\DI\ServiceProviderInterface;
use Heyloyalty\Services\AdminServices;
use Heyloyalty\Services\HeyloyaltyServices;

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
    
        $container['heyloyalty-services'] = function($container) {
            return new HeyloyaltyServices();
        };
    
        $container['admin-services'] = function($app) {
            return new AdminServices();
        };
    }

}