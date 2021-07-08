<?php
namespace Stanford\Redwood;

class Author_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'author_widget' // slug
      , __('Redwood Author', 'text_domain') // name
      , [ 'description' => __( 'Display author info on author pages.', TEXT_DOMAIN ) ]
    );
  }

	/**
	 * Front-end display of widget
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     widget arguments
	 * @param array $instance saved values from database
	 */
	public function widget( $args, $instance ) {
    $author = '';

    // if we're on the author page and the widget displays on the author page
    if ( is_author() && $instance[ 'display_on_author_page' ] ) {
      // get the author info, either by name (slug) or by id
      $author = get_query_var('author_name')
              ? get_user_by('slug', get_query_var('author_name' ) )
              : get_userdata( get_query_var( 'author' ) );
    }
    // else if we're on on a single post and the widget displays on post pages
    elseif ( is_single() && $instance[ 'display_on_posts' ] ) {
      global $post; // the post we're displaying
      // figure out the id of the author of the current post
      if ( !empty( $post ) && is_a( $post, 'WP_Post' ) ) {
        $author_id = $post->post_author;
      } else {
        global $wp_query;
        if ( !empty( $wp_query->post ) && is_a( $wp_query->post, 'WP_Post' ) ) {
          $author_id = $wp_query->post->post_author;
        }
      }
      // if we've figured out the author's id, get her data
      if ( is_numeric( $author_id ) ) {
        $author = get_user_by('id', $author_id) ;
      }
    }
    // otherwise remain silent
    else {
      return;
    }

    // if we're supposed to display something, but we haven't figured for whom,
    // or if we don't have any relevant data on the user, bail
    if ( empty( $author ) || !$this->_have_data( $instance, $author ) ) return;

    // gather the data to be rendered
    $data = [
        'name'          => $author->display_name
      , 'page_url'      => get_author_posts_url( $author->ID, $author->user_nicename )
      , 'description'   => trim( $author->description )
      , 'before_widget' => $args[ 'before_widget' ]
      , 'after_widget'  => $args[ 'after_widget' ]
      , 'before_title'  => $args[ 'before_title' ]
      , 'after_title'   => $args[ 'after_title' ]
    ];

    if ( $instance[ 'display_avatar' ] ) {
      $data[ 'avatar' ] = get_avatar(
          $author->ID
        , 240
        , ''
        , 'avatar for ' . $author->display_name
        , [ 'force_display' => TRUE ] // ignore setting for displaying avatars in comments
      );
    }

    if ( $instance[ 'display_website' ] && !empty( $author->user_url ) ) {
      $data[ 'author_url' ] = $author->user_url;
    }

    if ( $instance[ 'display_email' ] && !empty( $author->user_email ) ) {
      $data[ 'author_email' ] = $author->user_email;
    }

    // render the template
    echo \Timber\Timber::fetch( 'widgets/author.twig', $data );
	}

  /**
   * Determine if we have any data to display in this instance of the widget
   * 
   * @param array $instance
   * @param WP_User $author
   * @return boolean TRUE if there's something to display, FALSE if not
   */
  protected function _have_data($instance, $author) {
    if (!empty($author->description)) return TRUE;
    if ($instance['display_avatar'])  return TRUE;
    if ($instance['display_website'] && !empty($author->user_url))   return TRUE;
    if ($instance['display_email']   && !empty($author->user_email)) return TRUE;
    return FALSE;
  }

  /**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance previously saved values from database
	 */
	public function form( $instance ) {
    $legend_where = __('<h3>Where to display:</h3>', TEXT_DOMAIN);
    $legend_what  = __('<h3>What to display:</h3>',  TEXT_DOMAIN);

    $display_avatar = array(
        'id'    => $this->get_field_id('display_avatar')
      , 'name'  => $this->get_field_name( 'display_avatar' )
      , 'label' => __('Display author\'s avatar', TEXT_DOMAIN )
      , 'value' => isset($instance['display_avatar']) ? $instance['display_avatar'] : true
    );

    $display_website = array(
        'id'    => $this->get_field_id('display_website')
      , 'name'  => $this->get_field_name( 'display_website' )
      , 'label' => __('Display author\'s website', TEXT_DOMAIN )
      , 'value' => isset($instance['display_website']) ? $instance['display_website'] : true
    );

    $display_email = array(
        'id'    => $this->get_field_id('display_email')
      , 'name'  => $this->get_field_name( 'display_email' )
      , 'label' => __('Display author\'s email address', TEXT_DOMAIN )
      , 'value' => isset($instance['display_email']) ? $instance['display_email'] : false
      , 'warn'  => __('This applies to all authors. Please ensure all authors consent to having their email displayed.', TEXT_DOMAIN)
    );

    $display_on_author_page = array(
        'id'    => $this->get_field_id('display_on_author_page')
      , 'name'  => $this->get_field_name( 'display_on_author_page' )
      , 'label' => __('Display on author pages', TEXT_DOMAIN )
      , 'value' => isset($instance['display_on_author_page']) ? $instance['display_on_author_page'] : false
      , 'info'  => __('Check this box to display this widget on the author pages. See <a href="/wp-admin/customize.php?autofocus[section]=author_page">author page options</a> to display author info inline on author pages.', TEXT_DOMAIN)
    );

    $display_on_posts = array(
        'id'    => $this->get_field_id('display_on_posts')
      , 'name'  => $this->get_field_name( 'display_on_posts' )
      , 'label' => __('Display on post pages', TEXT_DOMAIN )
      , 'value' => isset($instance['display_on_posts']) ? $instance['display_on_posts'] : true
      , 'info'  => __('Check this box to display the author\'s info on post pages.', TEXT_DOMAIN)
    );
		?>
    <fieldset>
      <legend><?php echo $legend_where; ?></legend>
      <p>
        <label for="<?php echo $display_on_author_page['id']; ?>"><?php echo $display_on_author_page['label']; ?></label> 
        <input class="widefat"
               id="<?php echo $display_on_author_page['id']; ?>"
               name="<?php echo $display_on_author_page['name']; ?>"
               type="checkbox"
               <?php echo ($display_on_author_page['value']) ? 'checked="checked"' : ""; ?>
               />
        <br/><em><?php echo $display_on_author_page['info']; ?></em>
      </p>

      <p>
        <label for="<?php echo $display_on_posts['id']; ?>"><?php echo $display_on_posts['label']; ?></label> 
        <input class="widefat"
               id="<?php echo $display_on_posts['id']; ?>"
               name="<?php echo $display_on_posts['name']; ?>"
               type="checkbox"
               <?php echo ($display_on_posts['value']) ? 'checked="checked"' : ""; ?>
               />
        <br/><em><?php echo $display_on_posts['info']; ?></em>
      </p>
    </fieldset>

    <fieldset>
      <legend><?php echo $legend_what; ?></legend>
      <p>
        <label for="<?php echo $display_avatar['id']; ?>"><?php echo $display_avatar['label']; ?></label> 
        <input class="widefat"
               id="<?php echo $display_avatar['id']; ?>"
               name="<?php echo $display_avatar['name']; ?>"
               type="checkbox"
               <?php echo ($display_avatar['value']) ? 'checked="checked"' : ""; ?>
               />
      </p>

      <p>
        <label for="<?php echo $display_website['id']; ?>"><?php echo $display_website['label']; ?></label> 
        <input class="widefat"
               id="<?php echo $display_website['id']; ?>"
               name="<?php echo $display_website['name']; ?>"
               type="checkbox"
               <?php echo ($display_website['value']) ? 'checked="checked"' : ""; ?>
               />
      </p>

      <p>
        <label for="<?php echo $display_email['id']; ?>"><?php echo $display_email['label']; ?></label> 
        <input class="widefat"
               id="<?php echo $display_email['id']; ?>"
               name="<?php echo $display_email['name']; ?>"
               type="checkbox"
               <?php echo ($display_email['value']) ? 'checked="checked"' : ""; ?>
               />
        <br/><em><?php echo $display_email['warn']; ?></em>
      </p>
    </fieldset>
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
		$instance = array(
        'display_on_author_page' => empty($new_instance['display_on_author_page']) ? FALSE : TRUE
      , 'display_on_posts'       => empty($new_instance['display_on_posts'])       ? FALSE : TRUE
      , 'display_avatar'         => empty($new_instance['display_avatar'])         ? FALSE : TRUE
      , 'display_website'        => empty($new_instance['display_website'])        ? FALSE : TRUE
      , 'display_email'          => empty($new_instance['display_email'])          ? FALSE : TRUE
    );

		return $instance;
	}
}