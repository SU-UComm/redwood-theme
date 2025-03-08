<?php
/**
 * The template for displaying Author Archive pages
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

global $wp_query;

$context          = Timber::get_context();
$context['posts'] = Timber::query_post();

if ( isset( $wp_query->query_vars['author'] ) ) {
	$author = Timber::get_user( $wp_query->query_vars['author'] );
	$context[ 'author' ] = $author;
	$context[ 'title'  ] = $author->name();

	$show_inline  = get_theme_mod( 'show_author_info_inline', TRUE );
	$show_avatar  = $show_inline && get_theme_mod( 'show_author_avatar', TRUE );
	$show_website = $show_inline && get_theme_mod( 'show_author_website', TRUE );
	$show_email   = $show_inline && get_theme_mod( 'show_author_email', FALSE );
	$context[ 'show_inline' ] = [
	    'bio'     => $show_inline
    , 'website' => $show_website
    , 'email'   => $show_email
    , 'avatar'  => $show_avatar
          ? get_avatar(
                $author->ID
              , 240
              , ''
              , 'avatar for '. $author->name()
              , [ 'force_display' => TRUE ] // ignore setting for displaying avatars in comments
            )
          : FALSE
  ];
}

$context['content_template'] = "author.twig";

$layout = $context[ 'layout' ][ 'author'];
$layout_template = $context[ 'layout' ][ 'template' ][ $layout ] ;
Timber::render( $layout_template, $context );