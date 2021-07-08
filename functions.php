<?php
namespace Stanford\Redwood;

require_once( __DIR__ . '/vendor/autoload.php' );

/** @var string Text domain for internationalization */
const TEXT_DOMAIN = 'stanford';

// load Timber
$timber = new \Timber\Timber();

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	
	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});
	
	return;
}

\Timber\Timber::$dirname = [ 'templates', 'views' ];

//Include the comment reply Javascript
add_action('wp_print_scripts', function(){
  if ( (!is_admin()) && is_singular() && comments_open() && get_option('thread_comments') ) wp_enqueue_script( 'comment-reply' );
});

/*
 * load our theme
 */
global $theme;
$theme = Redwood::get_instance();
