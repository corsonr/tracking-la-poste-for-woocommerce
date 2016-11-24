<?php
/**
 * La Poste Tracking
 *
 * Shows tracking information in the HTML order email
 *
 * @author  Remi Corson
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $tracking_items ) : ?>
	<h2><?php echo apply_filters( 'woocommerce_la_poste_tracking_my_orders_title', __( 'Tracking Information', 'tracking-la-poste-for-woocommerce' ) ); ?></h2>

	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">

		<thead>
			<tr>
				<th class="tracking-number" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Tracking Number', 'tracking-la-poste-for-woocommerce' ); ?></th>
				<th class="date-shipped" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Date', 'tracking-la-poste-for-woocommerce' ); ?></th>
				<th class="tracking-status" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Status', 'tracking-la-poste-for-woocommerce' ); ?></th>
				<th class="order-actions" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">&nbsp;</th>
			</tr>
		</thead>

		<tbody><?php
			foreach ( $tracking_items as $tracking_item ) {
				
				?><tr class="tracking">
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'tracking-la-poste-for-woocommerce' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item[ 'tracking_number' ] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Date', 'tracking-la-poste-for-woocommerce' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item[ 'date_shipped' ] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item[ 'date_shipped' ] ); ?></time>
					</td>
					<td class="tracking-status" data-title="<?php _e( 'Status', 'tracking-la-poste-for-woocommerce' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item[ 'tracking_message' ] ); ?> (<?php echo esc_html( $tracking_item[ 'tracking_status' ] ); ?>)
					</td>
					<td class="order-actions" style="text-align: center; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
							<?php if( $tracking_item[ 'formatted_tracking_link' ] ) : ?><a href="<?php echo esc_url( $tracking_item[ 'formatted_tracking_link' ] ); ?>" target="_blank"><?php _e( 'Track', 'tracking-la-poste-for-woocommerce' ); ?></a><?php else: ?><em><?php _e( 'Tracking unavailable', 'tracking-la-poste-for-woocommerce' ); ?></em><?php endif; ?>
					</td>
				</tr><?php
			}
		?></tbody>
	</table>

<?php
endif;
