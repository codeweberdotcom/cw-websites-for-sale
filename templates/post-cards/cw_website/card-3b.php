<?php
/**
 * Template: Website Card 3b
 *
 * Dark card with inset screenshot, category/status badges, price, Details + Preview modal buttons.
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
	'screen_height' => 220,
	'scroll_mode'   => false,
] );

$border_radius = class_exists( 'Codeweber_Options' )
	? ( Codeweber_Options::style( 'card-radius' ) ?: $template_args['border_radius'] )
	: $template_args['border_radius'];

$btn_style = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : '';

$cat_colors = [
	[ 'bg' => '#dbeafe', 'fg' => '#1d4ed8' ],
	[ 'bg' => '#dcfce7', 'fg' => '#15803d' ],
	[ 'bg' => '#fce7f3', 'fg' => '#9d174d' ],
	[ 'bg' => '#fef3c7', 'fg' => '#92400e' ],
	[ 'bg' => '#ede9fe', 'fg' => '#6d28d9' ],
	[ 'bg' => '#fee2e2', 'fg' => '#991b1b' ],
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
$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';

$cats     = $post_id ? get_the_terms( $post_id, 'website_category' ) : [];
$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
$cat_c    = ( $cats && ! is_wp_error( $cats ) ) ? $cat_colors[ $cats[0]->term_id % 6 ] : $cat_colors[0];
$st       = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'post-title h5 mb-0 text-dark';
$lift_class  = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';
?>

<article class="card h-100 shadow-sm<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; echo $lift_class; ?>">

	<?php
	$screen_h    = (int) $template_args['screen_height'];
	$scroll_mode = ! empty( $template_args['scroll_mode'] );
	$screen_wrap_class = 'position-relative overflow-hidden mx-2 mt-2' . ( $border_radius ? ' ' . esc_attr( $border_radius ) : '' ) . ( $scroll_mode ? ' cw-it-screen' : '' );
	$img_attrs = $scroll_mode
		? [ 'alt' => esc_attr( $title ), 'class' => 'w-100 cw-it-screenshot', 'style' => 'height:auto' ]
		: [ 'alt' => esc_attr( $title ), 'class' => 'w-100 d-block', 'style' => 'height:100%;object-fit:cover;object-position:top center;' ];
	?>
	<div class="<?php echo esc_attr( $screen_wrap_class ); ?>" style="height:<?php echo $screen_h; ?>px;">
		<?php if ( $screenshot ) :
			echo wp_get_attachment_image( $screenshot, 'full', false, $img_attrs );
		else : ?>
		<div class="w-100 h-100 bg-ash"></div>
		<?php endif; ?>
		<?php if ( $cat_name ) : ?>
		<span class="badge position-absolute top-0 start-0 m-2" style="background:<?php echo esc_attr( $cat_c['bg'] ); ?>;color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
		<?php endif; ?>
		<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 end-0 m-2"><?php echo $st['label']; ?></span>
	</div>

	<div class="card-body p-4">
		<div class="post-header">
			<?php if ( $display['show_title'] && $title ) : ?>
			<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>
			<p class="price text-primary fs-22 fw-bold mb-0">
				<ins><span class="amount"><?php echo $price ? esc_html( $price ) . ' ₽' : ''; ?></span></ins>
				<?php if ( $launch_time ) : ?>
				<span class="text-muted fs-sm ms-2">· <?php echo esc_html( $launch_time ); ?></span>
				<?php endif; ?>
			</p>
		</div>
	</div>

	<div class="card-footer d-flex gap-2 bg-transparent border-0 pt-0 px-4 pb-4">
		<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-primary<?php echo esc_attr( $btn_style ); ?> has-ripple flex-grow-1"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
		<?php if ( $website_url ) : ?>
		<button type="button" class="btn btn-outline-primary<?php echo esc_attr( $btn_style ); ?> has-ripple"
			data-bs-toggle="modal"
			data-bs-target="#cw-preview-modal"
			data-website-url="<?php echo esc_url( $website_url ); ?>"
			data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
			<i class="uil uil-play-circle me-1"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
		</button>
		<?php endif; ?>
	</div>

</article>
