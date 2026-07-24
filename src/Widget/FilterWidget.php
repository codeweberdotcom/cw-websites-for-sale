<?php

namespace CW\WebsitesForSale\Widget;

class FilterWidget extends \WP_Widget {

	public function __construct() {
		parent::__construct(
			'cw_wfs_filter',
			esc_html__( 'Websites: Categories & Tags', 'cw-websites-for-sale' ),
			[ 'description' => esc_html__( 'Navigation by website categories and tags.', 'cw-websites-for-sale' ) ]
		);
	}

	public function widget( $args, $instance ): void {
		$title      = ! empty( $instance['title'] )      ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$show_cats  = ! isset( $instance['show_cats'] )  || $instance['show_cats'];
		$show_tags  = ! isset( $instance['show_tags'] )  || $instance['show_tags'];
		$cats_title = $instance['cats_title'] ?? esc_html__( 'Categories', 'cw-websites-for-sale' );
		$tags_title = $instance['tags_title'] ?? esc_html__( 'Tags', 'cw-websites-for-sale' );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		if ( $show_cats ) {
			$categories = get_terms( [
				'taxonomy'   => 'website_category',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			] );
			if ( $categories && ! is_wp_error( $categories ) ) {
				$archive_url = get_post_type_archive_link( 'cw_website' );
				$is_all      = ! is_tax( 'website_category' ) && ! is_tax( 'website_tag' );
				?>
				<div class="cw-wfs-widget-cats mb-4">
					<?php if ( $cats_title ) : ?>
					<h6 class="widget-title mb-3"><?php echo esc_html( $cats_title ); ?></h6>
					<?php endif; ?>
					<ul class="list-unstyled mb-0">
						<li class="mb-1">
							<a href="<?php echo esc_url( $archive_url ); ?>"
							   class="<?php echo $is_all ? 'fw-semibold' : ''; ?>">
								<?php esc_html_e( 'All', 'cw-websites-for-sale' ); ?>
							</a>
						</li>
						<?php foreach ( $categories as $cat ) :
							$is_active = is_tax( 'website_category', $cat->term_id );
							?>
						<li class="mb-1">
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
							   class="<?php echo $is_active ? 'fw-semibold' : ''; ?>">
								<?php echo esc_html( $cat->name ); ?>
								<span class="text-muted small">(<?php echo esc_html( (string) $cat->count ); ?>)</span>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
			}
		}

		if ( $show_tags ) {
			$tags = get_terms( [
				'taxonomy'   => 'website_tag',
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
			] );
			if ( $tags && ! is_wp_error( $tags ) ) {
				?>
				<div class="cw-wfs-widget-tags">
					<?php if ( $tags_title ) : ?>
					<h6 class="widget-title mb-3"><?php echo esc_html( $tags_title ); ?></h6>
					<?php endif; ?>
					<div class="d-flex flex-wrap gap-1">
						<?php foreach ( $tags as $tag ) :
							$is_active = is_tax( 'website_tag', $tag->term_id );
							?>
						<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"
						   class="badge text-decoration-none <?php echo $is_active ? 'bg-primary' : 'bg-soft-ash text-ash'; ?>">
							<?php echo esc_html( $tag->name ); ?>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			}
		}

		echo $args['after_widget'];
	}

	public function form( $instance ): void {
		$title      = $instance['title']      ?? '';
		$show_cats  = $instance['show_cats']  ?? 1;
		$show_tags  = $instance['show_tags']  ?? 1;
		$cats_title = $instance['cats_title'] ?? esc_html__( 'Categories', 'cw-websites-for-sale' );
		$tags_title = $instance['tags_title'] ?? esc_html__( 'Tags', 'cw-websites-for-sale' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget Title:', 'cw-websites-for-sale' ); ?></label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<input class="checkbox" type="checkbox" value="1"
				id="<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_cats' ) ); ?>"
				<?php checked( $show_cats ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?>"><?php esc_html_e( 'Show categories', 'cw-websites-for-sale' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cats_title' ) ); ?>"><?php esc_html_e( 'Categories section title:', 'cw-websites-for-sale' ); ?></label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'cats_title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'cats_title' ) ); ?>"
				value="<?php echo esc_attr( $cats_title ); ?>">
		</p>
		<p>
			<input class="checkbox" type="checkbox" value="1"
				id="<?php echo esc_attr( $this->get_field_id( 'show_tags' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_tags' ) ); ?>"
				<?php checked( $show_tags ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_tags' ) ); ?>"><?php esc_html_e( 'Show tags', 'cw-websites-for-sale' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tags_title' ) ); ?>"><?php esc_html_e( 'Tags section title:', 'cw-websites-for-sale' ); ?></label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'tags_title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'tags_title' ) ); ?>"
				value="<?php echo esc_attr( $tags_title ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ): array {
		return [
			'title'      => sanitize_text_field( $new_instance['title']      ?? '' ),
			'show_cats'  => ! empty( $new_instance['show_cats'] ) ? 1 : 0,
			'show_tags'  => ! empty( $new_instance['show_tags'] ) ? 1 : 0,
			'cats_title' => sanitize_text_field( $new_instance['cats_title'] ?? '' ),
			'tags_title' => sanitize_text_field( $new_instance['tags_title'] ?? '' ),
		];
	}
}
