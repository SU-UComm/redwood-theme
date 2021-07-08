<?php
namespace Stanford\Redwood;

class Hero_Image_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'hero_image_widget' // slug
      , __('Redwood Hero Image', TEXT_DOMAIN) // name
      , [ 'description' => __( 'Display a hero image.', TEXT_DOMAIN ) ]
      , [ 'width' => 450 ]
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
    global $post;

    //
    $use_post_image = apply_filters('widget_hero_image_use_post_image', $instance['use_post_image']);
		$url     = apply_filters('widget_hero_image_img_src', $instance['img_src']);
		$alt     = apply_filters('widget_hero_image_alt', $instance['alt']);
		$caption = apply_filters('widget_hero_image_caption', $instance['caption']);
		$srcset  = '';

    // if  we're to use the page / post's image
    // and we're on singular page
    // and the current post has a featured image
    if ( $use_post_image && is_singular() && has_post_thumbnail( $post->ID)  ) {
      // get the info for the featured image
      $img_id  = get_post_thumbnail_id( $post->ID );
      $img     = get_post( $img_id );
      $caption = trim($img->post_excerpt);
      $alt     = get_post_meta( $img_id, '_wp_attachment_image_alt', TRUE );
      $url     = wp_get_attachment_image_url( $img_id, 'orig' );
      $srcset  = wp_get_attachment_image_srcset( $img_id );
    }

    // if we don't have an image to display, bail
    if ( empty( $url ) ) return;

    $data = [
        'url'           => esc_attr( $url )
      , 'srcset'        => esc_attr( $srcset )
      , 'alt'           => esc_attr( $alt )
      , 'caption'       => $caption
      , 'before_widget' => $args[ 'before_widget' ]
      , 'after_widget'  => $args[ 'after_widget' ]
    ];

    // render the template
    echo \Timber\Timber::fetch( 'widgets/hero_image.twig', $data );
	}


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance previously saved values from database
	 */
	public function form( $instance ) {
    $use_post_image = array(
        'id'    => $this->get_field_id('use_post_image')
      , 'name'  => $this->get_field_name( 'use_post_image' )
      , 'label' => __('<strong>Use post image if available</strong>', TEXT_DOMAIN )
      , 'value' => isset($instance['use_post_image']) ? $instance['use_post_image'] : TRUE
    );

    $default_img_src = array(
        'id'    => $this->get_field_id('img_src')
      , 'name'  => $this->get_field_name( 'img_src' )
      , 'label' => __('<strong>URL of default image</strong><br/><em>Leave blank to not display a default image.</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['img_src']) ? $instance['img_src'] : ''
    );

    $default_img_alt = array(
        'id'    => $this->get_field_id('alt')
      , 'name'  => $this->get_field_name( 'alt' )
      , 'label' => __('<strong>Alt text for default image</strong><br/><em>Required if default image is specified and is non-decorative.</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['alt']) ? $instance['alt'] : ''
    );

    $default_img_caption = array(
        'id'    => $this->get_field_id('caption')
      , 'name'  => $this->get_field_name( 'caption' )
      , 'label' => __('<strong>Caption for default image</strong><br/><em>HTML is accepted. Leave blank for no caption.</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['caption']) ? $instance['caption'] : ''
    );

		?>
		<p>
		<label for="<?php echo $use_post_image['id']; ?>" style="font-size: large; padding-right: 0.5em;"><?php echo $use_post_image['label']; ?></label>
		<input class="widefat"
           id="<?php echo $use_post_image['id']; ?>"
           name="<?php echo $use_post_image['name']; ?>"
           type="checkbox"
           <?php echo ($use_post_image['value']) ? 'checked="checked"' : ""; ?>
           />
		</p>
    <hr/>
    <h3>Default Image</h3>
    <p>If no post image is displayed, you may render a default image.</p>
		<p>
		<label for="<?php echo $default_img_src['id']; ?>"><?php echo $default_img_src['label']; ?></label> 
		<input class="widefat"
           id="<?php echo $default_img_src['id']; ?>"
           name="<?php echo $default_img_src['name']; ?>"
           type="text"
           value="<?php echo $default_img_src['value']; ?>"
           />
		</p>
		<p>
		<label for="<?php echo $default_img_alt['id']; ?>"><?php echo $default_img_alt['label']; ?></label> 
		<input class="widefat"
           id="<?php echo $default_img_alt['id']; ?>"
           name="<?php echo $default_img_alt['name']; ?>"
           type="text"
           value="<?php echo $default_img_alt['value']; ?>"
           />
		</p>
		<p>
		<label for="<?php echo $default_img_caption['id']; ?>"><?php echo $default_img_caption['label']; ?></label> 
		<textarea class="widefat"
           id="<?php echo $default_img_caption['id']; ?>"
           name="<?php echo $default_img_caption['name']; ?>"
           rows="3"
           ><?php echo $default_img_caption['value']; ?></textarea>
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
		$instance = array(
        'use_post_image'  => empty( $new_instance[ 'use_post_image' ] ) ? FALSE : TRUE
      , 'img_src'         => filter_var( $new_instance['img_src'], FILTER_VALIDATE_URL )
      , 'alt'             => esc_attr( $new_instance[ 'alt' ] )
      , 'caption'         => wp_kses_post( $new_instance['caption'] )
    );

		return $instance;
	}
}