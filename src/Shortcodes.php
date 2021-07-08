<?php
namespace Stanford\Redwood;

use \Timber;

class Shortcodes {

  /******************************************************************************
   *
   * Class / Instance Variables
   *
   ******************************************************************************/

  /** @var Shortcodes singleton instance of this class */
  protected static $instance = null;

  /** @var int $depth - the posts shortcode can cause recursion - stop it at depth = 1 */
  protected $depth = 1;

  /******************************************************************************
   *
   * Shortcodes
   *
   ******************************************************************************/

  /**
   * [banner]...[/banner] shortcode
   * Render the banner image
   *
   * @param array $atts - not used
   * @param string $content - not used
   * @return string html - shortcodes/banner.twig
   * @since 1.0.0
   */
  public function banner($atts, $content) {
    // data for the banner image is already added to Timber's context
    return Timber::fetch( 'shortcodes/banner.twig', Timber::get_context() );
  }

  /**
   * [button]...[/button] shortcode
   * Render $content as a button
   *
   * @param array $attr - attributes passed to the [button] shortcode
   * @param string $content - text to be displayed in the button
   * @return string html - shortcodes/button.twig
   */
  public function button($attr, $content) {
    $atts = shortcode_atts(
        [
            'url'   => '',
            'align' => 'center',
            'style' => '',
        ],
        $attr, 'button'
    );

    $data = [
        'align'          => $atts[ 'align' ],
        'url'            => $atts[ 'url' ],
        'text'           => $content,
        'modifier_class' => ''
    ];
    if ( !empty( $atts[ 'style' ] ) ) {
      $styles = explode( ',', $atts[ 'style' ] );
      $classes = array_map( function( $style ) { return 'button--' . $style; }, $styles );
      $data[ 'modifier_class' ] = implode( ' ', $classes );
    }
    return Timber::fetch( 'shortcodes/button.twig', $data );
  }

  /**
   * Generates the markup for the classic editor's [gallery] shortcode
   * Invoked via the post_gallery filter
   *
   * Most of the logic was copied from the gallery_shortcode() function in
   * wp-includes/media.php so that we could make the rendering match the
   * rendering of Gutenberg galleries.
   *
   * @see gallery_shortcode()
   *
   * @param string $empty - empty string
   * @param array $attr - attributes passed to the [gallery] shortcode
   * @param integer $instance - unique gallery id within the post
   * @return string html - shortcodes/gallery.twig
   */
  public function gallery( $empty, $attr, $instance ) {
    $post = get_post();
    $atts  = shortcode_atts(
      [
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post ? $post->ID : 0,
        'columns'    => 3,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => '',
        'link'       => '',
      ],
      $attr, 'gallery'
    );

    $id = intval( $atts['id'] );

    if ( ! empty( $atts['include'] ) ) {
      $_attachments = get_posts(
        [
          'include'        => $atts['include'],
          'post_status'    => 'inherit',
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'order'          => $atts['order'],
          'orderby'        => $atts['orderby'],
        ]
      );
      $attachments = [];
      foreach ( $_attachments as $key => $val ) {
        $attachments[ $val->ID ] = $_attachments[ $key ];
      }
    } elseif ( ! empty( $atts['exclude'] ) ) {
      $attachments = get_children(
        [
          'post_parent'    => $id,
          'exclude'        => $atts['exclude'],
          'post_status'    => 'inherit',
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'order'          => $atts['order'],
          'orderby'        => $atts['orderby'],
        ]
      );
    } else {
      $attachments = get_children(
        [
          'post_parent'    => $id,
          'post_status'    => 'inherit',
          'post_type'      => 'attachment',
          'post_mime_type' => 'image',
          'order'          => $atts['order'],
          'orderby'        => $atts['orderby'],
        ]
      );
    }

    if ( empty( $attachments ) ) {
      return '';
    }

    if ( is_feed() ) {
      $output = "\n";
      foreach ( $attachments as $att_id => $attachment ) {
        $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
      }
      return $output;
    }


    $data = [
        'instance' => $instance
      , 'columns'  => intval( $atts['columns'] )
      , 'imgs'     => []
    ];
    foreach ( $attachments as $img ) {
      switch ( $atts[ 'link' ] ) {
        case 'file':
          $url = wp_get_attachment_image_url( $img->ID, 'orig' );
          break;
        case 'none':
          $url = '';
          break;
        default:
          $url = get_attachment_link( $img->ID );
          break;
      }
      array_push( $data[ 'imgs' ], [
          'src'     => wp_get_attachment_image_url( $img->ID, 'orig' )
        , 'alt'     => esc_attr( get_post_meta( $img->ID, '_wp_attachment_image_alt', true ) )
        , 'url'     => $url
        , 'srcset'  => wp_get_attachment_image_srcset( $img->ID )
        , 'caption' => wptexturize( trim( $img->post_excerpt ) )
      ]);
    }
    //return $empty;
    return Timber::fetch( 'shortcodes/gallery.twig', $data );
  }

