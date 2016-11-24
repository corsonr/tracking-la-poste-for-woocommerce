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

?>

<h3><?php echo apply_filters( 'woocommerce_la_poste_tracking_my_orders_title', __( 'Tracking Information', 'tracking-la-poste-for-woocommerce' ) ); ?></h3>

<?php foreach ( $items as $item ) : ?>
<p class="tracking-content">
	<strong><?php _e( 'Tracking Information', 'tracking-la-poste-for-woocommerce' ); ?></strong>
	<?php if( strlen( $item[ 'formatted_tracking_link' ] ) > 0 ) : ?>
		- <?php echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Click here to track your shipment', 'tracking-la-poste-for-woocommerce' ) ) . '">' . __( 'Track', 'tracking-la-poste-for-woocommerce' ) . '</a>', $item[ 'formatted_tracking_link' ] ); ?>
	<?php endif; ?>
	<br/>
	<em><?php echo esc_html( $item[ 'tracking_number' ] ); ?> :  <?php echo esc_html( $item[ 'tracking_message' ] ); ?></em>
	<br />
	<span style="font-size: 0.8em"><?php echo esc_html( sprintf( __( 'Shipped on %s', 'tracking-la-poste-for-woocommerce' ), date_i18n( 'Y-m-d', $item[ 'date_shipped' ] ) ) ); ?></span>
</p>
<?php endforeach; ?>
