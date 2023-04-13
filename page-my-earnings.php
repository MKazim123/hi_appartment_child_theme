<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
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
                <main id="content" class="g5ere__page-main earning-page-main">
                    <div class="container-fluid p-xl-5 p-md-3 p-sm-3">
						<?php
						// while ( have_posts() ) : the_post();
							// the_content();
						// endwhile; // End of the loop.
						?>
                        <div class="card mb-3 earning-card" style="max-width: 18rem;">
                            <div class="card-header earning-header">My Earnings</div>
                            <div class="card-body text-dark">
                                <h5 class="card-title">Total Earnings</h5>
								<?php
									global $wpdb;
									$author_id = get_current_user_id();
									$sql = "SELECT SUM(earnings) AS total_earnings FROM wp_pro_earnings WHERE `user_id`=".$author_id." AND `earn_type`='listing'";
									$results = $wpdb->get_results($sql);
									if($results[0]->total_earnings){
										echo '<p class="card-text total-earning">$'.$results[0]->total_earnings.'</p>';
									}
									else{
										echo '<p class="card-text total-earning">$0</p>';
									}
								?>
                                
                            </div>
                        </div>
                        <div class="active_listings_container mt-5">
						<h3 class="stats_heading_3">Earning Details</h3>
						<div class="table-responsive">
							<table class="table bg-white">
								<thead>
									<tr>
										<th class="align-middle">Earning Type</th>
										<th class="align-middle">Earning Date</th>
										<th class="align-middle">Earning</th>
									</tr>
								</thead>
								<tbody>
									<?php
									global $wpdb;
									$author_id = get_current_user_id();
									$sql = "SELECT * FROM wp_pro_earnings WHERE `user_id`=".$author_id." AND `earn_type`='listing'";
									$results = $wpdb->get_results($sql);
									if(!empty($results)){
										foreach( $results as $result ) {
											$earn_type = $result->earn_type == 'listing' ? 'Property Listing' : 'Social Sharing';
											echo '
												<tr>
													<td class="align-middle">'.$earn_type.'</td>
													<td class="align-middle">'.date_i18n( get_option( 'date_format' ), strtotime( $result->earning_date ) ).'</td>
													<td class="align-middle">'.$result->earnings.'</td>
												</tr>
												';
										}
									}
									else{
										echo '
											<tr>
												<td class="align-middle">No Earnings Yet.</td>
											</tr>
											';
									}
									
									// if( $author_posts->have_posts() ) {
									// 	while( $author_posts->have_posts() ) { 
									// 		$author_posts->the_post();
									// 		$property_views = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : '0';
									// 		echo '
									// 		<tr>
									// 			<td class="align-middle">'.get_the_title().'</td>
									// 			<td class="align-middle">'.date_i18n( get_option( 'date_format' ), strtotime( get_the_date() ) ).'</td>
									// 			<td class="align-middle"><span class="badge badge-info">Published</span></td>
									// 			<td class="align-middle">'.$property_views.'</td>
									// 			<td class="align-middle">20</td>
									// 			<td class="align-middle">'.get_comments_number( '0', '1', '%' ).'</td>
									// 			<td class="align-middle" data-title="Action">
									// 				<a class="btn-action" data-toggle="tooltip" data-placement="bottom" title=""
									// 				href="'.get_permalink().'"
									// 				data-original-title="View Property">
									// 				<i class="fal fa-eye"></i></a>
									// 			</td>
									// 		</tr>
									// 		';
									// 	}
									// 	wp_reset_postdata();
									// }
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