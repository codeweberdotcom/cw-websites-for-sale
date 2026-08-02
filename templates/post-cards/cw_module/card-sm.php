<?php
/**
 * Template: Module Card SM
 *
 * Compact horizontal card: icon on the left, title + description on the right.
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
	'border_radius' => 'rounded',
	'enable_lift'   => false,
] );

// Redux card-radius overrides block attribute (keeps cards consistent with the site style).
$border_radius = class_exists( 'Codeweber_Options' )
	? ( Codeweber_Options::style( 'card-radius' ) ?: $template_args['border_radius'] )
	: $template_args['border_radius'];

$post_id = $post_data['id'] ?? 0;
$icon    = $post_id ? ( get_post_meta( $post_id, '_module_icon', true ) ?: 'check-circle' ) : 'check-circle';
$hex     = $post_id ? ( get_post_meta( $post_id, '_module_color', true ) ?: '#605dba' ) : '#605dba';

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
$title_class = ! empty( $display['title_class'] ) ? esc_attr( $display['title_class'] ) : 'mb-1';

$lift_class = ! empty( $template_args['enable_lift'] ) ? ' lift' : '';
?>

<article class="card shadow-lg p-0 h-100<?php echo $border_radius ? ' ' . esc_attr( $border_radius ) : ''; echo $lift_class; ?>">
	<div class="card-body py-4 px-5">
		<div class="d-flex flex-row align-items-center">

			<div class="icon btn btn-circle btn-soft-<?php echo esc_attr( $color_name ); ?> fs-20 pe-none flex-shrink-0 me-3">
				<i class="uil uil-<?php echo esc_attr( $icon ); ?>"></i>
			</div>

			<div>
				<?php if ( $display['show_title'] && $title ) : ?>
					<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>">
						<?php echo esc_html( $title ); ?>
					</<?php echo esc_attr( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( $excerpt ) : ?>
					<p class="fs-14 lh-sm mb-0"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</article>
