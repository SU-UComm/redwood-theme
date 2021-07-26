<?php
namespace Stanford\Redwood;

use Stanford\Redwood\Utilities;

class Twig_Extensions {

  /******************************************************************************
   *
   * Class / Instance Variables
   *
   ******************************************************************************/

  /** @var Twig_Extensions singleton instance of this class */
  protected static $instance = null;

  /** @var Redwood instance of our theme, which is an instance of \Timber\Site */
  protected $theme = null;


  /******************************************************************************
   *
   * Twig context
   *
   ******************************************************************************/

  /**
   * Add information to Twig's context
   *
   * @param array $context
   * @return array mixed
   */
  public function add_to_context( $context ) {
    $Utilites = Utilities::init();

    // allow for site-specific styling
    $site_name = get_bloginfo( 'name' );
    $site_name = sanitize_title( $site_name );
    $context[ 'body_class' ] .= ' site-' . $site_name;

    // set menus
    if ( has_nav_menu( 'top'  ) ) {
      $context[ 'top_nav' ] = new RWMenu( 'top' );
    }
    if ( has_nav_menu( 'left' ) ) {
      $context[ 'left_nav' ] = new RWMenu( 'left' );
    }

    // set sidebars
    if ( is_front_page() ) {
      $context[ 'left_sidebar'  ] = \Timber\Timber::get_widgets( 'sidebar-home-first'  );
      $context[ 'right_sidebar' ] = \Timber\Timber::get_widgets( 'sidebar-home-second' );
    }
    else {
      $context[ 'left_sidebar'  ] = \Timber\Timber::get_widgets( 'sidebar-first'  );
      $context[ 'right_sidebar' ] = \Timber\Timber::get_widgets( 'sidebar-second' );
    }
    $context[ 'pre_footer'  ] = \Timber\Timber::get_widgets( 'fat-footer'  );

    // add query strings to Timber context
    $query_strings = $context[ 'request' ]->get;
    foreach ( $query_strings as $query_string => $value ) {
      $context[ $query_string ] = empty( $value ) ? TRUE : $value;
    }

    // add previous, next post links to Timber context
    if ( is_single() ) {
      $context[ 'prev_next' ] = $Utilites->get_prev_next();
    }

    // add theme options to Timber context
    $context[ 'banner' ] = [
        'url' => get_header_image()
      , 'text' => get_theme_mod( 'banner_text', FALSE )
      , 'text_location' => get_theme_mod( 'banner_text_location', 'bottom-left' )
      , 'width' => get_theme_mod( 'banner_width', 'content' )
    ];
    $context[ 'brand' ] = [
        'bar'            => get_theme_mod('brand_bar',  'bar' )
      , 'color'          => get_theme_mod('brand_color','cardinal' )
      , 'lockup'         => ( get_theme_mod('lockup','false' ) === 'true' ) ? TRUE : FALSE
      , 'show_in_footer' => get_theme_mod( 'show_site_name_in_footer', TRUE )
    ];
    if ( $context[ 'brand' ][ 'lockup' ] ) {
      $context[ 'brand' ][ 'class' ] = 'su-lockup--option-' . ( empty( $context[ 'site' ]->description ) ? 'n' : 'd' );
    }
    $context[ 'layout' ] = [
        'home'      => get_theme_mod('home_layout',   Options::DEFAULT_LAYOUT )
      , 'page'      => get_theme_mod('page_layout',   Options::DEFAULT_LAYOUT )
      , 'post'      => get_theme_mod('post_layout',   Options::DEFAULT_LAYOUT )
      , 'author'    => get_theme_mod('author_layout', Options::DEFAULT_LAYOUT )
      , 'default'   => get_theme_mod('default_layout',Options::DEFAULT_LAYOUT )
      , 'template'  => [
            'none'  => 'layout/one-column.twig'
          , 'left'  => 'layout/two-column--left-sidebar.twig'
          , 'right' => 'layout/two-column--right-sidebar.twig'
          , 'both'  => 'layout/three-column.twig'
        ]
    ];
    $context[ 'option' ] = [
        'show_search'           => get_theme_mod( 'show_search', TRUE )
      , 'show_featured_image'   => get_theme_mod( 'show_featured_image', TRUE )
      , 'show_author_in_byline' => get_theme_mod( 'show_author_in_byline', TRUE )
      , 'list_page_display'     => get_theme_mod( 'list_page_display', 'excerpts' )
      , 'ga_property'           => get_theme_mod( 'ga_property', '' )
      , 'show_homepage_title'   => get_theme_mod( 'show_homepage_title', FALSE )
    ];

    if ( is_home() ) {
      // if we're displaying the posts page, get the title
      // if it's the front (home) page, get the option from the customizer
      // if it's a static posts page, use the page title
      $context[ 'posts_title' ] = is_front_page()
          ? get_theme_mod( 'post_section_title', '' )
          : $Utilites->get_posts_page_title();
    }

    $context[ 'multiple_categories' ] = $Utilites->multiple_categories();

    // add discussion settings to Timber context
    $context[ 'comments' ] = [
        'show_avatars'         => !empty( get_option( 'show_avatars'         ) )
      , 'require_name_email'   => !empty( get_option( 'require_name_email'   ) )
      , 'comment_registration' => !empty( get_option( 'comment_registration' ) )
    ];

    return $context;
  }


