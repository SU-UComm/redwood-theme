<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context[ 'post' ] = $post;

if ( is_front_page() ) {
  $context[ 'homepage' ] = TRUE;
  $context[ 'content_template' ] = 'page.twig';
  $layout = $context[ 'layout' ][ 'home' ];
}
elseif ( post_password_required( $post->ID ) ) {
  $context[ 'content_template' ] = 'single-password.twig';
  $layout = $context[ 'layout' ][ 'page' ];
}
else {
  $loader = new \Timber\Loader( __DIR__ . '/templates/main-content' );
  $context[ 'content_template' ] = $loader->choose_template( [
      'page-' . $post->post_name . '.twig'
    , 'page.twig'
  ] );
  $layout = get_post_meta( $post->ID, \Stanford\Redwood\Options::LAYOUT_META_KEY, TRUE );
  if ( empty( $layout ) || $layout == 'default' ) {
    $layout = $context[ 'layout' ][ 'page' ];
  }
}
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;
Timber::render( $layout_template, $context );