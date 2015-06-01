<?php
/**
 * Adds Friendbuy Settings
 *
 * @package  WCFB_Settings
 * @category Integration
 * @author   Devin Price
 */

if ( ! class_exists( 'WCFB_Settings' ) ) :

class WCFB_Settings extends WC_Integration {

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		global $woocommerce;

		$this->id                 = 'friendbuy';
		$this->method_title       = __( 'Friendbuy', 'woocommerce-friendbuy' );
		$this->method_description = sprintf( __( 'Please enter your <a href="%s">Friendbuy</a> account information.', 'woocommerce-friendbuy' ), esc_url( 'https://friendbuy.com' ) );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->site_key          = $this->get_option( 'site_key' );
		$this->verify_customer	 = $this->get_option( 'verify_customer' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );

		// Filters.
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );

	}

	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'site_key' => array(
				'title'             => __( 'Site Key', 'woocommerce-friendbuy' ),
				'type'              => 'text',
				'description'       => __( 'Log into the Friendbuy dashboard and click "Get SmartPixel".<br>You will need to find the site id in this text.', 'woocommerce-friendbuy' ),
				'default'           => '',
				'placeholder'		=>  'site-5112e3fb-example.com'
			),
			'verify_customer' => array(
				'title'             => __( 'Verify New Customers', 'woocommerce-friendbuy' ),
				'type'              => 'checkbox',
				'label'             => __( 'Verify', 'woocommerce-friendbuy' ),
				'default'           => 'no',
				'description'       => __( 'If rewards are only available for new customers, check this box to enable customer verification.<br>Warning: This can have performance impacts.', 'woocommerce-friendbuy' ),
			)
		);
	}

	/**
	 * Santize our settings
	 * @see process_admin_options()
	 */
	public function sanitize_settings( $settings ) {
		// We're just going to make the api key all upper case characters since that's how our imaginary API works
		if ( isset( $settings ) && isset( $settings['site_key'] ) ) {
			$settings['site_key'] = esc_textarea( $settings['site_key'] );
		}
		return $settings;
	}

	/**
	 * Validate the API key
	 * @see validate_settings_fields()
	 */
	public function validate_site_key_field( $key ) {
		// get the posted value
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

		// Check if the API key is longer than 60 characters.
		if ( isset( $value ) && 60 < strlen( $value ) ) {
			$this->errors[] = $key;
		}
		return $value;
	}

}

endif;