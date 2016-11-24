<?php
/**
 * View Order: Tracking information
 *
 * Shows tracking numbers view order page
 *
 * @author  Remi Corson
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $tracking_items ) : ?>

	<h2><?php echo apply_filters( 'woocommerce_la_poste_tracking_my_orders_title', __( 'Tracking Information', 'tracking-la-poste-for-woocommerce' ) ); ?></h2>

	<table class="shop_table shop_table_responsive my_account_tracking">
		<thead>
			<tr>
				<th class="tracking-number"><span class="nobr"><?php _e( 'Tracking Number', 'tracking-la-poste-for-woocommerce' ); ?></span></th>
				<th class="date-shipped"><span class="nobr"><?php _e( 'Date', 'tracking-la-poste-for-woocommerce' ); ?></span></th>
				<th class="tracking-status"><span class="nobr"><?php _e( 'Status', 'tracking-la-poste-for-woocommerce' ); ?></span></th>
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody><?php
			foreach ( $tracking_items as $tracking_item ) {
				
				?><tr class="tracking">
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'tracking-la-poste-for-woocommerce' ); ?>">
						<?php echo $tracking_item[ 'tracking_number' ]; ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Date', 'tracking-la-poste-for-woocommerce' ); ?>" style="text-align:left; white-space:nowrap;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item[ 'date_shipped' ] ); ?></time>
					</td>
					<td class="tracking-status" data-title="<?php _e( 'Status', 'tracking-la-poste-for-woocommerce' ); ?>">
						<?php echo esc_html( $tracking_item[ 'tracking_message' ] ); ?>
					</td>
					<td class="order-actions" style="text-align: center;">
							<?php if( $tracking_item[ 'tracking_link' ] ) : ?><a href="<?php echo esc_url( $tracking_item[ 'formatted_tracking_link' ] ); ?>" target="_blank" class="button"><?php _e( 'Track', 'tracking-la-poste-for-woocommerce' ); ?></a><?php else: ?><em><?php _e( 'Tracking unavailable', 'tracking-la-poste-for-woocommerce' ); ?></em><?php endif; ?>
					</td>
				</tr><?php
			}
		?></tbody>
	</table>

<?php
endif;