  /******************************************************************************
   *
   * Twig functions
   *
   ******************************************************************************/

  /**
   * Test implementing a WordPress template tag as a Twig function
   *
   * @param string $whol Who to say hello to
   */
  public function hello( $who = 'world' ) {
    echo "<p>The <code>hello(\$who)</code> custom twig function says: Hello there, {$who}!</p>";
  }

  /**
   * Test implementing a WordPress template tag as a Twig function
   *
   * @param array $context Twig context
   * @param string $whol Who to say hello to
   */
  public function hello_with_context( $context, $who = 'world' ) {
    echo "<p>The context-aware <code>hello(\$who)</code> custom twig function says: Hello there, {$who}!</p>";
  }

  /**
   * Echo the appropriate singular or plural form of a word based on $count.
   *
   * @param int    $count
   * @param string $singular
   * @param string $plural
   * @param bool   $verbatim - true if $plural should be echoed verbatim,
   *                           false to do standard pluralization
   */
  public function pluralize( $count, $singular, $plural = '', $verbatim = FALSE ) {
    if ( $count == 1 ) {
      echo $singular;
    }
    elseif ( $verbatim ) {
      echo $plural;
    }
    elseif ( preg_match( '/(s|x|z|sh|ch)$/', $singular ) ) {
      echo $singular . 'es';
    }
    elseif ( preg_match( '/[^aeiouy]y$/', $singular ) ) {
      echo preg_replace( '/y$/', 'ies', $singular );
    }
    else {
      echo $singular . 's';
    }
  }

  /**
   * Provides the {{ dump_context() }} twig function.
   * To use:
   *  - Place the {{ dump_context() }} tag at an appropriate place in a twig template.
   *  - Add ?dump=section to the query string when you're visiting a page that uses that template.
   *    section is one of the top-level indexes of the $context array. Valid values depend on the context.
   *    If no section is provided (just ?dump), all of $context is dumped.
   *    The following sections are always valid:
   *    - site
   *    - request
   *    - user
   *    - theme
   *    - wp_head
   *    - wp_footer
   *    - others???
   *   The following sections may be available depending on context:
   *    - top_nav
   *    - left_nav
   *    - posts
   *    - post
   *    - left_sidebar
   *    - right_sidebar
   *    - pre_footer
   *    - others???
   *
   * @param array $context Twig context
   */
  public function dump_context( $context ) {
    if ( isset( $context[ 'request' ]->get[ 'dump' ] ) ) {
      $style = <<<EODUMPSTYLE

		<style>
			details.dump {
				margin: 2em 1em 1em;
				padding: 1em;
				border: 1px solid #333;
				border-radius: 8px;
				background-color: #fff8dc;
			}
		</style>
EODUMPSTYLE;
      echo $style;

      foreach ( explode(',', $context[ 'request' ]->get[ 'dump' ] ) as $what_to_dump ) {
        $dump = "<details class='dump'>\n";
        if ( is_string( $what_to_dump ) && isset( $context[ $what_to_dump ] ) ) {
          $dump .= "<summary>Dump of {$what_to_dump}</summary>\n";
          $dump .= "<pre>\n" . htmlentities( print_r( $context[ $what_to_dump ], TRUE ) ) . "</pre>\n";
        } else {
          $dump .= "<summary>Dump of \$context</summary>\n";
          $dump .= "<pre>\n" . htmlentities( print_r( $context, TRUE ) ) . "</pre>\n";
        }
        $dump .= "</details>\n";
        echo $dump;
      }
    }
  }

