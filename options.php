<?php

class AdsInserter_Options {


	const OPTIONS = 'adsinserter_options';
	const OPTION_INTEXT_ENABLED = 'intext_enabled';
	const OPTION_INTEXT_PLACEMENT_ID = 'intext_placement_id';
	const OPTION_INTEXT_START = 'intext_start';
	const OPTION_INTEXT_DENSITY = 'intext_density';


	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;


	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_init', array( $this, 'register_options' ) );
	}


	/**
	 * Add options page
	 */
	public function add_menu_item() {
		add_menu_page(
		        'AdsInserter',
		        'AdsInserter',
		        'manage_options',
		        'adsinserter',
		        [ $this, 'create_page' ],
		        WP_PLUGIN_URL . '/adsinserter/images/logo16.png'
		);
	}


	public function create_page() {
		$this->options = get_option( self::OPTIONS );
		include (WP_PLUGIN_DIR . '/adsinserter/options/main.php');
    }


	/**
	 * Register and add settings
	 */
	public function register_options() {

		register_setting(
			'adsinserter', // Option group
			self::OPTIONS, // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_intext', // ID
			'In text placement', // Title
			'',/*array( $this, 'print_section_info' ),*/ // Callback
			'AdsInserter' // Page
		);

		add_settings_field(
			self::OPTION_INTEXT_ENABLED, // ID
			'Enabled', // Title
			array( $this, self::OPTION_INTEXT_ENABLED . '_callback' ), // Callback
			'AdsInserter', // Page
			'setting_intext' // Section
		);

		add_settings_field(
			self::OPTION_INTEXT_PLACEMENT_ID,
			'Placement ID',
			array( $this, self::OPTION_INTEXT_PLACEMENT_ID . '_callback' ),
			'AdsInserter',
			'setting_intext'
		);

		add_settings_field(
			self::OPTION_INTEXT_START,
			'Start from paragraph',
			array( $this, self::OPTION_INTEXT_START . '_callback' ),
			'AdsInserter',
			'setting_intext'
		);

		add_settings_field(
			self::OPTION_INTEXT_DENSITY,
			'Density, every paragraphs after start',
			array( $this, self::OPTION_INTEXT_DENSITY . '_callback' ),
			'AdsInserter',
			'setting_intext'
		);
	}



	/**
	 * Get the settings option array and print one of its values
	 */
	public function intext_enabled_callback() {
		printf(
			'<input type="checkbox" id="' . self::OPTION_INTEXT_ENABLED .'" name="' . self::OPTIONS. '[' . self::OPTION_INTEXT_ENABLED .']" %s />',
			isset( $this->options[self::OPTION_INTEXT_ENABLED] ) && $this->options[self::OPTION_INTEXT_ENABLED] ? 'checked' : ''
		);
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function intext_placement_id_callback() {
		printf(
			'<input type="number" id="' . self::OPTION_INTEXT_PLACEMENT_ID . '" name="' . self::OPTIONS. '[' . self::OPTION_INTEXT_PLACEMENT_ID . ']" value="%s" />',
			isset( $this->options[self::OPTION_INTEXT_PLACEMENT_ID] ) ? absint($this->options[self::OPTION_INTEXT_PLACEMENT_ID]) : ''
		);
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function intext_start_callback() {
		printf(
			'<input type="number" id="' . self::OPTION_INTEXT_START . '" name="' . self::OPTIONS. '[' . self::OPTION_INTEXT_START . ']" value="%s" />',
			isset( $this->options[self::OPTION_INTEXT_START] ) ? absint($this->options[self::OPTION_INTEXT_START]) : 1
		);
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function intext_density_callback() {
		printf(
			'<input type="number" id="' . self::OPTION_INTEXT_DENSITY . '" name="' . self::OPTIONS. '[' . self::OPTION_INTEXT_DENSITY . ']" value="%s" />',
			isset( $this->options[self::OPTION_INTEXT_DENSITY] ) ? absint($this->options[self::OPTION_INTEXT_DENSITY]) : 3
		);
	}



	/**
     * Sanitize each setting field as needed
     *
	 * @param $input Contains all settings fields as array keys
	 * @return array
	 */
	public function sanitize( $input ) {

        $input = $input ? $input : array();
		$new_input = array();

		$new_input[self::OPTION_INTEXT_ENABLED] = array_key_exists(self::OPTION_INTEXT_ENABLED, $input) ? (bool)$input[self::OPTION_INTEXT_ENABLED] : false;

		$new_input[self::OPTION_INTEXT_PLACEMENT_ID] = array_key_exists(self::OPTION_INTEXT_PLACEMENT_ID, $input) ? absint( $input[self::OPTION_INTEXT_PLACEMENT_ID] ) : 0;
		if ($new_input[self::OPTION_INTEXT_ENABLED] && !$new_input[self::OPTION_INTEXT_PLACEMENT_ID]) {
			add_settings_error( self::OPTIONS, self::OPTION_INTEXT_PLACEMENT_ID, 'Placement ID is required', 'error' );
		}

		$new_input[self::OPTION_INTEXT_START] = array_key_exists(self::OPTION_INTEXT_START, $input) ? absint( $input[self::OPTION_INTEXT_START] ) : 1;

		$new_input[self::OPTION_INTEXT_DENSITY] = array_key_exists(self::OPTION_INTEXT_DENSITY, $input) ? absint( $input[self::OPTION_INTEXT_DENSITY] ) : 3;
		if ($new_input[self::OPTION_INTEXT_ENABLED] && !$new_input[self::OPTION_INTEXT_DENSITY]) {
			add_settings_error( self::OPTIONS, self::OPTION_INTEXT_DENSITY, 'Density is required', 'error' );
		}

		return $new_input;
	}

}


