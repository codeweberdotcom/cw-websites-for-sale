<?php
/**
 * Archive: Websites For Sale
 * Template provided by cw-websites-for-sale plugin.
 * Override by creating archive-cw_website.php in your (child) theme.
 */

get_header();

if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}

$grid_gap  = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'grid-gap' ) : 'gx-md-8 gy-10 gy-md-13';

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
?>

<style>
.cw-browser-bar  { height: 32px; }
.cw-browser-dot  { width: 10px; height: 10px; }
.cw-browser-dot--red    { background: #ff5f57; }
.cw-browser-dot--yellow { background: #ffbd2e; }
.cw-browser-dot--green  { background: #28c840; }
.cw-browser-url  { min-width: 0; font-size: 11px; line-height: 1.6; }
.cw-it-screen    { height: 220px; }
.cw-it-screenshot { transform: translateY(0); }
.cw-it-screenshot-placeholder { height: 220px; background: #f1f3f5; }
.cw-it-qv {
	position: absolute; bottom: 10px; right: 10px;
	opacity: 0; transform: translateY(6px);
	transition: opacity .2s ease, transform .2s ease;
	z-index: 2;
}
.card:hover .cw-it-qv { opacity: 1; transform: translateY(0); }
.cw-wfs-tag-btn { cursor: pointer; }
</style>

<section id="content-wrapper" class="wrapper">
	<div class="container py-14 py-md-16">

		<?php if ( $has_cats || $has_tags ) : ?>
		<div class="cw-wfs-filters mb-10">

			<?php if ( $has_cats ) : ?>
			<div class="isotope-filter filter cw-wfs-cat-filters mb-4">
				<ul>
					<li><a class="filter-item active" data-cat-id="0"><?php esc_html_e( 'All', 'cw-websites-for-sale' ); ?></a></li>
					<?php foreach ( $cat_terms as $term ) : ?>
					<li>
						<a class="filter-item" data-cat-id="<?php echo esc_attr( $term->term_id ); ?>">
							<?php echo esc_html( $term->name ); ?>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $has_tags ) : ?>
			<div class="d-flex flex-wrap gap-2 cw-wfs-tag-filters">
				<span class="badge bg-primary cw-wfs-tag-btn active" data-tag-id="0">
					<?php esc_html_e( 'All tags', 'cw-websites-for-sale' ); ?>
				</span>
				<?php foreach ( $tag_terms as $term ) : ?>
				<span class="badge bg-soft-ash text-ash cw-wfs-tag-btn" data-tag-id="<?php echo esc_attr( $term->term_id ); ?>">
					<?php echo esc_html( $term->name ); ?>
				</span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<div id="cw-wfs-grid-results">
			<?php if ( have_posts() ) : ?>
			<div class="row <?php echo esc_attr( $grid_gap ); ?>">
				<?php while ( have_posts() ) : the_post();
					cw_wfs_render_card( get_the_ID() );
				endwhile; ?>
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

<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

<script>
(function () {
	var catBtns     = document.querySelectorAll('.cw-wfs-cat-filters .filter-item');
	var tagBtns     = document.querySelectorAll('.cw-wfs-tag-filters .cw-wfs-tag-btn');
	var resultsWrap = document.getElementById('cw-wfs-grid-results');

	var activeCatId = 0;
	var activeTagId = 0;
	var SCROLL_SPEED = 120; // px per second

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
			wrap.addEventListener('mouseenter', function () {
				var d = getScrollDist();
				if (d > 0) scrollTo(img, -Math.round(d * 0.9));
			});
			wrap.addEventListener('mouseleave', function () { scrollTo(img, 0); });
		});
	}

	initScreenScroll();

	function fetchFiltered() {
		if (!resultsWrap || typeof fetch_vars === 'undefined') return;
		resultsWrap.style.opacity       = '0.5';
		resultsWrap.style.pointerEvents = 'none';

		var filters = {};
		if (activeCatId) filters.website_category = activeCatId;
		if (activeTagId) filters.website_tag       = activeTagId;

		var body = new FormData();
		body.append('action',     'fetch_action');
		body.append('nonce',      fetch_vars.nonce);
		body.append('actionType', 'filterPosts');
		body.append('params',     JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_1', filters: filters }));

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
				if (resultsWrap) {
					resultsWrap.style.opacity       = '';
					resultsWrap.style.pointerEvents = '';
				}
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
			tagBtns.forEach(function (b) {
				b.classList.remove('active', 'bg-primary', 'text-white');
				b.classList.add('bg-soft-ash', 'text-ash');
			});
			btn.classList.remove('bg-soft-ash', 'text-ash');
			btn.classList.add('active', 'bg-primary', 'text-white');
			fetchFiltered();
		});
	});
})();
</script>

<?php get_footer(); ?>
