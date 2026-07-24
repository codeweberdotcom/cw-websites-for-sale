<?php

namespace CW\WebsitesForSale\Admin;

class Metaboxes {

	public function init(): void {
		add_action( 'add_meta_boxes',        [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_cw_website',  [ $this, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'cw_wfs_details',
			esc_html__( 'Website Details', 'cw-websites-for-sale' ),
			[ $this, 'render' ],
			'cw_website',
			'normal',
			'high'
		);
	}

	public function render( \WP_Post $post ): void {
		wp_nonce_field( 'cw_wfs_save_' . $post->ID, 'cw_wfs_nonce' );

		$url         = get_post_meta( $post->ID, '_ws_url',           true );
		$screenshot  = (int) get_post_meta( $post->ID, '_ws_screenshot', true );
		$price       = get_post_meta( $post->ID, '_ws_price',         true );
		$cms         = get_post_meta( $post->ID, '_ws_cms',           true );
		$launch_time = get_post_meta( $post->ID, '_ws_launch_time',   true );
		$status      = get_post_meta( $post->ID, '_ws_status',        true ) ?: 'for_sale';

		$thumb_src = $screenshot ? wp_get_attachment_image_url( $screenshot, 'medium' ) : '';
		?>
		<table class="form-table" role="presentation">

			<tr>
				<th><label for="cw_wfs_url"><?php esc_html_e( 'Demo URL', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<?php
					$sites = function_exists( 'get_sites' ) ? get_sites( [ 'number' => 200 ] ) : [];
					if ( $sites ) :
					?>
					<select id="cw_wfs_url" name="cw_wfs_url" class="regular-text cw-wfs-select2" style="min-width:400px;">
						<option value=""><?php esc_html_e( '— select site —', 'cw-websites-for-sale' ); ?></option>
						<?php foreach ( $sites as $site ) :
							$home = trailingslashit( get_home_url( $site->blog_id ) );
							$name = get_blog_details( $site->blog_id )->blogname ?? $home;
						?>
						<option value="<?php echo esc_attr( $home ); ?>" <?php selected( trailingslashit( $url ), $home ); ?>>
							<?php echo esc_html( $name . ' — ' . $home ); ?>
						</option>
						<?php endforeach; ?>
					</select>
					<?php else : ?>
					<input type="url" id="cw_wfs_url" name="cw_wfs_url"
						value="<?php echo esc_attr( $url ); ?>"
						class="regular-text" placeholder="https://demo.example.com">
					<?php endif; ?>
					<p class="description"><?php esc_html_e( 'Shown in browser bar and iframe preview.', 'cw-websites-for-sale' ); ?></p>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Screenshot', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<input type="hidden" id="cw_wfs_screenshot" name="cw_wfs_screenshot" value="<?php echo esc_attr( $screenshot ?: '' ); ?>">
					<div id="cw_wfs_screenshot_preview" style="margin-bottom:8px;">
						<?php if ( $thumb_src ) : ?>
							<img src="<?php echo esc_url( $thumb_src ); ?>" style="max-width:320px;height:auto;display:block;">
						<?php endif; ?>
					</div>
					<button type="button" id="cw_wfs_screenshot_select" class="button">
						<?php esc_html_e( 'Select screenshot', 'cw-websites-for-sale' ); ?>
					</button>
					<?php if ( $screenshot ) : ?>
					<button type="button" id="cw_wfs_screenshot_remove" class="button" style="margin-left:4px;">
						<?php esc_html_e( 'Remove', 'cw-websites-for-sale' ); ?>
					</button>
					<?php endif; ?>
					<p class="description"><?php esc_html_e( 'Full-height page screenshot — used for hover-scroll effect.', 'cw-websites-for-sale' ); ?></p>
				</td>
			</tr>

			<tr>
				<th><label for="cw_wfs_price"><?php esc_html_e( 'Price', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<input type="text" id="cw_wfs_price" name="cw_wfs_price"
						value="<?php echo esc_attr( $price ); ?>"
						class="regular-text" placeholder="<?php esc_attr_e( 'e.g. 50 000 ₽', 'cw-websites-for-sale' ); ?>">
				</td>
			</tr>

			<tr>
				<th><label for="cw_wfs_cms"><?php esc_html_e( 'CMS / Platform', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<input type="text" id="cw_wfs_cms" name="cw_wfs_cms"
						value="<?php echo esc_attr( $cms ); ?>"
						class="regular-text" placeholder="<?php esc_attr_e( 'WordPress, Laravel, OpenCart…', 'cw-websites-for-sale' ); ?>">
				</td>
			</tr>

			<tr>
				<th><label for="cw_wfs_launch_time"><?php esc_html_e( 'Launch Time', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<input type="text" id="cw_wfs_launch_time" name="cw_wfs_launch_time"
						value="<?php echo esc_attr( $launch_time ); ?>"
						class="regular-text" placeholder="<?php esc_attr_e( '1–2 days', 'cw-websites-for-sale' ); ?>">
					<p class="description"><?php esc_html_e( 'Time to deploy on client\'s domain.', 'cw-websites-for-sale' ); ?></p>
				</td>
			</tr>

			<tr>
				<th><label for="cw_wfs_status"><?php esc_html_e( 'Status', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<select id="cw_wfs_status" name="cw_wfs_status">
						<option value="for_sale" <?php selected( $status, 'for_sale' ); ?>><?php esc_html_e( 'For Sale', 'cw-websites-for-sale' ); ?></option>
						<option value="reserved" <?php selected( $status, 'reserved' ); ?>><?php esc_html_e( 'Reserved', 'cw-websites-for-sale' ); ?></option>
						<option value="sold"     <?php selected( $status, 'sold' ); ?>><?php esc_html_e( 'Sold', 'cw-websites-for-sale' ); ?></option>
					</select>
				</td>
			</tr>

		</table>
		<?php
	}

	public function save( int $post_id ): void {
		if ( ! isset( $_POST['cw_wfs_nonce'] )
			|| ! wp_verify_nonce( $_POST['cw_wfs_nonce'], 'cw_wfs_save_' . $post_id )
		) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$allowed_statuses = [ 'for_sale', 'reserved', 'sold' ];

		$fields = [
			'_ws_url'         => esc_url_raw( wp_unslash( $_POST['cw_wfs_url'] ?? '' ) ),
			'_ws_screenshot'  => absint( $_POST['cw_wfs_screenshot'] ?? 0 ) ?: '',
			'_ws_price'       => sanitize_text_field( wp_unslash( $_POST['cw_wfs_price'] ?? '' ) ),
			'_ws_cms'         => sanitize_text_field( wp_unslash( $_POST['cw_wfs_cms'] ?? '' ) ),
			'_ws_launch_time' => sanitize_text_field( wp_unslash( $_POST['cw_wfs_launch_time'] ?? '' ) ),
			'_ws_status'      => in_array( $_POST['cw_wfs_status'] ?? '', $allowed_statuses, true )
									? $_POST['cw_wfs_status']
									: 'for_sale',
		];

		foreach ( $fields as $key => $value ) {
			if ( $value !== '' ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	public function enqueue_scripts( string $hook ): void {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'cw_website' ) {
			return;
		}
		wp_enqueue_media();
		// Select2 — bundled by WooCommerce; fallback to CDN if not registered
		if ( wp_script_is( 'select2', 'registered' ) ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
		} else {
			wp_enqueue_script( 'cw-wfs-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', [ 'jquery' ], '4.1.0', true );
			wp_enqueue_style( 'cw-wfs-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0' );
		}
		wp_enqueue_script(
			'cw-wfs-admin',
			CW_WFS_URL . 'assets/js/admin.js',
			[ 'jquery', 'media-upload' ],
			CW_WFS_VERSION,
			true
		);
	}
}
