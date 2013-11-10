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

		add_action ('wp_enqueue_scripts', array($this,'register_scripts'));
        add_shortcode ('spaceboxes', array($this,'space_boxes_sc'));
		add_shortcode ('spaceboxes_archive', array($this,'space_box_archive_sc'));
	}

	function register_scripts(){

		wp_register_style('spaceboxes-style', plugins_url( '../css/spaceboxes.css', __FILE__ ), self::version );

		// swipebox
		wp_register_style( 'spaceboxes-lb-style', plugins_url( '../libs/swipebox/swipebox.css', __FILE__ ), self::version, true);
		wp_register_script('spaceboxes-lb',       plugins_url( '../libs/swipebox/jquery.swipebox.min.js', __FILE__ ), array('jquery'), self::version, true);
	}

	function space_boxes_sc($atts,$content = null){

		// shortcode defaults
		$defaults = array(
			'id'		=> '',
			'columns'	=> 3,
			'size'		=> 'spacebox-small',
			'layout'	=> 'stack',
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

		// setup vars
		$hash 	= rand();
		$cols 	= sprintf('space-boxes-col%s',$atts['columns']);
		$opts 	= get_option('ba_spacebox_settings');
		$lb_txt = isset($opts['lb_txt']) ? $opts['lb_txt'] : false;
		$lb_bg 	= isset($opts['lb_bg']) ? $opts['lb_bg'] : false;

		// load lightbox stuffs on demand
		if ('on' == $atts['lightbox']){

			wp_enqueue_script('spaceboxes-lb');
			wp_enqueue_style('spaceboxes-lb-style');

			?>
			<!-- Space Boxes by @nphaskins -->
			<script>
				jQuery(document).ready(function(){
					jQuery('.space-boxes.space-boxes-<?php echo $hash;?> .swipebox').swipebox();
				});
			</script>
			<?php if ($lb_txt || $lb_bg): ?>
				<style>
					body #swipebox-action,
					body #swipebox-caption,
					body #swipebox-overlay { background: <?php echo $lb_bg;?>;border:none;}
					body #swipebox-caption { color: <?php echo $lb_txt;?> !important;border:none;text-shadow:none;}
				</style>
			<?php endif; ?>

		<?php }

		// load styles & scripts
		wp_enqueue_style('spaceboxes-style');

		// print the shortcode
		$out = sprintf('<section class="clearfix space-boxes space-boxes-%s %s">',$hash,$cols);

			foreach($images as $image):

				$img_title 	  	= $image->post_title;
				$get_caption 	= $image->post_excerpt;
				$get_desc  		= $image->post_content;
				$getimage 		= wp_get_attachment_image($image->ID, $atts['size'], false, array('class' => 'spacebox-box-image'));
				$getimgsrc 		= wp_get_attachment_image_src($image->ID,'large');

				if('on' == $atts['lightbox']) {
					$image 		= sprintf('<a class="swipebox" href="%s" title="%s">%s</a>',$getimgsrc[0],$img_title,$getimage);
				} else {
					$image 		= wp_get_attachment_image($image->ID, $atts['size'], false, array('class' => 'spacebox-box-image'));
				}

	            $title 			= $img_title ? sprintf('<h3 itemprop="title" class="spacebox-box-title">%s</h3>',$img_title) : false;
	            $caption 		= $get_caption ? sprintf('<figcaption class="spacebox-box-caption">%s</figcaption>',$get_caption) : false;

               	$out 			.= sprintf('<figure class="spacebox">%s%s%s</figure>',$image,$title,$caption);

            endforeach;

        $out .= sprintf('</section>');

		return apply_filters('space_boxes_output',$out);

	}

	function space_box_archive_sc($atts,$content = null){
		// shortcode defaults
		$defaults = array(
			'category'		=> '',
			'columns'		=> 3
		);

		$atts 	  = shortcode_atts($defaults, $atts);

		if($atts['category']){
			$args = array(
				'post_type' => 'spaceboxes',
				'posts_per_page' => 100,
				'tax_query' => array(
					array(
						'taxonomy' => 'spacebox-categories',
						'field' => 'name',
						'terms' => array($atts['category'])
					)
				)
			);
    	} else {
			$args = array(
				'post_type' => 'spaceboxes',
				'posts_per_page' => 100,

			);
		}

		$q = new wp_query($args);

		$cols = sprintf('space-boxes-col%s',$atts['columns']);

		$out = sprintf('<section class="space-boxes space-boxes-archive %s">',$cols);

			if ($q->have_posts()) : while($q->have_posts()) : $q->the_post();

				$title = sprintf('<h3 itemprop="title" class="spacebox-box-title">%s</h3>', get_the_title());
				$image = sprintf('%s', get_the_post_thumbnail(get_the_ID(), 'spacebox-small', false, array('class' => 'spacebox-box-image')));
				$link = get_post_meta(get_the_ID(),'ba_spacebox_single_link', true) ? get_post_meta(get_the_ID(),'ba_spacebox_single_link', true) : false;

				$out .= sprintf('<div class="spacebox"><a class="spacebox-link" href="%s">%s%s</a></div>',$link,$image, $title);

			endwhile;endif; wp_reset_query();

		$out .= sprintf('</section>');

		return apply_filters('space_boxes_archive_output',$out);

	}

   	function get_match( $regex, $content ) {
        preg_match($regex, $content, $matches);
        return $matches[1];
    }
}
new ba_SpaceBoxes_SC;