<?php
//For making child theme 
function my_theme_enqueue_styles()
{

    $parent_style = 'parent-style';
    $child_style = 'child-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style($child_style, get_stylesheet_directory_uri() . '/style.css');
    wp_enqueue_script('chart-js-script', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js', array('jquery'));
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/custom_script.js', array('jquery'), true);
    wp_localize_script(
		'custom-script',
		'opt',
		array('ajaxUrl' => admin_url('admin-ajax.php'),
		'home_url' => home_url(),
		'noResults' => esc_html__('No data found', 'textdomain'),
		)
	);
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

add_action('wp_head', 'vt_styles_in_head');
function vt_styles_in_head(){
	?>
	<style>
		.foot-col4 .foot-add:before {
            background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/image-40-Traced-2.png");?>');
        }
        
        .foot-col4 .foot-num:before {
            background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/image-39-Traced-1.png");?>');
        }
        
        .foot-col4 .foot-email:before {
            background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/image-38-Traced-2.png");?>');
            width: 28px !important;
            height: 21px !important;
        }
        .hi-hero-tagline p:after {
            background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/curve-arrow-1.png");?>');
        }
		.hi-new-search-box .ube-search-box:before{
			background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/search-heart-2.png");?>');
		}
		ul#main-menu li.current-menu-item a:before{
			background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/Rectangle-2.png");?>');
		}
		header#site-header:after{
			background-image: url('<?php echo home_url("/wp-content/uploads/2022/11/Rectangle-4.png");?>');
		}
	</style>
	<?php
}




// $args = array(
//     'post_type' => 'property',
//     'post_status' => 'publish',
//     'posts_per_page' => -1
// );
// $data = new WP_Query($args);
// while ($data->have_posts()): $data->the_post();
//     $post_id = get_the_ID();
//     $author_id = get_the_author_meta('ID');
//     $paid_submission_type = get_user_meta( $author_id, 'real_estate_free_package', true );
//     $property_date = strtotime(get_the_date("Y-m-d", $post_id));
//     $today = time();
//     $datediff = $today - $property_date;
//     $total_published_days = round($datediff / (60 * 60 * 24));
//     if($paid_submission_type == "yes"){
//         if ($total_published_days > 7) {
//             $args = array(
//                 'ID' => $post_id,
//                 'post_type' => 'property',
//                 'post_status' => 'expired'
//             );
//             wp_update_post($args);
//         }
//     } 
//     else{
//         if ($total_published_days > 45) {
//             $args = array(
//                 'ID' => $post_id,
//                 'post_type' => 'property',
//                 'post_status' => 'expired'
//             );
//             wp_update_post($args);
//         }
//     }     
// endwhile;
// wp_reset_postdata();

// $user_id = get_current_user_id();
// $paid_submission_type = get_user_meta( $user_id, 'real_estate_free_package', true );
// print_r($paid_submission_type);



function earning_after_approved( $new_status, $old_status, $post ) {
    if ( $new_status == 'publish' ) {
        $property_id = $post->ID;
        $property_earned = get_post_meta($post->ID, 'listing_cash_earned');
        $post_type = $post->post_type;
        $post_author_id = get_post_field( 'post_author', $property_id );
        // $paid_submission_type = get_user_meta( $post_author_id, 'real_estate_free_package', true );
        global $wpdb;
        if($property_earned[0] == 'no'){
            $wpdb->insert('wp_pro_earnings', array(
                'user_id' => $post_author_id,
                'earnings' => '5',
                'earn_type' => 'listing',
                'type_id' => $property_id
            ));
            update_post_meta($property_id, 'listing_cash_earned', 'yes');
        }
    }
}
add_action('transition_post_status', 'earning_after_approved', 10, 3 );



add_action( 'wp_ajax_earning_chart_front_action', 'earning_chart_front_funt' );
add_action( 'wp_ajax_nopriv_earning_chart_front_action', 'earning_chart_front_funt' );
function earning_chart_front_funt(){
    global $wpdb;
	$data_to_return = array();
    $dates_array = array();
    $earnings_array = array();
    $author_id = get_current_user_id();
    $previous_date = date("Y-m-d",strtotime("-1 month"));
    $current_date = date("Y-m-d");
    $period = new DatePeriod(
        new DateTime($previous_date),
        new DateInterval('P1D'),
        new DateTime($current_date)
    );
    foreach ($period as $key => $value) {
        // echo $value->format('Y-m-d') . '<br>';
        $period_date = $value->format('Y-m-d');
        $sql = "SELECT SUM(earnings) AS day_earning FROM wp_pro_earnings WHERE `user_id`=".$author_id." AND DATE(earning_date) = '".$period_date."'";
        $results = $wpdb->get_results($sql);
        array_push($dates_array, $value->format('d'));
        if(!empty($results)){
            array_push($earnings_array, (int)$results[0]->day_earning);
        }else{
            array_push($earnings_array, 0);
        }
    }
    $data_to_return['dates'] = $dates_array;
    $data_to_return['earnings'] = $earnings_array;
	echo json_encode($data_to_return);
	die();
}

