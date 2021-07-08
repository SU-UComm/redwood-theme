<?php
namespace Stanford\Redwood;

class Page_Nav_Walker extends \Walker_Page {

  protected $depth_increment = 1;
  protected $child_prefix    = "&rsaquo;&nbsp;";
  protected $show_lock       = 0;

  public function __construct( $options = [] ) {
    $this->depth_increment = isset( $options[ 'increment' ] ) ? intval( $options[ 'increment' ] ) : 1;
    $this->child_prefix    = isset( $options[ 'prefix' ] ) ? $options[ 'prefix' ] : "&rsaquo;&nbsp;";
    $this->show_lock       = isset( $options[ 'show_lock' ] ) ? intval( $options[ 'show_lock' ] ) : 0;
  }

  /**
   * Outputs the beginning of the current level in the tree before elements are output.
   *
   * @see Walker::start_lvl()
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
   * @param array  $args   Optional. Arguments for outputing the next level.
   *                       Default empty array.
   */
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= "\n<ul>\n";
  }

  /**
   * Outputs the end of the current level in the tree after elements are output.
   *
   * @see Walker::end_lvl()
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
   * @param array  $args   Optional. Arguments for outputting the end of the current level.
   *                       Default empty array.
   */
  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= "\n</ul>\n";
  }

  /**
   * Outputs the beginning of the current element in the tree.
   *
   * @see Walker::start_el()
   * @since 2.1.0
   * @access public
   *
   * @param string  $output       Used to append additional content. Passed by reference.
   * @param WP_Post $page         Page data object.
   * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
   * @param array   $args         Optional. Array of arguments. Default empty array.
   * @param int     $current_page Optional. Page ID. Default 0.
   */
  public function start_el_HIDE( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
    if ( !empty( $this->child_prefix ) ) {
      $args[ 'link_before' ] = str_repeat( $this->child_prefix, $depth + $this->depth_increment ) . "&nbsp;";
    }
    parent::start_el( $output, $page, $depth, $args, $current_page );
  }

  /**
   * Outputs the end of the current element in the tree.
   *
   * @since 2.1.0
   *
   * @see Walker::end_el()
   *
   * @param string  $output Used to append additional content. Passed by reference.
   * @param WP_Post $page   Page data object. Not used.
   * @param int     $depth  Optional. Depth of page. Default 0 (unused).
   * @param array   $args   Optional. Array of arguments. Default empty array.
   */
  public function end_el( &$output, $page, $depth = 0, $args = array() ) {
    $private_html = '&nbsp;&nbsp;<i class="fas fa-lock" aria-label="private"></i>';
    if ( $this->show_lock && $page->post_status == "private" ) {
      $last_a = strrpos( $output , '</a>' );
      $beg = substr( $output, 0, $last_a );
      $end = substr( $output, $last_a );
      $output = $beg . $private_html . $end;
    }
    parent::end_el( $output, $page, $depth, $args );
  }
}
