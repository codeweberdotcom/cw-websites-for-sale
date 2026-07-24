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
		add_settings_section( 'cw_wfs_layout',  esc_html__( 'Archive Layout', 'cw-websites-for-sale' ),   '__return_null', 'cw_wfs_templates' );

		add_settings_field( 'archive_template', '', [ $this, 'render_archive_field' ], 'cw_wfs_templates', 'cw_wfs_archive' );
		add_settings_field( 'single_template',  '', [ $this, 'render_single_field' ],  'cw_wfs_templates', 'cw_wfs_single' );
		add_settings_field( 'archive_columns',  esc_html__( 'Columns', 'cw-websites-for-sale' ), [ $this, 'render_columns_field' ], 'cw_wfs_templates', 'cw_wfs_layout' );
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
			'archive_columns' => in_array( $input['archive_columns'] ?? '', [ '2', '3', '4' ] )
				? $input['archive_columns']
				: '3',
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

	public function render_columns_field(): void {
		$current = cw_wfs_setting( 'archive_columns', '3' );
		$option  = self::OPTION;
		$options = [
			'2' => esc_html__( '2 columns', 'cw-websites-for-sale' ),
			'3' => esc_html__( '3 columns', 'cw-websites-for-sale' ),
			'4' => esc_html__( '4 columns', 'cw-websites-for-sale' ),
		];
		echo '<fieldset>';
		foreach ( $options as $val => $label ) {
			echo '<label style="margin-right:20px;">';
			echo '<input type="radio" name="' . esc_attr( "{$option}[archive_columns]" ) . '" value="' . esc_attr( $val ) . '"' . checked( $val, $current, false ) . '> ';
			echo esc_html( $label );
			echo '</label>';
		}
		echo '</fieldset>';
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
			'1a' => [
				'title'       => __( 'Browser Frame', 'cw-websites-for-sale' ),
				'description' => __( 'Light card with browser bar, colored category badge, price + term footer, indigo buttons.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_1a(),
			],
			'1c' => [
				'title'       => __( 'Price Pill + Dark Footer', 'cw-websites-for-sale' ),
				'description' => __( 'White card, price pill on screenshot, dot+category row, two-column dark footer buttons.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_1c(),
			],
			'2' => [
				'title'       => __( 'Scroll Rows', 'cw-websites-for-sale' ),
				'description' => __( 'Alternating rows — large screenshot left/right with details.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_rows(),
			],
			'2a' => [
				'title'       => __( 'Price Pill + Icon Button', 'cw-websites-for-sale' ),
				'description' => __( 'White card, price pill on screenshot, dot+category row, wide indigo button + square icon button.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_2a(),
			],
			'3' => [
				'title'       => __( 'Overlay Cards', 'cw-websites-for-sale' ),
				'description' => __( '3-column overlay grid — title fixed at bottom, description slides in on hover.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_overlay(),
			],
			'3a' => [
				'title'       => __( 'Dark — Violet Accent', 'cw-websites-for-sale' ),
				'description' => __( 'Dark page (#0b1120), dark cards, dot+category row, lavender price, violet button + square icon.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_3a(),
			],
			'3b' => [
				'title'       => __( 'Dark — White Button', 'cw-websites-for-sale' ),
				'description' => __( 'Dark page, inset rounded screenshot with category badge, white primary button + outline preview.', 'cw-websites-for-sale' ),
				'preview'     => $this->svg_preview_3b(),
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

	private function svg_preview_1a(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="43" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="82" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="4"  y="4"  width="34" height="6"  rx="2" fill="#f5f5f5"/>
			<rect x="43" y="4"  width="34" height="6"  rx="2" fill="#f5f5f5"/>
			<rect x="82" y="4"  width="34" height="6"  rx="2" fill="#f5f5f5"/>
			<circle cx="8"  cy="7" r="2" fill="#f87171"/>
			<circle cx="13" cy="7" r="2" fill="#fbbf24"/>
			<circle cx="18" cy="7" r="2" fill="#34d399"/>
			<circle cx="47" cy="7" r="2" fill="#f87171"/>
			<circle cx="52" cy="7" r="2" fill="#fbbf24"/>
			<circle cx="57" cy="7" r="2" fill="#34d399"/>
			<circle cx="86" cy="7" r="2" fill="#f87171"/>
			<circle cx="91" cy="7" r="2" fill="#fbbf24"/>
			<circle cx="96" cy="7" r="2" fill="#34d399"/>
			<rect x="4"  y="10" width="34" height="22" fill="#c3c4c7"/>
			<rect x="43" y="10" width="34" height="22" fill="#c3c4c7"/>
			<rect x="82" y="10" width="34" height="22" fill="#c3c4c7"/>
			<rect x="5"  y="34" width="14" height="4"  rx="1" fill="#818cf8"/>
			<rect x="44" y="34" width="14" height="4"  rx="1" fill="#4ade80"/>
			<rect x="83" y="34" width="14" height="4"  rx="1" fill="#f472b6"/>
			<rect x="5"  y="40" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="44" y="40" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="83" y="40" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="5"  y="47" width="20" height="5"  rx="1" fill="#4f46e5"/>
			<rect x="44" y="47" width="20" height="5"  rx="1" fill="#4f46e5"/>
			<rect x="83" y="47" width="20" height="5"  rx="1" fill="#4f46e5"/>
		</svg>';
	}

	private function svg_preview_1c(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="43" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="82" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="4"  y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="43" y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="82" y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="23" y="6"  width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="62" y="6"  width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="101" y="6" width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="5"  y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="44" y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="83" y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="5"  y="38" width="20" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="44" y="38" width="20" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="83" y="38" width="20" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="4"  y="45" width="17" height="9"  fill="#0f172a"/>
			<rect x="21" y="45" width="17" height="9"  fill="#1e293b"/>
			<rect x="43" y="45" width="17" height="9"  fill="#0f172a"/>
			<rect x="60" y="45" width="17" height="9"  fill="#1e293b"/>
			<rect x="82" y="45" width="17" height="9"  fill="#0f172a"/>
			<rect x="99" y="45" width="17" height="9"  fill="#1e293b"/>
		</svg>';
	}

	private function svg_preview_2a(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="4"  y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="43" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="82" y="4"  width="34" height="50" rx="2" fill="#e0e0e0"/>
			<rect x="4"  y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="43" y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="82" y="4"  width="34" height="26" fill="#c3c4c7"/>
			<rect x="23" y="6"  width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="62" y="6"  width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="101" y="6" width="14" height="5" rx="2" fill="#fff" opacity=".9"/>
			<rect x="5"  y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="44" y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="83" y="32" width="30" height="4"  rx="1" fill="#646970"/>
			<rect x="5"  y="38" width="30" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="44" y="38" width="30" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="83" y="38" width="30" height="4"  rx="1" fill="#3f4a52"/>
			<rect x="5"  y="44" width="22" height="7"  rx="1" fill="#4f46e5"/>
			<rect x="44" y="44" width="22" height="7"  rx="1" fill="#4f46e5"/>
			<rect x="83" y="44" width="22" height="7"  rx="1" fill="#4f46e5"/>
			<rect x="29" y="44" width="7"  height="7"  rx="1" fill="#e0e0e0"/>
			<rect x="68" y="44" width="7"  height="7"  rx="1" fill="#e0e0e0"/>
			<rect x="107" y="44" width="7" height="7"  rx="1" fill="#e0e0e0"/>
		</svg>';
	}

	private function svg_preview_3a(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="0" y="0" width="120" height="80" fill="#0b1120"/>
			<rect x="4"  y="4"  width="34" height="50" rx="3" fill="#1a2234" stroke="#26324a" stroke-width="1"/>
			<rect x="43" y="4"  width="34" height="50" rx="3" fill="#1a2234" stroke="#26324a" stroke-width="1"/>
			<rect x="82" y="4"  width="34" height="50" rx="3" fill="#1a2234" stroke="#26324a" stroke-width="1"/>
			<rect x="4"  y="4"  width="34" height="22" fill="#26324a"/>
			<rect x="43" y="4"  width="34" height="22" fill="#26324a"/>
			<rect x="82" y="4"  width="34" height="22" fill="#26324a"/>
			<rect x="5"  y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="44" y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="83" y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="5"  y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="44" y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="83" y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="5"  y="40" width="22" height="7"  rx="1" fill="#818cf8"/>
			<rect x="44" y="40" width="22" height="7"  rx="1" fill="#818cf8"/>
			<rect x="83" y="40" width="22" height="7"  rx="1" fill="#818cf8"/>
			<rect x="29" y="40" width="7"  height="7"  rx="1" fill="#26324a"/>
			<rect x="68" y="40" width="7"  height="7"  rx="1" fill="#26324a"/>
			<rect x="107" y="40" width="7" height="7"  rx="1" fill="#26324a"/>
		</svg>';
	}

	private function svg_preview_3b(): string {
		return '<svg width="120" height="80" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
			<rect x="0" y="0" width="120" height="80" fill="#0b1120"/>
			<rect x="4"  y="4"  width="34" height="50" rx="3" fill="#171f30" stroke="#26324a" stroke-width="1"/>
			<rect x="43" y="4"  width="34" height="50" rx="3" fill="#171f30" stroke="#26324a" stroke-width="1"/>
			<rect x="82" y="4"  width="34" height="50" rx="3" fill="#171f30" stroke="#26324a" stroke-width="1"/>
			<rect x="6"  y="6"  width="30" height="20" rx="2" fill="#26324a"/>
			<rect x="45" y="6"  width="30" height="20" rx="2" fill="#26324a"/>
			<rect x="84" y="6"  width="30" height="20" rx="2" fill="#26324a"/>
			<rect x="7"  y="7"  width="10" height="4"  rx="1" fill="#dbeafe" opacity=".9"/>
			<rect x="46" y="7"  width="10" height="4"  rx="1" fill="#dcfce7" opacity=".9"/>
			<rect x="85" y="7"  width="10" height="4"  rx="1" fill="#fce7f3" opacity=".9"/>
			<rect x="5"  y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="44" y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="83" y="28" width="30" height="4"  rx="1" fill="#f1f5f9" opacity=".8"/>
			<rect x="5"  y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="44" y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="83" y="34" width="18" height="4"  rx="1" fill="#a5b4fc"/>
			<rect x="5"  y="40" width="22" height="7"  rx="1" fill="#ffffff"/>
			<rect x="44" y="40" width="22" height="7"  rx="1" fill="#ffffff"/>
			<rect x="83" y="40" width="22" height="7"  rx="1" fill="#ffffff"/>
			<rect x="29" y="40" width="7"  height="7"  rx="1" fill="#1e293b"/>
			<rect x="68" y="40" width="7"  height="7"  rx="1" fill="#1e293b"/>
			<rect x="107" y="40" width="7" height="7"  rx="1" fill="#1e293b"/>
		</svg>';
	}
}
