<?php

namespace CW\WebsitesForSale\Admin;

class SettingsPage {

	const OPTION = 'cw_wfs_settings';

	public function init(): void {
		add_action( 'admin_menu',    [ $this, 'add_menu' ] );
		add_action( 'admin_init',    [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	public function add_menu(): void {
		add_submenu_page(
			'edit.php?post_type=cw_website',
			esc_html__( 'Templates', 'cw-websites-for-sale' ),
			esc_html__( 'Templates', 'cw-websites-for-sale' ),
			'manage_options',
			'cw-wfs-templates',
			[ $this, 'render_page' ]
		);
	}

	public function register_settings(): void {
		register_setting( 'cw_wfs_templates', self::OPTION, [
			'sanitize_callback' => [ $this, 'sanitize' ],
		] );

		add_settings_section( 'cw_wfs_archive', esc_html__( 'Archive Template', 'cw-websites-for-sale' ), '__return_null', 'cw_wfs_templates' );
		add_settings_section( 'cw_wfs_single',  esc_html__( 'Single Template', 'cw-websites-for-sale' ),  '__return_null', 'cw_wfs_templates' );

		add_settings_field( 'archive_template', '', [ $this, 'render_archive_field' ], 'cw_wfs_templates', 'cw_wfs_archive' );
		add_settings_field( 'single_template',  '', [ $this, 'render_single_field' ],  'cw_wfs_templates', 'cw_wfs_single' );
	}

	public function sanitize( $input ): array {
		$archive_templates = array_keys( $this->get_archive_templates() );
		$single_templates  = array_keys( $this->get_single_templates() );
		return [
			'archive_template' => in_array( $input['archive_template'] ?? '', $archive_templates )
				? $input['archive_template']
				: '1',
			'single_template' => in_array( $input['single_template'] ?? '', $single_templates )
				? $input['single_template']
				: '1',
		];
	}

	public function enqueue_styles( string $hook ): void {
		if ( $hook !== 'cw_website_page_cw-wfs-templates' ) {
			return;
		}
		wp_add_inline_style( 'wp-admin', '
			.cw-wfs-templates-grid { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 8px; }
			.cw-wfs-tpl-card { border: 2px solid #dcdcde; border-radius: 6px; padding: 0; width: 260px; cursor: pointer; transition: border-color .15s; background: #fff; }
			.cw-wfs-tpl-card:hover { border-color: #2271b1; }
			.cw-wfs-tpl-card.selected { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
			.cw-wfs-tpl-card input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
			.cw-wfs-tpl-preview { height: 160px; background: #f0f0f1; border-radius: 4px 4px 0 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
			.cw-wfs-tpl-preview img { width: 100%; height: 100%; object-fit: cover; }
			.cw-wfs-tpl-preview svg { opacity: .3; }
			.cw-wfs-tpl-info { padding: 14px 16px; }
			.cw-wfs-tpl-info h4 { margin: 0 0 4px; font-size: 13px; font-weight: 600; }
			.cw-wfs-tpl-info p { margin: 0; font-size: 12px; color: #646970; }
			.cw-wfs-tpl-badge { display: inline-block; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 10px; background: #2271b1; color: #fff; margin-bottom: 6px; }
		' );
		wp_add_inline_script( 'jquery', '
			jQuery(function($){
				$(".cw-wfs-tpl-card").on("click", function(){
					var grp = $(this).data("group");
					$(".cw-wfs-tpl-card[data-group=" + grp + "]").removeClass("selected");
					$(this).addClass("selected");
					$(this).find("input[type=radio]").prop("checked", true);
				});
			});
		' );
	}

	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Websites For Sale — Templates', 'cw-websites-for-sale' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'cw_wfs_templates' );
				do_settings_sections( 'cw_wfs_templates' );
				submit_button( esc_html__( 'Save Settings', 'cw-websites-for-sale' ) );
				?>
			</form>
		</div>
		<?php
	}

	public function render_archive_field(): void {
		$current   = cw_wfs_setting( 'archive_template', '1' );
		$templates = $this->get_archive_templates();
		$this->render_template_picker( 'archive_template', $current, $templates );
	}

	public function render_single_field(): void {
		$current   = cw_wfs_setting( 'single_template', '1' );
		$templates = $this->get_single_templates();
		$this->render_template_picker( 'single_template', $current, $templates );
	}

	private function render_template_picker( string $field, string $current, array $templates ): void {
		$option = self::OPTION;
		echo '<div class="cw-wfs-templates-grid">';
		foreach ( $templates as $key => $tpl ) {
			$selected = ( $key === $current ) ? ' selected' : '';
			echo '<label class="cw-wfs-tpl-card' . $selected . '" data-group="' . esc_attr( $field ) . '">';
			echo '<input type="radio" name="' . esc_attr( "{$option}[{$field}]" ) . '" value="' . esc_attr( $key ) . '"' . checked( $key, $current, false ) . '>';
			echo '<div class="cw-wfs-tpl-preview">' . $tpl['preview'] . '</div>';
			echo '<div class="cw-wfs-tpl-info">';
			echo '<span class="cw-wfs-tpl-badge">' . esc_html__( 'Template', 'cw-websites-for-sale' ) . ' ' . esc_html( $key ) . '</span>';
			echo '<h4>' . esc_html( $tpl['title'] ) . '</h4>';
			echo '<p>' . esc_html( $tpl['description'] ) . '</p>';
			echo '</div>';
			echo '</label>';
		}
		echo '</div>';
	}

	private function get_archive_templates(): array {
		return [
			'1' => [
				'title'       => __( 'IT Cards', 'cw-websites-for-sale' ),
				'description' => __( '3-column grid, browser bar, hover-scroll screenshot.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_grid(),
			],
			'2' => [
				'title'       => __( 'Scroll Rows', 'cw-websites-for-sale' ),
				'description' => __( 'Alternating rows — large screenshot left/right with details.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_rows(),
			],
			'3' => [
				'title'       => __( 'Overlay Cards', 'cw-websites-for-sale' ),
				'description' => __( '3-column overlay grid — title fixed at bottom, description slides in on hover.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_overlay(),
			],
		];
	}

	private function get_single_templates(): array {
		return [
			'1' => [
				'title'       => __( 'Sidebar', 'cw-websites-for-sale' ),
				'description' => __( 'Screenshot on the left (8 cols), sticky info card on the right (4 cols).', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_sidebar(),
			],
			'2' => [
				'title'       => __( 'Wide', 'cw-websites-for-sale' ),
				'description' => __( 'Full-width screenshot, price and details below in a two-column row.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_wide(),
			],
		];
	}

	private function svg_preview_grid(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="34" height="40" rx="2" fill="#c3c4c7"/>
			<rect x="43" y="4"  width="34" height="40" rx="2" fill="#c3c4c7"/>
			<rect x="82" y="4"  width="34" height="40" rx="2" fill="#c3c4c7"/>
			<rect x="4"  y="50" width="34" height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="43" y="50" width="34" height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="82" y="50" width="34" height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="4"  y="60" width="22" height="4"  rx="1" fill="#ebebec"/>
			<rect x="43" y="60" width="22" height="4"  rx="1" fill="#ebebec"/>
			<rect x="82" y="60" width="22" height="4"  rx="1" fill="#ebebec"/>
		</svg>';
	}

	private function svg_preview_rows(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="68" height="30" rx="2" fill="#c3c4c7"/>
			<rect x="78" y="4"  width="38" height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="78" y="14" width="30" height="4"  rx="1" fill="#ebebec"/>
			<rect x="78" y="22" width="34" height="4"  rx="1" fill="#ebebec"/>
			<rect x="38" y="46" width="68" height="30" rx="2" fill="#c3c4c7"/>
			<rect x="4"  y="46" width="28" height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="4"  y="56" width="22" height="4"  rx="1" fill="#ebebec"/>
			<rect x="4"  y="64" width="26" height="4"  rx="1" fill="#ebebec"/>
		</svg>';
	}

	private function svg_preview_sidebar(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="74" height="72" rx="2" fill="#c3c4c7"/>
			<rect x="84" y="4"  width="32" height="72" rx="2" fill="#e0e0e0"/>
			<rect x="88" y="10" width="24" height="4"  rx="1" fill="#c3c4c7"/>
			<rect x="88" y="18" width="18" height="4"  rx="1" fill="#c3c4c7"/>
			<rect x="88" y="26" width="22" height="4"  rx="1" fill="#c3c4c7"/>
			<rect x="88" y="46" width="24" height="8"  rx="2" fill="#2271b1"/>
		</svg>';
	}

	private function svg_preview_wide(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="112" height="44" rx="2" fill="#c3c4c7"/>
			<rect x="4"  y="54" width="52"  height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="64" y="54" width="52"  height="6"  rx="1" fill="#e0e0e0"/>
			<rect x="4"  y="64" width="38"  height="4"  rx="1" fill="#ebebec"/>
			<rect x="64" y="64" width="44"  height="4"  rx="1" fill="#ebebec"/>
		</svg>';
	}

	private function svg_preview_overlay(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="34" height="72" rx="2" fill="#c3c4c7"/>
			<rect x="43" y="4"  width="34" height="72" rx="2" fill="#c3c4c7"/>
			<rect x="82" y="4"  width="34" height="72" rx="2" fill="#c3c4c7"/>
			<rect x="4"  y="4"  width="34" height="72" rx="2" fill="url(#ov)"/>
			<rect x="43" y="4"  width="34" height="72" rx="2" fill="url(#ov)"/>
			<rect x="82" y="4"  width="34" height="72" rx="2" fill="url(#ov)"/>
			<rect x="6"  y="62" width="28" height="5"  rx="1" fill="#fff" opacity=".85"/>
			<rect x="45" y="62" width="22" height="5"  rx="1" fill="#fff" opacity=".85"/>
			<rect x="84" y="62" width="26" height="5"  rx="1" fill="#fff" opacity=".85"/>
			<defs>
				<linearGradient id="ov" x1="0" y1="0" x2="0" y2="1">
					<stop offset="40%" stop-color="#000" stop-opacity="0"/>
					<stop offset="100%" stop-color="#000" stop-opacity=".55"/>
				</linearGradient>
			</defs>
		</svg>';
	}
}
