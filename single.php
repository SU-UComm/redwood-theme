<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();
$context[ 'post' ] = $post;

if ( post_password_required( $post->ID ) ) {
  $context[ 'content_template' ] = 'single-password.twig';
}
else {
  $loader = new \Timber\Loader( __DIR__ . '/templates/main-content' );
  $context[ 'content_template' ] = $loader->choose_template( [
      'single-' . $post->ID . '.twig'
    , 'single-' . $post->post_type . '.twig'
    , 'single.twig'
  ] );
}

$layout = get_post_meta( $post->ID, \Stanford\Redwood\Options::LAYOUT_META_KEY, TRUE );
if ( empty( $layout ) || $layout == 'default' ) {
  $layout = $context[ 'layout' ][ 'post' ];
}
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;
Timber::render( $layout_template, $context );