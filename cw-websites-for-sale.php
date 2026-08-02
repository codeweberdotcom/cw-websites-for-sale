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
	load_plugin_textdomain( 'cw-websites-for-sale', false, dirname( plugin_basename( CW_WFS_FILE ) ) . '/languages' );
	( new CW\WebsitesForSale\Plugin() )->init();
} );

/**
 * Template helper — renders a single website card column.
 */
function cw_wfs_render_card( int $post_id ): void {
	\CW\WebsitesForSale\Plugin::render_card( $post_id );
}

/**
 * Renders a post-card template by slug, isolated to prevent variable leakage.
 *
 * @param int    $post_id          Post ID.
 * @param string $card_slug        Slug from templates/post-cards/cw_website/ (e.g. 'card-3b').
 * @param array  $display_settings Passed as $display_settings to the template.
 * @param array  $template_args    Passed as $template_args to the template.
 */
function cw_wfs_include_card( int $post_id, string $card_slug, array $display_settings = [], array $template_args = [] ): void {
	$template_file = CW_WFS_DIR . 'templates/post-cards/cw_website/' . $card_slug . '.php';
	if ( ! file_exists( $template_file ) ) {
		return;
	}
	$post_data = [
		'id'    => $post_id,
		'title' => get_the_title( $post_id ),
		'link'  => get_permalink( $post_id ),
		'type'  => 'cw_website',
	];
	( static function () use ( $template_file, $post_data, $display_settings, $template_args ) {
		include $template_file;
	} )();
}

/**
 * Returns a plugin setting value.
 */
function cw_wfs_setting( string $key, $default = '' ) {
	$opts = get_option( \CW\WebsitesForSale\Admin\SettingsPage::OPTION, [] );
	return $opts[ $key ] ?? $default;
}
