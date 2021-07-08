<?php
namespace Stanford\Redwood;

class Well_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'well_widget' // slug
      , __('Redwood Well', 'text_domain') // name
      , [ 'description' => __( 'Displays content in box with colored stroke.', TEXT_DOMAIN ) ]
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
    $data = [
        'title'         => apply_filters( 'widget_title', trim( $instance[ 'title' ] ) )
      , 'content'       => do_shortcode( $instance[ 'content' ] )
      , 'before_widget' => $args[ 'before_widget' ]
      , 'after_widget'  => $args[ 'after_widget' ]
      , 'before_title'  => $args[ 'before_title' ]
      , 'after_title'   => $args[ 'after_title' ]
    ];
    echo \Timber\Timber::fetch( 'widgets/well.twig', $data );
	}


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance previously saved values from database
	 */
	public function form( $instance ) {
    $title   = isset($instance['title'])   ? $instance['title']   : __( 'Well Title', TEXT_DOMAIN );
    $content = isset($instance['content']) ? $instance['content'] : <<<EOSampleContent
<p>Enter plain text or HTML.</p>
<ul>
  <li><a href="#">Item 1</a></li>
  <li><a href="#">Item 2</a></li>
</ul>
EOSampleContent;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', TEXT_DOMAIN ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:', TEXT_DOMAIN ); ?></label>
		<textarea class="widefat" rows="15" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo $content; ?></textarea>
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
		$instance = array();
		$instance['title']   = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['content'] = (!empty($new_instance['content'])) ? trim($new_instance['content']) : '';

		return $instance;
	}
}