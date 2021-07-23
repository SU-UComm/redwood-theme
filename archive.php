<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = [ 'archive.twig' ];

$context = Timber::get_context();

$context[ 'title' ] = get_the_archive_title();
if ( is_post_type_archive() ) {
  array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
} else if ( is_category() ) {
	array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} else if ( is_tag() ) {
  array_unshift( $templates, 'archive-' . get_query_var( 'tag' ) . '.twig' );
}

$context['posts'] = new Timber\PostQuery();

$loader = new \Timber\Loader( trailingslashit( get_stylesheet_directory() ) . '/templates/main-content' );
$context[ 'content_template' ] = $loader->choose_template( $templates );

$layout = $context[ 'layout' ][ 'default'];
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;
Timber::render( $layout_template, $context );
