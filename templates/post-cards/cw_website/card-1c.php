<?php
/**
 * Card Template: Card 1c
 * Description: Price pill on screenshot, dot category, dark footer with Details / Preview
 * Supports: title
 * Order: 30
 *
 * Price pill overlay on screenshot, colored dot category, dark footer with Details/Preview.
 *
 * @param array $post_data        Post data from cw_get_post_card_data().
 * @param array $display_settings Display settings.
 * @param array $template_args    Extra arguments.
 */

if ( ! isset( $post_data ) || ! $post_data ) {
	return;
}

\CW\WebsitesForSale\Plugin::request_preview_modal();

$display = function_exists( 'cw_get_post_card_display_settings' )
	? cw_get_post_card_display_settings( $display_settings ?? [] )
	: wp_parse_args( $display_settings ?? [], [
		'show_title'     => true,
		'show_excerpt'   => false,
		'title_length'   => 0,
		'excerpt_length' => 20,
		'title_tag'      => 'h3',
		'title_class'    => '',
	] );

$template_args = wp_parse_args( $template_args ?? [], [
	'border_radius' => 'rounded',
	'enable_lift'   => false,
	'screen_height' => 176,
] );

$border_radius = class_exists( 'Codeweber_Options' )
	? ( Codeweber_Options::style( 'card-radius' ) ?: $template_args['border_radius'] )
	: $template_args['border_radius'];

$cat_colors = [
	[ 'fg' => '#1d4ed8', 'dot' => '#60a5fa' ],
	[ 'fg' => '#15803d', 'dot' => '#4ade80' ],
	[ 'fg' => '#9d174d', 'dot' => '#f472b6' ],
	[ 'fg' => '#92400e', 'dot' => '#fbbf24' ],
	[ 'fg' => '#6d28d9', 'dot' => '#a78bfa' ],
	[ 'fg' => '#991b1b', 'dot' => '#f87171' ],
];

$status_cfg = [
	'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ), 'class' => 'bg-success' ],
	'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),     'class' => 'bg-secondary' ],
	'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ), 'class' => 'bg-warning text-dark' ],
];

$post_id     = $post_data['id'] ?? 0;
$title       = $post_data['title'] ?? '';
$title       = get_post_meta( $post_id, '_alt_title', true ) ?: $title;
$permalink   = $post_data['link'] ?? '';
$website_url = get_post_meta( $post_id, '_ws_url', true );
$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
$price       = get_post_meta( $post_id, '_ws_price', true );
$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
$cms         = get_post_meta( $post_id, '_ws_cms', true ) ?: 'WordPress';
$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';

$cats     = $post_id ? get_the_terms( $post_id, 'website_category' ) : [];
$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
$cat_c    = ( $cats && ! is_wp_error( $cats ) ) ? $cat_colors[ $cats[0]->term_id % 6 ] : $cat_colors[0];
$st       = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'fs-20 fw-bold text-dark mb-0';
$lift_class  = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';
?>

<article class="card shadow-sm border-0<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; ?> overflow-hidden h-100<?php echo $lift_class; ?>">

	<div class="cw-it-screen overflow-hidden position-relative" style="height:<?php echo (int) $template_args['screen_height']; ?>px;">
		<?php if ( $screenshot ) :
			echo wp_get_attachment_image( $screenshot, 'full', false, [
				'alt'   => esc_attr( $title ),
				'class' => 'cw-it-screenshot d-block w-100 h-auto',
			] );
		else : ?>
		<div class="w-100 h-100 bg-soft-ash"></div>
		<?php endif; ?>
		<?php if ( $price ) : ?>
		<span class="badge bg-white text-dark rounded-pill shadow-sm position-absolute top-0 end-0 m-2 z-2 fs-14 fw-bold px-3 py-2"><?php echo esc_html( $price ); ?> ₽</span>
		<?php endif; ?>
		<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2 z-2"><?php echo $st['label']; ?></span>
	</div>

	<div class="card-body d-flex flex-column gap-3 p-4">
		<div class="d-flex align-items-center gap-2">
			<?php if ( $cat_name ) : ?>
			<span style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:<?php echo esc_attr( $cat_c['dot'] ); ?>;"></span>
			<span class="small fw-bold" style="color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
			<span class="text-muted small">·</span>
			<?php endif; ?>
			<span class="small text-muted fw-semibold"><?php echo esc_html( $cms ); ?><?php if ( $launch_time ) echo ' · ' . esc_html( $launch_time ); ?></span>
		</div>

		<?php if ( $display['show_title'] && $title ) : ?>
		<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
		<?php endif; ?>
	</div>

	<div class="d-flex mt-auto">
		<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-primary text-dark rounded-0 flex-fill"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
		<?php if ( $website_url ) : ?>
		<button type="button"
			class="btn btn-outline-primary rounded-0 flex-fill"
			data-bs-toggle="modal"
			data-bs-target="#cw-preview-modal"
			data-website-url="<?php echo esc_url( $website_url ); ?>"
			data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
			<i class="uil uil-play-circle me-2"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
		</button>
		<?php endif; ?>
	</div>

</article>
