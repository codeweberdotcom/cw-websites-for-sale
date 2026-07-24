<?php
/**
 * Plugin Name: CW Websites For Sale
 * Description: Website sales catalog: IT-style cards with hover-scroll screenshots, device preview modal, category and tag AJAX filters.
 * Version:     1.0.1
 * Author:      CodeWeber
 * Text Domain: cw-websites-for-sale
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'CW_WFS_VERSION', '1.0.1' );
define( 'CW_WFS_FILE',    __FILE__ );
define( 'CW_WFS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'CW_WFS_URL',     plugin_dir_url( __FILE__ ) );

spl_autoload_register( function ( $class ) {
	$prefix   = 'CW\\WebsitesForSale\\';
	$base_dir = CW_WFS_DIR . 'src/';
	$len      = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}
	$file = $base_dir . str_replace( '\\', '/', substr( $class, $len ) ) . '.php';
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

register_activation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );

add_action( 'plugins_loaded', function () {
	( new CW\WebsitesForSale\Plugin() )->init();
} );

/**
 * Template helper — renders a single website card column.
 */
function cw_wfs_render_card( int $post_id ): void {
	\CW\WebsitesForSale\Plugin::render_card( $post_id );
}

/**
 * Returns a plugin setting value.
 */
function cw_wfs_setting( string $key, $default = '' ) {
	$opts = get_option( \CW\WebsitesForSale\Admin\SettingsPage::OPTION, [] );
	return $opts[ $key ] ?? $default;
}
