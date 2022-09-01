<?php
/**
 * wom functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wom
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wom_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on wom, use a find and replace
		* to change 'wom' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'wom', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'wom' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'wom_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'wom_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wom_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wom_content_width', 640 );
}
add_action( 'after_setup_theme', 'wom_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wom_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wom' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'wom' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wom_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function wom_scripts() {
	wp_enqueue_style( 'wom-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'wom-style', 'rtl', 'replace' );

	wp_enqueue_script( 'wom-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wom_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


add_action('wp_dashboard_setup', 'dashboard_weather_widget');

function dashboard_weather_widget() {

	if ( current_user_can('manage_options')) {
		wp_add_dashboard_widget('weather_widget', 'Weather for Brookvale, NSW','weather_widget_func');
	}
}

function weather_widget_func($atts) {
	
	$defaults =[
		'title' => 'Table title'	
	];

	$atts = [
		$defaults,
		$atts,
		''
	];
	
		$url = 'https://api.openweathermap.org/data/2.5/weather?lat=33.7667&lon=151.2667&appid=23ea4f8c6144e1caa427bdd3b0737c21';

	   $arguments = array(
        'method' => 'GET'
    );

	$response = wp_remote_get( $url, $arguments );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	} 

	$results = json_decode (wp_remote_retrieve_body( $response) );

	/* var_dump($results); */

/* 	echo '<pre>'; 
	print_r($results);
	echo '</pre>'; */
	

 	$html = '';
	echo "<br>";
	echo "Clouds: ".$results->weather[0]->description.'<br>';
	echo "Temprature: ".$results->main->temp.'°c<br>';
	echo "Feels like: ".$results->main->feels_like.'°c<br>';
	echo "Minimum Temprature: ".$results->main->temp_min.'°c<br>';
	echo "Maximum Temprature: ".$results->main->temp_max.'°c<br>';
	echo "Pressure: ".$results->main->pressure.'<br>';
	echo "Humidity: ".$results->main->humidity.'<br>';
	echo "Sea Level: ".$results->main->sea_level.'<br>';
	echo "Ground Level: ".$results->main->grnd_level.'<br>';
	echo "Visibility: ".$results->visibility.'<br>';
	echo "Wind Speed: ".$results->wind->speed.'<br>';
	echo "Wind Direction/Degrees: ".$results->wind->deg.'<br>';
	echo "Wind Gust: ".$results->wind->gust.'<br>';
	echo $html; 

}