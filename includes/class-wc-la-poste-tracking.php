<?php

/**
 * # WooCommerce La Poste Tracking Actions
 *
 * @since 1.0
 */

class WC_La_Poste_Tracking_Actions {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		// hook into cron event to check if shipments status are upated
		add_action( 'wc_la_poste_tracking_update_check', array( $this, 'check_for_shipments_statuses_to_update' ), 10 );
		
	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
    private static $instance;

	/**
     * Get the class instance
	 *
	 * @return WC_La_Poste_Tracking_Actions
	 */
    public static function get_instance() {
        return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
    }

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tracking-la-poste-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function admin_styles() {
		wp_enqueue_style( 'la_poste_tracking_styles', plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/assets/css/admin.css' );
	}

	/**
	 * Define La Poste tracking column in admin orders list.
	 *
	 * @since 1.0
	 *
	 * @param array $columns Existing columns
	 *
	 * @return array Altered columns
	 */
	public function shop_order_columns( $columns ) {
		$columns['la_poste_tracking'] = __( 'La Poste Tracking', 'tracking-la-poste-for-woocommerce' );
		return $columns;
	}

	/**
	 * Render La Poste tracking in custom column.
	 *
	 * @since 1.0
	 *
	 * @param string $column Current column
	 */
	public function render_shop_order_columns( $column ) {
		global $post;

		if ( 'la_poste_tracking' === $column ) {
			echo $this->get_la_poste_tracking_column( $post->ID );
		}
	}

	/**
	 * Get content for La Poste tracking column.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id Order ID
	 *
	 * @return string Column content to render
	 */
	public function get_la_poste_tracking_column( $order_id ) {
		ob_start();

		$tracking_items = $this->get_tracking_items( $order_id );

		if ( count( $tracking_items ) > 0 ) {
			foreach( $tracking_items as $tracking_item ) {
				if ( $tracking_item === end( $tracking_items )) {
					$formatted = $this->get_formatted_tracking_item( $order_id, $tracking_item );
					printf(
						'<a href="%s" target="_blank">%s</a><br />%s',
						esc_url( $formatted['formatted_tracking_link' ] ),
						esc_html( $tracking_item[ 'tracking_number' ] ), 
						$this->get_formatted_response( esc_html( $tracking_item[ 'tracking_message' ] ) )
					);
				}
			}
		} else {
			echo '–';
		}

		return apply_filters( 'woocommerce_la_poste_tracking_get_la_poste_tracking_column', ob_get_clean(), $order_id, $tracking_items );
	}

	/**
	 * Add the meta box for La Poste info on the order page
	 *
	 * @access public
	 */
	public function add_meta_box() {
		add_meta_box( 'tracking-la-poste-for-woocommerce', __( 'La Poste Tracking', 'tracking-la-poste-for-woocommerce' ), array( $this, 'meta_box' ), 'shop_order', 'side', 'high' );
	}

	/**
	 * Returns a HTML node for a tracking item for the admin meta box
	 *
	 * @access public
	 */
	public function display_html_tracking_item_for_meta_box( $order_id, $item ) {
			$formatted = $this->get_formatted_tracking_item( $order_id, $item );
			?>
			<div class="tracking-item" id="tracking-item-<?php echo esc_attr( $item[ 'tracking_id' ] ); ?>">
				<p class="tracking-content">
					<strong><?php _e( 'Code', 'tracking-la-poste-for-woocommerce' ); ?> : </strong><?php echo esc_html( $item[ 'tracking_number' ] ); ?>
					<?php if( $item[ 'tracking_status' ] != '') { ?>
					<br />
					<strong><?php _e( 'Status', 'tracking-la-poste-for-woocommerce' ); ?> : </strong><?php echo esc_html( $item[ 'tracking_status' ] ); ?>
					<?php } ?>
					<?php if( $item[ 'tracking_type' ] != '') { ?>
					<br />
					<strong><?php _e( 'Type', 'tracking-la-poste-for-woocommerce' ); ?> : </strong><?php echo esc_html( $this->get_formatted_response( $item[ 'tracking_type' ] ) ); ?>
					<?php } ?>
					<?php if( $item[ 'tracking_date' ] != '') { ?>
					<br />
					<strong><?php _e( 'Date', 'tracking-la-poste-for-woocommerce' ); ?> : </strong><?php echo esc_html( $this->get_formatted_response( $item[ 'tracking_date' ] ) ); ?>
					<?php } ?>
					<?php if( $item[ 'tracking_message' ] != '') { ?>
					<br />
					<strong><?php _e( 'Message', 'tracking-la-poste-for-woocommerce' ); ?> : </strong><em><?php echo esc_html( $this->get_formatted_response( $item[ 'tracking_message' ] ) ); ?></em>
					<?php } ?>
				</p>
				<p class="meta">
					<?php echo esc_html( sprintf( __( 'Shipped on %s', 'tracking-la-poste-for-woocommerce' ), date_i18n( 'Y-m-d', $item[ 'date_shipped' ] ) ) ); ?>
					<?php if( strlen( $item[ 'tracking_link' ] ) > 0 ) : ?>
						| <?php echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Click here to track your shipment', 'tracking-la-poste-for-woocommerce' ) ) . '">' . __( 'Track', 'tracking-la-poste-for-woocommerce' ) . '</a>', $item[ 'tracking_link' ] ); ?>
					<?php endif; ?>
					- <a href="#" class="delete-tracking" rel="<?php echo esc_attr( $item[ 'tracking_id' ] ); ?>"><?php _e( 'Delete', 'tracking-la-poste-for-woocommerce' ); ?></a>
				</p>
			</div>
			<?php
	}

	/**
	 * Show the meta box for shipment info on the order page
	 *
	 * @access public
	 */
	public function meta_box() {
		
		global $woocommerce, $post;

		$tracking_items = $this->get_tracking_items( $post->ID );

		echo '<div id="tracking-items">';

		if ( count( $tracking_items ) > 0 ) {
			foreach( $tracking_items as $tracking_item ) {
				$this->display_html_tracking_item_for_meta_box( $post->ID, $tracking_item );
			}
		}

		echo '</div>';

		echo '<button class="button button-show-form" type="button">' . __( 'Add Tracking Number', 'tracking-la-poste-for-woocommerce' ) . '</button>';

		echo '<div id="la-poste-tracking-form">';

		woocommerce_wp_hidden_input( array(
			'id'    => 'WC_La_Poste_Tracking_delete_nonce',
			'value' => wp_create_nonce( 'delete-tracking-item' )
		) );

		woocommerce_wp_hidden_input( array(
			'id'    => 'WC_La_Poste_Tracking_create_nonce',
			'value' => wp_create_nonce( 'create-tracking-item' )
		) );

		woocommerce_wp_text_input( array(
			'id'          => 'tracking_number',
			'label'       => __( 'La Poste Tracking Number:', 'tracking-la-poste-for-woocommerce' ),
			'placeholder' => '',
			'description' => '',
			'value'       => ''
		) );
		
		woocommerce_wp_text_input( array(
			'id'          => 'date_shipped',
			'label'       => __( 'Date shipped:', 'tracking-la-poste-for-woocommerce' ),
			'placeholder' => date_i18n( __( 'Y-m-d', 'tracking-la-poste-for-woocommerce' ), time() ),
			'description' => '',
			'class'       => 'date-picker-field',
			'value'       => date_i18n( __( 'Y-m-d', 'tracking-la-poste-for-woocommerce' ), current_time( 'timestamp' ) )
		) );
		
		woocommerce_wp_hidden_input( array(
			'id'          => 'tracking_status',
			'value'       => '',
		) );
		
		woocommerce_wp_hidden_input( array(
			'id'          => 'tracking_type',
			'value'       => '',
		) );
		
		woocommerce_wp_hidden_input( array(
			'id'          => 'tracking_date',
			'value'       => '',
		) );
		
		woocommerce_wp_hidden_input( array(
			'id'          => 'tracking_message',
			'value'       => '',
		) );
		
		woocommerce_wp_hidden_input( array(
			'id'          => 'tracking_link',
			'value'       => '',
		) );
		

		echo '<button class="button button-primary button-save-form">' . __( 'Save Tracking Number', 'tracking-la-poste-for-woocommerce' ) . '</button>';

		echo '</div>';

		$js = "

			jQuery('input#tracking_number').change(function(){

				var tracking = jQuery('input#tracking_number').val();


			}).change();";

		if ( function_exists( 'wc_enqueue_js' ) ) {
			wc_enqueue_js( $js );
		} else {
			$woocommerce->add_inline_js( $js );
		}

		wp_enqueue_script( 'wc-la-poste-tracking-js', $GLOBALS['WC_La_Poste_Tracking']->plugin_url . '/assets/js/admin.min.js' );

	}
	
	/**
	 * Get shipment tracking via the API
	 *
	 * Function to get the shipment tracking
	 */
	public function get_shipment_tracking( $code = '' ) {
		
		global $post;
		$order_id = $post->ID;

		$options = get_option( 'woocommerce_la_poste_tracking_settings', array() );

		$endpoint = 'https://api.laposte.fr/suivi/v1';
		$request  = "code=" . $code;
		$headers  = array( 
							'X-Okapi-Key' => $options['api_key'],
							'Content-Type' => 'application/json',
							'Accept' => 'application/json'
							);

		$response      = wp_remote_get( $endpoint . '?' . $request, array(
			'timeout' => 70,
			'headers' => $headers,
		) );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response = json_decode( $response['body'] );
		
		return $response;

	}

	/**
	 * Order Tracking Save
	 *
	 * Function for saving tracking items
	 */
	public function save_meta_box( $post_id, $post ) {

		if ( isset( $_POST['tracking_number'] ) && strlen( $_POST['tracking_number'] ) > 0 ) {

			$tracking = $this->get_shipment_tracking( $_POST['tracking_number'] );
			$args = array(
				'tracking_number'          => wc_clean( $_POST[ 'tracking_number' ] ),
				'tracking_status'          => $tracking->status,
				'tracking_type'            => $tracking->type,
				'tracking_date'            => $tracking->date,
				'tracking_message'         => $tracking->message,
				'tracking_link'            => $tracking->link,
				'date_shipped'             => wc_clean( $_POST[ 'date_shipped' ] )
			);

			$this->add_tracking_item( $post_id, $args );
		}
	}

	/**
	 * Order Tracking Save AJAX
	 *
	 * Function for saving tracking items via AJAX
	 */
	public function save_meta_box_ajax() {

		check_ajax_referer( 'create-tracking-item', 'security', true );

		if ( isset( $_POST['tracking_number'] ) && strlen( $_POST['tracking_number'] ) > 0 ) {

			$order_id = wc_clean( $_POST[ 'order_id' ] );
			
			$tracking = $this->get_shipment_tracking( $_POST['tracking_number'] );
			$args = array(
				'tracking_number'          => wc_clean( $_POST[ 'tracking_number' ] ),
				'tracking_status'          => $tracking->status,
				'tracking_type'            => $tracking->type,
				'tracking_date'            => $tracking->date,
				'tracking_message'         => $tracking->message,
				'tracking_link'            => $tracking->link,
				'date_shipped'             => wc_clean( $_POST[ 'date_shipped' ] )
			);

			$tracking_item = $this->add_tracking_item( $order_id, $args );

			$this->display_html_tracking_item_for_meta_box( $order_id, $tracking_item );
		}

		die();
	}

	/**
	 * Order Tracking Delete
	 *
	 * Function to delete a tracking item
	 */
	public function meta_box_delete_tracking() {

		check_ajax_referer( 'delete-tracking-item', 'security', true );

		$order_id = wc_clean( $_POST[ 'order_id' ] );
		$tracking_id = wc_clean( $_POST[ 'tracking_id' ] );

		$this->delete_tracking_item( $order_id, $tracking_id );
	}

	/**
	 * Display Shipment info in the frontend (order view/tracking page).
	 *
	 * @access public
	 */
	public function display_tracking_info( $order_id ) {
		wc_get_template( 'myaccount/view-order.php', array( 'tracking_items' => $this->get_tracking_items( $order_id, true ) ), 'tracking-la-poste-for-woocommerce/', $this->get_plugin_path() . '/templates/' );
	}

	/**
	 * Display shipment info in customer emails.
	 *
	 * @access public
	 * @return void
	 */
	public function email_display( $order, $sent_to_admin, $plain_text = null ) {

		if ( $plain_text === true ) {
			wc_get_template( 'email/plain/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order->id, true ) ), 'tracking-la-poste-for-woocommerce/', $this->get_plugin_path() . '/templates/' );
		}
		else {
			wc_get_template( 'email/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order->id, true ) ), 'tracking-la-poste-for-woocommerce/', $this->get_plugin_path() . '/templates/' );
		}
	}
	
	/**
	 * Called via wp-cron every 1 hour to check if there are shipments statuses to update
	 *
	 * @since 1.0
	 */
	public function check_for_shipments_statuses_to_update() {
		
		do_action( 'wc_la_poste_tracking_before_automatic_update_check' );
		
		$args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_order',
			'post_status' 	 => 'wc-completed',
			'date_query' => array(
								array(
									'column' => 'post_modified_gmt',
									'after'     => apply_filters( 'wc_la_poste_tracking_status_check_period', '1 week ago'),
									'inclusive' => true,
								),
							),		
			'meta_query' => array(
				array(
					'key'   	=> '_WC_La_Poste_Tracking_items',
					'compare' 	=> 'EXISTS',
				),
			),
		);
		
		$query = new WP_Query( $args );
		if ( empty( $query->posts ) ) {
			return;
		}
		
		foreach ( $query->posts as $order_post ) {
			
			$order = new WC_Order( $order_post );
			$shipments = get_post_meta( $order->id, '_WC_La_Poste_Tracking_items', true );
			
			foreach( $shipments as $shipment ) {
				
				// Check last status only
				if ( $shipment === end( $shipments )) {
					$current_shipment_status = $shipment[ 'tracking_status' ];
					$new_shipment_data = $this->get_shipment_tracking( $shipment[ 'tracking_number' ] );
					$new_shipment_status = $new_shipment_data->status;
					
					if( $new_shipment_status == $current_shipment_status ) {
						return;
					} else {
						
						$args = array(
							'tracking_number'          => wc_clean( $shipment[ 'tracking_number' ] ),
							'tracking_status'          => wc_clean( $new_shipment_status ),
							'tracking_type'            => $new_shipment_data->type,
							'tracking_date'            => $new_shipment_data->date,
							'tracking_message'         => $new_shipment_data->message,
							'tracking_link'            => $new_shipment_data->link,
							'date_shipped'             => wc_clean( $shipment[ 'tracking_date' ] )
						);
			
						$this->add_tracking_item( $order->id, $args );
						
					}
				}
				
			}
			
		}		
		
		do_action( 'wc_la_poste_tracking_after_automatic_update_check' );
	
	}

	/**
	 * Adds support for Customer/Order CSV Export by adding appropriate column headers
	 *
	 * @param array $headers existing array of header key/names for the CSV export
	 * @return array
	 */
	public function add_la_poste_tracking_info_to_csv_export_column_headers( $headers ) {

		$headers['la_poste_tracking'] = 'la_poste_tracking';
		return $headers;
	}

	/**
	 * Adds support for Customer/Order CSV Export by adding data for the column headers
	 *
	 * @param array $order_data generated order data matching the column keys in the header
	 * @param WC_Order $order order being exported
	 * @param \WC_CSV_Export_Generator $csv_generator instance
	 * @return array
	 */
	public function add_la_poste_tracking_info_to_csv_export_column_data( $order_data, $order, $csv_generator ) {

		$tracking_items   = $this->get_tracking_items( $order->id, true );
		$new_order_data   = array();
		$one_row_per_item = false;

		$la_poste_tracking_csv_output = '';

		if ( count( $tracking_items ) > 0 ) {
			foreach( $tracking_items as $item ) {
				$pipe = null;
				foreach( $item as $key => $value ) {
					if ( $key == 'date_shipped' )
						$value = date( 'Y-m-d', $value );
					$la_poste_tracking_csv_output .= "$pipe$key:$value";
					if ( !$pipe )
						$pipe = '|';
				}
				$la_poste_tracking_csv_output .= ';';
			}
		}

		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
		} elseif ( isset( $csv_generator->format_definition ) ) {
			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}

		if ( $one_row_per_item ) {

			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, array( 'la_poste_tracking' => $la_poste_tracking_csv_output ) );
			}

		} else {

			$new_order_data = array_merge( $order_data, array( 'la_poste_tracking' => $la_poste_tracking_csv_output ) );
		}

