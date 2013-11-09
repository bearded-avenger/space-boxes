<?php
/**
* creates setting tabs
*
* @since version 1.0
* @param null
* @return global settings
*/

require_once dirname( __FILE__ ) . '/class.settings-api.php';

if ( !class_exists('ba_spaceboxes_settings_api' ) ):
class ba_spaceboxes_settings_api {

    private $settings_api;

    const version = '1.0';

    function __construct() {

        $this->dir  		= plugin_dir_path( __FILE__ );
        $this->url  		= plugins_url( '', __FILE__ );
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this,'submenu_page'));

    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

	function submenu_page() {
		add_submenu_page( 'edit.php?post_type=spaceboxes', 'Settings', 'Settings', 'manage_options', 'spaceboxes-settings', array($this,'submenu_page_callback') );
	}

	function submenu_page_callback() {

		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Spaceboxes Settings</h2>';
			//$this->settings_api->show_navigation();
        	$this->settings_api->show_forms();

		echo '</div>';

	}

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'ba_spacebox_settings',
                'title' => __( 'Setup', 'projects-part-deux' )
            )
        );
        return $sections;
    }

    function get_settings_fields() {
        $settings_fields = array(
            'ba_spacebox_settings' => array(
            	array(
                    'name' => 'projects_domain',
                    'label' => __( 'Naming Convention', 'projects-part-deux' ),
                    'desc' => __( 'By default its called Projects. You can rename this to something like, portfolio.', 'projects-part-deux' ),
                    'type' => 'text',
                    'std' => 'projects',
                    'sanitize_callback' => ''
                )
            )
        );

        return $settings_fields;
    }
}
endif;

$settings = new ba_spaceboxes_settings_api();




