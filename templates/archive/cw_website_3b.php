<?php
/**
 * Archive: Websites For Sale — Template 3b (Dark — Inset Screenshot + White Button)
 */

get_header();
if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}

$cat_colors = [
	[ 'bg' => '#dbeafe', 'fg' => '#1d4ed8' ],
	[ 'bg' => '#dcfce7', 'fg' => '#15803d' ],
	[ 'bg' => '#fce7f3', 'fg' => '#9d174d' ],
	[ 'bg' => '#fef3c7', 'fg' => '#92400e' ],
	[ 'bg' => '#ede9fe', 'fg' => '#6d28d9' ],
	[ 'bg' => '#fee2e2', 'fg' => '#991b1b' ],
];

$cat_terms = get_terms( [ 'taxonomy' => 'website_category', 'hide_empty' => true, 'orderby' => 'name' ] );
$has_cats  = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms );
$tag_terms = get_terms( [ 'taxonomy' => 'website_tag', 'hide_empty' => true, 'orderby' => 'name' ] );
$has_tags  = ! empty( $tag_terms ) && ! is_wp_error( $tag_terms );
$grid_gap  = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'grid-gap' ) : 'gx-md-8 gy-10 gy-md-13';

$card_radius = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'card-radius' ) : 'rounded';
$btn_style   = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'button' ) : '';
$cols_setting = cw_wfs_setting( 'archive_columns', '3' );
$cols_map     = [ '2' => 'col-md-6', '3' => 'col-md-6 col-xl-4', '4' => 'col-md-6 col-xl-3' ];
$col_class    = $cols_map[ $cols_setting ] ?? 'col-md-6 col-xl-4';
$screen_h     = $cols_setting === '4' ? 210 : 285;

$status_cfg = [
	'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ), 'class' => 'bg-success' ],
	'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),     'class' => 'bg-secondary' ],
	'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ), 'class' => 'bg-warning text-dark' ],
];
?>

<section id="content-wrapper" class="wrapper">
	<div class="container py-14 py-md-16">

		<?php if ( $has_cats || $has_tags ) : ?>
		<div class="cw-wfs-filters mb-10">
			<?php if ( $has_cats ) : ?>
			<div class="isotope-filter filter cw-wfs-cat-filters mb-4">
				<ul>
					<li><a class="filter-item active" data-cat-id="0"><?php esc_html_e( 'All', 'cw-websites-for-sale' ); ?></a></li>
					<?php foreach ( $cat_terms as $term ) : ?>
					<li><a class="filter-item" data-cat-id="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<?php if ( $has_tags ) : ?>
			<div class="d-flex flex-wrap gap-2 cw-wfs-tag-filters">
				<span class="badge bg-primary cw-wfs-tag-btn active" data-tag-id="0"><?php esc_html_e( 'All tags', 'cw-websites-for-sale' ); ?></span>
				<?php foreach ( $tag_terms as $term ) : ?>
				<span class="badge bg-soft-ash text-ash cw-wfs-tag-btn" data-tag-id="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div id="cw-wfs-grid-results">
		<?php if ( have_posts() ) : ?>
		<div class="row <?php echo esc_attr( $grid_gap ); ?>">
		<?php while ( have_posts() ) : the_post();
			$post_id     = get_the_ID();
			$title       = get_post_meta( $post_id, '_alt_title', true ) ?: get_the_title();
			$website_url = get_post_meta( $post_id, '_ws_url', true );
			$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
			$price       = get_post_meta( $post_id, '_ws_price', true );
			$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
			$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';
			$permalink   = get_permalink();
			$cats        = get_the_terms( $post_id, 'website_category' );
			$cat_name    = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
			$cat_c       = ( $cats && ! is_wp_error( $cats ) ) ? $cat_colors[ $cats[0]->term_id % 6 ] : $cat_colors[0];
			$st          = $status_cfg[ $status ] ?? $status_cfg['for_sale'];
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<div class="card h-100 bg-dark shadow-lg <?php echo esc_attr( $card_radius ); ?>">
				<div class="cw-it-screen position-relative overflow-hidden mx-2 mt-2 <?php echo esc_attr( $card_radius ); ?>" style="height:<?php echo $screen_h; ?>px">
					<?php if ( $screenshot ) :
						echo wp_get_attachment_image( $screenshot, 'full', false, [
							'alt'   => esc_attr( $title ),
							'class' => 'w-100 cw-it-screenshot',
							'style' => 'height:auto',
						] );
					else : ?>
					<div class="w-100 h-100 bg-ash"></div>
					<?php endif; ?>
					<?php if ( $cat_name ) : ?>
					<span class="badge position-absolute top-0 start-0 m-2" style="background:<?php echo esc_attr( $cat_c['bg'] ); ?>;color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
					<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 end-0 m-2"><?php echo $st['label']; ?></span>
				</div>
				<div class="card-body p-4">
					<div class="post-header">
						<h3 class="post-title h5 mb-0 text-white"><a href="<?php echo esc_url( $permalink ); ?>" class="link-inverse"><?php echo esc_html( $title ); ?></a></h3>
						<p class="price text-primary fs-22 fw-bold mb-0">
							<ins><span class="amount"><?php echo $price ? esc_html( $price ) . ' ₽' : ''; ?></span></ins>
							<?php if ( $launch_time ) : ?>
							<span class="text-muted fs-sm ms-2">· <?php echo esc_html( $launch_time ); ?></span>
							<?php endif; ?>
						</p>
					</div>
				</div>
				<div class="card-footer d-flex gap-2 bg-transparent border-0 pt-0 px-4 pb-4">
					<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-outline-white<?php echo esc_attr( $btn_style ); ?> has-ripple flex-grow-1"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
					<?php if ( $website_url ) : ?>
					<button type="button" class="btn btn-outline-primary<?php echo esc_attr( $btn_style ); ?> has-ripple"
						data-bs-toggle="modal" data-bs-target="#cw-preview-modal"
						data-website-url="<?php echo esc_url( $website_url ); ?>"
						data-website-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>">
						<i class="uil uil-play-circle me-1"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?>
					</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endwhile; ?>
		</div>

		<?php if ( function_exists( 'codeweber_posts_pagination' ) ) :
			codeweber_posts_pagination( [ 'nav_class' => 'd-flex justify-content-center mt-10' ] );
		else :
			the_posts_pagination( [ 'mid_size' => 2 ] );
		endif; ?>

		<?php else : ?>
		<p class="text-muted"><?php esc_html_e( 'No websites found.', 'cw-websites-for-sale' ); ?></p>
		<?php endif; ?>
		</div>

	</div>
