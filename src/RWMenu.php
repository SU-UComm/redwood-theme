<?php
namespace Stanford\Redwood;

/**
 * Class RWMenu extends \Timber\Menu
 *
 * Let \Timber\Menu parse the menu structure, and add a method to return the data formatted in the way
 * Decanter's main-nav.twig expects it.
 *
 * @package Stanford\Redwood
 */
class RWMenu extends \Timber\Menu {

  /** @var array data formatted for Decanter's main-nav.twig */
  protected $decanter_data;

  /**
   * Initialize a Redwood menu.
   *
   * @param int|string $slug    A menu slug, the term ID of the menu, the full name from the admin
   *                            menu, the slug of the registered location or nothing. Passing nothing
   *                            is good if you only have one menu. Timber will grab what it finds.
   * @param array      $options An array of options, right now only `depth` is supported
   */
  public function __construct( $slug = 0, array $options = [] ) {
    if ( !isset( $options[ 'depth' ] ) || $options[ 'depth' ] > 2 ) {
      $options[ 'depth' ] = 2;
    }
    parent::__construct( $slug, $options );

    $modifier_classes = [];
    if ( $slug == 'top' ) {
      $align = get_theme_mod('top_nav_align','left' );
      $modifier_classes[] = "su-main-nav--{$align}";
      if ( get_theme_mod( 'show_search', TRUE ) ) {
        $modifier_classes[] = "su-main-nav--mobile-search";
      }

      $theme = get_theme_mod('top_nav_theme','default' );
      if ( $theme != 'default' ) {
        $modifier_classes[] = "su-main-nav--" . $theme;
      }
    }
    $modifier_class = implode( ' ', $modifier_classes );
    $this->decanter_data = [
        "toggle_text" => "Menu"
      , "list_items" => $this->_structure_items( $this->items )
      , "modifier_class" => $modifier_class
      , "mobile_search" => [
            "action" => "/"
          , "method" => "get"
          , "search_label" => "Search this site"
          , "placeholder" => "Search this site"
          , "search_input_name" => "s"
        ]
    ];
  }

  /**
   * @param \Timber\MenuItem[] $items
   * @param int $level nesting level (1 is top level)
   * @return array
   */
  protected function _structure_items( $items, $level = 1 ) {
    $menu_items = [];
    if ( !empty( $items ) ) {
      foreach ( $items as $item ) {
        $menu_item = [
            "href" => $item->url
          , "text" => $item->name
        ];
        if ( !empty( $item->target ) ) {
          $menu_item[ 'target' ] = $item->target;
        }
        if ( $item->current || $item->current_item_parent ) {
          $menu_item[ 'current' ] = "true";
        }
        if ( !empty( $item->children ) ) {
          $next_level = $level + 1;
          $menu_item[ "lv{$next_level}" ] = $this->_structure_items( $item->children, $next_level );
        }
        $menu_items[] = $menu_item;
      }
    }
    return $menu_items;
  }

  /**
   * @return array data formatted for Decanter's main-nav.twig template
   */
  public function get_decanter_data() {
    return $this->decanter_data;
  }

}