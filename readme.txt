=== wp heyloyalty ===

Contributors: amras

Donate link: http://heyloyalty.com

Tags: email, marketing, newsletter, woocommerce, e-commerce, sms, email marketing, send email

Requires at least: 4.3

Tested up to: 4.3

Stable tag: 1.0.1

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html


Wp-Heyloyal is a plugin for integrating with Heyloyalty an email markeing platform.

== Description ==

This plugin makes the connection between your wordpress users and a Heyloyalty list.
When a wordpress user is updated or created is will sync that user to your Heyloyalty list.

The plugin add support for woocommerce by adding extra field you can use like last buy or last visit among the fields.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Input your api key and api secret.
4. Select a Heyloyal list and map the fields.

== Update ==

1. deactivate the plugin.
2. override old files with the new files.
3. activate the plugin.


== Changelog ==

= 0.5 =
* Plugin goes in beta main functions is working.

== 0.6 ==
* Added tools menu
* Added styling to status

== 1.0 ==
* Updated method for getting fields
* Refactored plugin service provider
* Support for all Heyloyalty fields
* Wordpress help pages (in upper right corner)
* Heyloyalty Webhook handler for unsubscribe.
* Install method for setting up webhook handler.


