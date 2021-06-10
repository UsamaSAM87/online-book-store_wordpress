<?php
/*
Plugin Name: DisplayFactPlugin
Author: Chan Lim
Version: 1.0
*/

// The widget class
class My_Custom_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'my_custom_widget',
			__( 'My Custom Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'select'   => ''
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>


		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Select', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'option_1' => __( 'Events', 'text_domain' ),
				'option_2' => __( 'Births', 'text_domain' ),
				'option_3' => __( 'Deaths', 'text_domain' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';

			} ?>
			</select>
		</p>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {

		extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		
		$select   = isset( $instance['select'] ) ? $instance['select'] : '';

		// WordPress core before_widget hook (always include )
		echo $before_widget;
 
        
        //set url to get the API
        $url = "http://history.muffinlabs.com/date";
        //intitialize a curl session
        $ch = curl_init();

        //set options with a curl session resource and an option
        curl_setopt($ch, CURLOPT_URL, $url);
        //make curl return a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute the curl
        $result=curl_exec($ch);
        //convert json format to php array
        $result=json_decode($result, true);

        //get the length of each array for fact to use in creating random numbers
        $lenEvent = sizeof($result['data']['Events']);
        $lenBirth = sizeof($result['data']['Births']);
        $lenDeath = sizeof($result['data']['Deaths']);

        //creata each random number
        $i = rand(0,$lenEvent-1);
        $j = rand(0,$lenBirth-1);
        $k = rand(0,$lenDeath-1);

        
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';

			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			// Display select field
/*			if ( $select ) {
				echo '<p>' . $select . '</p>';
			}
*/        
            switch( $select ) {
                case 'option_1':    
                    echo '<p>Fact of the day (<strong style="color:goldenrod">' . $result['date'] . '</strong>)</p>';
                    echo '<p><strong style="color:goldenrod">Events</strong> - ' . $result['data']['Events'][$i]['year'] . '-' . $result['data']['Events'][$i]['text'] . '</p>';
                    break;
                case 'option_2':    
                    echo '<p>Fact of the day (<strong style="color:goldenrod">' . $result['date'] . '</strong>)</p>';
                    echo '<p><strong style="color:goldenrod">Births</strong> - ' . $result['data']['Births'][$j]['year'] . '-' . $result['data']['Births'][$j]['text'] . '</p>';
                    break;
                case 'option_3':    
                    echo '<p>Fact of the day (<strong style="color:goldenrod">' . $result['date'] . '</strong>)</p>';
                    echo '<p><strong style="color:goldenrod">Deaths</strong> - ' . $result['data']['Deaths'][$k]['year'] . '-' . $result['data']['Deaths'][$k]['text'] . '</p>';
                    break; 
                default:    
                    break;
			}
		echo '</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

}

// Register the widget
function my_register_custom_widget() {
	register_widget( 'My_Custom_Widget' );
}
add_action( 'widgets_init', 'my_register_custom_widget' );

?>