  /**
   * [headline]...[/headline] shortcode
   * Render $content as headline
   *
   * @param array $atts - not used
   * @param string $content - text to be displayed as headline
   * @return string html - shortcodes/headline.twig
   * @since 1.0.0
   */
  public function headline($atts, $content) {
    $data = [
        'content' => wp_kses( $content, [
            'a'      => [ 'href' => [], 'title' => [] ]
          , 'b'      => []
          , 'em'     => []
          , 'i'      => []
          , 'strong' => []
        ] )
    ];
    return Timber::fetch( 'shortcodes/headline.twig', $data );
  }

  /**
   * [lead]...[/lead] shortcode
   * Render $content as lead paragraph
   *
   * @param array $atts - no attribtutes
   * @param string $content - text to be displayed as lead paragraph
   * @return string html - shortcodes/lead.twig
   * @since 1.0.0
   */
  public function lead($atts, $content) {
    $data = [
        'content' => do_shortcode( $content ) // process any shortcodes the author may have included
    ];
    return Timber::fetch( 'shortcodes/lead.twig', $data );
  }

  /**
   * [more]...[/more] shortcode
   * Render a more link
   *
   * @param array $atts 'url' (required), 'align' => 'left', 'center', 'right' (defaults to 'left')
   * @param string $content - anchor text; defaults to "See more"
   * @return string html- shortcodes/more.twig
   * @since 1.0.0
   */
  public function more($atts, $content) {
    extract( shortcode_atts( [
        'url'   => NULL
      , 'align' => 'left'
    ], $atts ) );

    $data = [];

    if ( empty( $url ) ) {
      $data[ 'shortcode' ] = 'more';
      $data[ 'msg'       ] = "No url specified";
      return Timber::fetch( 'shortcodes/error.twig', $data );
    }

    $data[ 'url' ] = esc_url_raw( $url );
    $data[ 'content' ] = empty( $content )
        ? __("See more", TEXT_DOMAIN)
        : strip_tags( $content );

    $data[ 'align' ] = in_array( $align, [ 'center', 'right'] )
        ? " align{$align}"
        : "";

    return Timber::fetch( 'shortcodes/more.twig', $data );
  }