  /******************************************************************************
   *
   * Twig filters
   *
   ******************************************************************************/

  /**
   * Test Twig filter
   *
   * @param string $text Text to be altered
   * @return string Text turned into a quote
   */
  public function emphasize( $text ) {
    $func = __FUNCTION__;
    return "The <code>{$func}()</code> custom twig filter says: <em>{$text}</em>.";
  }

  /******************************************************************************
   *
   * Timber filters
   *
   ******************************************************************************/

  /**
   * Indicate whether a post is sticky or not in the only place Timber seems to
   * let us add to a post's representation.
   * Invoked via the timber_post_get_meta filter.
   *
   * @param array $post_meta meta fields for the post
   * @param integer $pid id of the post
   * @param \Timber\Post $timber_post
   * @return array
   */
  public function add_stickiness( $post_meta, $pid, $timber_post ) {
    $post_meta[ 'is_sticky' ] = is_sticky( $pid );
    return $post_meta;
  }


  /******************************************************************************
   *
   * Utilities
   *
   ******************************************************************************/

  /**
   * Add functions, filters, etc. to Twig
   *
   * @param \Twig_Environment $twig
   * @return \Twig_Environment mixed
   */
  public function add_to_twig( $twig ) {
    /* this is where you can add your own functions to twig */
    $twig->addExtension( new \Twig_Extension_StringLoader() );
    $twig->addFunction(  new \Twig_Function( 'hello', [ $this, 'hello' ] ) );
    $twig->addFunction(  new \Twig_Function( 'pluralize', [ $this, 'pluralize' ] ) );
    $twig->addFunction(  new \Twig_Function( 'hello_with_context', [ $this, 'hello_with_context' ], [ 'needs_context' => TRUE ] ) );
    $twig->addFunction(  new \Twig_Function( 'dump_context', [ $this, 'dump_context' ], [ 'needs_context' => TRUE ] ) );
    $twig->addFilter(    new \Twig_SimpleFilter( 'emphasize', [ $this, 'emphasize'] ) );

    return $twig;
  }

  /**
   * Allow including Decanter's templates directly
   *
   * @param \Twig_Loader_Filesystem $loader
   * @return \Twig_Loader_Filesystem
   */
  public function add_template_paths( $loader ) {
    $theme_dir = get_template_directory();
    $loader->addPath( $theme_dir . "/templates/decanter", "decanter" );
    return $loader;
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
  protected function __construct( $theme ) {
    $this->theme = $theme;

    add_filter( 'timber/loader/loader', [ $this, 'add_template_paths' ] );
    add_filter( 'timber_context',       [ $this, 'add_to_context'     ] );
    add_filter( 'timber_post_get_meta', [ $this, 'add_stickiness'     ], 10, 3 );
    add_filter( 'get_twig',             [ $this, 'add_to_twig'        ] );
  }

  /**
   * Create singleton instance, if necessary.
   */
  public static function init( $theme ) {
    if (!is_a(self::$instance, __CLASS__)) {
      self::$instance = new Twig_Extensions( $theme );
    }
    return self::$instance;
  }


}