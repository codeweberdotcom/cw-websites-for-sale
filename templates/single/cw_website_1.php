<?php
/**
 * Single: Website For Sale
 * Template provided by cw-websites-for-sale plugin.
 * Override by creating single-cw_website.php in your (child) theme.
 */

get_header();

if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}

while ( have_posts() ) :
	the_post();

	$post_id     = get_the_ID();
	$title       = get_post_meta( $post_id, '_alt_title', true ) ?: get_the_title();
	$website_url = get_post_meta( $post_id, '_ws_url',         true );
	$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
	$price       = get_post_meta( $post_id, '_ws_price',       true );
	$cms         = get_post_meta( $post_id, '_ws_cms',         true );
	$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
	$status      = get_post_meta( $post_id, '_ws_status',      true ) ?: 'for_sale';
	$url_display = $website_url ? preg_replace( '#^https?://#', '', rtrim( $website_url, '/' ) ) : '';
	$btn_style   = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : ' rounded-pill';

	$status_cfg = [
		'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ),  'class' => 'bg-success' ],
		'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),       'class' => 'bg-secondary' ],
		'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ),   'class' => 'bg-warning text-dark' ],
	];
	$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

	$cats = get_the_terms( $post_id, 'website_category' );
	$tags = get_the_terms( $post_id, 'website_tag' );
	?>

	<style>
	.cw-browser-bar  { height: 32px; }
	.cw-browser-dot  { width: 10px; height: 10px; }
	.cw-browser-dot--red    { background: #ff5f57; }
	.cw-browser-dot--yellow { background: #ffbd2e; }
	.cw-browser-dot--green  { background: #28c840; }
	.cw-browser-url  { min-width: 0; font-size: 11px; line-height: 1.6; }
	.cw-single-screen { max-height: 420px; overflow: hidden; position: relative; }
	.cw-single-screenshot { transition: transform 14s linear; transform: translateY(0); }
	.cw-single-screen:hover .cw-single-screenshot {
		transform: translateY(calc(-100% + 420px));
	}
	</style>

	<section class="wrapper">
		<div class="container py-14 py-md-16">
			<div class="row gx-md-8 gy-10">

				<!-- Main column -->
				<div class="col-lg-8">

					<!-- Browser mockup -->
					<div class="<?php echo class_exists( 'Codeweber_Options' ) ? esc_attr( Codeweber_Options::style( 'card-radius' ) ) : 'rounded'; ?> overflow-hidden mb-8 shadow-sm">
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
						<div class="cw-single-screen">
							<?php if ( $screenshot ) :
								echo wp_get_attachment_image( $screenshot, 'full', false, [
									'class' => 'cw-single-screenshot d-block w-100 h-auto',
									'alt'   => esc_attr( $title ),
								] );
							else : ?>
								<div style="height:420px;background:#f1f3f5;"></div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Preview button -->
					<?php if ( $website_url ) : ?>
					<div class="mb-8">
						<button type="button"
							class="btn btn-primary<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start has-ripple"
							data-bs-toggle="modal"
							data-bs-target="#cw-preview-modal"
							data-website-url="<?php echo esc_url( $website_url ); ?>"
							data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
							<i class="uil uil-desktop"></i>
							<?php esc_html_e( 'Preview website', 'cw-websites-for-sale' ); ?>
						</button>
					</div>
					<?php endif; ?>

					<!-- Description -->
					<?php if ( get_the_content() ) : ?>
					<div class="post-content">
						<?php the_content(); ?>
					</div>
					<?php endif; ?>

				</div>

				<!-- Sidebar -->
				<aside class="col-lg-4">
					<div class="card bg-pale-primary p-6 sticky-top" style="top:80px;">

						<!-- Status badge -->
						<span class="badge <?php echo esc_attr( $st['class'] ); ?> mb-3">
							<?php echo $st['label']; ?>
						</span>

						<!-- Price -->
						<?php if ( $price ) : ?>
						<div class="h2 fw-bold mb-4"><?php echo esc_html( $price ); ?></div>
						<?php endif; ?>

						<!-- Meta list -->
						<ul class="list-unstyled mb-5">
							<?php if ( $cms ) : ?>
							<li class="d-flex justify-content-between border-bottom py-2">
								<span class="text-muted"><?php esc_html_e( 'Platform', 'cw-websites-for-sale' ); ?></span>
								<strong><?php echo esc_html( $cms ); ?></strong>
							</li>
							<?php endif; ?>
							<?php if ( $launch_time ) : ?>
							<li class="d-flex justify-content-between border-bottom py-2">
								<span class="text-muted"><?php esc_html_e( 'Launch time', 'cw-websites-for-sale' ); ?></span>
								<strong><?php echo esc_html( $launch_time ); ?></strong>
							</li>
							<?php endif; ?>
							<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
							<li class="d-flex justify-content-between border-bottom py-2">
								<span class="text-muted"><?php esc_html_e( 'Category', 'cw-websites-for-sale' ); ?></span>
								<strong><?php echo esc_html( $cats[0]->name ); ?></strong>
							</li>
							<?php endif; ?>
						</ul>

						<!-- Tags -->
						<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
						<div class="d-flex flex-wrap gap-1 mb-5">
							<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"
							   class="badge bg-soft-ash text-ash text-decoration-none">
								<?php echo esc_html( $tag->name ); ?>
							</a>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>

						<!-- CTA -->
						<?php if ( $status !== 'sold' ) : ?>
						<a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>?subject=<?php echo esc_attr( sprintf( __( 'Buy website: %s', 'cw-websites-for-sale' ), get_the_title() ) ); ?>"
						   class="btn btn-primary<?php echo esc_attr( $btn_style ); ?> w-100 mb-2">
							<?php esc_html_e( 'Buy this website', 'cw-websites-for-sale' ); ?>
						</a>
						<?php endif; ?>

						<?php if ( $website_url ) : ?>
						<button type="button"
							class="btn btn-outline-primary<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start w-100 has-ripple"
							data-bs-toggle="modal"
							data-bs-target="#cw-preview-modal"
							data-website-url="<?php echo esc_url( $website_url ); ?>"
							data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
							<i class="uil uil-eye"></i>
							<?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
						</button>
						<?php endif; ?>

					</div>
				</aside>

			</div>
		</div>
	</section>

	<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

<?php endwhile; ?>

<?php get_footer(); ?>
