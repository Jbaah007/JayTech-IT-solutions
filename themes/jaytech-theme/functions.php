<?php
/**
 * JayTech theme functions.
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JAYTECH_VERSION', '1.0.0' );

/**
 * Theme setup.
 */
function jaytech_setup() {
	load_theme_textdomain( 'jaytech-theme', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'jaytech-theme' ),
			'footer'  => __( 'Footer Menu', 'jaytech-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'jaytech_setup' );

/**
 * Enqueue styles and scripts.
 */
function jaytech_enqueue_assets() {
	wp_enqueue_style(
		'jaytech-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'jaytech-style', get_stylesheet_uri(), array(), JAYTECH_VERSION );
	wp_enqueue_style(
		'jaytech-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'jaytech-style', 'jaytech-fonts' ),
		JAYTECH_VERSION
	);
	wp_enqueue_script(
		'jaytech-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		JAYTECH_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'jaytech_enqueue_assets' );

/**
 * Flag JavaScript support so scroll animations only hide content when JS runs.
 */
function jaytech_js_class() {
	wp_print_inline_script_tag( "document.documentElement.classList.add('js');" );
}
add_action( 'wp_head', 'jaytech_js_class', 0 );

/**
 * Send empty searches (/?s=) to the homepage instead of listing every page.
 */
function jaytech_redirect_empty_search() {
	if ( is_search() && '' === trim( get_search_query( false ) ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'jaytech_redirect_empty_search' );

require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/about-content.php';
require_once get_template_directory() . '/inc/contact-form.php';
require_once get_template_directory() . '/inc/required-pages.php';
