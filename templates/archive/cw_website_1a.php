<?php
/**
 * Archive: Websites For Sale — Template 1a (Browser Frame Card)
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
.cw-wfs-browser   { height:30px; background:#f8fafc; border-bottom:1px solid #eef2f7; }
.cw-wfs-dot       { width:9px; height:9px; flex-shrink:0; }
.cw-wfs-dot-r     { background:#f87171; }
.cw-wfs-dot-y     { background:#fbbf24; }
.cw-wfs-dot-g     { background:#34d399; }
.cw-wfs-frame     { position:relative; overflow:hidden; }
.cw-wfs-sc        { height:168px; }
.cw-wfs-sc img    { width:100%; height:100%; object-fit:cover; object-position:top center; display:block; }
.cw-wfs-ov        { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(15,23,42,.55); opacity:0; transition:opacity .18s; }
.cw-wfs-frame:hover .cw-wfs-ov { opacity:1; }
.cw-wfs-cat       { display:inline-flex; align-items:center; height:24px; padding:0 10px; border-radius:6px; font-size:12px; font-weight:700; }
.cw-wfs-price     { font-size:22px; }
.cw-btn-indigo    { background:#4f46e5; color:#fff; border-color:#4f46e5; height:42px; display:flex; align-items:center; justify-content:center; }
.cw-btn-indigo:hover { background:#4338ca; color:#fff; }
.cw-btn-sec       { height:42px; display:flex; align-items:center; justify-content:center; }
.cw-btn-grid      { display:grid; grid-template-columns:1fr auto; gap:8px; }
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
			$cms         = get_post_meta( $post_id, '_ws_cms', true ) ?: 'WordPress';
			$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';
			$permalink   = get_permalink();
			$url_display = $website_url ? preg_replace( '#^https?://#', '', rtrim( $website_url, '/' ) ) : '';
			$cats        = get_the_terms( $post_id, 'website_category' );
			$cat_name    = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
			$cat_c       = ( $cats && ! is_wp_error( $cats ) ) ? $cat_colors[ $cats[0]->term_id % 6 ] : $cat_colors[0];
			$st          = $status_cfg[ $status ] ?? $status_cfg['for_sale'];
		?>
		<div class="col-md-6 col-xl-4">
			<div class="card shadow-sm border rounded-3 overflow-hidden h-100">
				<div class="cw-wfs-browser d-flex align-items-center gap-2 px-3">
					<span class="cw-wfs-dot cw-wfs-dot-r rounded-circle"></span>
					<span class="cw-wfs-dot cw-wfs-dot-y rounded-circle"></span>
					<span class="cw-wfs-dot cw-wfs-dot-g rounded-circle"></span>
					<?php if ( $url_display ) : ?>
					<span class="flex-grow-1 text-truncate ms-2 text-muted" style="font-family:ui-monospace,monospace;font-size:11px;"><?php echo esc_html( $url_display ); ?></span>
					<?php endif; ?>
				</div>
				<div class="cw-wfs-frame cw-wfs-sc">
					<?php if ( $screenshot ) :
						echo wp_get_attachment_image( $screenshot, 'full', false, [ 'alt' => esc_attr( $title ) ] );
					else : ?>
					<div class="w-100 h-100 bg-soft-ash"></div>
					<?php endif; ?>
					<span class="badge <?php echo esc_attr( $st['class'] ); ?> position-absolute top-0 start-0 m-2" style="z-index:2;"><?php echo $st['label']; ?></span>
					<?php if ( $website_url ) : ?>
					<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener" class="cw-wfs-ov text-decoration-none">
						<span class="rounded-pill fw-bold px-4 py-2 bg-white text-dark" style="font-size:14px;"><i class="uil uil-play-circle me-2"></i><?php esc_html_e( 'Live Preview', 'cw-websites-for-sale' ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="card-body d-flex flex-column gap-3 p-4">
					<div class="d-flex align-items-center gap-2">
						<?php if ( $cat_name ) : ?>
						<span class="cw-wfs-cat" style="background:<?php echo esc_attr( $cat_c['bg'] ); ?>;color:<?php echo esc_attr( $cat_c['fg'] ); ?>;"><?php echo esc_html( $cat_name ); ?></span>
						<?php endif; ?>
						<span class="small text-muted"><?php echo esc_html( $cms ); ?></span>
					</div>
					<h3 class="h6 fw-bold text-dark mb-0"><?php echo esc_html( $title ); ?></h3>
					<div class="mt-auto pt-3 border-top">
						<?php if ( $price ) : ?>
						<div class="fw-bold text-dark cw-wfs-price"><?php echo esc_html( $price ); ?> <span class="text-muted" style="font-size:15px;">₽</span></div>
						<?php endif; ?>
						<?php if ( $launch_time ) : ?>
						<div class="small text-muted mt-1"><?php echo esc_html__( 'Term:', 'cw-websites-for-sale' ) . ' ' . esc_html( $launch_time ); ?></div>
						<?php endif; ?>
						<div class="cw-btn-grid mt-3">
							<a href="<?php echo esc_url( $permalink ); ?>" class="btn btn-indigo rounded-2 fw-bold"><?php esc_html_e( 'Details', 'cw-websites-for-sale' ); ?></a>
							<?php if ( $website_url ) : ?>
							<a href="<?php echo esc_url( $website_url ); ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary rounded-2 fw-bold cw-btn-sec px-3"><?php esc_html_e( 'Preview', 'cw-websites-for-sale' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
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

<?php get_template_part( 'templates/components/cw-preview-modal' ); ?>

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
		body.append( 'params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_1a', filters: filters }) );
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
