<?php
/**
 * The template for displaying Attachment pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$post = Timber::get_post();

$context = Timber::get_context();
$context[ 'post' ] = $post;
$context[ 'post' ]->link = wp_get_attachment_url( $post->ID );
$context[ 'parent' ] = Timber::get_post( wp_get_post_parent_id( $post->ID ) );

// Create list of possible templates based on mime type, most specific first. For example
// image/jpg looks for
//   attachment-image-jpg.twig
//   attachment-image.twig
//   attachment.twig
// application/pdf looks for
//   attachment-application-pdf.twig
//   attachment-application.twig
//   attachment.twig
$mime_type_parts = explode( '/', $post->post_mime_type );
$templates = [ 'attachment.twig' ];
$template = 'attachment';
foreach ( $mime_type_parts as $part ) {
  $template .= '-' . $part;
  array_unshift( $templates, $template . '.twig' );
}

$loader = new \Timber\Loader( trailingslashit( get_template_directory() ) . 'templates/main-content' );
$context[ 'content_template' ] = $loader->choose_template( $templates );

$layout = $context[ 'layout' ][ 'default'];
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;
Timber::render( $layout_template, $context );
