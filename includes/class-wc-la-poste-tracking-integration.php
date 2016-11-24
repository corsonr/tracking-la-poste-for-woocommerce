<?php
	
if ( ! class_exists( 'WC_La_Poste_Tracking_Integration' ) ) :

class WC_La_Poste_Tracking_Integration extends WC_Integration {
	
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		
		global $woocommerce;
		
		$this->id                 = 'la_poste_tracking';
		$this->method_title       = __( 'La Poste Tracking', 'tracking-la-poste-for-woocommerce' );
		$this->method_description = sprintf( __( 'Allow shipment tracking using the La Poste API. Ket your API key here: %s', 'tracking-la-poste-for-woocommerce' ), '<a href="https://developer.laposte.com">La Poste Developer</a>' );
		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		
		// Define variables.
		$this->api_key          = $this->get_option( 'api_key' );
		$this->api_sandbox_key  = $this->get_option( 'api_sandbox_key' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
	}
	
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'api_key' => array(
				'title'             => __( 'API Key', 'tracking-la-poste-for-woocommerce' ),
				'type'              => 'text',
				'description'       => __( 'Enter with your API Key.', 'tracking-la-poste-for-woocommerce' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'api_sandbox_key' => array(
				'title'             => __( 'API Sandbox Key', 'tracking-la-poste-for-woocommerce' ),
				'type'              => 'text',
				'description'       => __( 'Enter with your API Sandbox Key (used for testing).', 'tracking-la-poste-for-woocommerce' ),
				'desc_tip'          => true,
				'default'           => ''
			),
		);
	}
}

endif;