<?php

/**
 * WooCommerce La Poste Tracking compatibility with PIP extension.
 *
 * @since 1.0
 */

class WC_La_Poste_Tracking_PIP_Compat {

	/**
	 * Constructor.
	 *
	 * Hooks into various PIP filters.
	 */
	public function __construct() {
		// Add settings option.
		add_filter( 'wc_pip_general_settings', array( $this, 'add_settings' ), 20, 1 );

		// Display save button.
		add_filter( 'wc_pip_settings_hide_save_button', array( $this, 'show_save_button' ), 20, 2 );

		// Output fields in template.
		add_action( 'wc_pip_after_customer_addresses', array( $this, 'display_la_poste_tracking' ), 99, 4 );
	}

	/**
	 * Add La Poste tracking settings to PIP settings.
	 *
	 * @since 1.01.6.0
	 *
	 * @param array $settings Settings
	 * @return array Settings
	 */
	public function add_settings( $settings ) {
		return array_merge(
			$settings,
			array(
				array(
					'name' => __( 'La Poste Tracking', 'tracking-la-poste-for-woocommerce' ),
					'type' => 'title',
				),
				array(
					'id'      => 'woocommerce_pip_la_poste_tracking',
					'name'    => __( 'La Poste tracking', 'tracking-la-poste-for-woocommerce' ),
					'desc'    => __( 'Select which document types should display shipment tracking.', 'tracking-la-poste-for-woocommerce' ),
					'type'    => 'multiselect',
					'class'   => 'wc-enhanced-select',
					'options' => wc_pip()->get_document_types(),
				),
				array(
					'type' => 'sectionend',
				)
			)
		);
	}

	/**
	 * Show save button in general tab.
	 *
	 * @since 1.01.6.0
	 *
	 * @param bool $hide Whether to hide or not the save button, default false (do not hide), true on general tab
	 * @param string $current_section the current settings section
	 *
	 * @return bool
	 */
	public function show_save_button( $hide, $current_section ) {
		if ( 'general' === $current_section ) {
			$hide = false;
		}

		return $hide;
	}

	/**
	 * Display shipment tracking info in PIP document.
	 *
	 * @since 1.0
	 *
	 * @param string $type Document type
	 * @param string $action Current action running on Document
	 * @param WC_PIP_Document $document Document object
	 * @param WC_Order $order Order object
	 *
	 * @return void
	 */
	public function display_la_poste_tracking( $document_type, $action, $document, $order ) {
		$sta   = WC_La_Poste_Tracking_Actions::get_instance();
		$items = $sta->get_tracking_items( $order->id, true );

		if ( empty( $items ) ) {
			return;
		}

		$document_types = get_option( 'woocommerce_pip_la_poste_tracking', array() );
		if ( ! empty( $document_types ) && in_array( $document_type, $document_types ) ) {
			wc_get_template( 'pip/tracking-info.php', array( 'items' => $items ), 'tracking-la-poste-for-woocommerce/', $sta->get_plugin_path() . '/templates/' );
		}
	}
}
