<?php

namespace CW\WebsitesForSale;

class Plugin {

	public function init(): void {
		add_action( 'init',               [ $this, 'register_cpt' ] );
		add_action( 'init',               [ $this, 'register_taxonomies' ] );
		add_action( 'init',               [ $this, 'register_cpt_module' ] );
		add_action( 'init',               [ $this, 'register_taxonomy_module_category' ] );
		add_action( 'widgets_init',       [ $this, 'register_widget' ] );
		add_filter( 'template_include',   [ $this, 'template_include' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'pre_get_posts',      [ $this, 'set_archive_per_page' ] );

		( new Admin\Metaboxes() )->init();
		( new Admin\SettingsPage() )->init();
		( new Admin\ModuleMetaboxes() )->init();

		$this->register_module_card_templates();
		$this->register_website_card_templates();
	}

	private function register_website_card_templates(): void {
		add_filter( 'codeweber_post_card_templates_registry', function ( $registry ) {
			$registry['cw_website'] = [
				'dir'       => 'cw_website',
				'templates' => [
					'card' => [
						'label'       => __( 'Card', 'cw-websites-for-sale' ),
						'description' => __( 'Browser-bar card: screenshot, category, price and CMS badge', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
					'card-1a' => [
						'label'       => __( 'Card 1a', 'cw-websites-for-sale' ),
						'description' => __( 'Browser-bar card with colored category badge and Details / Preview buttons', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
					'card-1c' => [
						'label'       => __( 'Card 1c', 'cw-websites-for-sale' ),
						'description' => __( 'Price pill on screenshot, dot category, dark footer with Details / Preview', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
					'card-2a' => [
						'label'       => __( 'Card 2a', 'cw-websites-for-sale' ),
						'description' => __( 'Price pill on screenshot, dot category, indigo Details button', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
					'card-3a' => [
						'label'       => __( 'Card 3a', 'cw-websites-for-sale' ),
						'description' => __( 'Dark card with violet accent, screenshot, price and Details button', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
					'card-3b' => [
						'label'       => __( 'Card 3b', 'cw-websites-for-sale' ),
						'description' => __( 'Dark card with inset screenshot, category / status badges, Details + Preview modal', 'cw-websites-for-sale' ),
						'supports'    => [ 'title' ],
					],
				],
			];
			return $registry;
		} );

		add_filter( 'codeweber_post_card_template_path', function ( $path, $template_name, $post_type ) {
			if ( $post_type !== 'cw_website' ) {
				return $path;
			}
			$plugin_path = CW_WFS_DIR . 'templates/post-cards/cw_website/' . sanitize_file_name( $template_name ) . '.php';
			return file_exists( $plugin_path ) ? $plugin_path : $path;
		}, 10, 3 );
	}

	private function register_module_card_templates(): void {
		add_filter( 'codeweber_post_card_templates_registry', function ( $registry ) {
			$registry['cw_module'] = [
				'dir'       => 'cw_module',
				'templates' => [
					'card' => [
						'label'       => __( 'Card', 'cw-websites-for-sale' ),
						'description' => __( 'Card with icon, accent color and description', 'cw-websites-for-sale' ),
						'supports'    => [ 'title', 'excerpt' ],
					],
					'card-sm' => [
						'label'       => __( 'Card SM', 'cw-websites-for-sale' ),
						'description' => __( 'Compact horizontal card: icon left, title and description right', 'cw-websites-for-sale' ),
						'supports'    => [ 'title', 'excerpt' ],
					],
					'card-2' => [
						'label'       => __( 'Card 2', 'cw-websites-for-sale' ),
						'description' => __( 'Frosted flat card with icon, description and category subtitle', 'cw-websites-for-sale' ),
						'supports'    => [ 'title', 'excerpt' ],
					],
				],
			];
			return $registry;
		} );

		add_filter( 'codeweber_post_card_template_path', function ( $path, $template_name, $post_type ) {
			if ( $post_type !== 'cw_module' ) {
				return $path;
			}
			$plugin_path = CW_WFS_DIR . 'templates/post-cards/cw_module/' . sanitize_file_name( $template_name ) . '.php';
			return file_exists( $plugin_path ) ? $plugin_path : $path;
		}, 10, 3 );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CPT
	// ─────────────────────────────────────────────────────────────────────────

	public function register_cpt(): void {
		$labels = [
			'name'               => esc_html__( 'Websites For Sale', 'cw-websites-for-sale' ),
			'singular_name'      => esc_html__( 'Website', 'cw-websites-for-sale' ),
			'menu_name'          => esc_html__( 'Websites For Sale', 'cw-websites-for-sale' ),
			'all_items'          => esc_html__( 'All Websites', 'cw-websites-for-sale' ),
			'add_new'            => esc_html__( 'Add New', 'cw-websites-for-sale' ),
			'add_new_item'       => esc_html__( 'Add New Website', 'cw-websites-for-sale' ),
			'edit_item'          => esc_html__( 'Edit Website', 'cw-websites-for-sale' ),
			'new_item'           => esc_html__( 'New Website', 'cw-websites-for-sale' ),
			'view_item'          => esc_html__( 'View Website', 'cw-websites-for-sale' ),
			'search_items'       => esc_html__( 'Search Websites', 'cw-websites-for-sale' ),
			'not_found'          => esc_html__( 'No websites found', 'cw-websites-for-sale' ),
			'not_found_in_trash' => esc_html__( 'No websites found in trash', 'cw-websites-for-sale' ),
		];

		register_post_type( 'cw_website', [
			'label'               => esc_html__( 'Websites For Sale', 'cw-websites-for-sale' ),
			'labels'              => $labels,
			'description'         => 'Ready-made websites available for sale',
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'has_archive'         => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'delete_with_user'    => false,
			'exclude_from_search' => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'can_export'          => true,
			'rewrite'             => [ 'slug' => 'websites', 'with_front' => true ],
			'query_var'           => true,
			'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
			'menu_icon'           => 'dashicons-laptop',
			'menu_position'       => 6,
			'show_in_graphql'     => false,
		] );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Taxonomies
	// ─────────────────────────────────────────────────────────────────────────

	public function register_taxonomies(): void {
		register_taxonomy( 'website_category', [ 'cw_website' ], [
			'label'        => esc_html__( 'Categories', 'cw-websites-for-sale' ),
			'labels'       => [
				'name'          => esc_html__( 'Categories', 'cw-websites-for-sale' ),
				'singular_name' => esc_html__( 'Category', 'cw-websites-for-sale' ),
				'all_items'     => esc_html__( 'All Categories', 'cw-websites-for-sale' ),
				'add_new_item'  => esc_html__( 'Add New Category', 'cw-websites-for-sale' ),
				'edit_item'     => esc_html__( 'Edit Category', 'cw-websites-for-sale' ),
				'new_item'      => esc_html__( 'New Category', 'cw-websites-for-sale' ),
				'search_items'  => esc_html__( 'Search Categories', 'cw-websites-for-sale' ),
				'not_found'     => esc_html__( 'No categories found', 'cw-websites-for-sale' ),
			],
			'public'       => true,
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => [ 'slug' => 'website-category', 'with_front' => true ],
		] );

		register_taxonomy( 'website_tag', [ 'cw_website' ], [
			'label'        => esc_html__( 'Tags', 'cw-websites-for-sale' ),
			'labels'       => [
				'name'          => esc_html__( 'Tags', 'cw-websites-for-sale' ),
				'singular_name' => esc_html__( 'Tag', 'cw-websites-for-sale' ),
				'all_items'     => esc_html__( 'All Tags', 'cw-websites-for-sale' ),
				'add_new_item'  => esc_html__( 'Add New Tag', 'cw-websites-for-sale' ),
				'edit_item'     => esc_html__( 'Edit Tag', 'cw-websites-for-sale' ),
				'new_item'      => esc_html__( 'New Tag', 'cw-websites-for-sale' ),
				'search_items'  => esc_html__( 'Search Tags', 'cw-websites-for-sale' ),
				'not_found'     => esc_html__( 'No tags found', 'cw-websites-for-sale' ),
			],
			'public'       => true,
			'hierarchical' => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => [ 'slug' => 'website-tag', 'with_front' => true ],
		] );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CPT: Modules (admin-only, not public)
	// ─────────────────────────────────────────────────────────────────────────

	public function register_cpt_module(): void {
		$labels = [
			'name'               => esc_html__( 'Modules', 'cw-websites-for-sale' ),
			'singular_name'      => esc_html__( 'Module', 'cw-websites-for-sale' ),
			'menu_name'          => esc_html__( 'Modules', 'cw-websites-for-sale' ),
			'all_items'          => esc_html__( 'All Modules', 'cw-websites-for-sale' ),
			'add_new'            => esc_html__( 'Add New', 'cw-websites-for-sale' ),
			'add_new_item'       => esc_html__( 'Add New Module', 'cw-websites-for-sale' ),
			'edit_item'          => esc_html__( 'Edit Module', 'cw-websites-for-sale' ),
			'new_item'           => esc_html__( 'New Module', 'cw-websites-for-sale' ),
			'search_items'       => esc_html__( 'Search Modules', 'cw-websites-for-sale' ),
			'not_found'          => esc_html__( 'No modules found', 'cw-websites-for-sale' ),
			'not_found_in_trash' => esc_html__( 'No modules found in trash', 'cw-websites-for-sale' ),
		];

		register_post_type( 'cw_module', [
			'label'               => esc_html__( 'Modules', 'cw-websites-for-sale' ),
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'delete_with_user'    => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'can_export'          => true,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => [ 'title', 'editor' ],
			'taxonomies'          => [ 'module_category' ],
			'menu_icon'           => 'dashicons-grid-view',
			'menu_position'       => 7,
			'show_in_graphql'     => false,
		] );
	}

	public function register_taxonomy_module_category(): void {
		register_taxonomy( 'module_category', [ 'cw_module' ], [
			'label'              => esc_html__( 'Module Categories', 'cw-websites-for-sale' ),
			'labels'             => [
				'name'          => esc_html__( 'Module Categories', 'cw-websites-for-sale' ),
				'singular_name' => esc_html__( 'Module Category', 'cw-websites-for-sale' ),
				'all_items'     => esc_html__( 'All Categories', 'cw-websites-for-sale' ),
				'add_new_item'  => esc_html__( 'Add New Category', 'cw-websites-for-sale' ),
				'edit_item'     => esc_html__( 'Edit Category', 'cw-websites-for-sale' ),
				'new_item'      => esc_html__( 'New Category', 'cw-websites-for-sale' ),
				'search_items'  => esc_html__( 'Search Categories', 'cw-websites-for-sale' ),
				'not_found'     => esc_html__( 'No categories found', 'cw-websites-for-sale' ),
			],
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => true,
			'show_in_nav_menus'  => false,
			'hierarchical'       => true,
			'rewrite'            => false,
			'query_var'          => false,
		] );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Widget
	// ─────────────────────────────────────────────────────────────────────────

	public function register_widget(): void {
		register_widget( Widget\FilterWidget::class );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Templates
	// ─────────────────────────────────────────────────────────────────────────

	public function template_include( string $template ): string {
		if ( is_post_type_archive( 'cw_website' ) || is_tax( 'website_category' ) || is_tax( 'website_tag' ) ) {
			$theme = locate_template( 'archive-cw_website.php' );
			if ( $theme ) {
				return $theme;
			}
			$tpl = cw_wfs_setting( 'archive_template', '1' );
			$file = CW_WFS_DIR . "templates/archive/cw_website_{$tpl}.php";
			return file_exists( $file ) ? $file : CW_WFS_DIR . 'templates/archive/cw_website_1.php';
		}
		if ( is_singular( 'cw_website' ) ) {
			$theme = locate_template( 'single-cw_website.php' );
			if ( $theme ) {
				return $theme;
			}
			$tpl = cw_wfs_setting( 'single_template', '1' );
			$file = CW_WFS_DIR . "templates/single/cw_website_{$tpl}.php";
			return file_exists( $file ) ? $file : CW_WFS_DIR . 'templates/single/cw_website_1.php';
		}
		return $template;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Scripts
	// ─────────────────────────────────────────────────────────────────────────

	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'cw-wfs-archive',
			CW_WFS_URL . 'assets/js/archive.js',
			[],
			CW_WFS_VERSION,
			true
		);
	}

	public function set_archive_per_page( \WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'cw_website' ) ) {
			return;
		}
		$per_page = (int) cw_wfs_setting( 'archive_per_page', '12' );
		if ( $per_page > 0 ) {
			$query->set( 'posts_per_page', $per_page );
		}
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Card renderer (static — used from template + filterPosts render helper)
	// ─────────────────────────────────────────────────────────────────────────

	public static function render_card( int $post_id ): void {
		$card_radius = class_exists( 'Codeweber_Options' ) ? \Codeweber_Options::style( 'card-radius' ) : 'rounded';
		$btn_style   = class_exists( 'Codeweber_Options' ) ? \Codeweber_Options::style( 'button' ) : ' rounded-pill';

		$title         = get_post_meta( $post_id, '_alt_title', true ) ?: get_the_title( $post_id );
		$website_url   = get_post_meta( $post_id, '_ws_url', true );
		$screenshot_id = (int) get_post_meta( $post_id, '_ws_screenshot', true );
		$price         = get_post_meta( $post_id, '_ws_price', true );
		$cms           = get_post_meta( $post_id, '_ws_cms', true );
		$launch_time   = get_post_meta( $post_id, '_ws_launch_time', true );
		$status        = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';
		$permalink     = get_permalink( $post_id );
		$url_display   = $website_url ? preg_replace( '#^https?://#', '', rtrim( $website_url, '/' ) ) : '';

		$cats     = get_the_terms( $post_id, 'website_category' );
		$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

		$status_cfg = [
			'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ),  'class' => 'bg-success' ],
			'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),       'class' => 'bg-secondary' ],
			'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ),   'class' => 'bg-warning text-dark' ],
		];
		$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];
		?>
		<div class="col-md-6 col-xl-4">
			<div class="card h-100 overflow-hidden <?php echo esc_attr( $card_radius ); ?>">

				<!-- Browser bar + screenshot -->
				<div class="position-relative">
					<a href="<?php echo esc_url( $permalink ); ?>" class="d-block text-decoration-none">
						<div class="cw-browser-bar d-flex align-items-center bg-navy gap-1 px-3 py-0">
							<span class="cw-browser-dot cw-browser-dot--red rounded-circle flex-shrink-0"></span>
							<span class="cw-browser-dot cw-browser-dot--yellow rounded-circle flex-shrink-0"></span>
							<span class="cw-browser-dot cw-browser-dot--green rounded-circle flex-shrink-0"></span>
							<?php if ( $url_display ) : ?>
								<span class="cw-browser-url flex-grow-1 text-truncate bg-white rounded-1 px-2 text-muted ms-2">
									<?php echo esc_html( $url_display ); ?>
								</span>
							<?php endif; ?>
						</div>
						<div class="cw-it-screen overflow-hidden position-relative">
							<?php if ( $screenshot_id ) : ?>
								<?php echo wp_get_attachment_image( $screenshot_id, 'full', false, [
									'class' => 'cw-it-screenshot d-block w-100 h-auto',
									'alt'   => esc_attr( $title ),
								] ); ?>
							<?php else : ?>
								<div class="cw-it-screenshot-placeholder w-100"></div>
							<?php endif; ?>
						</div>
					</a>

					<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;">
						<?php echo $st['label']; ?>
					</span>

					<?php if ( $website_url ) : ?>
					<button type="button"
						class="cw-it-qv btn btn-sm btn-white<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start has-ripple"
						data-bs-toggle="modal"
						data-bs-target="#cw-preview-modal"
						data-website-url="<?php echo esc_url( $website_url ); ?>"
						data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"
						aria-label="<?php esc_attr_e( 'Preview', 'cw-websites-for-sale' ); ?>">
						<i class="uil uil-eye"></i>
						<?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
					</button>
					<?php endif; ?>
				</div>

				<!-- Card body -->
				<div class="card-body p-4 d-flex flex-column">
					<div class="post-header">
						<?php if ( $cat_name ) : ?>
						<div class="post-category text-line mb-2"><?php echo esc_html( $cat_name ); ?></div>
						<?php endif; ?>
						<h2 class="post-title h5 mb-3">
							<a href="<?php echo esc_url( $permalink ); ?>" class="link-dark text-decoration-none">
								<?php echo esc_html( $title ); ?>
							</a>
						</h2>
					</div>

					<div class="mt-auto d-flex flex-wrap gap-2 align-items-center">
						<?php if ( $price ) : ?>
						<span class="fw-bold text-primary fs-5"><?php echo esc_html( $price ); ?></span>
						<?php endif; ?>
						<?php if ( $cms ) : ?>
						<span class="badge bg-soft-ash text-ash"><?php echo esc_html( $cms ); ?></span>
						<?php endif; ?>
						<?php if ( $launch_time ) : ?>
						<span class="text-muted small">
							<i class="uil uil-clock me-1"></i><?php echo esc_html( $launch_time ); ?>
						</span>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Preview Modal
	// ─────────────────────────────────────────────────────────────────────────

	public static function request_preview_modal(): void {
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;
		add_action( 'wp_footer', [ self::class, 'render_preview_modal' ] );
	}

	public static function render_preview_modal(): void {
		if ( locate_template( 'templates/components/cw-preview-modal.php' ) ) {
			get_template_part( 'templates/components/cw-preview-modal' );
		}
	}
}
