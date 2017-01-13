=== wp heyloyalty ===
Contributors: René Skou
Tags: email, marketing, newsletter, woocommerce, e-commerce, sms, email marketing, send email
Requires at least: 4.0
Tested up to: 4.6
Stable tag: v1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin makes the connection between your wordpress users and a Heyloyalty list.
When a wordpress user is updated or created is will sync that user to your Heyloyalty list.
The plugin add support for woocommerce by adding extra field you can use like last buy or last visit among the fields.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Input your api key and api secret.
4. Select a Heyloyal list and map the fields.

== Changelog ==

= 1.0.1 =
* Added phpunit
* Added travis build file
* Tested for wordpress 4.5

= 1.0 =
* Updated method for getting fields
* Refactored plugin service provider
* Support for all Heyloyalty fields
* Wordpress help pages (in upper right corner)
* Heyloyalty Webhook handler for unsubscribe.
* Install method for setting up webhook handler

= 0.6 =
* Added tools menu
* Added styling to status

= 0.5 =
* Plugin goes in beta main functions is working.