		return $new_order_data;
	}

	/**
	 * Prevents data being copied to subscription renewals
	 */
	public function woocommerce_subscriptions_renewal_order_meta_query( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {
		$order_meta_query .= " AND `meta_key` NOT IN ( '_WC_La_Poste_Tracking_items' )";

		return $order_meta_query;
	}

	/*
	 * Works out the final tracking provider and tracking link and appends then to the returned tracking item
	 *
	*/
	public function get_formatted_tracking_item( $order_id, $tracking_item ) {

		$formatted = array();
		
		$formatted[ 'formatted_tracking_link' ] = $tracking_item[ 'tracking_link' ];

		return $formatted;

	}
	
	/*
	 * Format shipment API response
	 *
	*/
	public function get_formatted_response( $response ) {

		return ucwords( strtolower( str_replace( '_', ' ', $response ) ) );

	}

	/**
	 * Deletes a tracking item from post_meta array
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 *
	 * @return bool True if tracking item is deleted successfully
	 */
	public function delete_tracking_item( $order_id, $tracking_id ) {

		$tracking_items = $this->get_tracking_items( $order_id );

		$is_deleted = false;
		if ( count( $tracking_items ) > 0 ) {
			foreach( $tracking_items as $key => $item ) {
				if ( $item[ 'tracking_id' ] == $tracking_id ) {
					unset( $tracking_items[ $key ] );
					$is_deleted = true;
					break;
				}
			}
			$this->save_tracking_items( $order_id, $tracking_items );
		}

		return $is_deleted;
	}

	/*
	 * Adds a tracking item to the post_meta array
	 *
	 * @param int   $order_id    Order ID
	 * @param array $tracking_items List of tracking item
	 *
	 * @return array Tracking item
	 */
	public function add_tracking_item( $order_id, $args ) {

		$tracking_item = array();

		$tracking_item[ 'tracking_number' ]          = wc_clean( $args[ 'tracking_number' ] );
		$tracking_item[ 'date_shipped' ]             = wc_clean( strtotime( $args[ 'date_shipped' ] ) );
		$tracking_item[ 'tracking_status' ]          = wc_clean( $args[ 'tracking_status' ] );
		$tracking_item[ 'tracking_type' ]          	 = wc_clean( $args[ 'tracking_type' ] );
		$tracking_item[ 'tracking_date' ]            = wc_clean( $args[ 'tracking_date' ] );
		$tracking_item[ 'tracking_message' ]         = wc_clean( $args[ 'tracking_message' ] );
		$tracking_item[ 'tracking_link' ]            = wc_clean( $args[ 'tracking_link' ] );

		if ( (int) $tracking_item[ 'date_shipped' ] == 0 ) {
			 $tracking_item[ 'date_shipped' ] = time();
		}

		$tracking_item[ 'tracking_id' ] = md5( "{$tracking_item[ 'tracking_number' ]}-{$tracking_item[ 'tracking_status' ]}-{$tracking_item[ 'tracking_type' ]}-{$tracking_item[ 'tracking_date' ]}-{$tracking_item[ 'tracking_message' ]}-{$tracking_item[ 'tracking_link' ]}" . microtime() );

		$tracking_items = $this->get_tracking_items( $order_id );

		$tracking_items[] = $tracking_item;

		$this->save_tracking_items( $order_id, $tracking_items );

		return $tracking_item;

	}

	/**
	 * Saves the tracking items array to post_meta.
	 *
	 * @param int   $order_id       Order ID
	 * @param array $tracking_items List of tracking item
	 *
	 * @return void
	 */
	public function save_tracking_items( $order_id, $tracking_items ) {
		update_post_meta( $order_id, '_WC_La_Poste_Tracking_items', $tracking_items );
	}

	/**
	 * Gets a single tracking item from the post_meta array for an order.
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 * @param bool   $formatted   Wether or not to reslove the final tracking
	 *                            link and provider in the returned tracking item.
	 *                            Default to false.
	 *
	 * @return null|array Null if not found, otherwise array of tracking item will be returned
	 */
	public function get_tracking_item( $order_id, $tracking_id, $formatted = false ) {
		$tracking_items = $this->get_tracking_items( $order_id, $formatted );

		if ( count( $tracking_items ) ) {
			foreach( $tracking_items as $item ) {
				if ( $item['tracking_id'] === $tracking_id ) {
					return $item;
				}
			}
		}

		return null;
	}

	/*
	 * Gets all tracking items fron the post meta array for an order
	 *
	 * @param int  $order_id  Order ID
	 * @param bool $formatted Wether or not to reslove the final tracking link
	 *                        and provider in the returned tracking item.
	 *                        Default to false.
	 *
	 * @return array List of tracking items
	 */
	public function get_tracking_items( $order_id, $formatted = false ) {

		global $wpdb;
		$tracking_items = get_post_meta( $order_id, '_WC_La_Poste_Tracking_items', true );

		if ( is_array( $tracking_items ) ) {
			if ( $formatted ) {
				foreach( $tracking_items as &$item ) {
					$formatted_item = $this->get_formatted_tracking_item( $order_id, $item );
					$item = array_merge( $item, $formatted_item );
				}
			}
			return $tracking_items;
		}
		else {
			return array();
		}
	}

	/**
	* Gets the absolute plugin path without a trailing slash, e.g.
	* /path/to/wp-content/plugins/plugin-directory
	*
	* @return string plugin path
	*/
	public function get_plugin_path() {
		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}
}
