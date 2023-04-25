<?php
require_once get_template_directory().'/includes/search-route.php';


function university_custom_rest(): void {
	register_rest_field( 'post', 'authorName', array(
		'get_callback' => function () {
			return get_the_author();
		}
	) );
}

add_action( 'rest_api_init', 'university_custom_rest' );

function pageBanner( $args ) {
	// php logic will live here for Page Banner
	if ( ! $args['title'] ) {
		$args['title'] = get_the_title();
	}

	if ( ! $args['subtitle'] ) {
		$args['subtitle'] = get_field( 'page_banner_subtitle' );
	}

	if ( ! $args['photo'] ) {
		if ( get_field( 'page_banner_background_image' ) ) {
			$args['photo'] = get_field( 'page_banner_background_image' )['sizes']['pageBanner'];
		} else {
			$args['photo'] = get_theme_file_uri( '/images/ocean.jpg' );
		}
	}
	?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo
		$args['photo']; ?>)
                "></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
	<?php
}

function university_files(): void {
	wp_enqueue_script( 'googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyCFTutxYOrTX10KfJt42yt5yvaKySEo-7k', null, '1.0', true );
	wp_enqueue_script( 'main-university-js', get_theme_file_uri( '/build/index.js' ), array( 'jquery', ), '1.0', true );
	wp_enqueue_style( 'custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'university_main_styles', get_theme_file_uri( './build/style-index.css' ) );
	wp_enqueue_style( 'university_main_styles', get_theme_file_uri( './build/index.css' ) );

	wp_localize_script( 'main-university-js', 'universityData', array(
		'root_url' => get_site_url(),

	) );
}

// this adds action is used to load styles and scripts
add_action( "wp_enqueue_scripts", "university_files" );


function university_features(): void {
	register_nav_menu( 'headerMenuLocation', 'Header Menu Location' );
	register_nav_menu( 'footerMenuLocationOne', 'Footer Menu Location 1' );
	register_nav_menu( 'footerMenuLocationTwo', 'Footer Menu Location 2' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'professorLandscape', 400, 260, true );
	add_image_size( 'professorPortrait', 480, 650, true );
	add_image_size( 'pageBanner', 1500, 350, true );
}

// this adds action method will enable all different features of the theme
add_action( 'after_setup_theme', 'university_features' );


function university_adjust_queries( $query ): void {

	if ( ! is_admin() and is_post_type_archive( 'program' ) and is_main_query() ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', - 1 );

	}

	if ( ! is_admin() and is_post_type_archive( 'event' ) and $query->is_main_query() ) {
		$today = date( 'Ymd' );


		$query->set( 'meta_key', 'event_date' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
		$query->set( 'meta_query', array(
			array(
				'key'     => 'event_date',
				'compare' => '>=',
				'value'   => $today,
				'type'    => 'numeric',
			)
		) );
	}

	if ( ! is_admin() and is_post_type_archive( 'campus' ) and is_main_query() ) {
		$query->set( 'posts_per_page', - 1 );
	}
}

// this adds action method will help us to customize url or adjust url with custom query
add_action( 'pre_get_posts', 'university_adjust_queries' );


// this adds action method will load the Google map api
add_action( 'acf/fields/google_map/api', 'universityMapKey' );
function universityMapKey( $api ) {
	$api['key'] = 'AIzaSyCFTutxYOrTX10KfJt42yt5yvaKySEo-7k';

	return $api;
}