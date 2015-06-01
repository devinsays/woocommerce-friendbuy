=== Plugin Name ===

Contributors: @downstairsdev
Tags: woocommerce, friendbuy, friend referral
Requires at least: 4.3.0
Tested up to: 4.3.0
Stable tag: 4.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 2.2
WC tested up to: 2.3

Plugin allows easy integration between Friendbuy and WooCommerce.

== Description ==

Friendbuy (friendbuy.com) is a platform that allows e-commerce sites to create customer referral programs and provide rewards to customers who refer their friends. We're testing this platform out over at Branch Basics (branchbasics.com), and thought we'd share the integration code with other folks who might also be integrating with WooCommerce.

Friendbuy has basic WooCommerce implementation instructions in their docs (http://docs.friendbuy.com/article/85-woocommerce), which we've tested on our site, expanded upon, then turned into this plugin.

To get started, install this plugin and go to the settings page under "WooCommerce > Settings". Click the "Integrations" tab and then the "Friendbuy" sub-tab.

The "Site Key" can be found by logging into the Friendbuy dashboard and clicking "Get SmartPixel". Look for this line in the code: "window['friendbuy'].push(['site', 'site-xxxxxx']);". Your site key is: site-xxxxxx.

If you are only allowing rewards for new customers, make sure to click the "Verify New Customers" checkbox. Once a  customer has completed a purchase, this plugin checks their billing e-mail address against previous orders to check if they are a new customer and sends that data to Friendbuy.

Unfortunately customer verification requires a complex database query which can impact load times. There can be ways to avoid this query (for instance, verifying customers only if a certain coupon code was used in checkout), but many of these methods will require a bespoke solution. Get in touch if you'd like to collaborate on this.

== Installation ==

You will need to have an account with Friendbuy. Once you install the plugin, enter you account information on the settings page under "WooCommerce > Settings". Click the "Integrations" tab and then the "Friendbuy" sub-tab.

== Changelog ==

= 1.0.0 =

* Initial release.