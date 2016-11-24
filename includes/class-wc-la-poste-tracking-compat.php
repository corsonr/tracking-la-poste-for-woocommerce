<?php

/**
 * WooCommerce Shipoment Tracking compats handler.
 *
 * @since 1.01.6.0
 */

class WC_La_Poste_Tracking_Compat {

	/**
	 * Load compat classes and instantiate it.
	 */
	public function load_compats() {

		// Load built-in compat classes.
		require_once( 'compats/class-wc-la-poste-tracking-pip-compat.php' );
		require_once( 'compats/class-wc-la-poste-tracking-order-xml-export-compat.php' );

		$compats = array(
			'WC_La_Poste_Tracking_PIP_Compat',
			'WC_La_Poste_Tracking_XML_Export_Compat',
		);

		/**
		 * Filters the La Poste tracking compats.
		 *
		 * @since 1.01.6.0
		 *
		 * @param array $compats List of class names that provide compatibilities
		 *                       with WooCommerce La Poste Tracking
		 */
		$compats = apply_filters( 'WC_La_Poste_Tracking_compats', $compats );

		foreach ( $compats as $compat ) {
			if ( class_exists( $compat ) ) {
				new $compat();
			}
		}
	}
}
