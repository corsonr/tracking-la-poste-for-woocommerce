<?php
/**
 * Backwards compat.
 *
 *
 * @since 1.01.6.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/la-poste-tracking.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/la-poste-tracking.php', '/tracking-la-poste-for-woocommerce.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
