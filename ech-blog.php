<?php
/**
 * ECH Blog
 *
 * @link              https://echealthcare.com/
 * @since             1.0.0
 * @package           ECH_Blog
 * @wordpress-plugin
 * Plugin Name:       ECH Blog
 * Plugin URI:        https://echealthcare.com/
 * 
 * Description:       This plugin creates shortcode to show ECH blog content using ECH blog API
 * 
 * 
 *                    
 * Version:           1.0.0
 * Author:            Toby Wong
 * Author URI:        https://echealthcare.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ech_blog
 * Domain Path:       /languages
 */


if (! defined('ABSPATH')) {
	exit;
}


// loader.php is to load all files in folder "inc"
require_once(dirname(__FILE__). '/inc/loader.php');


// include CSS and JS files
add_action('init', 'register_ech_blog_styles');
add_action('wp_enqueue_scripts', 'enqueue_ech_blog_styles');

//load more posts using ajax
add_action('wp_ajax_nopriv_ECHB_load_more_posts', 'ECHB_load_more_posts');
add_action('wp_ajax_ECHB_load_more_posts', 'ECHB_load_more_posts');

//filter posts by title using ajax
add_action('wp_ajax_nopriv_filter_title_posts', 'filter_title_posts');
add_action('wp_ajax_filter_title_posts', 'filter_title_posts');

//filter posts by category using ajax
add_action('wp_ajax_nopriv_ECHB_filter_blog_list', 'ECHB_filter_blog_list');
add_action('wp_ajax_ECHB_filter_blog_list', 'ECHB_filter_blog_list');




// Register the shortcode
add_shortcode('ech_blog', 'ech_blog_fun' );


