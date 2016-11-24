<?php
/**
 * La Poste Tracking
 *
 * Shows tracking information in the plain text order email
 *
 * @author  Remi Corson
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $tracking_items ) : 
	
	echo apply_filters( 'woocommerce_la_poste_tracking_my_orders_title', __( 'TRACKING INFORMATION', 'tracking-la-poste-for-woocommerce' ) ); 

		echo  "\n";

		foreach ( $tracking_items as $tracking_item ) {
			
			echo esc_html( $tracking_item[ 'tracking_number' ] ) . "\n";
			echo esc_html( $tracking_item[ 'tracking_message' ] ) . "\n";
			echo esc_url( $tracking_item[ 'formatted_tracking_link' ] ) . "\n\n";
			
		}

	echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= \n\n";

endif;

?>
