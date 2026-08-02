<?php
/**
 * Template: Website Card 3
 *
 * Overlay-5 card: screenshot with hover description, tags and preview modal button.
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
		'show_excerpt'   => true,
		'title_length'   => 0,
		'excerpt_length' => 20,
		'title_tag'      => 'h3',
		'title_class'    => '',
	] );

$template_args = wp_parse_args( $template_args ?? [], [
	'border_radius' => 'rounded',
	'enable_lift'   => false,
] );

$btn_style = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : ' rounded-pill';

$post_id     = $post_data['id'] ?? 0;
$title       = $post_data['title'] ?? '';
$title       = get_post_meta( $post_id, '_alt_title', true ) ?: $title;
$permalink   = $post_data['link'] ?? '';
$website_url = get_post_meta( $post_id, '_ws_url', true );
$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
$price       = get_post_meta( $post_id, '_ws_price', true );
$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
$cms         = get_post_meta( $post_id, '_ws_cms', true ) ?: '';
$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';

$cats     = $post_id ? get_the_terms( $post_id, 'website_category' ) : [];
$tags     = $post_id ? get_the_terms( $post_id, 'website_tag' ) : [];
$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

$status_cfg = [
	'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ), 'class' => 'bg-success' ],
	'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),     'class' => 'bg-secondary' ],
	'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ), 'class' => 'bg-warning text-dark' ],
];
$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

$excerpt = '';
if ( ! empty( $display['show_excerpt'] ) && $display['excerpt_length'] > 0 ) {
	$raw     = ! empty( $post_data['excerpt'] ) ? $post_data['excerpt'] : '';
	$excerpt = $raw ? wp_trim_words( wp_strip_all_tags( $raw ), $display['excerpt_length'], '...' ) : '';
}
if ( ! $excerpt && $cms ) {
	$excerpt = $cms . ( $price ? ' · ' . $price : '' );
}

if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'h5 text-white mb-1';

if ( $screenshot ) {
	$img_html = '<div class="overflow-hidden" style="height:300px;">'
		. wp_get_attachment_image( $screenshot, 'full', false, [
			'alt'   => esc_attr( $title ),
			'class' => 'd-block w-100',
			'style' => 'height:100%;object-fit:cover;object-position:top center;',
		] )
		. '</div>';
} else {
	$img_html = '<div class="bg-soft-ash" style="height:300px;"></div>';
}
?>

<figure class="overlay overlay-5 rounded card-interactive mb-0">
	<a href="<?php echo esc_url( $permalink ); ?>">
		<div class="bottom-overlay post-meta fs-16 position-absolute zindex-1 d-flex flex-column h-100 w-100 p-5">
			<div class="mt-auto">
				<?php if ( $display['show_title'] && $title ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
				<?php endif; ?>
				<?php if ( $cat_name ) : ?>
				<div class="post-category text-white opacity-75 small mb-1"><?php echo esc_html( $cat_name ); ?></div>
				<?php endif; ?>
				<?php if ( $price ) : ?>
				<div class="fw-bold text-white opacity-90"><?php echo esc_html( $price ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<figcaption class="p-5">
		<div class="post-body h-100 d-flex flex-column from-left justify-content-end">
			<?php if ( $excerpt ) : ?>
			<p class="mb-3"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
			<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
			<div class="d-flex flex-wrap gap-1 mb-3">
				<?php foreach ( $tags as $tag ) : ?>
				<span class="badge bg-white text-dark opacity-90"><?php echo esc_html( $tag->name ); ?></span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<div class="d-flex align-items-center gap-3">
				<a href="<?php echo esc_url( $permalink ); ?>" class="hover more me-4"><?php esc_html_e( 'More details', 'cw-websites-for-sale' ); ?></a>
				<?php if ( $website_url ) : ?>
				<button type="button"
					class="btn btn-sm btn-white<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start has-ripple"
					data-bs-toggle="modal"
					data-bs-target="#cw-preview-modal"
					data-website-url="<?php echo esc_url( $website_url ); ?>"
					data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
					<i class="uil uil-eye"></i>
					<?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
				</button>
				<?php endif; ?>
			</div>
		</div>
	</figcaption>
	<div class="position-absolute top-0 end-0 p-4 zindex-10 d-flex flex-column align-items-end gap-1 hover_card_button_hide">
		<?php if ( $price ) : ?>
		<span class="badge bg-green rounded-pill"><?php echo esc_html( $price ); ?></span>
		<?php endif; ?>
		<?php if ( $launch_time ) : ?>
		<span class="badge bg-yellow text-yellow rounded-pill"><?php echo esc_html( $launch_time ); ?></span>
		<?php endif; ?>
	</div>
</figure>
