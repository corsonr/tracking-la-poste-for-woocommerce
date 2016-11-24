<?php

/**
 * WooCommerce Shipoment Tracking compatibility with Customer / Order XML Export
 *
 * @since 1.0.0
 */

class WC_La_Poste_Tracking_XML_Export_Compat {

	/**
	 * Constructor
	 */
	public function __construct() {

		// add fields based on XML Export version
		if ( version_compare( get_option( 'wc_customer_order_xml_export_suite_version' ), '2.0.0', '<' ) ) {
			add_filter( 'wc_customer_order_xml_export_suite_order_export_order_list_format', array( $this, 'add_fields_to_xml_export_order_format' ), 10, 2 );
		} else {
			add_filter( 'wc_customer_order_xml_export_suite_order_data', array( $this, 'add_fields_to_xml_export_order_format' ), 10, 2 );
		}
	}


	/**
	 * Adds fields to the order XML Export for La Poste tracking information
	 *
	 * @param array $order_format fields in the order XML output
	 * @param \WC_Order $order the order object being exported
	 * @return array - the updated fields
	 */
	public function add_fields_to_xml_export_order_format( $format, $order ) {

		$tracking_items             = $GLOBALS['WC_La_Poste_Tracking']->actions->get_tracking_items( $order->id, true );
		$format['LaPosteTracking'] = array();

		// bail if we have no tracking items
		if ( 0 === count( $tracking_items ) ) {
			return $format;
		}

		foreach ( $tracking_items as $key => $values ) {

			// format timestamps for humans
			$values['date_shipped'] = date( 'Y-m-d', $values['date_shipped'] );

			// add the values for each tracking item into a <Package> tag
			$format['LaPosteTracking']['Package'][] = $values;
		}

		return $format;
	}
}
