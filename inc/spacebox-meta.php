<?php
/**
* create custom meta boxes for project meta
*
* @since version 1.0
* @param null
* @return custom meta boxes
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'cmb_meta_boxes', 'ba_spaceboxes_meta' );
function ba_spaceboxes_meta( array $meta_boxes ) {

	$opts = array(
		array(
			'id'             => 'ba_spacebox_single_link',
			'name'           => 'Space Box Link',
			'type'           => 'text',
			'cols'			=> 8,
		),
		array(
		    'id'   			=> 'showoff_general_setup',
		    'name' 			=> __(' ', 'projects-part-deux'),
		    'type' 			=> 'title',
		    'cols'			=> 4,
			'desc'    		=> __('<span class="ba-help-icon">?</span>This only applies if you are using the Spacebox Archive Shortcode, and you\'d like to provide a link to the page that the [spacebox] shortcode is on.','projects-part-deux')
		)

	);

	$meta_boxes[] = array(
		'title' => __('Space Boxes', 'projects-part-deux'),
		'pages' => array('spaceboxes'),
		'fields' => $opts
	);

	return $meta_boxes;

}

