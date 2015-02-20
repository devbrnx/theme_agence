<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Illustratr
 */

function illustratr_jetpack_setup() {

	/**
	 * Add theme support for Infinite Scroll.
	 * See: http://jetpack.me/support/infinite-scroll/
	 */
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'footer'         => 'page',
		'footer_widgets' => array( 'sidebar-1', ),
		'render'         => 'illustratr_infinite_scroll_render',
	) );

	/**
	 * Add theme support for Portfolio Custom Post Type.
	 */
	add_theme_support( 'jetpack-portfolio' );
}
add_action( 'after_setup_theme', 'illustratr_jetpack_setup' );

/**
 * Define the code that is used to render the posts added by Infinite Scroll.
 *
 * Includes the whole loop. Used to include the correct template part for the Portfolio CPT.
 */
function illustratr_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();

		if ( is_post_type_archive( 'jetpack-portfolio' ) || is_tax( 'jetpack-portfolio-type' ) || is_tax( 'jetpack-portfolio-tag' ) ) {
			get_template_part( 'content', 'portfolio' );
		} else {
			get_template_part( 'content', get_post_format() );
		}
	}
}

/**
 * Load Jetpack scripts.
 */
function illustratr_jetpack_scripts() {
	if ( is_post_type_archive( 'jetpack-portfolio' ) || is_tax( 'jetpack-portfolio-type' ) || is_tax( 'jetpack-portfolio-tag' ) || is_page_template( 'page-templates/portfolio-page.php' ) ) {
		wp_enqueue_script( 'illustratr-portfolio', get_template_directory_uri() . '/js/portfolio.js', array( 'jquery', 'jquery-masonry' ), '20140325', true );
	}
	if ( is_singular() && 'jetpack-portfolio' == get_post_type() ) {
		wp_enqueue_script( 'illustratr-portfolio-single', get_template_directory_uri() . '/js/portfolio-single.js', array( 'jquery', 'underscore' ), '20140328', true );
	}
	if ( is_page_template( 'page-templates/portfolio-page.php' ) ) {
		wp_enqueue_script( 'illustratr-portfolio-page', get_template_directory_uri() . '/js/portfolio-page.js', array( 'jquery' ), '20140402', true );
	}
}
add_action( 'wp_enqueue_scripts', 'illustratr_jetpack_scripts' );
