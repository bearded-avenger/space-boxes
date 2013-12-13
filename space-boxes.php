<?php
/*
Author: Nick Haskins
Author URI: http://nickhaskins.com
Plugin Name: Space Boxes
Plugin URI: http://nickhaskins.com
Version: 0.1
Description: Generate unlimited boxes with multiple layouts and optional lightbox.
*/

class ba_SpaceBoxes_FarOut_Man {

	function __construct() {
		
		require_once('inc/galleryfield.php');
		require_once('inc/spacebox-meta.php' );
		require_once('inc/type.php' );
		require_once('inc/settings.php' );
		require_once('inc/shortcode.php' );
		require_once('inc/columns.php');
		
		if( !class_exists( 'CMB_Meta_Box' ) ) {
    		require_once(dirname( __FILE__ ) .'/libs/custom-meta-boxes/custom-meta-boxes.php' );
    	}

		add_action('init', 	array($this,'image_sizes'));
		add_action( 'init', array($this,'textdomain'));
	}

	function image_sizes() {
		add_image_size( 'spacebox-small',  			220, 147, true );
		add_image_size( 'spacebox-small-nocrop',  	220, 9999      );
		add_image_size( 'spacebox-medium', 			400, 267, true );
		add_image_size( 'spacebox-medium-nocrop', 	400, 9999      );
	}

	function textdomain() {
		load_plugin_textdomain( 'spaceboxes_translation', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

}
new ba_SpaceBoxes_FarOut_Man;