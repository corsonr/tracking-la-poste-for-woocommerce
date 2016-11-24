<?php
/**
 * WooCommerce La Poste Tracking
 *
 * @author      Remi Corson
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * La Poste Tracking Cron class
 *
 * Adds custom wp-cron schedule and shipments status change
 *
 * @since 1.0
 */
class WC_La_Poste_Tracking_Cron {
	
	/**
	 * Adds hooks and filters
	 *
	 * @since 1.0
	 * @return \WC_La_Poste_Tracking_Cron
	 */
	public function __construct() {
		// Add custom schedule, the default interval for pre-order check is every 1 hour
		add_filter( 'cron_schedules', array( $this, 'add_custom_schedules' ) );
		// Schedule a complete check event if it doesn't exist - activation hooks are unreliable, so attempt to schedule events on every page load
		add_action( 'init', array( $this, 'add_scheduled_events' ) );
	}
	
	/**
	 * Adds custom wp-cron schedule named 'wc_la_poste_tracking_update_check' with custom 1 hour interval
	 *
	 * @since 1.0
	 * @param array $schedules existing WP recurring schedules
	 * @return array
	 */
	public function add_custom_schedules( $schedules ) {
		$interval = apply_filters( 'wc_la_poste_tracking_update_check_interval', 3600, $schedules );
		$schedules['wc_la_poste_tracking_update_check'] = array(
			'interval' => $interval,
			'display'  => sprintf( __( 'Every %d minutes', 'tracking-la-poste-for-woocommerce' ), $interval / 60 )
		);
		return $schedules;
	}
	
	/**
	 * Add scheduled events to wp-cron if not already added
	 *
	 * @since 1.0
	 * @return array
	 */
	public function add_scheduled_events() {
		// Schedule shipment tracking check with custom interval named 'wc_la_poste_tracking_update_check'
		// note the next execution time if the plugin is deactivated then reactivated is the current time + 1 hour
		if ( ! wp_next_scheduled( 'wc_la_poste_tracking_update_check' ) )
			wp_schedule_event( time() + 3600, 'wc_la_poste_tracking_update_check', 'wc_la_poste_tracking_update_check' );
	}
} // end \WC_La_Poste_Tracking_Cron class