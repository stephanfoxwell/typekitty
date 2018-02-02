<?php
/*
   Plugin Name: Typekitty
   Plugin URI: https://github.com/stephansmith/typekitty
   Description: Minimal Typekit Plugin for WordPress
   Version: 1.0
   Author: Stephan Smith
   Author URI: http://stephan-smith.com
   License: GPL2
   */

class Typekitty {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	public $options;
	
	public $pluginName = 'Typekitty';
	public $pluginSlug = 'typekitty';
	

	public function __construct() {
		
		
		// Set class property
		$this->options = get_option( $this->pluginSlug );
		
		add_action( 'wp_head', array( $this, 'hook_stylesheet' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}
	
	
	public function get_typekitty() {
		return get_option( 'typekitty' );
	}


	public $sections = array(
		array(
			'id' => 'general',
			'fields' => array(
				array(
					'name'=>'kitId',
					'label'=>'Kit ID',
					'type'=>'text'
				)
			)
		)
	);

	public function add_plugin_page() {
		add_options_page(
			$this->pluginName,
			$this->pluginName,
			'manage_options',
			$this->pluginSlug,
			array( $this, 'create_admin_page' )
		);
	}


	public function create_admin_page() {
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div class="wrap">
			<h2><?php echo $this->pluginName; ?></h2>
			<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( $this->pluginSlug . '_group' );
					do_settings_sections( $this->pluginSlug );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {
		register_setting(
			$this->pluginSlug . '_group', // Option group
			$this->pluginSlug // Option name
		);

		foreach ( $this->sections as $section ) {
			add_settings_section(
				$section['id'], // ID
				$section['label'], // Title
				array( $this, 'print_section_info' ), // Callback
				$this->pluginSlug // Page
			);
		}
	}

	public function print_section_info( $arg ) {

		foreach ( $this->sections as $section ) {

			if ( $section['id'] == $arg['id'] ) {

				foreach ( $section['fields'] as $field ) {
					add_settings_field(
						$field['name'],
						$field['label'],
						array( $this, 'print_field' ),
						$this->pluginSlug,
						$section['id'],
						$field
					);
				}
			}

		}
		return;
	}

	public function print_field( $args ) {

		$options = $this->options;
		
		if ( strtolower( $args['type'] ) == 'checkbox' ) {
			
			$output = '<label><input id="' . $args['name'] . '" type="' . $args['type'] . '" name="' . $this->pluginSlug . '[' . $args['name'] . ']" value="1"';
			if ( $options[ $args['name'] ] == 1 ) {
				$output .= ' checked="checked" ';
			}
			$output .= '/>' . $args['help'] . '</label>';
			
			echo $output;
		
		} else {
		
			printf(
				'<input id="' . $args['name'] . '" class="regular-text" type="' . $args['type'] . '" name="' . $this->pluginSlug . '[' . $args['name'] . ']" value="%s" />',
				isset( $options[ $args['name'] ] ) ? esc_attr( $options[ $args['name'] ]) : ''
			);
		
		}

		return;
	}
	
	public function hook_javascript() {
		
		$options = $this->options;
		
		$output = '<link rel="stylesheet" href="https://use.typekit.net/' . $options['kitId'] . '.css">';
		
		echo $output;
	}


}

$Typekitty = new Typekitty();
