<?php
/**
 * Card Template: Card 4
 * Description: Horizontal split — screenshot left, dark feature panel right, stacks on mobile
 * Supports: title
 * Order: 70
 *
 * Horizontal split card: screenshot column + dark navy content column with
 * category, title, feature tags (website_tag) and a price/actions footer.
 * Columns stack vertically below the md breakpoint.
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
	'screen_height' => 197,
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

$cats     = $post_id ? get_the_terms( $post_id, 'website_category' ) : [];
$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

$tags       = $post_id ? get_the_terms( $post_id, 'website_tag' ) : [];
$tag_names  = ( $tags && ! is_wp_error( $tags ) ) ? wp_list_pluck( array_slice( $tags, 0, 3 ), 'name' ) : [];

if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'text-white mb-0';
$lift_class  = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';
?>

<article class="card border-0 bg-navy overflow-hidden h-100<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; ?><?php echo $lift_class; ?>">
	<div class="row g-0 h-100">

		<div class="col-12 col-md-5 position-relative">
			<div class="cw-it-screen overflow-hidden position-relative" style="height:<?php echo (int) $template_args['screen_height']; ?>px;">
				<?php if ( $screenshot ) :
					echo wp_get_attachment_image( $screenshot, 'full', false, [
						'alt'   => esc_attr( $title ),
						'class' => 'cw-it-screenshot d-block w-100 h-auto',
					] );
				else : ?>
				<div class="w-100 h-100 bg-soft-ash"></div>
				<?php endif; ?>
				<?php if ( $cat_name ) : ?>
				<span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-2 z-2"><?php echo esc_html( $cat_name ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-12 col-md-7 p-4 p-md-5 d-flex flex-column justify-content-center gap-3">

			<?php if ( $display['show_title'] && $title ) : ?>
			<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( $tag_names ) : ?>
			<div class="d-flex flex-wrap gap-2">
				<?php foreach ( $tag_names as $tag_name ) : ?>
				<span class="badge border border-light text-white bg-transparent rounded-pill fw-normal"><?php echo esc_html( $tag_name ); ?></span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-2">
				<div class="d-flex align-items-center gap-2">
					<?php if ( $price ) : ?>
					<span class="text-white fs-22 fw-bold"><?php echo esc_html( $price ); ?> ₽</span>
					<?php endif; ?>
					<?php if ( $launch_time ) : ?>
					<span class="text-white-50 fs-sm"><?php echo esc_html( $launch_time ); ?></span>
					<?php endif; ?>
				</div>
				<div class="d-flex gap-2">
					<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-primary<?php echo esc_attr( $btn_style ); ?>"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
					<?php if ( $website_url ) : ?>
					<button type="button"
						class="btn btn-outline-light<?php echo esc_attr( $btn_style ); ?>"
						data-bs-toggle="modal"
						data-bs-target="#cw-preview-modal"
						data-website-url="<?php echo esc_url( $website_url ); ?>"
						data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
						<i class="uil uil-play-circle me-2"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
					</button>
					<?php endif; ?>
				</div>
			</div>

		</div>

	</div>
</article>
