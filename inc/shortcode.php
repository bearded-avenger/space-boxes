<?php
/**
* return the space boxes. far out.
*
* @since version 1.0
* @param null
* @param null
* @return space boxes
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ba_SpaceBoxes_SC {

	const version = '0.1';

	function __construct() {

		add_action ('wp_enqueue_scripts', 	array($this,'register_scripts'));
        add_shortcode ('spaceboxes', 		array($this,'space_boxes_sc'));

	}

	function register_scripts(){
		wp_register_style('spaceboxes-style', plugins_url( '../css/spaceboxes.css', __FILE__ ), self::version );
	}

	function space_boxes_sc($atts,$content = null){

		// shortcode defaults
		$defaults = array(
			'id'		=> '',
			'columns'	=> 3,
			'layout'	=> 'stack',
			'icon' 		=> 'off',
			'image' 	=> 'on',
			'title' 	=> 'on',
			'lightbox' 	=> 'off'
		);
		$atts 	  = shortcode_atts($defaults, $atts);

		// get the post via ID so we can access data and print it within an array to fetch
		$post = get_post($atts['id'], ARRAY_A);

		// Get the gallery shortcode out of the post content, and parse the ID's in teh gallery shortcode
		$shortcode_args = shortcode_parse_atts($this->get_match('/\[gallery\s(.*)\]/isU', $post['post_content']));

		// set gallery shortcode image id's
		$ids = $shortcode_args["ids"];

		// setup some args so we can pull only images from this content
		$args = array(
            'include'        => $ids,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => 'menu_order ID',
            'orderby'        => 'post__in', //required to order results based on order specified the "include" param
        );

		// fetch the image id's that the user has within the gallery shortcode
		$images = get_posts($args);

		// load styles & scripts
		wp_enqueue_style('spaceboxes-style');

		// setup vars
		$hash = rand();
		$cols = sprintf('space-boxes-col%s',$atts['columns']);

		// print the shortcode
		$out = sprintf('<section class="clearfix space-boxes space-boxes-%s %s">',$hash,$cols);

				$set_title = $post['post_title'];

				foreach($images as $image):

					$img_title 	  	= $image->post_title;
					$get_caption 	= $image->post_excerpt;
					$get_desc  		= $image->post_content;
					$image 		 	= wp_get_attachment_image($image->ID, 'spacebox-small', false, array('class' => 'spacebox-box-image'));

		            $title 	= $img_title ? sprintf('<h3 itemprop="title" class="spacebox-box-title">%s</h3>',$img_title) : false;
		            $caption = $get_caption ? sprintf('<p class="spacebox-box-caption">%s</p>',$get_caption) : false;

	               	$out .= apply_filters('space_boxes_output',sprintf('<div class="spacebox">%s%s%s</div>',$title,$image,$caption));

	            endforeach;

        $out .= sprintf('</section>');

		return $out;

	}

   function get_match( $regex, $content ) {
        preg_match($regex, $content, $matches);
        return $matches[1];
    }
}
new ba_SpaceBoxes_SC;