<?php
/**
 * Archive: Websites For Sale — Template 3 (Overlay Cards)
 * services_4 pattern: overlay-5, fixed bottom title, hover description.
 */

get_header();

if ( function_exists( 'get_pageheader' ) ) {
	get_pageheader();
}
?>
<style>
.cw-it-screen    { height: 300px; }
.cw-it-screenshot { transform: translateY(0); transition: transform 0.8s ease; }
.cw-it-screenshot-placeholder { height: 300px; }
</style>
<?php

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
		<?php if ( have_posts() ) : ?>

		<?php
		$grid_gap = class_exists( 'Codeweber_Options' ) ? Codeweber_Options::style( 'grid-gap' ) : 'gx-md-8 gy-10 gy-md-13';
		echo '<div class="row ' . esc_attr( $grid_gap ) . '">';

		while ( have_posts() ) :
			the_post();
			$post_id    = get_the_ID();
			$title      = get_post_meta( $post_id, '_alt_title', true ) ?: get_the_title();
			$website_url = get_post_meta( $post_id, '_ws_url', true );
			$screenshot  = (int) get_post_meta( $post_id, '_ws_screenshot', true );
			$price       = get_post_meta( $post_id, '_ws_price', true );
			$launch_time = get_post_meta( $post_id, '_ws_launch_time', true );
			$cms         = get_post_meta( $post_id, '_ws_cms', true );
			$status      = get_post_meta( $post_id, '_ws_status', true ) ?: 'for_sale';
			$permalink   = get_permalink();

			$cats     = get_the_terms( $post_id, 'website_category' );
			$tags     = get_the_terms( $post_id, 'website_tag' );
			$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
			$excerpt  = get_the_excerpt();
			if ( ! $excerpt && $cms ) {
				$excerpt = $cms . ( $price ? ' · ' . $price : '' );
			}

			$status_cfg = [
				'for_sale' => [ 'label' => esc_html__( 'For Sale', 'cw-websites-for-sale' ),  'class' => 'bg-success' ],
				'sold'     => [ 'label' => esc_html__( 'Sold', 'cw-websites-for-sale' ),       'class' => 'bg-secondary' ],
				'reserved' => [ 'label' => esc_html__( 'Reserved', 'cw-websites-for-sale' ),   'class' => 'bg-warning text-dark' ],
			];
			$st = $status_cfg[ $status ] ?? $status_cfg['for_sale'];

			if ( $screenshot ) {
				$img_html = '<div class="cw-it-screen overflow-hidden" data-h="300">'
					. wp_get_attachment_image( $screenshot, 'full', false, [
						'class' => 'cw-it-screenshot d-block w-100 h-auto',
						'alt'   => esc_attr( $title ),
					] )
					. '</div>';
			} else {
				$img_html = '<div class="cw-it-screen cw-it-screenshot-placeholder overflow-hidden bg-soft-ash" data-h="300"></div>';
			}
			?>
			<div class="col-md-6 col-xl-4">
				<figure class="overlay overlay-5 rounded card-interactive mb-0">
					<a href="<?php echo esc_url( $permalink ); ?>">
						<div class="bottom-overlay post-meta fs-16 position-absolute zindex-1 d-flex flex-column h-100 w-100 p-5">
							<div class="mt-auto">
								<h3 class="h5 text-white mb-1"><?php echo esc_html( $title ); ?></h3>
								<?php if ( $cat_name ) : ?>
								<div class="post-category text-white opacity-75 small mb-1"><?php echo esc_html( $cat_name ); ?></div>
								<?php endif; ?>
								<?php if ( $price ) : ?>
								<div class="fw-bold text-white opacity-90"><?php echo esc_html( $price ); ?></div>
								<?php endif; ?>
							</div>
						</div>
						<?php echo $img_html; ?>
					</a>
					<figcaption class="p-5">
						<div class="post-body h-100 d-flex flex-column from-left justify-content-end">
							<?php if ( $excerpt ) : ?>
							<p class="mb-3"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>
							<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
							<div class="d-flex flex-wrap gap-1 mb-3">
								<?php foreach ( $tags as $tag ) : ?>
								<span class="badge bg-white text-dark opacity-90"><?php echo esc_html( $tag->name ); ?></span>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<div class="d-flex align-items-center gap-3">
								<a href="<?php echo esc_url( $permalink ); ?>" class="hover more me-4">
									<?php esc_html_e( 'More details', 'cw-websites-for-sale' ); ?>
								</a>
								<?php if ( $website_url ) : ?>
								<button type="button"
									class="btn btn-sm btn-white rounded-pill btn-icon btn-icon-start has-ripple"
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
					</figcaption>
					<div class="hover_card_button_hide position-absolute top-0 end-0 p-4 zindex-10 d-flex flex-column align-items-end gap-1">
						<?php if ( $price ) : ?>
						<span class="badge bg-green rounded-pill"><?php echo esc_html( $price ); ?></span>
						<?php endif; ?>
						<?php if ( $launch_time ) : ?>
						<span class="badge bg-yellow text-yellow rounded-pill"><?php echo esc_html( $launch_time ); ?></span>
						<?php endif; ?>
					</div>
				</figure>
			</div>
			<?php
		endwhile;
		echo '</div>';
		?>

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
		body.append('params', JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_3', filters: filters }));
		fetch(fetch_vars.ajaxurl, { method: 'POST', body: body })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data.status === 'success' && resultsWrap) {
					resultsWrap.innerHTML = data.data.html;
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
