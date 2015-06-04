<?php
/**
 * Plugin Name:       WooCommerce Friendbuy
 * Plugin URI:        http://github.com/devinsays/woocommerce-friendbuy
 * Description:       Helps integrate Friendbuy (friendbuy.com) with your WooCommerce site.
 * Version:           1.0.0
 * Author:            Devin Price
 * Author URI:        http://wptheming.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-friendbuy
 * Domain Path:       /languages
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WC_FriendBuy' ) ) :

class WC_FriendBuy {

	/**
	* Construct the plugin.
	*/
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	* Initialize the plugin.
	*/
	public function init() {

		// Checks if WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {

			$settings = get_option( 'woocommerce_friendbuy_settings', array( 'site_key' => '', 'verify_customer' => 'no' ) );

			if ( '' != $settings['site_key'] ) :

				// Loads smart pixel on front-end
				add_action( 'wp_head', array( $this, 'wcfb_load_scripts' ), 200 );

				// Notifies Friendbuy of conversion
				add_action( 'woocommerce_thankyou', array( $this, 'wcfb_conversion_tracker' ) );

			endif;

			// Load settings integration.
			include_once 'includes/settings.php';

			// Register the settings integration.
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		}
	}

	/**
	 * Load Friendbuy SmartPixel
	 *
	 * @link: http://docs.friendbuy.com/article/85-woocommerce
	 */
	function wcfb_load_scripts() {

		$settings = get_option( 'woocommerce_friendbuy_settings', array( 'site_key' => '', 'verify_customer' => 'no' ) );

		?>
		<script>
		    window['friendbuy'] = window['friendbuy'] || [];
		    window['friendbuy'].push(['site', '<?php echo esc_textarea( $settings['site_key'] ); ?>']);
		<?php
		$customer = wp_get_current_user();
		if ( ! empty( $customer->ID ) ) { ?>
			window['friendbuy'].push(['track', 'customer', {
			    id: '<?php echo $customer->ID; ?>',
			    email: '<?php echo $customer->user_email; ?>'
			}]);
		<?php } ?>
		(function (f, r, n, d, b, y) {
		    b = f.createElement(r), y = f.getElementsByTagName(r)[0];b.async = 1;b.src = n;y.parentNode.insertBefore(b, y);
		    })(document, 'script', '//djnf6e5yyirys.cloudfront.net/js/friendbuy.min.js');
		</script>
	<?php
	}

	/**
	 * Friendbuy conversion tracker
	 *
	 * @link: http://docs.friendbuy.com/article/85-woocommerce
	 */
	function wcfb_conversion_tracker( $order_id ) {

		// Lets grab the order
		$order = new WC_Order( $order_id );

		// Get customer information if customer verification is on
		$settings = get_option( 'woocommerce_friendbuy_settings', array( 'site_key' => '', 'verify_customer' => 'no' ) );

		if ( 'yes' == $settings['verify_customer'] ) :
			$new_customer = "true";
			if ( wcfb_is_returning_customer( $order->billing_email ) ) :
				$new_customer = "false";
			endif;
		endif;

		?>
		<script>
		    window['friendbuy'] = window['friendbuy'] || [];
		    window['friendbuy'].push(['track', 'order',
		        {
		            id: '<?php echo $order->get_order_number(); ?>',
		            amount: '<?php echo $order->get_total(); ?>',
		            email: '<?php echo $order->billing_email; ?>'
		            <?php if ( 'yes' == $settings['verify_customer'] ) : ?>, // <-- Comma not a typo
					new_customer: <?php echo $new_customer ? 'true' : 'false'; ?>
					<?php endif; ?>
		        }
		    ]);
		    window['friendbuy'].push(['track', 'products', [
			<?php
			$count = 0;
			foreach( $order->get_items() as $item_id => $item ) {
				$count++;
				$product = $order->get_product_from_item( $item ); ?>
		        {
		            sku: '<?php echo $product->get_sku(); ?>',
		            price: '<?php echo $order->get_line_subtotal($item); ?>',
		            quantity: '<?php echo $item['qty']; ?>'
		        }<?php if ( count( $order->get_items() ) > $count ) { echo ","; } ?>
			<?php } ?>
		   ]]);
		</script>
		<?php

	}

	/**
	 * Add a new settings page integration to WooCommerce.
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'WCFB_Settings';
		return $integrations;
	}

}

$WC_FriendBuy = new WC_FriendBuy( __FILE__ );

endif;