  /**
   * [postcard] shortcode
   * There are 2 ways to use this shortcode:
   *
   * 1. Display a post as a postcard
   *    In this case leave the content blank and specify either of the following attributes:
   *    + posturl - url of a post (easier for users to get than a post id)
   *    + postid - id of a post (when called programmatically, e.g. on list pages)
   *    + h - the heading level of the title, e.g., value of 3 would render the postcard title as an h3. The default is 2.
   *    The shortcode will display the post's featured image (if any), the post's title, author, date and excerpt.
   *
   * 2. Display a custom postcard
   *    In this case provide content and specify the following attributes:
   *    + title - the title rendered above the content - required
   *    + url   - where the title should link to
   *    + img   - url of the image to display
   *    + h     - heading level of the postcard title, default is 2 which would render the postcard title as an h2.
   *
   * @param array $atts (postid or posturl; or img, alt, title and url)
   * @param string $content ignored if postid or posturl are specified; otherwise this is the text of the postcard
   * @return string html - partial/tease-custom.twig, partial/tease.twig
   * @since 1.0.0
   */
  public function postcard( $atts, $content) {
    extract(shortcode_atts( [
        'postid'  => ''
      , 'posturl' => ''
      , 'title'   => ''
      , 'url'     => ''
      , 'img'     => ''
      , 'h'       => '2'
    ], $atts ) );

    // validate options
    $error = [ 'shortcode' => 'postcard' ];
    if ( empty( $postid ) && empty( $posturl ) && empty( $title ) ) {
      $error[ 'msg' ] = "Must specify postid, posturl or title";
      return Timber::fetch( 'shortcodes/error.twig', $error );
    }

    if ( empty( $postid ) && !empty( $posturl ) ) {
      $postid = url_to_postid( $posturl) ;
      if ( empty( $postid ) ) {
        $error[ 'msg' ] = "posturl {$posturl} does not link to a post";
        return Timber::fetch( 'shortcodes/error.twig', $error );
      }
    }

    // if we have a post id, display it
    if ( !empty( $postid ) ) {
      $timber_context = \Timber\Timber::get_context();
      $data = [
          'post'   => new \Timber\Post( $postid )
        , 'option' => $timber_context[ 'option' ]
        , 'heading' => $h
      ];

      return Timber::fetch( [ "partial/tease-{$data['post']->post_type}.twig", "partial/tease.twig" ], $data );
    }

    // if we're still here, we're displaying a custom postcard
    $data = [
        'post' => [
            'post_type' => 'custom'
          , 'title'     => $title
          , 'excerpt'   => do_shortcode( $content )
          , 'link'      => $url
          , 'thumbnail' => [ 'src' => $img ]
        ]
      , 'heading' => $h
    ];
    return Timber::fetch( [ "partial/tease-custom.twig", "partial/tease.twig" ], $data );
  }

  /**
   * [posts] shortcode
   * Display recent posts with optional link to see all posts
   *
   * @param array $atts
   *        'num'  => # of posts to display, defaults to 5;
   *        'type' => post type, defaults to 'post';
   *        'more' => text for link to posts page, defaults to no link
   *        'h'    => heading level of the post titles, default is 2 which would render the post titles as h2.
   * @param string $content - ignored
   * @return string html - shortcodes/posts.twig, shortcodes/more.twig
   * @since 1.0.0
   */
  public function posts( $atts, $content ) {
    // \Timber\Post completely processes the post, including processing all shortcodes.
    // This can result in infinite recursion processing of this shortcode. Stop the
    // processing after the first iteration.
    if ( $this->depth++ > 1 ) return;

    extract( shortcode_atts( [
          'num'  => 5
        , 'type' => 'post'
        , 'more' => ''
        , 'h'    => '2'
      ], $atts) );
    $type = explode( ',', $type );

    // generate a query to retrieve the num most recent posts
    $query_args = [
        'post_type'      => $type
      , 'post_status'    => 'publish'
      , 'posts_per_page' => $num
    ];
    $post_query = new \WP_Query( $query_args );


    $timber_context = \Timber\Timber::get_context();
    $data = [
        'posts'   => new \Timber\PostQuery( $post_query )
      , 'option'  => $timber_context[ 'option' ]
      , 'heading' => $h
    ];

    // add data for the more link - shortcodes/more.twig
    if ( !empty( $more ) ) {
      $more_link = get_site_url();
      if ( get_option( 'show_on_front' ) == 'page') {
        $blog_page_id = get_option( 'page_for_posts' );
        $more_link .= '/' . get_page_uri( $blog_page_id );
      }
      $data[ 'more' ] = [
          'url'     => $more_link
        , 'content' => $more
        , 'style'   => 'style="text-align: right;"'
      ];
    }

    return Timber::fetch( 'shortcodes/posts.twig', $data );
  }

  /**
   * [well]...[/well] shortcode
   * Render $content in a well
   *
   * @param array $atts - no attribtutes
   * @param string $content - text to be displayed in a well
   * @return string html - shortcodes/well.twig
   * @since 1.0.0
   */
  public function well($atts, $content) {
    $data = [
        'content' => do_shortcode( $content ) // process any shortcodes the author may have included
    ];
    return Timber::fetch( 'shortcodes/well.twig', $data );
  }