</section>

<script>
(function () {
	var catBtns     = document.querySelectorAll('.cw-wfs-cat-filters .filter-item');
	var tagBtns     = document.querySelectorAll('.cw-wfs-tag-filters .cw-wfs-tag-btn');
	var resultsWrap = document.getElementById('cw-wfs-grid-results');
	var activeCatId = 0, activeTagId = 0;
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
		(root || document).querySelectorAll('.cw-it-screen').forEach(function (wrap) {
			if (wrap.dataset.cwScrollInit) return;
			wrap.dataset.cwScrollInit = '1';
			var img = wrap.querySelector('.cw-it-screenshot');
			if (!img) return;
			function getScrollDist() {
				return Math.max(0, img.naturalHeight * (img.offsetWidth / (img.naturalWidth || 1)) - wrap.offsetHeight);
			}
			wrap.addEventListener('mouseenter', function () { var d = getScrollDist(); if (d > 0) scrollTo(img, -Math.round(d * 0.9)); });
			wrap.addEventListener('mouseleave', function () { scrollTo(img, 0); });
		});
	}
	initScreenScroll();

	// Direct preview handler — relatedTarget in show.bs.modal is unreliable
	document.addEventListener('click', function(e) {
		var btn = e.target.closest('[data-bs-target="#cw-preview-modal"]');
		if (!btn) return;
		var url     = btn.getAttribute('data-website-url') || '';
		var title   = btn.getAttribute('data-website-title') || '';
		var frame   = document.getElementById('cw-preview-frame');
		var titleEl = document.getElementById('cw-preview-title');
		var loader  = document.getElementById('cw-preview-loader');
		if (loader)  loader.classList.remove('done');
		if (titleEl) titleEl.textContent = title;
		if (frame)   { frame.src = ''; setTimeout(function() { frame.src = url; }, 0); }
	});

	function fetchFiltered() {
		if ( ! resultsWrap || typeof fetch_vars === 'undefined' ) return;
		resultsWrap.style.opacity = '0.5';
		resultsWrap.style.pointerEvents = 'none';
		var filters = {};
		if ( activeCatId ) filters.website_category = activeCatId;
		if ( activeTagId ) filters.website_tag = activeTagId;
		var body = new FormData();
		body.append( 'action', 'fetch_action' );
		body.append( 'nonce', fetch_vars.nonce );
		body.append( 'actionType', 'filterPosts' );
		body.append( 'params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_3b', filters: filters }) );
		fetch( fetch_vars.ajaxurl, { method: 'POST', body: body } )
			.then( function(r) { return r.json(); } )
			.then( function(data) { if ( data.status === 'success' && resultsWrap ) { resultsWrap.innerHTML = data.data.html; initScreenScroll(resultsWrap); } } )
			.catch( function(err) { console.error('[CW WFS] filter error:', err); } )
			.finally( function() { if ( resultsWrap ) { resultsWrap.style.opacity = ''; resultsWrap.style.pointerEvents = ''; } } );
	}

	catBtns.forEach( function(btn) {
		btn.addEventListener( 'click', function(e) {
			e.preventDefault();
			activeCatId = +btn.getAttribute('data-cat-id');
			catBtns.forEach( function(b) { b.classList.remove('active'); } );
			btn.classList.add('active');
			fetchFiltered();
		});
	});
	tagBtns.forEach( function(btn) {
		btn.addEventListener( 'click', function(e) {
			e.preventDefault();
			activeTagId = +btn.getAttribute('data-tag-id');
			tagBtns.forEach( function(b) { b.classList.remove('bg-primary','text-white'); b.classList.add('bg-soft-ash','text-ash'); b.classList.remove('active'); } );
			btn.classList.remove('bg-soft-ash','text-ash');
			btn.classList.add('active','bg-primary','text-white');
			fetchFiltered();
		});
	});
})();
</script>

<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

<?php get_footer(); ?>
