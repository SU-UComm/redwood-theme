<?php

namespace Stanford\Redwood;

use Twig\Cache\NullCache;

class Utilities {

  /******************************************************************************
   *
   * Class / Instance Variables
   *
   ******************************************************************************/

  /** @var Utilities singleton instance of this class */
  protected static $instance = null;

  protected $category_transient = 'redwood_num_categories';


  /******************************************************************************
   *
   * Methods
   *
   ******************************************************************************/

  /**
   * Get the title of the posts page.
   * Returns empty string if there is not posts page.
   *
   * @return string
   */
  public function get_posts_page_title() {
    $posts_page_id = get_option( 'page_for_posts' );
    if ( !empty( $posts_page_id ) ) {
      $posts_page = get_post( $posts_page_id) ;
      return trim( $posts_page->post_title );
    }
    else {
      return '';
    }
  }

  /**
   * Get data necessary to build links to the previous and next posts.
   * Returns false if not on a post.
   *
   * @return array|bool
   */
  public function get_prev_next() {
    if ( !is_single() ) return FALSE;

    $posts = [
        'previous' => is_attachment() ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true )
      , 'next'     => get_adjacent_post( false, '', false )
    ];

    $links = [];
    foreach ( $posts as $prevnext => $post ) {
      if ( is_a( $post, 'WP_Post') ) {
        $links[ $prevnext ] = [
            'title' => $post->post_title
          , 'url'   => get_permalink( $post )
        ];
      }
      else {
        $links[ $prevnext ] = FALSE;
      }
    }
    return $links;
  }

  /**
   * Does the website have more than one non-empty category?
   * Used determine whether or not to show categories on posts
   *
   * @return boolean TRUE if this site has >1 non-empty categories, FALSE otherwise
   */
  public function multiple_categories() {
    // check cache for number of (non-empty) categories
    $num_cats = get_transient( $this->category_transient );
    // if the number isn't in cache, calculate it and cache it
    if ( $num_cats === FALSE ) {
      $num_cats = count( get_categories( [ 'hide_empty' => 1 ] ) );
      set_transient( $this->category_transient, $num_cats );
    }
    // return true if there's more than 1 non-empty category, false otherwise
    return ( intval( $num_cats ) > 1 );
  }

  /**
   * Refresh the cache of category count
   * Invoked via the created_category, edit_category and deleted_category actions
   */
  public function refresh_category_transient() {
    // first flush the cache
    delete_transient( $this->category_transient );
    // then repopulate it
    $this->multiple_categories();
  }

  /**
   * Allow more tags in excerpts.
   * Invoked via the timber/trim_words/allowed_tags filter
   *
   * @param $allowed_tags string space separated list of allowable tags
   * @return string
   */
  public function allowed_tags_in_excerpts( $allowed_tags ) {
    return $allowed_tags . ' em strong';
  }

  /**
   * Add url of featured image to RSS feed
   *
   * @param int $comment_id The ID of the comment being displayed.
   * @param int $post_id    The ID of the post the comment is connected to.
   */
  public function rss_insert_image( $comment_id = NULL, $post_id = NULL ) {
    // if this is a comment item, there's no featured image
    if ( !empty( $comment_id ) ) return;

    // what image size should we insert?
    $size = get_theme_mod( 'rss_featured_img', 'none' );
    if ( $size == 'none' ) return; // we shouldn't even be called if it's none, but just to be safe

    // fetch the featured image
    global $post;
    if ( !has_post_thumbnail( $post->ID ) ) return; // if no featured image, there's nothing to do

    $image_id = get_post_thumbnail_id( $post->ID );

    $image_url = get_the_post_thumbnail_url( $post->ID, $size );
    if ( !empty( $image_url ) ) {
      $elem  = '    <media:content';
      $elem .= ' url="' . esc_url( $image_url ) . '"';
      $mime_type = get_post_mime_type( $image_id );
      if ( !empty( $mime_type ) ) {
        $elem .= ' type="' . esc_attr( $mime_type ) . '"';
      }
      $elem .= " medium=\"image\" />\n";
      echo $elem;

      $image = get_post( $image_id );
      if ( !empty( $image->post_title ) ) {
        echo '    <media:title type="plain">' . strip_tags( $image->post_title ) . "</media:title>\n";
      }
      if ( !empty( $image->post_excerpt ) ) {
        echo '    <media:description type="plain">' . strip_tags( $image->post_excerpt ) . "</media:description>\n";
      }
    }
  }

  /**
   * Add media namespace for RSS feed
   */
  public function rss_add_namespace() {
    echo 'xmlns:media="http://search.yahoo.com/mrss/"' . "\n";
  }

  /**
   * Reset the cache lifetime.
   *
   * @param int    $lifetime Cache duration in seconds. Default is 43200 seconds (12 hours).
   * @param string $filename Unique identifier for the cache object.
   */
  public function set_feed_cache_lifetime( $lifetime, $filename ) {
    return 1800; // 30 minutes
  }


  /******************************************************************************
   *
   * Class setup
   *
   ******************************************************************************/

  /**
   * Called once when singleton instance is created.
   * Declared as protected to prevent using new to instantiate instances other than the singleton.
   */
  protected function __construct() {
    add_action( 'created_category', [ $this, 'refresh_category_transient' ] );
    add_action( 'edit_category',    [ $this, 'refresh_category_transient' ] );
    add_action( 'deleted_category', [ $this, 'refresh_category_transient' ] );

    add_action( 'wp_feed_cache_transient_lifetime', [ $this, 'set_feed_cache_lifetime' ], 10, 2 );

    if ( get_theme_mod( 'rss_featured_img', 'none' ) !== 'none' ) {
      add_action( 'rss2_ns',   [ $this, 'rss_add_namespace' ], 10, 0 );
      add_action( 'rss2_item', [ $this, 'rss_insert_image'  ], 10, 2 );
    }

    add_filter( 'timber/trim_words/allowed_tags', [ $this, 'allowed_tags_in_excerpts' ] );
  }

  /**
   * Create singleton instance, if necessary.
   */
  public static function init() {
    if (!is_a(self::$instance, __CLASS__)) {
      self::$instance = new Utilities();
    }
    return self::$instance;
  }

}