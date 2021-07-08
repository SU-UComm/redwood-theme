<?php
namespace Stanford\Redwood;

class Options {
  /** @var string default layout for pages and posts */
  const DEFAULT_LAYOUT = 'right';

  /** @var string passed as action parameter to wp_nonce() */
  const NONCE_ACTION = 'layout_box';

  /** @var string passed as name parameter to wp_nonce() */
  const NONCE_NAME = 'layout_box_nonce';

  /** @var string field name of the layout field in post meta */
  const LAYOUT_META_KEY = 'page_layout';

  /** @var Options singleton instance of this class */
  protected static $instance = NULL;

  /** @var string theme author - salt */
  private $author = 'UComm';

  /**
   * add Redwood options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  public function add_theme_options( $wp_customize ) {
    $wp_customize->remove_section( 'colors' );

    $this->add_brand_options( $wp_customize );
    $this->add_banner_options( $wp_customize );
    $this->add_nav_options( $wp_customize );
    $this->add_homepage_options( $wp_customize );
    $this->add_layout_options( $wp_customize );
    $this->add_author_options( $wp_customize );
    $this->add_general_options( $wp_customize );
  }

  /**
   * add brand_bar, brand_color options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  protected function add_brand_options( $wp_customize ) {

    // show site name / lockup in footer?
    $wp_customize->add_setting('show_site_name_in_footer', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_site_name_in_footer', [
        'section'           => 'title_tagline'
      , 'type'              => 'checkbox'
      , 'label'             => __('Show site title and tagline in footer', TEXT_DOMAIN)
    ] );

    // lockup?
    $wp_customize->add_setting( 'lockup_hash', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => ''
      , 'transport' => 'refresh'
      , 'sanitize_callback' => [ $this, 'validate_lockup_hash' ]
    ] );
    $wp_customize->add_control( 'lockup_hash', [
        'section' => 'title_tagline'
      , 'type' => 'text'
      , 'label' => 'Lockup'
      , 'description' => 'To display a Stanford lockup, enter the string provided by University Communications. Leave blank to not use a lockup.'
    ] );

    // brand bar or stroke?
    $wp_customize->add_setting( 'brand_bar', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'bar'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'brand_bar', [
        'section' => 'title_tagline'
      , 'type' => 'radio'
      , 'label' => 'Branding'
      , 'choices' => [
            'bar' => 'Full brand bar'
          , 'stroke' => 'Stroke'
        ]
    ] );

    // brand bar color
    $wp_customize->add_setting( 'brand_color', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'cardinal'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'brand_color', [
        'section' => 'title_tagline'
      , 'type' => 'radio'
      , 'label' => 'Brand bar color'
      , 'description' => 'Only applies to full brand bar'
      , 'choices' => [
            'cardinal' => 'Cardinal red'
          , 'bright' => 'Bright red'
          , 'dark' => 'Black'
          , 'white' => 'White'
        ]
    ] );

  }

  /**
   * Validate the hash entered to enable displaying a full lockup.
   *
   * @param string $hash value entered by site admin
   * @return null|string
   */
  public function validate_lockup_hash( $hash ) {
    if ( empty( trim( $hash  ) ) ) {
      set_theme_mod( 'lockup', 'false');
      return '';
    }

    $site_url = get_bloginfo( 'url' );
    $valid = md5( $site_url . $this->author );
    if ( $hash == $valid ) {
      set_theme_mod( 'lockup', 'true');
      return $hash;
    }
    else {
      set_theme_mod( 'lockup', 'false');
      return NULL;
    }
  }

