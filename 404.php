<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();

$context[ 'content_template' ] = "404.twig";

$layout_template = $context[ 'layout' ][ 'template' ][ 'none' ] ;
Timber::render( $layout_template, $context );
