<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context          = Timber::get_context();
$context['title'] = 'Search results for "' . get_search_query() . '"';

$context['posts'] = new Timber\PostQuery();

$loader = new \Timber\Loader( __DIR__ . '/templates/main-content' );
$context[ 'content_template' ] = $loader->choose_template( [ 'search.twig', 'archive.twig', 'index.twig' ] );

$layout = $context[ 'layout' ][ 'default'];
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;

Timber::render( $layout_template, $context );