  /******************************************************************************
   *
   * Utilities
   *
   ******************************************************************************/

  /**
   * Add a tab to the Help panel on Create / Edit post pages describing what features are available
   * when you're editing a post.
   * Invoked via the load-post.php and load-post-new.php actions.
   *
   * @since 1.0.0
   */
  public function add_help_tab() {
    $admin_url = admin_url();

    $help_text  = __("The Redwwod theme provides the following shortcodes:", TEXT_DOMAIN);
    $help_text .= "<ul>\n";

    $help_text .= "<li><code>[banner]</code> ";
    $help_text .= __("<em>Useful primarily on a static home page.</em><br/>\n", TEXT_DOMAIN);
    $help_text .= __("Display the custom banner image for the site as specified in <a href='{$admin_url}customize.php?autofocus[section]=header_image'>Appearance > Customize > Header Image</a>.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[headline]...[/headline]</code><br/>\n";
    $help_text .= __("Display the enclosed text as a <strong>headline</strong>.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[lead]...[/lead]</code><br/>\n";
    $help_text .= __("Display the enclosed text as a <strong>lead</strong> paragraph.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[more url=\"<em>where to link</em>\" align=\"left|center|right\"]<em>link text</em>[/more]</code><br/>\n";
    $help_text .= __("Display a \"<strong>See more</strong>\" link. url is required. If <em>link text</em> is omitted it defaults to \"See more\".", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[postcard posturl=\"<em>URL of post</em>\" postid=\"<em>URL of post</em>\" h=\"<em>title heading level</em>\"]</code><br/>\n";
    $help_text .= __("Display a postcard block based on a post.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[postcard title=\"<em>Title</em>\" h=\"<em>title heading level</em>\" img=\"<em>URL of image</em>\" alt=\"<em>alt text for img</em>\" url=\"<em>URL that the postcard links to</em>\"]...[/postcard]</code><br/>\n";
    $help_text .= __("Create a custom postcard. Specify the image, title, text to be displayed and the URL that the postcard links to.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[posts num=\"5\" type=\"post\" h=\"<em>title heading level</em>\" more=\"See more posts\"]</code> ";
    $help_text .= __("<em>Useful primarily on a static home page.</em><br/>\n", TEXT_DOMAIN);
    $help_text .= __("Display <em>num</em> most recent posts of type <em>type</em>, most recent first. ", TEXT_DOMAIN);
    $help_text .= __("Post titles are displayed as heading level <em>h</em>. ", TEXT_DOMAIN);
    $help_text .= __("If <code>more</code> is specified, a link to the site's blog listing will be displayed with the specified text as the anchor text.\n", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "<li><code>[well]...[/well]</code><br/>\n";
    $help_text .= __("Display the enclosed text in a <strong>well</strong>.", TEXT_DOMAIN);
    $help_text .= "</li>\n";

    $help_text .= "</ul>\n";

    $screen = get_current_screen();
    $screen->add_help_tab(array(
        'id'      => 'rw_shortcodes'
      , 'title'   => __('Redwood shortcodes', TEXT_DOMAIN)
      , 'content' => $help_text
    ));
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
    add_shortcode('banner',       [ $this, 'banner'   ] );
    add_shortcode('button',       [ $this, 'button'   ] );
    add_filter(   'post_gallery', [ $this, 'gallery'  ], 10, 3 );
    add_shortcode('headline',     [ $this, 'headline' ] );
    add_shortcode('lead',         [ $this, 'lead'     ] );
    add_shortcode('more',         [ $this, 'more'     ] );
    add_shortcode('postcard',     [ $this, 'postcard' ] );
    add_shortcode('posts',        [ $this, 'posts'    ] );
    add_shortcode('well',         [ $this, 'well'     ] );

    if ( is_admin() ) {
      add_action('load-post-new.php',  [ $this, 'add_help_tab' ] );
      add_action('load-post.php',      [ $this, 'add_help_tab' ] );
    }
  }

  /**
   * Create singleton instance, if necessary.
   */
  public static function init() {
    if (!is_a(self::$instance, __CLASS__)) {
      self::$instance = new Shortcodes();
    }
    return self::$instance;
  }


}