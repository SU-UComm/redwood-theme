<?php
namespace Stanford\Redwood;

class Page_Nav_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'page_nav_widget' // slug
        , __( 'Redwood Page Nav', 'text_domain' ) // name
        , [ 'description' => __( 'If a page has a parent or children, display the family of pages as a nav.', TEXT_DOMAIN ) ]
        , [ 'width' => 450 ]
    );
  }

  /**
   * Front-end display of widget
   *
   * @see WP_Widget::widget()
   *
   * @param array $args widget arguments
   * @param array $instance saved values from database
   */
  public function widget( $args, $instance ) {
    // if we're not a page, there's nothing to do
    if ( !is_page() ) return;

    global $post;

    // if the type of the post we're on isn't hierarchical, there's nothing to do
    $post_type = get_post_type_object( $post->post_type );
    if ( !$post_type->hierarchical ) return;

    // find the id of the parent page
    if ( $post->post_parent ) {
      // if the current post has a parent, go up until we find the top level ancestor
      $p = get_post( $post->post_parent );
      while ( $p->post_parent ) {
        $p = get_post( $p->post_parent );
      }
      $parent_id = $p->ID;
    } else {
      // if the current post does not have parent, assume it's a parent
      $parent_id = $post->ID;
    }

    // get the <li>'s for all descendants of the parent
    $post_status = [
        'publish'
    ];
    if ( @$instance[ 'include_private' ] ) {
      $post_status[] = 'private';
    }
    $walker_opts = [
        'increment' => $instance[ 'include_parent' ] ? 1 : 0
      , 'prefix'    => $instance[ 'prefix' ]
      , 'show_lock' => @$instance[ 'show_lock' ] ? 1 : 0
    ];
    $pages = wp_list_pages( [
        'child_of'     => $parent_id
      , 'echo'         => FALSE
      , 'sort_column'  => 'menu_order'
      , 'title_li'     => NULL
      , 'post_type'    => $post->post_type
      , 'post_status'  => $post_status
      , 'walker'       => new Page_Nav_Walker( $walker_opts )
    ] );
    // if there are no <li>'s, then the current post has no children, so there's nothing to display
    if ( empty( $pages ) ) return;

    // build the markup
    $title = apply_filters( 'widget_title', trim( $instance[ 'title' ] ) );
    if ( !empty( $title ) ) {
      $title = "{$args['before_title']}{$title}{$args['after_title']}";
    }
    $parent = $instance[ 'include_parent' ] ? "<a href=\"" . get_permalink( $parent_id ) . "\">" . get_the_title( $parent_id ) . "</a>" : "";

    $data = [
        'title'         => apply_filters( 'widget_title', trim( $instance[ 'title' ] ) )
      , 'parent'        => $parent
      , 'parent_active' => $parent && is_page( $parent_id )
      , 'pages'         => $pages
      , 'before_widget' => $args[ 'before_widget' ]
      , 'after_widget'  => $args[ 'after_widget' ]
      , 'before_title'  => $args[ 'before_title' ]
      , 'after_title'   => $args[ 'after_title' ]
    ];
    echo \Timber\Timber::fetch( 'widgets/page_nav.twig', $data );
  }


  /**
   * Back-end widget form
   *
   * @see WP_Widget::form()
   *
   * @param array $instance previously saved values from database
   */
  public function form( $instance ) {
    $title = [
        'id'    => $this->get_field_id( "title" )
      , 'name'  => $this->get_field_name( "title" )
      , 'label' => __( '<strong>Title</strong>', TEXT_DOMAIN )
      , 'value' => isset( $instance[ "title" ] ) ? $instance[ "title" ] : __( 'See Also', TEXT_DOMAIN )
    ];
    $prefix = [
        'id'    => $this->get_field_id( "prefix" )
      , 'name'  => $this->get_field_name( "prefix" )
      , 'label' => __( '<strong>Child prefix</strong>', TEXT_DOMAIN )
      , 'value' => isset( $instance[ "prefix" ] ) ? $instance[ "prefix" ] : "&rsaquo;&nbsp;"
    ];
    $include_parent = [
        'id'    => $this->get_field_id( "include_parent" )
      , 'name'  => $this->get_field_name( "include_parent" )
      , 'label' => __( '<strong>Include parent in list?</strong>', TEXT_DOMAIN )
      , 'value' => isset( $instance[ "include_parent" ] ) ? $instance[ "include_parent" ] : 1
    ];
    $include_private = [
        'id'    => $this->get_field_id( "include_private" )
      , 'name'  => $this->get_field_name( "include_private" )
      , 'label' => __( '<strong>Include private pages in list?</strong>', TEXT_DOMAIN )
      , 'value' => isset( $instance[ "include_private" ] ) ? $instance[ "include_private" ] : 0
    ];
    $show_lock = [
        'id'    => $this->get_field_id( "show_lock" )
      , 'name'  => $this->get_field_name( "show_lock" )
      , 'label' => __( '<strong>If including private pages, show lock next to them?</strong>', TEXT_DOMAIN )
      , 'value' => isset( $instance[ "show_lock" ] ) ? $instance[ "show_lock" ] : 0
    ];
    ?>
    <p>
      <label for="<?php echo $title[ 'id' ]; ?>"><?php echo $title[ 'label' ]; ?></label>
      <input id="<?php echo $title[ 'id' ]; ?>"
             name="<?php echo $title[ 'name' ]; ?>"
             type="text"
             value="<?php echo $title[ 'value' ]; ?>"
             class="widefat"
      />
    </p>

    <p>
      <label for="<?php echo $prefix[ 'id' ]; ?>"><?php echo $prefix[ 'label' ]; ?></label>
      <input id="<?php echo $prefix[ 'id' ]; ?>"
             name="<?php echo $prefix[ 'name' ]; ?>"
             type="text"
             value="<?php echo $prefix[ 'value' ]; ?>"
             class="widefat"
      />
    </p>
    <p><em>String to prepend to links to child pages.</em></p>

    <p>
      <label for="<?php echo $include_parent[ 'id' ]; ?>"><?php echo $include_parent[ 'label' ]; ?></label>
      <input name="<?php echo $include_parent[ 'name' ]; ?>"
             type="radio"
             value="1"
          <?php if ( $include_parent[ 'value' ] == 1 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> Yes
      <input name="<?php echo $include_parent[ 'name' ]; ?>"
             type="radio"
             value="0"
          <?php if ( $include_parent[ 'value' ] == 0 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> No
    </p>

    <p>
      <label for="<?php echo $include_private[ 'id' ]; ?>"><?php echo $include_private[ 'label' ]; ?></label>
      <input name="<?php echo $include_private[ 'name' ]; ?>"
             type="radio"
             value="1"
          <?php if ( $include_private[ 'value' ] == 1 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> Yes
      <input name="<?php echo $include_private[ 'name' ]; ?>"
             type="radio"
             value="0"
          <?php if ( $include_private[ 'value' ] == 0 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> No
    </p>

    <p>
      <label for="<?php echo $show_lock[ 'id' ]; ?>"><?php echo $show_lock[ 'label' ]; ?></label>
      <input name="<?php echo $show_lock[ 'name' ]; ?>"
             type="radio"
             value="1"
          <?php if ( $show_lock[ 'value' ] == 1 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> Yes
      <input name="<?php echo $show_lock[ 'name' ]; ?>"
             type="radio"
             value="0"
          <?php if ( $show_lock[ 'value' ] == 0 ) echo 'checked="checked"'; ?>
             style="margin-left: 0.75em;"
      /> No
    </p>
    <?php
  }

  /**
   * Sanitize widget form values as they are saved
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved
   * @param array $old_instance Previously saved values from database
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = [
        'title'           => sanitize_text_field( trim( $new_instance[ 'title' ] ) )
      , 'prefix'          => sanitize_text_field( $new_instance[ 'prefix' ] )
      , 'include_parent'  => intval( $new_instance[ 'include_parent' ] )
      , 'include_private' => intval( $new_instance[ 'include_private' ] )
      , 'show_lock'       => intval( $new_instance[ 'show_lock' ] )
    ];

    return $instance;
  }
}
