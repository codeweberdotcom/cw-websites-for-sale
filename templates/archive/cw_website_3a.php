<?php
/**
 * Archive: Websites For Sale — Template 3a (Violet Accent Card)
 */

get_header();
if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}

$cat_terms = get_terms( [ 'taxonomy' => 'website_category', 'hide_empty' => true, 'orderby' => 'name' ] );
$has_cats  = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms );
$tag_terms = get_terms( [ 'taxonomy' => 'website_tag', 'hide_empty' => true, 'orderby' => 'name' ] );
$has_tags  = ! empty( $tag_terms ) && ! is_wp_error( $tag_terms );
$grid_gap  = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'grid-gap' ) : 'gx-md-8 gy-10 gy-md-13';

global $wp_query;
$per_page   = (int) ( $wp_query->query_vars['posts_per_page'] ?? get_option( 'posts_per_page', 9 ) );
if ( $per_page <= 0 ) $per_page = (int) get_option( 'posts_per_page', 9 );
$total      = (int) $wp_query->found_posts;
$has_more   = $total > $per_page;
$block_attrs = wp_json_encode( [
	'card_slug' => 'card-3a',
	'per_page'  => $per_page,
	'grid_gap'  => $grid_gap,
	'col_class' => 'col-md-6 col-xl-4',
	'filters'   => [],
] );
?>

<section id="content-wrapper" class="wrapper">
	<div class="container py-14 py-md-16">

		<?php if ( $has_cats || $has_tags ) : ?>
		<div class="cw-wfs-filters mb-10">
			<?php if ( $has_cats ) : ?>
			<div class="isotope-filter filter cw-wfs-cat-filters mb-4">
				<ul>
					<li><a class="filter-item active has-ripple" data-cat-id="0"><?php esc_html_e( 'All', 'cw-websites-for-sale' ); ?></a></li>
					<?php foreach ( $cat_terms as $term ) : ?>
					<li><a class="filter-item has-ripple" data-cat-id="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<?php if ( $has_tags ) : ?>
			<div class="d-flex flex-wrap gap-2 cw-wfs-tag-filters">
				<span class="badge bg-primary cw-wfs-tag-btn active has-ripple" data-tag-id="0"><?php esc_html_e( 'All tags', 'cw-websites-for-sale' ); ?></span>
				<?php foreach ( $tag_terms as $term ) : ?>
				<span class="badge bg-soft-ash text-ash cw-wfs-tag-btn has-ripple" data-tag-id="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div id="cw-wfs-grid-results">
		<?php if ( have_posts() ) : ?>
		<div class="cwgb-load-more-container"
		     data-block-id="cw-wfs-3a"
		     data-block-type="cw_website"
		     data-block-attributes="<?php echo esc_attr( $block_attrs ); ?>"
		     data-current-offset="<?php echo esc_attr( $per_page ); ?>"
		     data-load-count="<?php echo esc_attr( $per_page ); ?>"
		     data-post-id="0">
			<div class="cwgb-load-more-items row <?php echo esc_attr( $grid_gap ); ?>">
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="col-md-6 col-xl-4">
				<?php cw_wfs_include_card( get_the_ID(), 'card-3a' ); ?>
			</div>
			<?php endwhile; ?>
			</div>
			<?php if ( $has_more ) : ?>
			<div class="d-flex justify-content-center mt-10">
				<button class="btn btn-outline-primary cwgb-load-more-btn"><?php esc_html_e( 'Show more', 'cw-websites-for-sale' ); ?></button>
			</div>
			<?php endif; ?>
		</div>

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
		body.append( 'params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_3a', filters: filters }) );
		fetch( fetch_vars.ajaxurl, { method: 'POST', body: body } )
			.then( function(r) { return r.json(); } )
			.then( function(data) { if ( data.status === 'success' && resultsWrap ) resultsWrap.innerHTML = data.data.html; } )
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
			tagBtns.forEach( function(b) { b.classList.remove('active','bg-primary','text-white'); b.classList.add('bg-soft-ash','text-ash'); } );
			btn.classList.remove('bg-soft-ash','text-ash');
			btn.classList.add('active','bg-primary','text-white');
			fetchFiltered();
		});
	});
})();
</script>

<?php get_footer(); ?>
