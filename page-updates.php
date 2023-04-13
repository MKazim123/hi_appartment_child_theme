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
				<?php 
                G5ERE()->get_template( 'header/vt-dashboard-header.php' ); 
                ?>
                <main id="content" class="g5ere__page-main">
                    <div class="container-fluid p-xl-5">
                        <div class="vt_updates_container">
                            <h3 class="vt_updates_heading">Recent Updates</h3>
                            
                            <?php
                            $today = getdate();
                                $data_to_return = '<div class="get_vt_news">';
                                $args = array(  
                                    'post_type' => 'news',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1, 
                                    'orderby' => 'date', 
                                    'order' => 'DESC', 
                                    'date_query' => array(
                                        array(
                                            'year'  => $today['year'],
                                            'month' => $today['mon'],
                                            'day'   => $today['mday'],
                                        )
                                    ),
                                );
                            
                                $loop = new WP_Query( $args ); 
                                if($loop->have_posts()){
                                    while ( $loop->have_posts() ) : $loop->the_post(); 
                                        // $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-news-thumbnail' );
                                        $data_to_return .= '
                                        <div class="vt_news">
                                            <div class="sec_left">
                                                <h4 class="news_title">'.get_the_title().'</h4>
                                                <span  class="news_date">'.get_the_date().'</span>
                                                <span class="vt_separator"></span>
                                                '.get_the_content().'
                                            </div>
                                        </div>
                                        ';
                                    endwhile;
                                }
                                else{
                                    $data_to_return .= '<p>No Recent Updates.</p>'; 
                                }
                                
                            
                                wp_reset_postdata(); 
                            
                                $data_to_return .= '</div>';
                            
                                echo $data_to_return;
                            ?>
                        </div>
                        <div class="vt_updates_container">
                            <h3 class="vt_updates_heading">Past Updates</h3>
                            <?php
                                $data_to_return = '<div class="get_vt_news">';
                                $args = array(  
                                    'post_type' => 'news',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1, 
                                    'orderby' => 'date', 
                                    'order' => 'DESC', 
                                    'date_query' => array(
                                        array(
                                            'before' => date("Y-m-d")
                                        )
                                    ),
                                );
                            
                                $loop = new WP_Query( $args ); 
                                $i = 1;   
                                while ( $loop->have_posts() ) : $loop->the_post(); 
                                    // $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-news-thumbnail' );
                                    $data_to_return .= '
                                    <div class="vt_news">
                                        <div class="sec_left">
                                            <h4 class="news_title">'.get_the_title().'</h4>
                                            <span  class="news_date">'.get_the_date().'</span>
                                            <span class="vt_separator"></span>
                                            '.get_the_content().'
                                        </div>
                                    </div>
                                    ';
                                endwhile;
                            
                                wp_reset_postdata(); 
                            
                                $data_to_return .= '</div>';
                            
                                echo $data_to_return;
                            ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
	<?php
}
G5ERE()->get_template( 'footer.php' );


?>