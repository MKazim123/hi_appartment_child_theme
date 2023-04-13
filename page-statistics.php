<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
/**
 * @var $advanced_search_form
 * @var $advanced_search_layout
 * @var $advanced_search_sticky
 * @var $css_classes
 */
if ( ! is_user_logged_in() ) {
	global $wp;
	$current_url = home_url( $wp->request );
	wp_redirect( ere_get_permalink( 'login' ) . '?redirect_to='.urlencode($current_url) );

} else {
	G5ERE()->get_template( 'header.php' );
	?>
<div class="g5ere__page-wrapper g5ere__page-dashboard-wrapper">
	<div class="d-flex flex-wrap flex-xl-nowrap">
		<div class="g5ere__db-sidebar py-3">
			<?php ere_get_template( 'global/dashboard-menu.php' ); ?>
		</div>
		<div class="g5ere__page-content">
			<?php G5ERE()->get_template( 'header/vt-dashboard-header.php' ); ?>
			<main id="content" class="g5ere__page-main stats-page-main">
				<div class="container-fluid p-xl-5">
					<?php
						// while ( have_posts() ) : the_post();
							// the_content();
						// endwhile; // End of the loop.
						?>
					<!-- <div class="stats_graph_section">
						<div class="row">
							<div class="col-md-6">
								<h3 class="stats_heading_3">Total Views</h3>
								<div class="card mb-3 earning-card" style="max-width: 18rem;">
									<div class="card-header earning-header">My Listing Views</div>
									<div class="card-body text-dark">
										<h5 class="card-title text-center">Total Views</h5> -->
										<?php
										$author_id = get_current_user_id();
										$total_views = 0;
										$args2 = array(
											'author' => $author_id,
											'post_type' => 'property',
											'post_status' => 'publish',
											'posts_per_page' => -1
										);
										$views_count_posts = new WP_Query( $args2 );
										if( $views_count_posts->have_posts() ) {
											while( $views_count_posts->have_posts() ) {
												$views_count_posts->the_post();
												$single_property_view = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : 0;
												$total_views = $total_views + $single_property_view;
											}
										}
										wp_reset_postdata();
										?>
										<!-- <p class="card-text total-earning text-center"><i class="fal fa-eye mr-3"></i><?php echo $total_views; ?></p>
									</div>
								</div>
							</div>
						</div>
					</div> -->
					<style>
						#test_bar_chart{
							width: 550px;
							height: 550px;
							margin: 0 auto;	
						}
						@media(max-width: 1024px){
							#test_bar_chart{
								width: 100%;
								height: 100%;
								margin: unset;	
							}
						}
						@media(max-width: 767px){
							.stats_graph_section .total-earning{
								margin-top: 15px;
							}
							.stats_heading_3{
								font-size: 28px;
							}
						}
					</style>
					<div class="stats_graph_section mt-0">
						<div class="row">
							<div class="col-md-12">
								<h3 class="stats_heading_3">Total Views (<?php echo $total_views; ?>)</h3>
								<div id="test_bar_chart">
									<canvas id="bar_chart"></canvas>
								</div>
							</div>
							<div class="col-md-12">
								<h3 class="stats_heading_3 total-earning">Total Earnings</h3>
								<div id="test_line_chart">
									<canvas id="line_chart"></canvas>
								</div>
							</div>
						</div>
					</div>

					
					<div class="active_listings_container mt-5">
						<h3 class="stats_heading_3">Active Listings</h3>
						<div class="table-responsive">
							<table class="table bg-white">
								<thead>
									<tr>
										<th class="align-middle">Listing Title</th>
										<th class="align-middle">Date Published</th>
										<th class="align-middle">Status</th>
										<th class="align-middle">Views</th>
										<th class="align-middle">Share</th>
										<th class="align-middle">Comments</th>
										<th class="align-middle">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$author_id = get_current_user_id();
									$args = array(
										'author' => $author_id,
										'post_type' => 'property',
										'post_status' => 'publish',
										'orderby'   => array(
											'date' =>'DESC',
										)
									);
									$author_posts = new WP_Query( $args );
									if( $author_posts->have_posts() ) {
										while( $author_posts->have_posts() ) { 
											$share_links = "";
											$author_posts->the_post();
											$property_views = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : '0';
											echo '
											<tr>
												<td class="align-middle">'.get_the_title().'</td>
												<td class="align-middle">'.date_i18n( get_option( 'date_format' ), strtotime( get_the_date() ) ).'</td>
												<td class="align-middle"><span class="badge badge-info">Published</span></td>
												<td class="align-middle">'.$property_views.'</td>
												<td class="align-middle">'.do_shortcode('[Sassy_Social_Share]').'</td>
												<td class="align-middle">'.get_comments_number( '0', '1', '%' ).'</td>
												<td class="align-middle" data-title="Action">
													<a class="btn-action" data-toggle="tooltip" data-placement="bottom" title=""
													href="'.get_permalink().'"
													data-original-title="View Property">
													<i class="fal fa-eye"></i></a>
												</td>
											</tr>
											';
										}
										wp_reset_postdata();
									}
									?>
								</tbody>
							</table>	
						</div>
					</div>

				</div>
				<?php
                        //  get_footer('hi-appart-footer'); 

					$content_block = G5CORE()->options()->footer()->get_option( 'footer_content_block' );
					?>
				<footer id="site-footer" class="">
					<div class="container">
						<?php 
								echo g5core_get_content_block( $content_block );
								G5ERE()->get_template( 'footer.php' ); 
							?>
					</div>
				</footer>
			</main>
		</div>
	</div>

</div>
<?php
}





?>