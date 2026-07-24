<?php
/**
 * Single: Website For Sale — Template 2 (Wide)
 * Full-width screenshot, price and details below in a two-column row.
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
	$card_radius = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'card-radius' ) : 'rounded';

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
	.cw2s-screen { max-height: 540px; overflow: hidden; }
	.cw2s-screenshot { display: block; transform: translateY(0); }
	</style>

	<section class="wrapper">
		<div class="container py-14 py-md-16">

			<!-- Full-width browser mockup -->
			<div class="<?php echo esc_attr( $card_radius ); ?> overflow-hidden shadow-sm mb-10">
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
				<div class="cw2s-screen position-relative">
					<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;">
						<?php echo $st['label']; ?>
					</span>
					<?php if ( $screenshot ) :
						echo wp_get_attachment_image( $screenshot, 'full', false, [
							'class' => 'cw2s-screenshot d-block w-100 h-auto',
							'alt'   => esc_attr( $title ),
						] );
					else : ?>
						<div style="height:480px;background:#f1f3f5;"></div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Action buttons -->
			<div class="d-flex flex-wrap gap-2 mb-10">
				<?php if ( $website_url ) : ?>
				<button type="button"
					class="btn btn-primary<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start has-ripple"
					data-bs-toggle="modal"
					data-bs-target="#cw-preview-modal"
					data-website-url="<?php echo esc_url( $website_url ); ?>"
					data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
					<i class="uil uil-desktop"></i>
					<?php esc_html_e( 'Preview website', 'cw-websites-for-sale' ); ?>
				</button>
				<?php endif; ?>
				<?php if ( $status !== 'sold' ) : ?>
				<a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>?subject=<?php echo esc_attr( sprintf( __( 'Buy website: %s', 'cw-websites-for-sale' ), get_the_title() ) ); ?>"
				   class="btn btn-outline-primary<?php echo esc_attr( $btn_style ); ?> has-ripple">
					<?php esc_html_e( 'Buy this website', 'cw-websites-for-sale' ); ?>
				</a>
				<?php endif; ?>
			</div>

			<!-- Details + Description -->
			<div class="row gx-md-10 gy-8">

				<div class="col-lg-4">
					<div class="card bg-pale-primary p-6">
						<?php if ( $price ) : ?>
						<div class="h2 fw-bold mb-4"><?php echo esc_html( $price ); ?></div>
						<?php endif; ?>
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
						<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
						<div class="d-flex flex-wrap gap-1">
							<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"
							   class="badge bg-soft-ash text-ash text-decoration-none">
								<?php echo esc_html( $tag->name ); ?>
							</a>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="col-lg-8">
					<?php if ( get_the_content() ) : ?>
					<div class="post-content">
						<?php the_content(); ?>
					</div>
					<?php endif; ?>
				</div>

			</div>

		</div>
	</section>

	<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

	<script>
	(function () {
		var SCROLL_SPEED = 100;
		function getCurrentY(img) {
			var m = window.getComputedStyle(img).transform;
			if (!m || m === 'none') return 0;
			var v = m.match(/matrix\([^,]+,[^,]+,[^,]+,[^,]+,[^,]+,\s*([-\d.]+)\)/);
			return v ? parseFloat(v[1]) : 0;
		}
		var wrap = document.querySelector('.cw2s-screen');
		var img  = wrap ? wrap.querySelector('.cw2s-screenshot') : null;
		if (wrap && img) {
			function dist() { return Math.max(0, img.naturalHeight * (img.offsetWidth / (img.naturalWidth || 1)) - wrap.offsetHeight); }
			wrap.addEventListener('mouseenter', function () {
				var d = dist();
				if (d < 1) return;
				img.style.transition = 'transform ' + (d / SCROLL_SPEED).toFixed(2) + 's linear';
				img.style.transform  = 'translateY(-' + Math.round(d * 0.9) + 'px)';
			});
			wrap.addEventListener('mouseleave', function () {
				img.style.transition = 'transform 1s linear';
				img.style.transform  = 'translateY(0)';
			});
		}
	})();
	</script>

<?php endwhile; ?>

<?php get_footer(); ?>
