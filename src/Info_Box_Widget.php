<?php
namespace Stanford\Redwood;

class Info_Box_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'info_box_widget' // slug
      , __( 'Redwood Info Box', TEXT_DOMAIN ) // name
      , [ 'description' => __( 'Displays a large Font Awesome icon alongside custom text.', TEXT_DOMAIN ) ]
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
        'icon'          => trim( $instance['icon'] )
      , 'title'         => trim( $instance[ 'title' ] )
      , 'content'       => trim( $instance[ 'text' ] )
      , 'before_widget' => $args[ 'before_widget' ]
      , 'after_widget'  => $args[ 'after_widget' ]
      , 'before_title'  => $args[ 'before_title' ]
      , 'after_title'   => $args[ 'after_title' ]
    ];
    echo \Timber\Timber::fetch( 'widgets/info_box.twig', $data );
  }


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance previously saved values from database
	 */
	public function form( $instance ) {
    global $theme; // global instance of theme class (Cardinal or Cardinal_Custom)
    $title = array(
        'id'    => $this->get_field_id("title")
      , 'name'  => $this->get_field_name("title")
      , 'label' => __('<strong>Title</strong>', TEXT_DOMAIN )
      , 'value' => isset($instance["title"]) ? $instance["title"] : "Info Box Title"
    );

    $icon = array(
        'id'    => $this->get_field_id("icon")
      , 'name'  => $this->get_field_name("icon")
      , 'label' => __('<strong>Font Awesome icon</strong>', TEXT_DOMAIN)
      , 'value' => isset($instance["icon"]) ? $instance["icon"] : "fa-info-circle"
    );

    $text = array(
        'id'    => $this->get_field_id("text")
      , 'name'  => $this->get_field_name("text")
      , 'label' => __('<strong>Text</strong>', TEXT_DOMAIN )
      , 'value' => isset($instance["text"]) ? $instance["text"] : ""
    );
    if (empty($text['value'])) {
      $text['value'] = <<<EOTEXT
Enter plain text or HTML.
<ul>
  <li><a href="#">Item 1</a></li>
  <li><a href="#">Item 2</a></li>
</ul>
EOTEXT;
    }
		?>
    <p>
      <label for="<?php echo $icon['id']; ?>"><?php echo $icon['label']; ?></label><br/>
      <input class="info_box_widget_icon"
             id="<?php echo $icon['id']; ?>"
             name="<?php echo $icon['name']; ?>"
             type="text"
             value="<?php echo $icon['value']; ?>"
             />
      &nbsp;
      <i class="fa <?php echo $icon['value']; ?> fa-2x"></i>
      <br/>&nbsp;<em>See <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome icons</a> for valid icons.</em>
    </p>

    <p>
      <label for="<?php echo $title['id']; ?>"><?php echo $title['label']; ?></label><br/>
      <input class="info_box_widget_title widefat"
             id="<?php echo $title['id']; ?>"
             name="<?php echo $title['name']; ?>"
             type="text"
             value="<?php echo $title['value']; ?>"
             />
    </p>

    <p>
      <label for="<?php echo $text['id']; ?>"><?php echo $text['label']; ?></label> 
      <textarea class="widefat"
             id="<?php echo $text['id']; ?>"
             name="<?php echo $text['name']; ?>"
             rows="6"
             ><?php echo $text['value']; ?></textarea>
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
		    'title' => wp_kses( $new_instance[ 'title' ], [ 'b' => [], 'em' => [], 'i' => [], 'strong' => [] ] ),
        'icon'  => wp_kses( $new_instance[ 'icon'  ], [] ),
        'text'  => wp_kses_post( trim( $new_instance[ 'text' ] ) )
    ];

		return $instance;
	}
}