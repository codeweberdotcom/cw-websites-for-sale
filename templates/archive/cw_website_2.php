<?php
/**
 * Archive: Websites For Sale — Template 2 (Scroll Rows)
 * Alternating left/right layout with full-height scroll screenshot.
 */

get_header();

if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}

$cat_terms = get_terms( [
	'taxonomy'   => 'website_category',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );
$has_cats = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms );

$tag_terms = get_terms( [
	'taxonomy'   => 'website_tag',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );
$has_tags = ! empty( $tag_terms ) && ! is_wp_error( $tag_terms );

$btn_style = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : ' rounded-pill';
?>

<style>
.cw-browser-bar  { height: 32px; }
.cw-browser-dot  { width: 10px; height: 10px; }
.cw-browser-dot--red    { background: #ff5f57; }
.cw-browser-dot--yellow { background: #ffbd2e; }
.cw-browser-dot--green  { background: #28c840; }
.cw-browser-url  { min-width: 0; font-size: 11px; line-height: 1.6; }
.cw2-screen { max-height: 360px; overflow: hidden; cursor: default; }
.cw2-screen img { display: block; transform: translateY(0); }
.cw-wfs-tag-btn { cursor: pointer; }
</style>

<section id="content-wrapper" class="wrapper">
	<div class="container py-14 py-md-16">

		<?php if ( $has_cats || $has_tags ) : ?>
		<div class="cw-wfs-filters mb-12">

			<?php if ( $has_cats ) : ?>
			<div class="isotope-filter filter cw-wfs-cat-filters mb-4">
				<ul>
					<li><a class="filter-item active has-ripple" data-cat-id="0"><?php esc_html_e( 'All', 'cw-websites-for-sale' ); ?></a></li>
					<?php foreach ( $cat_terms as $term ) : ?>
					<li>
						<a class="filter-item has-ripple" data-cat-id="<?php echo esc_attr( $term->term_id ); ?>">
							<?php echo esc_html( $term->name ); ?>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $has_tags ) : ?>
			<div class="d-flex flex-wrap gap-2 cw-wfs-tag-filters">
				<span class="badge bg-primary cw-wfs-tag-btn active has-ripple" data-tag-id="0">
					<?php esc_html_e( 'All tags', 'cw-websites-for-sale' ); ?>
				</span>
				<?php foreach ( $tag_terms as $term ) : ?>
				<span class="badge bg-soft-ash text-ash cw-wfs-tag-btn has-ripple" data-tag-id="<?php echo esc_attr( $term->term_id ); ?>">
					<?php echo esc_html( $term->name ); ?>
				</span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<div id="cw-wfs-grid-results">
		<?php if ( have_posts() ) :
			$index = 0;
			while ( have_posts() ) : the_post();
				$post_id     = get_the_ID();
				$title       = get_post_meta( $post_id, '_alt_title', true ) ?: get_the_title();
				$website_url = get_post_meta( $post_id, '_ws_url', true );
				$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
				$price       = get_post_meta( $post_id, '_ws_price', true );
				$cms         = get_post_meta( $post_id, '_ws_cms', true );
				$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
				$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';
				$url_display = $website_url ? preg_replace( '#^https?://#', '', rtrim( $website_url, '/' ) ) : '';
				$permalink   = get_permalink();
				$is_even     = ( $index % 2 === 1 );
				$index++;

				$cats     = get_the_terms( $post_id, 'website_category' );
				$tags     = get_the_terms( $post_id, 'website_tag' );
				$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';

				$status_cfg = [
					'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ),  'class' => 'bg-success' ],
					'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),       'class' => 'bg-secondary' ],
					'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ),   'class' => 'bg-warning text-dark' ],
				];
				$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];
				$card_radius = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'card-radius' ) : 'rounded';
		?>

		<div class="row gy-10 align-items-center mb-15 mb-md-17">

			<!-- Screenshot column -->
			<div class="col-lg-7<?php echo $is_even ? ' order-lg-2' : ''; ?>">
				<div class="card shadow-sm <?php echo esc_attr( $card_radius ); ?> overflow-hidden">
					<!-- Browser bar -->
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
					<div class="cw2-screen position-relative">
						<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;">
							<?php echo $st['label']; ?>
						</span>
						<?php if ( $screenshot ) :
							echo wp_get_attachment_image( $screenshot, 'full', false, [
								'class' => 'w-100 d-block',
								'alt'   => esc_attr( $title ),
							] );
						else : ?>
							<div style="height:320px;background:#f1f3f5;"></div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Info column -->
			<div class="col-lg-4<?php echo $is_even ? ' me-auto' : ' ms-auto'; ?>">
				<?php if ( $cat_name ) : ?>
				<div class="post-category text-line mb-3"><?php echo esc_html( $cat_name ); ?></div>
				<?php endif; ?>
				<h2 class="h2 post-title ls-sm mb-4">
					<a href="<?php echo esc_url( $permalink ); ?>" class="link-dark text-decoration-none">
						<?php echo esc_html( $title ); ?>
					</a>
				</h2>

				<?php if ( $price || $cms || $launch_time ) : ?>
				<ul class="list-unstyled mb-5">
					<?php if ( $price ) : ?>
					<li class="d-flex justify-content-between border-bottom py-2">
						<span class="text-muted"><?php esc_html_e( 'Price', 'cw-websites-for-sale' ); ?></span>
						<strong class="text-primary"><?php echo esc_html( $price ); ?></strong>
					</li>
					<?php endif; ?>
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
				</ul>
				<?php endif; ?>

				<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
				<div class="d-flex flex-wrap gap-1 mb-5">
					<?php foreach ( $tags as $tag ) : ?>
					<span class="badge bg-soft-ash text-ash"><?php echo esc_html( $tag->name ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<div class="d-flex flex-wrap gap-2">
					<a href="<?php echo esc_url( $permalink ); ?>"
					   class="btn btn-primary<?php echo esc_attr( $btn_style ); ?> has-ripple">
						<?php esc_html_e( 'View details', 'cw-websites-for-sale' ); ?>
					</a>
					<?php if ( $website_url ) : ?>
					<button type="button"
						class="btn btn-outline-primary<?php echo esc_attr( $btn_style ); ?> btn-icon btn-icon-start has-ripple"
						data-bs-toggle="modal"
						data-bs-target="#cw-preview-modal"
						data-website-url="<?php echo esc_url( $website_url ); ?>"
						data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
						<i class="uil uil-eye"></i>
						<?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
					</button>
					<?php endif; ?>
				</div>
			</div>

		</div>
		<?php endwhile; ?>

		<?php if ( function_exists( 'codeweber_posts_pagination' ) ) :
			codeweber_posts_pagination( [ 'nav_class' => 'd-flex justify-content-center mt-10' ] );
		else :
			the_posts_pagination( [ 'mid_size' => 2 ] );
		endif; ?>

		<?php else : ?>
		<p class="text-muted"><?php esc_html_e( 'No websites found.', 'cw-websites-for-sale' ); ?></p>
		<?php endif; ?>
		</div><!-- #cw-wfs-grid-results -->

	</div>
</section>

<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

<script>
(function () {
	var catBtns     = document.querySelectorAll('.cw-wfs-cat-filters .filter-item');
	var tagBtns     = document.querySelectorAll('.cw-wfs-tag-filters .cw-wfs-tag-btn');
	var resultsWrap = document.getElementById('cw-wfs-grid-results');

	var activeCatId = 0;
	var activeTagId = 0;
	var SCROLL_SPEED = 120;

	function getCurrentY(img) {
		var m = window.getComputedStyle(img).transform;
		if (!m || m === 'none') return 0;
		var v = m.match(/matrix\([^,]+,[^,]+,[^,]+,[^,]+,[^,]+,\s*([-\d.]+)\)/);
		return v ? parseFloat(v[1]) : 0;
	}
	function scrollTo(img, targetY) {
		var dist = Math.abs(targetY - getCurrentY(img));
		if (dist < 1) return;
		img.style.transition = 'transform ' + (dist / SCROLL_SPEED).toFixed(2) + 's linear';
		img.style.transform  = 'translateY(' + targetY + 'px)';
	}
	function initScreenScroll(root) {
		(root || document).querySelectorAll('.cw2-screen').forEach(function (wrap) {
			if (wrap.dataset.cwScrollInit) return;
			wrap.dataset.cwScrollInit = '1';
			var img = wrap.querySelector('img');
			if (!img) return;
			function dist() { return Math.max(0, img.naturalHeight * (img.offsetWidth / (img.naturalWidth || 1)) - wrap.offsetHeight); }
			wrap.addEventListener('mouseenter', function () { var d = dist(); if (d > 0) scrollTo(img, -Math.round(d * 0.9)); });
			wrap.addEventListener('mouseleave', function () { scrollTo(img, 0); });
		});
	}
	initScreenScroll();

	function fetchFiltered() {
		if (!resultsWrap || typeof fetch_vars === 'undefined') return;
		resultsWrap.style.opacity = '0.5';
		resultsWrap.style.pointerEvents = 'none';
		var filters = {};
		if (activeCatId) filters.website_category = activeCatId;
		if (activeTagId) filters.website_tag = activeTagId;
		var body = new FormData();
		body.append('action', 'fetch_action');
		body.append('nonce', fetch_vars.nonce);
		body.append('actionType', 'filterPosts');
		body.append('params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_2', filters: filters }));
		fetch(fetch_vars.ajaxurl, { method: 'POST', body: body })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data.status === 'success' && resultsWrap) {
					resultsWrap.innerHTML = data.data.html;
					initScreenScroll(resultsWrap);
				}
			})
			.catch(function (err) { console.error('[CW WFS] filter error:', err); })
			.finally(function () {
				if (resultsWrap) { resultsWrap.style.opacity = ''; resultsWrap.style.pointerEvents = ''; }
			});
	}

	catBtns.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			activeCatId = +btn.getAttribute('data-cat-id');
			catBtns.forEach(function (b) { b.classList.remove('active'); });
			btn.classList.add('active');
			fetchFiltered();
		});
	});
	tagBtns.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			activeTagId = +btn.getAttribute('data-tag-id');
			tagBtns.forEach(function (b) { b.classList.remove('active', 'bg-primary', 'text-white'); b.classList.add('bg-soft-ash', 'text-ash'); });
			btn.classList.remove('bg-soft-ash', 'text-ash');
			btn.classList.add('active', 'bg-primary', 'text-white');
			fetchFiltered();
		});
	});
})();
</script>

<?php get_footer(); ?>
