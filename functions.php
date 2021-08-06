<?php
/**
 * @package WordPress
 * @subpackage PROJECTNAME
 */
 
// Disable the emojis
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

// Remove the tinymce emoji plugin.
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

// Remove comment-reply.min.js from footer
function comments_clean_header_hook(){
	wp_deregister_script( 'comment-reply' );
}
add_action('init','comments_clean_header_hook');

// Remove cache busting query strings
function _remove_script_version( $src ){
	$parts = explode( '?ver', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 ); 

// Remove some Admin menu items
function remove_admin_menu_item(){
	global $menu;
	unset($menu[15]);
  remove_menu_page( 'jetpack' ); // Jetpack
  remove_menu_page( 'edit-comments.php' ); // Comments
}
add_action('admin_menu', 'remove_admin_menu_item',999);

// Remove some dashboard widgets
function remove_wp_dashboard_widgets() {
	wp_unregister_sidebar_widget( 'dashboard_primary' );
	wp_unregister_sidebar_widget( 'dashboard_secondary' );
	wp_unregister_sidebar_widget( 'dashboard_plugins' );
	wp_unregister_sidebar_widget( 'dashboard_recent_comments' );
	wp_unregister_sidebar_widget( 'dashboard_recent_drafts' );
	remove_meta_box('dashboard_primary', 'dashboard', 'side');
	remove_meta_box('dashboard_secondary', 'dashboard', 'side');
	remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
}
add_action( 'wp_dashboard_setup', 'remove_wp_dashboard_widgets' );

// Remove some boxes from Post pages
function remove_post_boxes() {
	remove_meta_box('postcustom', 'post', 'normal');
	remove_meta_box('postcustom', 'page', 'normal');
	remove_meta_box('pagecustomdiv', 'page', 'normal');
	remove_meta_box('trackbacksdiv', 'post', 'normal');
	remove_meta_box('postexcerpt', 'post', 'normal');
	remove_meta_box('authordiv', 'post', 'normal');
	remove_meta_box('revisionsdiv', 'post', 'normal');
	remove_meta_box('commentstatusdiv', 'post', 'normal');
	remove_meta_box('commentstatusdiv', 'page', 'normal');
	remove_meta_box('edit-box-ppr', 'post', 'normal');
	remove_meta_box('edit-box-ppr', 'page', 'normal');	
	remove_meta_box('commentsdiv', 'post', 'normal');
}
add_action( 'admin_menu', 'remove_post_boxes' );

// Add Menu Support
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
		  'navigation' => 'Navigation',
		  'footer' => 'Footer',
		  'mobile' => 'Mobile',
		  'legal' => 'Legal',
		)
	);
}

// Add custom Post Thumbnails/Image size
add_theme_support( 'post-thumbnails' ); 
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'square', 800, 800, true);    
	add_image_size( 'square2x', 1600, 1600, true);
}

// Remove container from Menus
function my_wp_nav_menu_args( $args = '' ) {
	$args['container'] = false;
	return $args;
} 
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

// Add custom theme scripts/styles
function add_theme_scripts() {
  wp_enqueue_style( 'fontstypekit', 'https://use.typekit.net/XXX.css');  	    
  wp_enqueue_style( 'fontawesome', get_template_directory_uri() . "/fontawesome.min.css");
  wp_enqueue_style( 'fontawesome-brands', get_template_directory_uri() . "/fontawesome-brands.min.css");
  wp_enqueue_style( 'fontawesome-solid', get_template_directory_uri() . "/fontawesome-solid.min.css");
  wp_enqueue_style( 'bootstrap', get_bloginfo('template_url') .'/bootstrap.css'); 
  wp_enqueue_style( 'style', get_bloginfo('stylesheet_url') ); 
  wp_enqueue_style( 'print', get_template_directory_uri() . '/print.css', array(), '1.1', 'print');

  wp_enqueue_script( "site-jquery", get_stylesheet_directory_uri()."/js/plugins.js", array( 'jquery' ) ); 
  wp_enqueue_script( "site-js", get_stylesheet_directory_uri()."/js/site.js", array( 'site-jquery' ) ); 
}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );

// Trim Content to specific length
function trim_content($text, $max_length){
  if ( strlen($text) > $max_length ) {
    //$text = apply_filters('the_content', $text);
    //$text = strip_shortcodes($text);
    $text = preg_replace('/\[download(.*?)\]/s','',$text );
    $text = strip_tags($text);
    $excerpt_length = $max_length;
    if (strlen($text) > $max_length){
      $text = substr($text, 0, $max_length);
      $pos = strrpos($text, " ");
      if($pos === false) {              
        return force_balance_tags( substr($text, 0, $max_length).'...');
      }       
      return force_balance_tags( substr($text, 0, $pos).'...');
    }
  }
  force_balance_tags( $text );
  return $text;   
}

// Add Favicon
function PROJECTNAME_favicon() { 
  return '<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/apple-touch-icon-152x152.png" />
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="/favicon-128.png" sizes="128x128" />
	<link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196" />
	<meta name="application-name" content="&nbsp;"/>
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="/mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="/mstile-310x310.png" />';
}

// Add PROJECTNAME Settings Page for ACF
add_action('init', 'my_init_function');
function my_init_function() {
    if( function_exists('acf_add_options_page') ) {	
        acf_add_options_page(array(
            'page_title' 	=> 'PROJECTNAME Settings',
            'menu_title'	=> 'PROJECTNAME Settings',
            'menu_slug' 	=> 'PROJECTNAME-settings',
            'capability'	=> 'edit_posts',
            'redirect'		=> false
        ));
    }
}

// Create shortcode to output Contact Details HTML with ACF Options page settings
function PROJECTNAME_contact() {
	$contact = '<ul class="contact-list">';
	if( get_field('contact_phone', 'option') ) {
		$contact  .= '<li><a href="tel:'.get_field('contact_phone', 'option').'"><i class="fas fa-phone"></i> Contact Us: '.get_field('contact_phone', 'option').'</a></li>';
	} 
	$contact  .= '</ul>';  
    return $contact ;
}
add_shortcode( 'PROJECTNAME_contact', 'PROJECTNAME_contact' );

// Create shortcode to output Social Media list HTML with ACF Options page settings
function PROJECTNAME_social() {
	$social = '<ul class="list-inline social-list">';
	if( get_field('social_facebook', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_facebook', 'option').'"><i class="fab fa-facebook-f"></i></a></li>';
	} if( get_field('social_twitter', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_twitter', 'option').'"><i class="fab fa-twitter"></i></a></li>';
	} if( get_field('social_linkedin', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_linkedin', 'option').'"><i class="fab fa-linkedin"></i></a></li>';
	} if( get_field('social_pinterest', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_pinterest', 'option').'"><i class="fab fa-pinterest"></i></a></li>';
	} if( get_field('social_instagram', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_instagram', 'option').'"><i class="fab fa-instagram"></i></a></li>';
	} if( get_field('social_yelp', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_yelp', 'option').'"><i class="fab fa-yelp"></i></a></li>';
	} if( get_field('social_youtube', 'option') ) {
		$social .= '<li><a target="_blank" href="'.get_field('social_youtube', 'option').'"><i class="fab fa-youtube"></i></a></li>';
	} 
	$social .= '</ul>';  
    return $social;
}
add_shortcode( 'PROJECTNAME_social', 'PROJECTNAME_social' );

// Gravity Forms Confirmation anchor
add_filter( 'gform_confirmation_anchor', '__return_true' );

// Disable Yoast Primary term functionlity
add_filter( 'wpseo_primary_term_taxonomies', '__return_empty_array' );
