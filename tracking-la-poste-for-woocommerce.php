<?php
/*
	Plugin Name: La Poste Tracking for WooCommerce
	Plugin URI: http://www.remicorson.com/
	Description: Add tracking number to order package and update status dynamically.
	Version: 1.0
	Author: Remi Corson
	Author URI: http://www.remicorson.com/
	Text Domain: tracking-la-poste-for-woocommerce
	Domain Path: /languages

	Copyright: © 2016 Remi Corson.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	Shipment tracking URL: http://www.colissimo.fr/portail_colissimo/suivre.do?language=fr_FR&colispart=xxx
*/

/**
 * WC_La_Poste_Tracking class
 */
if ( ! class_exists( 'WC_La_Poste_Tracking' ) ) {

	class WC_La_Poste_Tracking {

		/**
		 * Instance of WC_La_Poste_Tracking_Actions.
		 *
		 * @var WC_La_Poste_Tracking_Actions
		 */
		public $actions;

		/**
		 * Instance of WC_La_Poste_Tracking_Compat.
		 *
		 * @var WC_La_Poste_Tracking_Compat
		 */
		public $compat;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) );

			// include required files
			$this->includes();
			
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'admin_print_styles', array( $this->actions, 'admin_styles' ) );
			add_action( 'add_meta_boxes', array( $this->actions, 'add_meta_box' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this->actions, 'save_meta_box' ), 0, 2 );
			add_action( 'plugins_loaded', array( $this->actions, 'load_plugin_textdomain' ) );

			// View Order Page
			add_action( 'woocommerce_view_order', array( $this->actions, 'display_tracking_info' ) );
			add_action( 'woocommerce_email_before_order_table', array( $this->actions, 'email_display' ), 0, 3 );

			// Custom tracking column in admin orders list.
			add_filter( 'manage_shop_order_posts_columns', array( $this->actions, 'shop_order_columns' ), 99 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this->actions, 'render_shop_order_columns' ) );

			// Order page metabox actions
			add_action( 'wp_ajax_WC_La_Poste_Tracking_delete_item', array( $this->actions, 'meta_box_delete_tracking' ) );
			add_action( 'wp_ajax_WC_La_Poste_Tracking_save_form', array( $this->actions, 'save_meta_box_ajax' ) );

			// Customer / Order CSV Export column headers/data
			add_filter( 'wc_customer_order_csv_export_order_headers', array( $this->actions, 'add_la_poste_tracking_info_to_csv_export_column_headers' ) );
			add_filter( 'wc_customer_order_csv_export_order_row', array( $this->actions, 'add_la_poste_tracking_info_to_csv_export_column_data' ), 10, 3 );
			
			$subs_version = class_exists( 'WC_Subscriptions' ) && ! empty( WC_Subscriptions::$version ) ? WC_Subscriptions::$version : null;

			// Prevent data being copied to subscriptions
			if ( null !== $subs_version && version_compare( $subs_version, '2.0.0', '>=' ) ) {
				add_filter( 'wcs_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 4 );
			} else {
				add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 4 );
			}

		}

		/**
		 * Initiate Plugin
		 *
		 * @since 1.0
		 */
		public function init() {
		
			//WooCommerce Integration
			if ( class_exists( 'WC_Integration' ) ) {
				include_once 'includes/class-wc-la-poste-tracking-integration.php';
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			}
			
		}

		/**
		 * Include required files
		 *
		 * @since 1.0
		 */
		private function includes() {
			
			// load main class
			require( 'includes/class-wc-la-poste-tracking.php' );
			$this->actions = WC_La_Poste_Tracking_Actions::get_instance();

			// load plugin compatibility class
			require_once( 'includes/class-wc-la-poste-tracking-compat.php' );
			$this->compat = new WC_La_Poste_Tracking_Compat();
			$this->compat->load_compats();
			
			// load wp-cron hooks for scheduled events
			require( 'includes/class-wc-la-poste-tracking-cron.php' );
			$this->cron = new WC_La_Poste_Tracking_Cron();
		}

		/**
		* Gets the absolute plugin path without a trailing slash, e.g.
		* /path/to/wp-content/plugins/plugin-directory
		*
		* @return string plugin path
		*/
		public function get_plugin_path() {
			if ( isset( $this->plugin_path ) ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		
		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               La Poste Tracking.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_La_Poste_Tracking_Integration';
			return $integrations;
		}
		
		/**
		 * Remove terms and scheduled events on plugin deactivation
		 *
		 * @since 1.0
		 */
		public function deactivate() {
			// Remove scheduling function before removing scheduled hook, or else it will get re-added
			remove_action( 'init', array( $this->cron, 'add_scheduled_events') );
			// clear pre-order completion check event
			wp_clear_scheduled_hook( 'wc_la_poste_tracking_completion_check' );
		}
		
	}

}

/**
 * Register this class globally
 */
$GLOBALS['WC_La_Poste_Tracking'] = new WC_La_Poste_Tracking();