add_action( 'wp_ajax_views_chart_front_action', 'views_chart_front_funt' );
add_action( 'wp_ajax_nopriv_views_chart_front_action', 'views_chart_front_funt' );
function views_chart_front_funt(){
    global $wpdb;
	$data_to_return = array();
    $title_array = array();
    $views_array = array();
    $author_id = get_current_user_id();

    $args2 = array(
        'author' => $author_id,
        'post_type' => 'property',
        'post_status' => 'publish',
        'meta_key' => 'real_estate_property_views_count',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 10
    );
    $views_count_posts = new WP_Query( $args2 );
    if( $views_count_posts->have_posts() ) {
        while( $views_count_posts->have_posts() ) {
            $views_count_posts->the_post();
            $single_property_view = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : 0;
            array_push($title_array, get_the_title());
            array_push($views_array, (int)$single_property_view);
        }
    }
    wp_reset_postdata();
    $data_to_return['listing_title'] = $title_array;
    $data_to_return['listing_views'] = $views_array;
	echo json_encode($data_to_return);
	die();
}

// Admin Menu

// enqueue admin scripts
function hiapartment_wp_admin_style($hook) {
    // Load only on ?page=mypluginname
    // echo $hook;
    if( $hook != 'toplevel_page_members-stats' && $hook != 'toplevel_page_members-earnings') {
            return;
    }
    wp_enqueue_style( 'admin_styles_css', get_stylesheet_directory_uri() . '/admin_style.css', array(), time(), false );
    wp_enqueue_style('bootstrap4', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css', array(), time(), false);
    wp_enqueue_script('bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', array('jquery'), time(), true);
    wp_enqueue_script('chart-js-script', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js', array('jquery'));
    wp_enqueue_script('admin_script', get_stylesheet_directory_uri() . '/admin_script.js', array('jquery'), time(), true);
    wp_localize_script(
        'admin_script',
        'opt',
        array('ajaxUrl' => admin_url('admin-ajax.php'),
        'noResults' => esc_html__('No data found', 'textdomain'),
                )
    );
}
add_action( 'admin_enqueue_scripts', 'hiapartment_wp_admin_style' );


function members_earnings_admin_menu() {
    add_menu_page(
        __( 'Members Earnings', 'my-textdomain' ),
        __( 'Members Earnings', 'my-textdomain' ),
        'manage_options',
        'members-earnings',
        'members_earnings_admin_page_contents',
        'dashicons-money'
    );
}
add_action( 'admin_menu', 'members_earnings_admin_menu' );

function members_earnings_admin_page_contents(){
    global $wpdb;
    ?>
        <div id="registration_form" style='width:100%'>
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <h1>Members Earnings</h1>
                        <select class="form-control user_earning">
                            <option>Select Member</option>
                            <?php
                            $arg = array(
                                'role_not_in' => ['administrator'],
                                'orderby' => 'user_nicename',
                                'order'   => 'ASC',
                                'fields' => 'all',
                            );
                            $members = get_users($args);
                            foreach($members as $member){
                                if($member->roles[0] != 'administrator'){
                                    echo '<option value="'.$member->ID.'">'.$member->display_name.'</option>';
                                }
                            }
                            // echo '<pre>';
                            // print_r($members);
                            // echo '</pre>';
                            
                            ?>
                        </select>
                        
                        <div class="earnings_results">

                        </div>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    <?php
}

add_action( 'wp_ajax_earning_page_admin_action', 'earning_page_admin_funt' );
add_action( 'wp_ajax_nopriv_earning_page_admin_action', 'earning_page_admin_funt' );
function earning_page_admin_funt(){
    global $wpdb;
	$data_to_return = "";
    $user_id = $_POST['user_id'];

    $data_to_return = '
        <div class="card mb-3 earning-card" style="max-width: 18rem; padding: 0px;">
            <div class="card-header earning-header">Members Earnings</div>
            <div class="card-body text-dark">
                <h5 class="card-title">Total Earnings</h5>
    ';
    $sql = "SELECT SUM(earnings) AS total_earnings FROM wp_pro_earnings WHERE `user_id`=".$user_id."";
    $results = $wpdb->get_results($sql);
    if($results[0]->total_earnings){
        $data_to_return .= '<h4 class="card-text total-earning">$'.$results[0]->total_earnings.'</h4>';
    }
    else{
        $data_to_return .= '<h4 class="card-text total-earning">$0</h4>';
    }
    $data_to_return .= '
            </div>
        </div>';

    $data_to_return .= '
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
    ';
    $sql = "SELECT * FROM wp_pro_earnings WHERE `user_id`=".$user_id." AND `earn_type`='listing'";
    $results = $wpdb->get_results($sql);
    if(!empty($results)){
        foreach( $results as $result ) {
            $earn_type = $result->earn_type == 'listing' ? 'Property Listing' : 'Social Sharing';
            $data_to_return .= '
                <tr>
                    <td class="align-middle">'.$earn_type.'</td>
                    <td class="align-middle">'.date_i18n( get_option( 'date_format' ), strtotime( $result->earning_date ) ).'</td>
                    <td class="align-middle">'.$result->earnings.'</td>
                </tr>
                ';
        }
    }
    else{
        $data_to_return .= '
            <tr>
                <td class="align-middle">No Earnings Yet.</td>
            </tr>
            ';
    }

    $data_to_return .= '
                    </tbody>
                </table>	
            </div>
        </div>
    ';

    echo $data_to_return;
    die();
}





function members_stats_admin_menu() {
    add_menu_page(
        __( 'Members Stats', 'my-textdomain' ),
        __( 'Members Stats', 'my-textdomain' ),
        'manage_options',
        'members-stats',
        'members_stats_admin_page_contents',
        'dashicons-chart-line'
    );
}
add_action( 'admin_menu', 'members_stats_admin_menu' );

function members_stats_admin_page_contents(){
    global $wpdb;
    ?>
        <div id="registration_form" style='width:100%'>
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <h1>Members Stats</h1>
                        <select class="form-control user_stats">
                            <option>Select Member</option>
                            <?php
                            $arg = array(
                                'role_not_in' => ['administrator'],
                                'orderby' => 'user_nicename',
                                'order'   => 'ASC',
                                'fields' => 'all',
                            );
                            $members = get_users($args);
                            foreach($members as $member){
                                if($member->roles[0] != 'administrator'){
                                    echo '<option value="'.$member->ID.'">'.$member->display_name.'</option>';
                                }
                            }
                            
                            ?>
                        </select>
                        
                        <div class="stats_results">

                        </div>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    <?php
}

add_action( 'wp_ajax_stats_page_admin_action', 'stats_page_admin_funt' );
add_action( 'wp_ajax_nopriv_stats_page_admin_action', 'stats_page_admin_funt' );
function stats_page_admin_funt(){
    global $wpdb;
	$data_to_return = "";
    $user_id = $_POST['user_id'];

    $total_views = 0;
    $args2 = array(
        'author' => $user_id,
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
    $data_to_return .= '
        <div class="stats_graph_section mt-5">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="stats_heading_3">Total Views ('.$total_views.')</h3>
                    <div id="test_bar_chart">
                        <canvas id="bar_chart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="stats_heading_3">Total Earnings</h3>
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
    ';
    $args = array(
        'author' => $user_id,
        'post_type' => 'property',
        'post_status' => 'publish',
        'orderby'   => array(
            'date' =>'DESC',
        )
    );
    $author_posts = new WP_Query( $args );
    if( $author_posts->have_posts() ) {
        while( $author_posts->have_posts() ) { 
            $author_posts->the_post();
            $property_views = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : '0';
            $data_to_return .= '
            <tr>
                <td class="align-middle">'.get_the_title().'</td>
                <td class="align-middle">'.date_i18n( get_option( 'date_format' ), strtotime( get_the_date() ) ).'</td>
                <td class="align-middle"><span class="badge badge-info">Published</span></td>
                <td class="align-middle">'.$property_views.'</td>
                <td class="align-middle">20</td>
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

    $data_to_return .= '
                    </tbody>
                </table>	
            </div>
        </div>
    ';
    

    echo $data_to_return;
    die();
}


add_action( 'wp_ajax_get_graphs_page_admin_action', 'get_graphs_page_admin_funt' );
add_action( 'wp_ajax_nopriv_get_graphs_page_admin_action', 'get_graphs_page_admin_funt' );
function get_graphs_page_admin_funt(){
    global $wpdb;
	$data_to_return = array();
    $author_id = $_POST['user_id'];

    $dates_array = array();
    $earnings_array = array();
    $previous_date = date("Y-m-d",strtotime("-1 month"));
    $current_date = date("Y-m-d");
    $period = new DatePeriod(
        new DateTime($previous_date),
        new DateInterval('P1D'),
        new DateTime($current_date)
    );
    foreach ($period as $key => $value) {
        // echo $value->format('Y-m-d') . '<br>';
        $period_date = $value->format('Y-m-d');
        $sql = "SELECT SUM(earnings) AS day_earning FROM wp_pro_earnings WHERE `user_id`=".$author_id." AND DATE(earning_date) = '".$period_date."'";
        $results = $wpdb->get_results($sql);
        array_push($dates_array, $value->format('d'));
        if(!empty($results)){
            array_push($earnings_array, (int)$results[0]->day_earning);
        }else{
            array_push($earnings_array, 0);
        }
    }
    $data_to_return['dates'] = $dates_array;
    $data_to_return['earnings'] = $earnings_array;


    // Doughnut Chart
    $title_array = array();
    $views_array = array();
    $args2 = array(
        'author' => $author_id,
        'post_type' => 'property',
        'post_status' => 'publish',
        'meta_key' => 'real_estate_property_views_count',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 10
    );
    $views_count_posts = new WP_Query( $args2 );
    if( $views_count_posts->have_posts() ) {
        while( $views_count_posts->have_posts() ) {
            $views_count_posts->the_post();
            $single_property_view = get_post_meta(get_the_ID(), 'real_estate_property_views_count') ? get_post_meta(get_the_ID(), 'real_estate_property_views_count')[0] : 0;
            array_push($title_array, get_the_title());
            array_push($views_array, (int)$single_property_view);
        }
    }
    wp_reset_postdata();
    $data_to_return['listing_title'] = $title_array;
    $data_to_return['listing_views'] = $views_array;



	echo json_encode($data_to_return);
	die();
}


// social share earnings
add_action( 'wp_ajax_social_earning_action', 'social_earning_funt' );
add_action( 'wp_ajax_nopriv_social_earning_action', 'social_earning_funt' );
function social_earning_funt(){
    global $wpdb;
	$data_to_return = "";
    $sharing_platform = $_POST['sharing_platform'];
    $listing_url = $_POST['listing_url'];
    $user_id = get_current_user_id();
    $listing_id = url_to_postid($listing_url);
    $paid_submission_type = get_user_meta( $user_id, 'real_estate_free_package', true );
    $wpdb->insert('wp_social_sharing', array(
        'user_id' => $user_id,
        'listing_id' => $listing_id,
        'sharing_platform' => $sharing_platform,
    ));
    echo 'success';
    die();
}


add_action( 'check_for_social_sharing', 'check_for_social_sharing_funt' );
function check_for_social_sharing_funt(){
    // code for cron job
    global $wpdb;
    $all_users = get_users( array( 'fields' => array( 'ID' ) ) );
    foreach($all_users as $user){
        $sharing_arr = array();
        $go_ahead = 1;
        $user_id = $user->ID;
        $sql_count = "SELECT COUNT(*) as `date_count`, date(sharing_date) as `sharing_date` FROM `wp_social_sharing` WHERE `user_id` = ".$user_id." AND `flag` = 1 GROUP BY `sharing_date`";
        $results = $wpdb->get_results($sql_count);
        if(!empty($results)){
            foreach($results as $result){
                $sharing_arr[$result->sharing_date] = $result->date_count;
            }
        }
        print_r($sharing_arr);
        if(!empty($sharing_arr)){
            // condition 0
            if (!array_key_exists(date('Y-m-d'),$sharing_arr) && $go_ahead == 1){
                $wpdb->update(
                    'wp_social_sharing',
                    array( 'flag' => 0),
                    array('user_id' => $user_id,)
                );
                $go_ahead = 0;
            }

            // condition 1
            foreach($sharing_arr as $date => $count){
                if($count < 5 && $go_ahead == 1){
                    $wpdb->update(
                        'wp_social_sharing',
                        array( 'flag' => 0),
                        array('user_id' => $user_id,)
                    );
                    $go_ahead = 0;
                }
            }

            // condition 2
            if(sizeof($sharing_arr) == 5  && $go_ahead == 1){
                $total_count = 0;
                foreach($sharing_arr as $date => $count){
                    $total_count = $total_count + $count;
                }
                if($total_count < 25){
                    $wpdb->update(
                        'wp_social_sharing',
                        array( 'flag' => 0),
                        array('user_id' => $user_id,)
                    );
                    $go_ahead = 0;
                }
            }

            // condition 3
            if(sizeof($sharing_arr) == 5  && $go_ahead == 1){
                $total_count = 0;
                foreach($sharing_arr as $date => $count){
                    $total_count = $total_count + $count;
                }
                if($total_count >= 25){
                    $wpdb->insert('wp_pro_earnings', array(
                        'user_id' => $user_id,
                        'earnings' => '10',
                        'earn_type' => 'sharing',
                        'type_id' => 0,
                        'status' => 'pending'
                    ));
                    $wpdb->update(
                        'wp_social_sharing',
                        array( 'flag' => 0),
                        array('user_id' => $user_id,)
                    );
                    $go_ahead = 0;
                }
            }
        }
    } 
}