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


use Heyloyalty\DI\Container;

class PluginBase extends Container implements IPlugin {
	/**
	 * @var iPlugin The One True Plugin Instance
	 */
	public static $instance;
	/**
	 * @var string The current version of the plugin
	 */
	protected $version = '1.0';
	/**
	 * @var string
	 */
	protected $file = '';
	/**
	 * @var string
	 */
	protected $dir = '';
	/**
	 * @var string
	 */
	protected $name = '';
	/**
	 * @var string
	 */
	protected $slug = '';
	/**
	 * @var int
	 */
	protected $id = 0;
	/**
	 * @return iPlugin
	 */
	public static function instance() {
		return self::$instance;
	}
	/**
	 * Constructor
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $version
	 * @param string $file
	 * @param string $dir
	 */
	public function __construct( $id, $name, $version, $file, $dir ) {
		$this->id = $id;
		$this->name = $name;
		$this->version = $version;
		$this->file = $file;
		$this->dir = $dir;
		$this->slug = plugin_basename( $file );
		//parent::__construct();
		// register services early since some add-ons need 'm
		$this->register_services();
		// load rest of classes on a later hook
		$this->load();
		// store instance
		self::$instance = $this;
	}
	/**
	 * Register services in the Service Container
	 */
	protected function register_services() {}
	/**
	 * Start loading classes on `plugins_loaded`, priority 20.
	 */
	public function load() {}
	/**
	 * @return int
	 */
	public function id() {
		return $this->id;
	}
	/**
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}
	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}
	/**
	 * @return string
	 */
	public function version() {
		return $this->version;
	}
	/**
	 * @return string
	 */
	public function file() {
		return $this->file;
	}
	/**
	 * @return string
	 */
	public function dir() {
		return $this->dir;
	}
	/**
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function url( $path = '' ) {
		return plugins_url( $path, $this->file() );
	}
}