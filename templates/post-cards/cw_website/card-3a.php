<?php
/**
 * Template: Website Card 3a
 *
 * Dark card with violet accent: screenshot, colored dot category, title, price, Details + Preview icon.
 *
 * @param array $post_data        Post data from cw_get_post_card_data().
 * @param array $display_settings Display settings.
 * @param array $template_args    Extra arguments.
 */

if ( ! isset( $post_data ) || ! $post_data ) {
	return;
}

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
] );

$border_radius = class_exists( 'Codeweber_Options' )
	? ( Codeweber_Options::style( 'card-radius' ) ?: $template_args['border_radius'] )
	: $template_args['border_radius'];

$btn_style = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : '';

$cat_colors = [
	[ 'dot' => '#60a5fa', 'fg' => '#60a5fa' ],
	[ 'dot' => '#4ade80', 'fg' => '#4ade80' ],
	[ 'dot' => '#f472b6', 'fg' => '#f472b6' ],
	[ 'dot' => '#fbbf24', 'fg' => '#fbbf24' ],
	[ 'dot' => '#a78bfa', 'fg' => '#a78bfa' ],
	[ 'dot' => '#f87171', 'fg' => '#f87171' ],
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
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'mb-0 fw-bold text-white';
$lift_class  = ! empty( $template_args['enable_lift'] ) ? ';transform:translateY(-4px)' : '';
?>

<article class="h-100 bg-dark<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; ?>" style="border:1px solid #26324a;overflow:hidden;display:flex;flex-direction:column;transition:transform .18s,box-shadow .18s,border-color .18s<?php echo $lift_class; ?>;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 22px 48px rgba(0,0,0,.55)';this.style.borderColor='#818cf8'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='#26324a'">

	<div class="position-relative overflow-hidden" style="height:178px;">
		<?php if ( $screenshot ) :
			echo wp_get_attachment_image( $screenshot, 'full', false, [
				'alt'   => esc_attr( $title ),
				'class' => 'd-block w-100',
				'style' => 'height:100%;object-fit:cover;object-position:top center;',
			] );
		else : ?>
		<div class="w-100 h-100 bg-ash"></div>
		<?php endif; ?>
		<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;"><?php echo $st['label']; ?></span>
		<?php if ( $website_url ) : ?>
		<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener"
			class="position-absolute d-flex align-items-center justify-content-center text-decoration-none"
			style="inset:0;background:rgba(11,17,32,.6);opacity:0;transition:opacity .18s;"
			onmouseenter="this.style.opacity='1'" onmouseleave="this.style.opacity='0'">
			<span class="rounded-pill fw-bold px-4 py-2 bg-primary text-dark" style="font-size:14px;"><i class="uil uil-play-circle me-2"></i><?php esc_html_e( 'Live Preview', 'cw-websites-for-sale' ); ?></span>
		</a>
		<?php endif; ?>
	</div>

	<div class="d-flex flex-column gap-3 p-4 flex-grow-1">
		<div class="d-flex align-items-center gap-2">
			<?php if ( $cat_name ) : ?>
			<span style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:<?php echo esc_attr( $cat_c['dot'] ); ?>;"></span>
			<span class="small fw-bold" style="color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
			<span class="text-muted" style="font-size:12px;">•</span>
			<?php endif; ?>
			<span class="small fw-semibold text-muted"><?php echo esc_html( $cms ); ?><?php if ( $launch_time ) echo ' · ' . esc_html( $launch_time ); ?></span>
		</div>

		<?php if ( $display['show_title'] && $title ) : ?>
		<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>" style="font-size:20px;letter-spacing:-.02em;line-height:1.2;"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
		<?php endif; ?>

		<?php if ( $price ) : ?>
		<div class="mt-auto text-primary" style="font-size:22px;font-weight:800;line-height:1;"><?php echo esc_html( $price ); ?> ₽</div>
		<?php endif; ?>
	</div>

	<div class="px-4 pb-4" style="display:grid;grid-template-columns:1fr auto;gap:8px;">
		<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-primary fw-bold<?php echo esc_attr( $btn_style ); ?>" style="height:46px;display:flex;align-items:center;justify-content:center;"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
		<?php if ( $website_url ) : ?>
		<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Preview', 'cw-websites-for-sale' ); ?>" class="btn btn-outline-secondary<?php echo esc_attr( $btn_style ); ?>" style="width:46px;height:46px;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="uil uil-play-circle"></i></a>
		<?php endif; ?>
	</div>

</article>
