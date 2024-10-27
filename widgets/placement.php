<?php

// The widget class
class AdsInserter_Placement_Widget extends WP_Widget {

	/**
	 * AdsInserter_Placement_Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'adsinserter_placement',
			__( 'AdsInserter placement', 'text_domain' ),
			array(
				'description' => 'Insert AdsInserter placement by ID',
				'customize_selective_refresh' => true,
			)
		);
	}


	/**
	 * Display options form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'placement_id' => ''
		);

		// Parse current settings with defaults
		extract( wp_parse_args( (array)$instance, $defaults) );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placement_id' ) ); ?>">
				<?php _e( 'Placement ID', 'text_domain' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo esc_attr( $this->get_field_id( 'placement_id' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'placement_id' ) ); ?>"
			       type="number"
			       value="<?php echo esc_attr( $placement_id ); ?>"
			       required/>
		</p>
		<?php
	}


	/**
	 * Handle update request
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['placement_id'] = isset( $new_instance['placement_id'] ) ? (int)$new_instance['placement_id'] : '';
		return $instance;
	}


	/**
	 * Display widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// Check the widget options
		$placement_id = isset( $instance['placement_id'] ) ? (int)$instance['placement_id'] : false;

		if (!$placement_id) {
			return;
		}

		// WordPress core before_widget hook (always include )
		echo $args['before_widget'];

		// Display placement
		echo '<div class="ai-placement" data-id="' . $placement_id . '"></div>';

		// WordPress core after_widget hook (always include )
		echo $args['after_widget'];
	}

}
