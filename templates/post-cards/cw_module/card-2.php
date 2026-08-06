<?php
/**
 * Template: Module Card 2
 *
 * Frosted card with flat corners, icon, title, description and the
 * module's category shown as an uppercase subtitle.
 *
 * @param array $post_data        Post data from cw_get_post_card_data().
 * @param array $display_settings Display settings from cw_get_post_card_display_settings().
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
	'enable_lift' => false,
] );

$post_id = $post_data['id'] ?? 0;
$icon    = $post_id ? ( get_post_meta( $post_id, '_module_icon', true ) ?: 'check-circle' ) : 'check-circle';
$hex     = $post_id ? ( get_post_meta( $post_id, '_module_color', true ) ?: '#605dba' ) : '#605dba';

// Map hex → Bootstrap theme color name for text-{color}.
$color_map = [
	'#5eb9f0' => 'sky',
	'#3f78e0' => 'blue',
	'#605dba' => 'grape',
	'#a07cc5' => 'violet',
	'#d16b86' => 'pink',
	'#e2626b' => 'red',
	'#f78b77' => 'orange',
	'#fab758' => 'yellow',
	'#45c4a0' => 'green',
	'#7cb798' => 'leaf',
	'#54a8c7' => 'aqua',
	'#343f52' => 'navy',
	'#9499a3' => 'ash',
];
$color_name = $color_map[ strtolower( $hex ) ] ?? 'primary';

$title = $post_data['title'] ?? '';
if ( $display['title_length'] > 0 && mb_strlen( $title ) > $display['title_length'] ) {
	$title = mb_substr( $title, 0, $display['title_length'] ) . '...';
}

$excerpt = '';
if ( ! empty( $display['show_excerpt'] ) && $display['excerpt_length'] > 0 ) {
	$raw     = ! empty( $post_data['excerpt'] ) ? $post_data['excerpt'] : ( $post_data['post']->post_content ?? '' );
	$excerpt = wp_trim_words( wp_strip_all_tags( $raw ), $display['excerpt_length'], '...' );
}

$title_tag   = isset( $display['title_tag'] ) ? sanitize_html_class( $display['title_tag'] ) : 'h3';
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'text-white text-start';

$lift_class = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';

$category_name = '';
if ( $post_id ) {
	$terms = get_the_terms( $post_id, 'module_category' );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$category_name = $terms[0]->name;
	}
}
?>

<div class="card h-100 border bg-frost rounded-0<?php echo esc_attr( $lift_class ); ?>">
	<div class="card-body">

		<div class="icon text-<?php echo esc_attr( $color_name ); ?> mb-4 fs-25">
			<i class="uil uil-<?php echo esc_attr( $icon ); ?> text-<?php echo esc_attr( $color_name ); ?>"></i>
		</div>

		<div class="d-flex flex-column">

			<?php if ( $display['show_title'] && $title ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>">
					<?php echo esc_html( $title ); ?>
				</<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( $excerpt ) : ?>
				<p class="text-muted mb-0"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $category_name ) : ?>
				<div class="text-muted text-uppercase mt-3"><?php echo esc_html( $category_name ); ?></div>
			<?php endif; ?>

		</div>

	</div>
</div>
