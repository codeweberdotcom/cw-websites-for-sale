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

$status_cfg = [
	'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ), 'class' => 'bg-success' ],
	'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),     'class' => 'bg-secondary' ],
	'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ), 'class' => 'bg-warning text-dark' ],
];
?>
<style>
#content-wrapper.cw-wfs-3b-page { background:#0b1120; }
.cw-wfs-3b-card  { background:#171f30; border:1px solid #26324a; border-radius:18px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 8px 24px rgba(0,0,0,.4); transition:transform .18s,box-shadow .18s,border-color .18s; }
.cw-wfs-3b-card:hover { transform:translateY(-4px); box-shadow:0 22px 48px rgba(0,0,0,.6); border-color:#818cf8; }
.cw-wfs-3b-wrap  { margin:10px 10px 0; border-radius:12px; overflow:hidden; position:relative; height:176px; }
.cw-wfs-3b-wrap img { width:100%; height:100%; object-fit:cover; object-position:top center; display:block; }
.cw-wfs-3b-cat   { position:absolute; top:10px; left:10px; display:inline-flex; align-items:center; height:26px; padding:0 11px; border-radius:7px; font-size:12px; font-weight:700; box-shadow:0 2px 8px rgba(0,0,0,.35); z-index:2; }
.cw-wfs-3b-ov    { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(11,17,32,.6); opacity:0; transition:opacity .18s; }
.cw-wfs-3b-wrap:hover .cw-wfs-3b-ov { opacity:1; }
.cw-wfs-3b-price { font-size:22px; font-weight:800; color:#a5b4fc; line-height:1; }
.cw-wfs-3b-btn   { display:flex; align-items:center; justify-content:center; height:46px; border-radius:11px; background:#fff; color:#0f172a; font-weight:700; font-size:15px; text-decoration:none; }
.cw-wfs-3b-btn:hover { background:#e2e8f0; color:#0f172a; }
.cw-wfs-3b-btn-sec { display:flex; align-items:center; justify-content:center; height:46px; padding:0 16px; border-radius:11px; border:1px solid #334155; color:#cbd5e1; font-weight:700; font-size:15px; text-decoration:none; white-space:nowrap; }
.cw-wfs-3b-btn-sec:hover { border-color:#a5b4fc; color:#fff; }
.cw-btn-grid-3b  { display:grid; grid-template-columns:1fr auto; gap:8px; }
.cw-wfs-3b-filter-bar .filter-item { color:#94a3b8; }
.cw-wfs-3b-filter-bar .filter-item:hover,.cw-wfs-3b-filter-bar .filter-item.active { color:#a5b4fc; border-color:#818cf8; }
</style>

<section id="content-wrapper" class="wrapper cw-wfs-3b-page">
	<div class="container py-14 py-md-16">

		<?php if ( $has_cats || $has_tags ) : ?>
		<div class="cw-wfs-filters mb-10">
			<?php if ( $has_cats ) : ?>
			<div class="isotope-filter filter cw-wfs-cat-filters cw-wfs-3b-filter-bar mb-4">
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
		<div class="col-md-6 col-xl-4">
			<div class="cw-wfs-3b-card h-100">
				<div class="cw-wfs-3b-wrap">
					<?php if ( $screenshot ) :
						echo wp_get_attachment_image( $screenshot, 'full', false, [ 'alt' => esc_attr( $title ) ] );
					else : ?>
					<div class="w-100 h-100" style="background:#26324a;"></div>
					<?php endif; ?>
					<?php if ( $cat_name ) : ?>
					<span class="cw-wfs-3b-cat" style="background:<?php echo esc_attr( $cat_c['bg'] ); ?>;color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
					<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 end-0 m-2" style="z-index:3;"><?php echo $st['label']; ?></span>
					<?php if ( $website_url ) : ?>
					<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener" class="cw-wfs-3b-ov text-decoration-none">
						<span class="rounded-pill fw-bold px-4 py-2" style="background:#818cf8;color:#0b1120;font-size:14px;"><i class="uil uil-play-circle me-2"></i><?php esc_html_e( 'Live Preview', 'cw-websites-for-sale' ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="d-flex flex-column gap-3 p-4 flex-grow-1">
					<h3 class="mb-0 fw-bold" style="font-size:19px;color:#f1f5f9;letter-spacing:-.02em;line-height:1.2;"><?php echo esc_html( $title ); ?></h3>
					<div class="d-flex align-items-baseline gap-2">
						<?php if ( $price ) : ?>
						<span class="cw-wfs-3b-price"><?php echo esc_html( $price ); ?> ₽</span>
						<?php endif; ?>
						<?php if ( $launch_time ) : ?>
						<span class="small fw-semibold" style="color:#64748b;">· <?php echo esc_html( $launch_time ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<div class="cw-btn-grid-3b px-4 pb-4">
					<a href="<?php echo esc_url( $permalink ); ?>" class="cw-wfs-3b-btn"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
					<?php if ( $website_url ) : ?>
					<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener" class="cw-wfs-3b-btn-sec"><i class="uil uil-play-circle me-1"></i><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?></a>
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
		<p style="color:#94a3b8;"><?php esc_html_e( 'No websites found.', 'cw-websites-for-sale' ); ?></p>
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
		body.append( 'params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_3b', filters: filters }) );
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