  /**
   * add banner caption location, width options to Customizer
   *
   * @param $wp_customize
   */
  public function add_banner_options( $wp_customize ) {
    // banner width
    $wp_customize->add_setting( 'banner_width', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'content'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'banner_width', [
        'section' => 'header_image'
      , 'type'    => 'radio'
      , 'label'   => 'Banner width'
      , 'choices' => [
            'content' => __( 'Content width', TEXT_DOMAIN )
          , 'full' => __( 'Full width', TEXT_DOMAIN )
        ]
    ] );

    // banner text
    $wp_customize->add_setting( 'banner_text', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => ''
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'wp_kses_post'
    ] );
    $wp_customize->add_control( 'banner_text', [
        'section' => 'header_image'
      , 'type'    => 'textarea'
      , 'label'   => 'Banner Text'
      , 'description' => 'Enter text or HTML. Use &lt;h2&gt; for a title.'
    ] );

    // banner text location
    $wp_customize->add_setting( 'banner_text_location', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'bottom-left'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'banner_text_location', [
        'section' => 'header_image'
      , 'type'    => 'radio'
      , 'label'   => 'Banner text location'
      , 'choices' => [
            'bottom-left' => __( 'Bottom left', TEXT_DOMAIN )
          , 'top-left' => __( 'Top left', TEXT_DOMAIN )
          , 'top-right' => __( 'Top right', TEXT_DOMAIN )
          , 'bottom-right' => __( 'Bottom right', TEXT_DOMAIN )
        ]
    ] );
  }

  /**
   * add top_nav_theme, top_nav_align options to Customizer
   *
   * @param $wp_customize
   */
  protected function add_nav_options( $wp_customize ) {
    // menu theme
    $wp_customize->add_setting( 'top_nav_theme', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'default'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'top_nav_theme', [
        'section' => 'menu_locations'
      , 'type' => 'radio'
      , 'label' => 'Top nav theme'
      , 'choices' => [
            'default' => 'Default - light background, dark dropdowns'
          , 'light' => 'Light - light background, light dropdowns'
//        , 'dark'   => 'Dark - transparent background, dark dropdowns (use on dark backgrounds)'
        ]
      , 'priority' => 14
    ] );

    // menu alignment
    $wp_customize->add_setting( 'top_nav_align', [
        'type' => 'theme_mod'
      , 'capability' => 'edit_theme_options'
      , 'default' => 'left'
      , 'transport' => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'top_nav_align', [
        'section' => 'menu_locations'
      , 'type' => 'radio'
      , 'label' => 'Top nav alignment'
      , 'choices' => [
            'left' => 'Left'
          , 'center' => 'Center'
          , 'right' => 'Right'
        ]
      , 'priority' => 15
    ] );
  }

  /**
   * add homepage options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  protected function add_homepage_options( $wp_customize ) {
    $wp_customize->add_setting('post_section_title', [
        'type'        => 'theme_mod'
      , 'capability'  => 'edit_theme_options'
      , 'default'     => __('Recent posts', 'stanford_text_domain')
      , 'transport'   => 'refresh'
    ] );
    $wp_customize->add_control('post_section_title', [
        'label'       => __('Title of posts section', 'stanford_text_domain')
      , 'description' => __('<p>Enter a title that will appear above your latest posts. Leave blank for no title.</p>', TEXT_DOMAIN)
      , 'section'     => 'static_front_page'
      , 'settings'    => 'post_section_title'
      , 'type'        => 'text'
      , 'priority'    => 11
    ] );

    $wp_customize->add_setting('show_homepage_title', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => FALSE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_homepage_title', [
        'label'       => __('Show page title on homepage', 'stanford_text_domain')
      , 'description' => __('<p>When your homepage displays a static page, check this box to display the page\'s title on the homepage.</p>', TEXT_DOMAIN)
      , 'section'     => 'static_front_page'
      , 'settings'    => 'show_homepage_title'
      , 'type'        => 'checkbox'
      , 'priority'    => 20
    ] );
  }

  /**
   * add layout options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  protected function add_layout_options( $wp_customize ) {
    $wp_customize->add_section('layout', [
        'title'       => __('Page Layout', TEXT_DOMAIN)
      , 'priority'    => 150
      , 'description' => __('<p>Specify the layout of pages on large devices. Pages are responsive and may display fewer columns on smaller devices.</p><p>We recommend not using a left sidebar, but, if you choose to, we recommend only putting navigational widgets in the left sidebar.</p>', TEXT_DOMAIN)
    ] );

    $wp_customize->add_setting('home_layout', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => self::DEFAULT_LAYOUT
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control('home_layout', [
        'label'    => __('Home page layout', TEXT_DOMAIN)
      , 'section'  => 'layout'
      , 'type'     => 'select'
      , 'choices'  => $this->_get_supported_layouts()
    ] );

    $wp_customize->add_setting('page_layout', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => self::DEFAULT_LAYOUT
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control('page_layout', [
        'label'    => __('Default page layout (authors can choose a different layout for individual pages)', TEXT_DOMAIN)
      , 'section'  => 'layout'
      , 'type'     => 'select'
      , 'choices'  => $this->_get_supported_layouts()
    ] );

    $wp_customize->add_setting('post_layout', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => self::DEFAULT_LAYOUT
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control('post_layout', [
        'label'    => __('Default post layout (authors can choose a different layout for individual posts)', TEXT_DOMAIN)
      , 'section'  => 'layout'
      , 'type'     => 'select'
      , 'choices'  => $this->_get_supported_layouts()
    ] );

    $wp_customize->add_setting('author_layout', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => self::DEFAULT_LAYOUT
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control('author_layout', [
        'label'    => __('Layout for author pages', TEXT_DOMAIN)
      , 'section'  => 'layout'
      , 'type'     => 'select'
      , 'choices'  => $this->_get_supported_layouts()
    ] );

    $wp_customize->add_setting('default_layout', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => self::DEFAULT_LAYOUT
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control('default_layout', [
        'label'    => __('Layout for other pages', TEXT_DOMAIN)
      , 'section'  => 'layout'
      , 'type'     => 'select'
      , 'choices'  => $this->_get_supported_layouts()
    ] );
  }

  /**
   * add author options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  protected function add_author_options( $wp_customize ) {
    $wp_customize->add_section('author_page', [
        'title'       => __('Author page', TEXT_DOMAIN)
      , 'priority'    => 160
      , 'description' => __('<p>Specify what information about an author is displayed inline on the author page.</p>', TEXT_DOMAIN)
    ] );

    $wp_customize->add_setting('show_author_info_inline', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_author_info_inline', [
        'label'             => __('Show author\'s info inline on author page. (Use Redwood Author widget to display author\'s info in a sidebar.)', TEXT_DOMAIN)
      , 'section'           => 'author_page'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_author_avatar', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_author_avatar', [
        'label'             => __('Show author\'s avatar', TEXT_DOMAIN)
      , 'section'           => 'author_page'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_author_website', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_author_website', [
        'label'             => __('Show author\'s website', TEXT_DOMAIN)
      , 'section'           => 'author_page'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_author_email', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => FALSE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_author_email', [
        'label'             => __('Show author\'s email', TEXT_DOMAIN)
      , 'section'           => 'author_page'
      , 'type'              => 'checkbox'
    ] );
  }

  /**
   * add general theme options to Customizer
   *
   * @param \WP_Customize_Manager $wp_customize
   */
  protected function add_general_options( $wp_customize ) {
    $wp_customize->add_section('general', [
        'title'       => __('General options', TEXT_DOMAIN)
      , 'priority'    => 175
      , 'description' => __('<strong>General options for Redwood</strong>', TEXT_DOMAIN)
    ] );

    $wp_customize->add_setting('show_search', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_search', [
        'label'             => __('Show search form', TEXT_DOMAIN)
      , 'description'       => __('Show the "Search this site" form in the site\'s masthead.', TEXT_DOMAIN)
      , 'section'           => 'general'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_featured_image', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_featured_image', [
        'label'             => __('Display featured image inline', TEXT_DOMAIN)
      , 'description'       => __('Use Redwood Hero Image widget to display the post\'s featured image in a sidebar.', TEXT_DOMAIN)
      , 'section'           => 'general'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_author_in_byline', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => TRUE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_author_in_byline', [
        'label'             => __('Show author in byline', TEXT_DOMAIN)
      , 'section'           => 'general'
      , 'type'              => 'checkbox'
    ] );

    $wp_customize->add_setting('show_private_to_subscribers', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => FALSE
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => [ $this, 'sanitize_checkbox' ]
    ] );
    $wp_customize->add_control('show_private_to_subscribers', [
        'label'             => __('Allow subscribers to see private content?', TEXT_DOMAIN)
      , 'section'           => 'general'
      , 'type'              => 'checkbox'
    ] );

    // display excerpts or full posts?
    $wp_customize->add_setting( 'list_page_display', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => 'excerpts'
      , 'transport'         => 'refresh'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'list_page_display', [
        'label'             => 'On post list pages, display'
      , 'section'           => 'general'
      , 'type'              => 'radio'
      , 'choices'           => [
            'excerpts' => __( 'Post excerpts', TEXT_DOMAIN )
          , 'content'  => __( 'Full posts', TEXT_DOMAIN )
        ]
    ] );

    // Google analytics
    $wp_customize->add_setting( 'ga_property', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => ''
      , 'transport'         => 'postMessage'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'ga_property', [
        'label'             => 'Google analytics'
      , 'section'           => 'general'
      , 'type'              => 'text'
      , 'description'       => 'If you would like Google Analytics code included on your pages, enter your property\'s tracking ID here. Your tracking ID looks like <strong>UA-55555555-55</strong>.'
    ] );

    // RSS feed
    $wp_customize->add_setting( 'rss_featured_img', [
        'type'              => 'theme_mod'
      , 'capability'        => 'edit_theme_options'
      , 'default'           => ''
      , 'transport'         => 'postMessage'
      , 'sanitize_callback' => 'sanitize_text_field'
    ] );
    $wp_customize->add_control( 'rss_featured_img', [
        'label'             => 'Size of featured image to include in RSS feed'
      , 'section'           => 'general'
      , 'type'              => 'select'
      , 'choices'           => $this->get_image_sizes()
      , 'default'           => 'none'
      , 'description'       => 'If you would like to includes posts\' featured image in the RSS feed, select this size of image to include.'
    ] );

  }

  /**
   * Returns an array of supported layout types.
   *
   * @return array layout_slug => layout_display_name
   * @since 1.0.0
   */
  protected function _get_supported_layouts() {
    $layouts = array(
        'right' => __( 'Right sidebar', TEXT_DOMAIN )
      , 'left'  => __( 'Left sidebar',  TEXT_DOMAIN )
      , 'both'  => __( 'Both sidebars', TEXT_DOMAIN )
      , 'none'  => __( 'No sidebars',   TEXT_DOMAIN )
    );
    return $layouts;
  }

  /**
   * Add a meta box to the post and page edit pages that allows authors to specify a layout
   * other than the default.
   * Called when author is creating / editing a post / page.
   * Invoked via that add_meta_boxes action.
   *
   * @param string $post_type
   */
  public function add_meta_box( $post_type ) {
    // only add the meta box for posts and pages
    if ( in_array( $post_type, [ 'post', 'page' ] ) ) {
      add_meta_box(
          'layout'
          , __( 'Layout', TEXT_DOMAIN )
          , [ $this, 'render_layout_meta_box' ]
          , $post_type
          , 'side'
          , 'low'
      );
    }
  }

  /**
   * Echo the markup to render the Layout meta box for pages and posts.
   * Passed as a callback to the add_meta_box() call in $this->admin_add_meta_box.
   *
   * @param \WP_Post $post
   * @since 1.0.0
   */
  public function render_layout_meta_box( $post ) {
    // Add an nonce field so we can check for it later.
    wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

    // get the current value
    $value = get_post_meta($post->ID, self::LAYOUT_META_KEY, true);
    if ( empty( $value ) ) $value = 'default';

    // display the options, selecting the current value.
    $options = array_merge(
        [ 'default' => __( 'Default' , TEXT_DOMAIN ) ]
        , $this->_get_supported_layouts()
    );
    $default_layout = get_theme_mod($post->post_type . '_layout',Options::DEFAULT_LAYOUT );
    $options['default'] .= " ({$options[ $default_layout ]})";
    foreach ($options as $layout => $name) {
      echo ' <input type="radio" name="layout" value="' . $layout . '"';
      if ($value == $layout) echo 'checked="checked"';
      echo ' style="margin-left: 2em;" /> ';
      echo $name . '<br/>';
    }
  }

  /**
   * Save our custom (meta) field(s), passed to us in $_POST:
   * - for pages and posts, save the layout
   *
   * Invoked via the save_post_post and save_post_page actions.
   *
   * @param int $post_id
   * @param \WP_Post $post the original post
   * @param bool $update whether or not we're updating an existing post
   * @return int $post_id
   */
  public function save_post( $post_id, $post, $update ) {
    // if this is an autosave our form has not been submitted so don't do anything
    if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // validate nonce
    if ( !isset( $_POST[ self::NONCE_NAME ] ) ) return;
    if ( !wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION ) ) return;

    // Check the user's permissions.
    if ( !current_user_can('edit_' . $_POST[ 'post_type' ], $post_id ) ) return;

    // OK, it's safe for us to save the data now.
    $layouts = $this->_get_supported_layouts();
    $layout  = $_POST[ 'layout' ];
    if ( !in_array( $layout, array_keys( $layouts ) ) )
      $layout = 'default';
    update_post_meta( $post_id, self::LAYOUT_META_KEY, $layout );
  }

  /******************************************************************************
   *
   * Utilities
   *
   ******************************************************************************/

  /**
   * Ensure checkboxes are boolean
   * @param bool $value
   * @return bool
   */
  public function sanitize_checkbox( $value ) {
    return is_bool( $value ) ? $value : TRUE;
  }

  /**
   * Return an array of  image sizes
   * in the form of slug => Capitalized slug
   * @return array
   */
  public function get_image_sizes() {
    $size_names = [
        'none'    => __( 'Don\'t include featured images', TEXT_DOMAIN )
      , 'thumb'   => __( 'Thumbnail',     TEXT_DOMAIN )
      , 'medium'  => __( 'Medium',        TEXT_DOMAIN )
      , 'large'   => __( 'Large',         TEXT_DOMAIN )
      , 'orig'    => __( 'Original size', TEXT_DOMAIN )
    ];
    $additional_sizes = wp_get_additional_image_sizes();
    foreach ( $additional_sizes as $name => $attrs ) {
      $size_names[ $name ] = ucfirst( $name );
    }
    return $size_names;
  }

  /******************************************************************************
   *
   * Class setup
   *
   ******************************************************************************/

  /**
   * Options constructor.
   * Called once when singleton instance is created.
   * Declared as protected to prevent using new to instantiate instances other than the singleton.
   */
  protected function __construct() {
    add_action( 'customize_register', [ $this, 'add_theme_options' ] ) ;
    add_action( 'add_meta_boxes',     [ $this, 'add_meta_box'      ] ) ;
    add_action( 'save_post_post',     [ $this, 'save_post'         ], 10, 3 ) ;
    add_action( 'save_post_page',     [ $this, 'save_post'         ], 10, 3 ) ;
  }

  /**
   * Create singleton instance, if necessary.
   */
  public static function init() {
    if ( !is_a( self::$instance, __CLASS__ ) ) {
      self::$instance = new Options();
    }
    return self::$instance;
  }

}