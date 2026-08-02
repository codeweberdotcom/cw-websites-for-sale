<?php

namespace CW\WebsitesForSale\Admin;

class ModuleMetaboxes {

	private const NONCE_ACTION = 'cw_wfs_module_save_%d';
	private const NONCE_FIELD  = 'cw_wfs_module_nonce';
	private const META_ICON    = '_module_icon';
	private const META_COLOR   = '_module_color';

	// ─── Theme colors from _theme-colors.scss ────────────────────────────────
	private const COLORS = [
		'#5eb9f0' => 'Sky',
		'#3f78e0' => 'Blue',
		'#605dba' => 'Grape',
		'#a07cc5' => 'Violet',
		'#d16b86' => 'Pink',
		'#e2626b' => 'Red',
		'#f78b77' => 'Orange',
		'#fab758' => 'Yellow',
		'#45c4a0' => 'Green',
		'#7cb798' => 'Leaf',
		'#54a8c7' => 'Aqua',
		'#343f52' => 'Navy',
		'#9499a3' => 'Ash',
	];

	public function init(): void {
		add_action( 'add_meta_boxes',          [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_cw_module',     [ $this, 'save' ] );
		add_action( 'admin_enqueue_scripts',   [ $this, 'enqueue_scripts' ] );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'cw_wfs_module_meta',
			esc_html__( 'Module Settings', 'cw-websites-for-sale' ),
			[ $this, 'render' ],
			'cw_module',
			'normal',
			'high'
		);
	}

	public function render( \WP_Post $post ): void {
		wp_nonce_field( sprintf( self::NONCE_ACTION, $post->ID ), self::NONCE_FIELD );

		$icon  = get_post_meta( $post->ID, self::META_ICON,  true ) ?: 'star';
		$color = get_post_meta( $post->ID, self::META_COLOR, true ) ?: '#605dba';

		$icons = $this->get_icon_list();
		?>
		<table class="form-table" role="presentation">

			<!-- ── Icon ──────────────────────────────────────────────────────── -->
			<tr>
				<th><label><?php esc_html_e( 'Icon', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
						<span style="font-size:28px;line-height:1;width:34px;text-align:center;">
							<i class="uil uil-<?php echo esc_attr( $icon ); ?>" id="cw-module-icon-preview"></i>
						</span>
						<code id="cw-module-icon-label" style="font-size:13px;color:#50575e;"><?php echo esc_html( $icon ); ?></code>
					</div>

					<input type="text"
					       id="cw-module-icon-search"
					       placeholder="<?php esc_attr_e( 'Search icons…', 'cw-websites-for-sale' ); ?>"
					       style="width:100%;max-width:280px;margin-bottom:8px;"
					       class="regular-text">

					<div id="cw-module-icon-grid"
					     style="display:flex;flex-wrap:wrap;gap:2px;max-height:224px;overflow-y:auto;
					            border:1px solid #c3c4c7;padding:6px;border-radius:4px;background:#f6f7f7;">
						<?php foreach ( $icons as $name => $label ) :
							$selected = ( $name === $icon );
						?>
						<button type="button"
						        class="cw-module-icon-btn<?php echo $selected ? ' cw-icon-selected' : ''; ?>"
						        data-icon="<?php echo esc_attr( $name ); ?>"
						        title="<?php echo esc_attr( $name ); ?>"
						        style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;
						               border:1px solid <?php echo $selected ? '#3858e9' : 'transparent'; ?>;
						               border-radius:4px;cursor:pointer;
						               background:<?php echo $selected ? '#e8edfb' : 'transparent'; ?>;
						               font-size:17px;padding:0;">
							<i class="uil uil-<?php echo esc_attr( $name ); ?>"></i>
						</button>
						<?php endforeach; ?>
					</div>

					<input type="hidden"
					       name="<?php echo esc_attr( self::META_ICON ); ?>"
					       id="cw-module-icon-input"
					       value="<?php echo esc_attr( $icon ); ?>">

					<p class="description" style="margin-top:6px;"><?php esc_html_e( 'Unicons icon used in cards and blocks.', 'cw-websites-for-sale' ); ?></p>
				</td>
			</tr>

			<!-- ── Color ─────────────────────────────────────────────────────── -->
			<tr>
				<th><label><?php esc_html_e( 'Color', 'cw-websites-for-sale' ); ?></label></th>
				<td>
					<div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;" id="cw-module-color-grid">
						<?php foreach ( self::COLORS as $hex => $label ) :
							$selected = ( $hex === $color );
						?>
						<button type="button"
						        class="cw-module-color-btn"
						        data-color="<?php echo esc_attr( $hex ); ?>"
						        title="<?php echo esc_attr( $label ); ?>"
						        style="width:30px;height:30px;border-radius:6px;cursor:pointer;background:<?php echo esc_attr( $hex ); ?>;
						               border:2px solid <?php echo $selected ? '#fff' : $hex; ?>;
						               box-shadow:<?php echo $selected ? '0 0 0 2px #3858e9' : 'none'; ?>;
						               padding:0;"></button>
						<?php endforeach; ?>
					</div>

					<input type="hidden"
					       name="<?php echo esc_attr( self::META_COLOR ); ?>"
					       id="cw-module-color-input"
					       value="<?php echo esc_attr( $color ); ?>">

					<div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
						<span id="cw-module-color-swatch"
						      style="display:inline-block;width:20px;height:20px;border-radius:4px;background:<?php echo esc_attr( $color ); ?>;border:1px solid #c3c4c7;"></span>
						<code id="cw-module-color-label" style="font-size:13px;color:#50575e;"><?php echo esc_html( $color ); ?></code>
					</div>

					<p class="description" style="margin-top:6px;"><?php esc_html_e( 'Accent color for this module (hex).', 'cw-websites-for-sale' ); ?></p>
				</td>
			</tr>

		</table>

		<script>
		(function () {
			// ── Icon picker ──────────────────────────────────────────────────
			var iconInput   = document.getElementById('cw-module-icon-input');
			var iconPreview = document.getElementById('cw-module-icon-preview');
			var iconLabel   = document.getElementById('cw-module-icon-label');
			var iconSearch  = document.getElementById('cw-module-icon-search');
			var iconGrid    = document.getElementById('cw-module-icon-grid');
			var iconBtns    = Array.prototype.slice.call( iconGrid.querySelectorAll('.cw-module-icon-btn') );

			function selectIcon(btn) {
				iconBtns.forEach(function(b) {
					b.style.border = '1px solid transparent';
					b.style.background = 'transparent';
					b.classList.remove('cw-icon-selected');
				});
				btn.style.border = '1px solid #3858e9';
				btn.style.background = '#e8edfb';
				btn.classList.add('cw-icon-selected');
				var name = btn.getAttribute('data-icon');
				iconInput.value = name;
				iconPreview.className = 'uil uil-' + name;
				iconLabel.textContent = name;
			}

			iconBtns.forEach(function(btn) {
				btn.addEventListener('click', function() { selectIcon(btn); });
			});

			iconSearch.addEventListener('input', function() {
				var q = this.value.toLowerCase().trim();
				iconBtns.forEach(function(btn) {
					btn.style.display = (!q || btn.getAttribute('data-icon').indexOf(q) !== -1) ? '' : 'none';
				});
			});

			// ── Color picker ─────────────────────────────────────────────────
			var colorInput  = document.getElementById('cw-module-color-input');
			var colorSwatch = document.getElementById('cw-module-color-swatch');
			var colorLabel  = document.getElementById('cw-module-color-label');
			var colorBtns   = Array.prototype.slice.call( document.querySelectorAll('.cw-module-color-btn') );

			function selectColor(btn) {
				colorBtns.forEach(function(b) {
					var c = b.getAttribute('data-color');
					b.style.border = '2px solid ' + c;
					b.style.boxShadow = 'none';
				});
				var hex = btn.getAttribute('data-color');
				btn.style.border = '2px solid #fff';
				btn.style.boxShadow = '0 0 0 2px #3858e9';
				colorInput.value = hex;
				colorSwatch.style.background = hex;
				colorLabel.textContent = hex;
			}

			colorBtns.forEach(function(btn) {
				btn.addEventListener('click', function() { selectColor(btn); });
			});
		})();
		</script>
		<?php
	}

	public function save( int $post_id ): void {
		if ( ! isset( $_POST[ self::NONCE_FIELD ] )
			|| ! wp_verify_nonce( $_POST[ self::NONCE_FIELD ], sprintf( self::NONCE_ACTION, $post_id ) )
		) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$icon  = sanitize_key( wp_unslash( $_POST[ self::META_ICON ]  ?? '' ) );
		$color = sanitize_hex_color( wp_unslash( $_POST[ self::META_COLOR ] ?? '' ) );

		if ( $icon ) {
			update_post_meta( $post_id, self::META_ICON, $icon );
		} else {
			delete_post_meta( $post_id, self::META_ICON );
		}

		if ( $color ) {
			update_post_meta( $post_id, self::META_COLOR, $color );
		} else {
			delete_post_meta( $post_id, self::META_COLOR );
		}
	}

	public function enqueue_scripts( string $hook ): void {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'cw_module' ) {
			return;
		}

		$css = get_transient( 'cw_wfs_admin_unicons_css_v1' );

		if ( false === $css ) {
			$font_url = get_theme_file_uri( 'dist/assets/fonts/unicons/' );
			$css      = "@font-face{font-family:'Unicons';src:url('{$font_url}Unicons.woff2') format('woff2'),url('{$font_url}Unicons.woff') format('woff');font-weight:normal;font-style:normal;font-display:block}";
			$css     .= "[class*='uil-']:before,.uil:before{font-family:'Unicons'!important;speak:none;font-style:normal;font-weight:normal;font-variant:normal;text-transform:none;line-height:1;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}";

			$json_file = get_theme_file_path( 'dist/assets/fonts/unicons/selection.json' );
			if ( file_exists( $json_file ) ) {
				$data = json_decode( file_get_contents( $json_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				foreach ( $data['icons'] ?? [] as $item ) {
					$name = $item['properties']['name'] ?? '';
					$code = $item['properties']['code'] ?? 0;
					if ( $name && $code ) {
						$css .= ".uil-{$name}:before{content:'\\" . sprintf( '%x', $code ) . "'}";
					}
				}
			}

			set_transient( 'cw_wfs_admin_unicons_css_v1', $css, WEEK_IN_SECONDS );
		}

		wp_register_style( 'cw-wfs-admin-unicons', false, [], null );
		wp_enqueue_style( 'cw-wfs-admin-unicons' );
		wp_add_inline_style( 'cw-wfs-admin-unicons', $css );
	}

	// ─────────────────────────────────────────────────────────────────────────

	private function get_icon_list(): array {
		if ( function_exists( 'codeweber_get_unicons_icons' ) ) {
			return codeweber_get_unicons_icons();
		}

		$cached = get_transient( 'cw_wfs_module_icon_list_v1' );
		if ( false !== $cached ) {
			return $cached;
		}

		$icons = [];
		$file  = WP_PLUGIN_DIR . '/codeweber-gutenberg-blocks/src/utilities/font_icon.js';

		if ( file_exists( $file ) ) {
			$content = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( preg_match( '/fontIcons\s*=\s*\[(.*?)\];/s', $content, $m ) ) {
				preg_match_all( "/value:\s*'uil-([^']+)'[^}]*label:\s*'([^']+)'/s", $m[1], $matches, PREG_SET_ORDER );
				foreach ( $matches as $match ) {
					$icons[ $match[1] ] = ucwords( str_replace( '-', ' ', $match[2] ) );
				}
			}
		}

		asort( $icons );
		set_transient( 'cw_wfs_module_icon_list_v1', $icons, WEEK_IN_SECONDS );
		return $icons;
	}
}
