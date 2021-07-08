<?php
namespace Stanford\Redwood;

class Social_Widget extends \WP_Widget {

  public function __construct() {
    parent::__construct(
        'social-icons' // slug
      , __('Redwood Social Links', 'text_domain') // name
      , [ 'description' => __( 'Links to social media sites, rendered as icons.', TEXT_DOMAIN ) ]
      , [ 'width' => 350 ]
    );
  }

	/**
	 * Front-end display of widget
	 *
	 * @see WP_Widget::widget()ß
	 *
	 * @param array $args     widget arguments
	 * @param array $instance saved values from database
	 */
	public function widget( $args, $instance ) {
    $data = [
        'title'         => trim( @$instance[ 'title' ] )
      , 'facebook_url'  => @$instance[ 'facebook' ]
      , 'twitter_url'   => @$instance[ 'twitter' ]
      , 'linkedin_url'  => @$instance[ 'linkedin' ]
      , 'itunes_url'    => @$instance[ 'itunes' ]
      , 'youtube_url'   => @$instance[ 'youtube' ]
      , 'instagram_url' => @$instance[ 'instagram' ]
      , 'reddit_url'    => @$instance[ 'reddit' ]
      , 'before_widget' => @$args[ 'before_widget' ]
      , 'after_widget'  => @$args[ 'after_widget' ]
      , 'before_title'  => @$args[ 'before_title' ]
      , 'after_title'   => @$args[ 'after_title' ]
    ];
    echo \Timber\Timber::fetch( 'widgets/social_icons.twig', $data );
	}


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance previously saved values from database
	 */
	public function form( $instance ) {
    $title = array(
        'id'    => $this->get_field_id('title')
      , 'name'  => $this->get_field_name( 'title' )
      , 'label' => __('<strong>Title</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['title']) ? esc_html($instance['title']) : __('Connect with us', TEXT_DOMAIN)
    );
    $facebook = array(
        'id'    => $this->get_field_id('facebook')
      , 'name'  => $this->get_field_name( 'facebook' )
      , 'label' => __('<strong>URL of your Facebook page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['facebook']) ? esc_html($instance['facebook']) : ''
    );
    $twitter = array(
        'id'    => $this->get_field_id('twitter')
      , 'name'  => $this->get_field_name( 'twitter' )
      , 'label' => __('<strong>URL of your Twitter page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['twitter']) ? esc_html($instance['twitter']) : ''
    );
    $linkedin = array(
      'id'    => $this->get_field_id('linkedin')
    , 'name'  => $this->get_field_name( 'linkedin' )
    , 'label' => __('<strong>URL of your LinkedIn page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
    , 'value' => isset($instance['linkedin']) ? esc_html($instance['linkedin']) : ''
    );
    $itunes = array(
        'id'    => $this->get_field_id('itunes')
      , 'name'  => $this->get_field_name( 'itunes' )
      , 'label' => __('<strong>URL of your iTunes page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['itunes']) ? esc_html($instance['itunes']) : ''
    );
    $youtube = array(
        'id'    => $this->get_field_id('youtube')
      , 'name'  => $this->get_field_name( 'youtube' )
      , 'label' => __('<strong>URL of your YouTube page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['youtube']) ? esc_html($instance['facebook']) : ''
    );
    $instagram = array(
        'id'    => $this->get_field_id('instagram')
      , 'name'  => $this->get_field_name( 'instagram' )
      , 'label' => __('<strong>URL of your Instagram page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['instagram']) ? esc_html($instance['instagram']) : ''
    );
    $reddit = array(
        'id'    => $this->get_field_id('reddit')
      , 'name'  => $this->get_field_name( 'reddit' )
      , 'label' => __('<strong>URL of your Reddit page</strong><br/><em>Leave blank to not display</em>', TEXT_DOMAIN )
      , 'value' => isset($instance['reddit']) ? esc_html($instance['reddit']) : ''
    );

		?>
		<p>
      <label for="<?php echo $title['id']; ?>"><?php echo $title['label']; ?></label>
      <input class="widefat"
             id="<?php echo $title['id']; ?>"
             name="<?php echo $title['name']; ?>"
             type="text"
             value="<?php echo $title['value']; ?>"
             />
		</p>
    <hr/>
		<p>
      <i aria-hidden="true" class="fab fa-facebook fa-2x" style="float: right;"></i>
      <label for="<?php echo $facebook['id']; ?>"><?php echo $facebook['label']; ?></label>
      <input class="widefat"
             id="<?php echo $facebook['id']; ?>"
             name="<?php echo $facebook['name']; ?>"
             type="text"
             value="<?php echo $facebook['value']; ?>"
             />
		</p>
		<p>
      <i aria-hidden="true" class="fab fa-twitter fa-2x" style="float: right;"></i>
      <label for="<?php echo $twitter['id']; ?>"><?php echo $twitter['label']; ?></label>
      <input class="widefat"
             id="<?php echo $twitter['id']; ?>"
             name="<?php echo $twitter['name']; ?>"
             type="text"
             value="<?php echo $twitter['value']; ?>"
             />
		</p>
    <p>
      <i aria-hidden="true" class="fab fa-linkedin fa-2x" style="float: right;"></i>
      <label for="<?php echo $linkedin['id']; ?>"><?php echo $linkedin['label']; ?></label>
      <input class="widefat"
             id="<?php echo $linkedin['id']; ?>"
             name="<?php echo $linkedin['name']; ?>"
             type="text"
             value="<?php echo $linkedin['value']; ?>"
      />
    </p>
		<p>
      <i aria-hidden="true" class="fab fa-apple fa-2x" style="float: right;"></i>
      <label for="<?php echo $itunes['id']; ?>"><?php echo $itunes['label']; ?></label>
      <input class="widefat"
             id="<?php echo $itunes['id']; ?>"
             name="<?php echo $itunes['name']; ?>"
             type="text"
             value="<?php echo $itunes['value']; ?>"
             />
		</p>
		<p>
      <i aria-hidden="true" class="fab fa-youtube fa-2x" style="float: right;"></i>
      <label for="<?php echo $youtube['id']; ?>"><?php echo $youtube['label']; ?></label>
      <input class="widefat"
             id="<?php echo $youtube['id']; ?>"
             name="<?php echo $youtube['name']; ?>"
             type="text"
             value="<?php echo $youtube['value']; ?>"
             />
		</p>
		<p>
      <i aria-hidden="true" class="fab fa-instagram fa-2x" style="float: right;"></i>
      <label for="<?php echo $instagram['id']; ?>"><?php echo $instagram['label']; ?></label>
      <input class="widefat"
             id="<?php echo $instagram['id']; ?>"
             name="<?php echo $instagram['name']; ?>"
             type="text"
             value="<?php echo $instagram['value']; ?>"
             />
		</p>
		<p>
      <i aria-hidden="true" class="fab fa-reddit fa-2x" style="float: right;"></i>
      <label for="<?php echo $reddit['id']; ?>"><?php echo $reddit['label']; ?></label>
      <input class="widefat"
             id="<?php echo $reddit['id']; ?>"
             name="<?php echo $reddit['name']; ?>"
             type="text"
             value="<?php echo $reddit['value']; ?>"
             />
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
        'title'     => strip_tags($new_instance['title'])
      , 'facebook'  => filter_var($new_instance['facebook'],  FILTER_VALIDATE_URL)
      , 'twitter'   => filter_var($new_instance['twitter'],   FILTER_VALIDATE_URL)
      , 'linkedin'  => filter_var($new_instance['linkedin'],  FILTER_VALIDATE_URL)
      , 'itunes'    => filter_var($new_instance['itunes'],    FILTER_VALIDATE_URL)
      , 'youtube'   => filter_var($new_instance['youtube'],   FILTER_VALIDATE_URL)
      , 'instagram' => filter_var($new_instance['instagram'], FILTER_VALIDATE_URL)
      , 'reddit'    => filter_var($new_instance['reddit'],    FILTER_VALIDATE_URL)
    );

		return $instance;
	}
}