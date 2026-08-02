<?php
/**
 * Template: Website Card
 *
 * Browser-bar card: screenshot with URL bar, status badge, category, title, price/cms.
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
$url_display = $website_url ? preg_replace( '#^https?://#', '', rtrim( $website_url, '/' ) ) : '';

$cats     = $post_id ? get_the_terms( $post_id, 'website_category' ) : [];
$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

$status_cfg = [
	'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ), 'class' => 'bg-success' ],
	'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),     'class' => 'bg-secondary' ],
	'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ), 'class' => 'bg-warning text-dark' ],
];
$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'post-title h5 mb-3';
$lift_class  = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';
?>

<article class="card h-100 overflow-hidden<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; echo $lift_class; ?>">

	<div class="position-relative">
		<a href="<?php echo esc_url( $permalink ); ?>" class="d-block text-decoration-none">
			<div class="d-flex align-items-center bg-navy gap-1 px-3" style="height:32px;">
				<span style="width:10px;height:10px;background:#ff5f57;border-radius:50%;flex-shrink:0;"></span>
				<span style="width:10px;height:10px;background:#ffbd2e;border-radius:50%;flex-shrink:0;"></span>
				<span style="width:10px;height:10px;background:#28c840;border-radius:50%;flex-shrink:0;"></span>
				<?php if ( $url_display ) : ?>
				<span class="flex-grow-1 text-truncate bg-white rounded-1 px-2 text-muted ms-2" style="font-size:11px;line-height:1.6;min-width:0;"><?php echo esc_html( $url_display ); ?></span>
				<?php endif; ?>
			</div>
			<div class="overflow-hidden" style="height:200px;">
				<?php if ( $screenshot ) :
					echo wp_get_attachment_image( $screenshot, 'full', false, [
						'alt'   => esc_attr( $title ),
						'class' => 'd-block w-100',
						'style' => 'height:100%;object-fit:cover;object-position:top center;',
					] );
				else : ?>
				<div class="w-100 h-100 bg-soft-ash"></div>
				<?php endif; ?>
			</div>
		</a>

		<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;"><?php echo $st['label']; ?></span>
	</div>

	<div class="card-body p-4 d-flex flex-column">

		<div class="post-header">
			<?php if ( $cat_name ) : ?>
			<div class="post-category text-line mb-2"><?php echo esc_html( $cat_name ); ?></div>
			<?php endif; ?>
			<?php if ( $display['show_title'] && $title ) : ?>
			<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>">
				<a href="<?php echo esc_url( $permalink ); ?>" class="link-dark text-decoration-none"><?php echo esc_html( $title ); ?></a>
			</<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>
		</div>

		<div class="mt-auto d-flex flex-wrap gap-2 align-items-center">
			<?php if ( $price ) : ?>
			<span class="fw-bold text-primary fs-5"><?php echo esc_html( $price ); ?></span>
			<?php endif; ?>
			<?php if ( $cms ) : ?>
			<span class="badge bg-soft-ash text-ash"><?php echo esc_html( $cms ); ?></span>
			<?php endif; ?>
			<?php if ( $launch_time ) : ?>
			<span class="text-muted small"><i class="uil uil-clock me-1"></i><?php echo esc_html( $launch_time ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( $website_url ) : ?>
		<div class="mt-3 pt-3 border-top">
			<button type="button"
				class="btn btn-sm btn-outline-primary<?php echo esc_attr( $btn_style ); ?> has-ripple"
				data-bs-toggle="modal"
				data-bs-target="#cw-preview-modal"
				data-website-url="<?php echo esc_url( $website_url ); ?>"
				data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
				<i class="uil uil-eye me-1"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
			</button>
		</div>
		<?php endif; ?>

	</div>
</article>
