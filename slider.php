<?php
/**
 * Plugin Name: SlideEASY
 * Plugin URI: http://www.payoda.com
 * Description:  The plugin enables the cropped image so easy, plus the addition of links, captions and other settings.
 * Version: 1.2
 * Author: Payoda
 * Author URI: http://www.payoda.com
 * License: A "Slug" license name e.g. GPL2
 */
 
 
add_action( 'init', 'pyda_se_create_slider' );

function pyda_se_create_slider() {
	
	register_post_type( 'slidereasy',
		array(
			'labels' => array(
				'name' => __( 'SliderEASY' ),
				'singular_name' => __( 'Slider' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'slidereasy'),
			'supports' => array( 'title','excerpt','thumbnail' ),
		)
	);
	
	$labels = array(
	'name'              => _x( 'Categories', 'taxonomy general name' ),
	'singular_name'     => _x( 'Categories', 'taxonomy singular name' ),
	'search_items'      => __( 'Search Categories' ),
	'all_items'         => __( 'All Categories' ),
	'parent_item'       => __( 'Parent Categories' ),
	'parent_item_colon' => __( 'Parent Categories:' ),
	'edit_item'         => __( 'Edit Categories' ),
	'update_item'       => __( 'Update Categories' ),
	'add_new_item'      => __( 'Add New Categories' ),
	'new_item_name'     => __( 'New Categorie Name' ),
	'menu_name'         => __( 'Categories' ),
);

$args = array(
	'hierarchical'      => true,
	'labels'            => $labels,
	'show_ui'           => true,
	'show_admin_column' => true,
	'query_var'         => true,
	'rewrite'           => array( 'slug' => 'pyda_se_cat' ),
);

register_taxonomy( 'pyda_se_cat', array( 'slidereasy' ), $args );
}

/*Default slider settings*/
 
add_option( 'pyda_se_image_width', 960 );
add_option( 'pyda_se_image_height', 500 );
add_option( 'pyda_se_thumb_image_width', 150 );
add_option( 'pyda_se_thumb_image_height', 80 );
add_option( 'pyda_se_title_show', 1 );
add_option( 'pyda_se_desc_show', 1 );
add_option( 'pyda_se_content_bg', '#3d3d3d' );
add_option( 'pyda_se_title_color', '#e8e8e8' );
add_option( 'pyda_se_desc_color', '#828282' );
add_option( 'pyda_se_arw_type', 'medium' );
add_option( 'pyda_se_arw_show', 1 );
add_option( 'pyda_se_btn_type', 1 );
add_option( 'pyda_se_btn_color', '#545454' );
add_option( 'pyda_se_btn_act_color', '#000000' );
add_option( 'pyda_se_delay', 5000 );
add_option( 'pyda_se_animation', 800 );
add_option( 'pyda_se_effect', 'slide' );

add_action('wp_print_scripts', 'pyda_se_register_scripts');
add_action('wp_print_styles', 'pyda_se_register_styles');

add_image_size( 'pyda-se-thumb', get_option('pyda_se_thumb_image_width'), get_option('pyda_se_thumb_image_height'), true );
add_image_size( 'pyda-se-image', get_option('pyda_se_image_width'), get_option('pyda_se_image_height'), true );

function pyda_se_register_scripts() {
    if (!is_admin()) {
        // register script
        wp_register_script('lightSlider-min', plugins_url('js/jquery.lightSlider.js', __FILE__), array( 'jquery' )); 
        // enqueue
        wp_enqueue_script('lightSlider-min');
             
    }
}
 
function pyda_se_register_styles() {
    // register style
    wp_register_style('lightslider_styles', plugins_url('css/lightSlider.css', __FILE__));
    // enqueue
    wp_enqueue_style('lightslider_styles');

}

function pyda_se_display($atts , $content = null ) { 
	
	extract(shortcode_atts(
		array(
			'cat' => '5',
			'per_page' => '5',
			'width'=>'0',
			'thumb'=>'1',
			'image_content'=>'1',
			'title'=>'1',
			'desc'=>'1'
		), $atts) 
		);
		
		$rand_id = rand(0, 999); 			
	?>	
			<ul class="<?php echo $width;?>" id="imageGallery_<?php echo $cat.'_'.$rand_id;?>" style="margin:0; padding:0;">

			<?php   $args = array( 'post_type' => 'slidereasy', 'posts_per_page' => $per_page, 'pyda_se_cat'=>$cat);
					$loop = new WP_Query( $args );
					$s=0;
					while ( $loop->have_posts() ) : $loop->the_post(); 										
					$thumb_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'pyda-se-thumb');
					$thumb_url  = $thumb_img[0];	
					
					$img_cdn_url=get_post_meta(get_the_ID(),'pyda_se_cdn_image_url', true );
							
					$img_slide_url=get_post_meta(get_the_ID(),'pyda_se_image_url', true );
																					
					$img_slide_openurl=get_post_meta(get_the_ID(),'pyda_se_image_url_openblank', true );
															
			?>
					
						<li style="list-style:none; " data-thumb="<?php if($img_cdn_url) { echo $img_cdn_url; } else { echo $thumb_url;}?>">						
						
						<?php
							
							
							if($img_slide_openurl!=0){ $target='_blank'; } else { $target=''; }	
							
							
							
						    if($img_slide_url!='') { ?> <a href="<?php echo $img_slide_url;?>" target="<?php echo $target;?>">
								
								<?php  
								
								if($img_cdn_url) { 
									
									echo "<img src='".$img_cdn_url."'/>";
									
								}else{
									$thumb_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID), 'pyda-se-image' ); 
									
									the_post_thumbnail('pyda-se-image');
									
								}	
								?>
							</a>
							
							<?php } else {  
								
									$thumb_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID), 'pyda-se-image' ); 
									
									the_post_thumbnail('pyda-se-image');
									
								
								 } ?>
							
						<?php if(get_option('pyda_se_title_show')==1  && get_option('pyda_se_desc_show')==1 && $image_content!='0' && $width=='0') { ?> 
												
							<div class="imageGalleryContent">
									
									<?php if(get_option('pyda_se_title_show')==1 && $title==1) { ?>
									
										<h1 class="slider_title"><?php the_title(); ?></h1>
										
									<?php } if(get_option('pyda_se_desc_show')==1 && $desc==1) { ?>
									
										<div class="slider_desc">
											<?php the_excerpt(); ?>
										</div>
										
									<?php } ?>
									
								</div>
								
						<?php } ?>
						
						</li>						
													 
			<?php  $s++;  endwhile; ?>		
			
			</ul>

<script>				
jQuery(document).ready(function($) {
	
	$('#imageGallery_<?php echo $cat.'_'.$rand_id;?>').lightSlider({
						
		<?php 
		
			if(get_option('pyda_se_thumb_show')==1 && $width=='0' && $thumb!=0) {   
										
				echo 'gallery:true, thumbWidth:'.get_option('pyda_se_thumb_image_width').','.' thumbMargin:3,';  			  
			} 					
				echo 'maxSlide:1,minSlide:1, currentPagerPosition:"left",';
			
			if(get_option('pyda_se_effect')=='slide_vr' ) {  echo 'vertical:true,'; } 
		
			if(get_option('pyda_se_start')==1) {  echo 'auto: true,'; }
		 
				echo 'pause:'.get_option('pyda_se_delay').',';
				
				echo 'speed: '.get_option('pyda_se_animation').',';
			
			if(get_option('pyda_se_effect')=='fade' && $width=='0') { echo 'mode:"fade",'; }  
		
			if(get_option('pyda_se_show')==1) { echo 'controls:true,'; } else { 'controls:false,'; }  
		
			if($width!='0'){ echo 'slideWidth:'.$width.',maxSlide:3,';} else { 'maxSlide:1,'; } 
		
		?>							
	});
});
	
</script>
			
<style>						
	.csAction > a{ background-image: url('<?php echo plugins_url('img', __FILE__).'/'.get_option('pyda_se_arw_type').'.png';?>'); !important; }
	
	.csPager .active a{ background:<?php echo get_option('pyda_se_btn_act_color');?> !important; }
	
	.csPager a{ background:<?php echo get_option('pyda_se_btn_color');?> !important; }
	
	.slider_title { color:<?php echo get_option('pyda_se_title_color');?> !important; }
	
	.slider_desc, .slider_desc p { color:<?php echo get_option('pyda_se_desc_color');?> !important; }
	
	<?php if(get_option('pyda_se_btn_type')==2) { ?> .csSlideOuter .csPager.cSpg > li a { border-radius:0; }
	
	<?php } if(get_option('pyda_se_btn_type')==2) { ?> 
	
	.imageGalleryContent { background: <?php echo get_option('pyda_se_content_bg');?> !important; }
	
	<?php } ?>
	
</style>

<?php 
}
add_shortcode( 'se_slider', 'pyda_se_display' );

add_action('admin_menu' , 'pyda_se_settings_menu');

function pyda_se_settings_menu() {
	add_submenu_page('edit.php?post_type=slidereasy', 'Custom Post Type Admin', 'Settings', 'edit_posts', basename(__FILE__), 'pyda_se_form');	
}

function pyda_se_form() {	
?>
<style>
	input[type="text"],select{
	width:250px;
	padding:7px;
	height:32px;
}
.slide_settings td, th {
    padding: 5px 0;
}
</style>
<script>
jQuery(document).ready(function($){
    $('.pyda-color-picker').wpColorPicker();
});
</script>

<div class="section panel">

  <h1>Slider Settings</h1>
	 
	<form method="post" action="options.php" enctype="multipart/form-data">
   <?php 
		wp_nonce_field('update-options');
	?>
    <table width="100%" class="slide_settings" style="text-align: left;">
	
		<tr valign="top">
            <th width="250" scope="row">Slider Image Size:</th> 
            <td>
					Width:<input type="text" style="width:80px;" name="pyda_se_image_width" placeholder="width"  value="<?php echo get_option('pyda_se_image_width');?>">
					Height:<input type="text" style="width:80px;" name="pyda_se_image_height" placeholder="Height"  value="<?php echo get_option('pyda_se_image_height');?>">
            </td>
        </tr>
                
		<tr valign="top">
            <th width="250" scope="row">Slider Thumb Image Size:</th> 
            <td>
					Width:<input type="text" style="width:80px;" name="pyda_se_thumb_image_width" placeholder="width"   value="<?php echo get_option('pyda_se_thumb_image_width');?>">
					Height:<input type="text" style="width:80px;"  name="pyda_se_thumb_image_height" placeholder="Height"   value="<?php echo get_option('pyda_se_thumb_image_height');?>">
            </td>
        </tr>
                 
         <tr valign="top">
            <th width="250" scope="row">Show Title:</th> 
            <td>
               <select name="pyda_se_title_show">
					<option <?php if(get_option('pyda_se_title_show')==1) { echo 'selected';} ?>  value="1">Yes</option>
					<option <?php if(get_option('pyda_se_title_show')==2) { echo 'selected';} ?>  value="2">No</option>
			   </select>
            </td>
        </tr>
                
        <tr valign="top">
            <th width="250" scope="row">Show Description:</th> 
            <td>
               <select name="pyda_se_desc_show">
					<option <?php if(get_option('pyda_se_desc_show')==1) { echo 'selected';} ?>  value="1">Yes</option>
					<option <?php if(get_option('pyda_se_desc_show')==2) { echo 'selected';} ?>  value="2">No</option>
			   </select>
            </td>
        </tr>
                
          <tr valign="top">
            <th width="250" scope="row">Show Thumbnail</th> 
            <td>
               <select name="pyda_se_thumb_show">
					<option <?php if(get_option('thumb_show')==1) { echo 'selected';} ?>  value="1">Yes</option>
					<option <?php if(get_option('thumb_show')==2) { echo 'selected';} ?>  value="2">No</option>
			   </select>
            </td>
        </tr>
        		
		<tr valign="top">
            <th width="250" scope="row">Content Background color:</th> 
            <td>
				<input type="text" value="<?php echo get_option('pyda_se_content_bg'); ?>" name="pyda_se_content_bg" class="pyda-color-picker" data-default-color="#c6c6c6" />
			</td>
        </tr>
                
        <tr valign="top">
            <th width="250" scope="row">Title Color:</th> 
            <td>
				<input type="text" value="<?php echo get_option('pyda_se_title_color'); ?>" name="pyda_se_title_color" class="pyda-color-picker" data-default-color="#c6c6c6" />
			</td>
        </tr>
		
        <tr valign="top">
            <th width="250" scope="row">Description Color:</th> 
            <td>
				<input type="text" value="<?php echo get_option('pyda_se_desc_color'); ?>" name="pyda_se_desc_color" class="pyda-color-picker" data-default-color="#c6c6c6" />
			</td>
        </tr>
		       
		<tr valign="top">
            <th width="250" scope="row">Select Arrow Type:</th>
            <td>
               <select name="pyda_se_arw_type">
					<option <?php if(get_option('pyda_se_arw_type')=='light') { echo 'selected';} ?>  value="light">Light</option>
					<option <?php if(get_option('pyda_se_arw_type')=='medium') { echo 'selected';} ?>  value="medium">Medium</option>
					<option <?php if(get_option('pyda_se_arw_type')=='dark') { echo 'selected';} ?>  value="dark">Dark</option>
			   </select>
            </td>
        </tr>
		
		<tr valign="top">
            <th width="250" scope="row">Show Arrow:</th> 
            <td>
               <select name="pyda_se_arw_show">
					<option <?php if(get_option('pyda_se_arw_show')==1) { echo 'selected';} ?>  value="1">Yes</option>
					<option <?php if(get_option('pyda_se_arw_show')==2) { echo 'selected';} ?>  value="2">No</option>
			   </select>
            </td>
        </tr>               
		
		 <tr valign="top">
            <th width="250" scope="row">Button Type:</th> 
            <td>
               <select name="pyda_se_btn_type">
					<option <?php if(get_option('pyda_se_btn_type')==1) { echo 'selected';} ?>  value="1">Circle</option>
					<option <?php if(get_option('pyda_se_btn_type')==2) { echo 'selected';} ?>  value="2">Square</option>
			   </select>
            </td>
        </tr>      
        		
		<tr valign="top">
            <th width="250" scope="row">Button Color:</th> 
            <td>
				<input type="text" value="<?php echo get_option('pyda_se_btn_color'); ?>" name="pyda_se_btn_color" class="pyda-color-picker" data-default-color="#c6c6c6" />
			</td>
        </tr>
		
		<tr valign="top">
            <th width="250" scope="row">Button Active Color:</th>
            <td>
				<input type="text" value="<?php echo get_option('pyda_se_btn_act_color'); ?>" name="pyda_se_btn_act_color" class="pyda-color-picker" data-default-color="#C41230" />
			</td>
        </tr>		
		
        <tr valign="top">
            <th width="250" scope="row">Slider Delay:</th> 
            <td>
				<input name="pyda_se_delay" type="text" id="pyda_se_slider_delay" value="<?php echo get_option('pyda_se_delay'); ?>" /> milliseconds
			</td>
        </tr>
						
        <tr valign="top">
            <th width="250" scope="row">Animation Duration:</th>
            <td>
				<input name="pyda_se_animation" type="text" id="pyda_se_animation" value="<?php echo get_option('pyda_se_animation'); ?>" /> milliseconds
			</td>
        </tr>
						
        <tr valign="top">
            <th width="250" scope="row">Transition Effect:</th>
            <td>
				<select name="pyda_se_effect" id="pyda_se_effect">
					<option <?php if(get_option('pyda_se_effect')=='fade') { echo 'selected';} ?> value="fade">Fade</option>
					<option <?php if(get_option('pyda_se_effect')=='slide_hr') { echo 'selected';} ?> value="slide_hr">Slide Horizontal</option>
					<option <?php if(get_option('pyda_se_effect')=='slide_vr') { echo 'selected';} ?> value="slide_vr">Slide Vertical</option>
				</select>
			</td>
        </tr>
				
		<tr>
			<th scope="row">Start Automatically:</th>
			<td><input type="checkbox" <?php if(get_option('pyda_se_start')==1) { echo 'checked="checked"';} ?>  value="1" name="pyda_se_start" id="pyda_se_start"></td>
		</tr>
		
    </table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="pyda_se_image_width,pyda_se_image_height,pyda_se_thumb_image_width,pyda_se_thumb_image_height,pyda_se_content_bg,pyda_se_title_show,pyda_se_desc_show,pyda_se_thumb_show,pyda_se_title_color,pyda_se_desc_color,pyda_se_arw_type,pyda_se_arw_show,pyda_se_btn_type,pyda_se_btn_color,pyda_se_btn_act_color,pyda_se_delay,pyda_se_animation,pyda_se_effect,pyda_se_start" />
    <p><input class="button button-primary" type="submit" value="<?php _e('Save Changes') ?>" /></p>
	
</form>
</div>

<?php
}
add_action( 'admin_enqueue_scripts', 'pyda_se_color_picker');

function pyda_se_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

// Extra field's for image URL and link

function pyda_se_image(){
	global $post;
	$custom = get_post_custom($post->ID);
	$pyda_se_cdn_image_url = isset($custom['pyda_se_cdn_image_url']) ?  $custom['pyda_se_cdn_image_url'][0] : '';
	
	$slider_image_url = isset($custom['pyda_se_image_url']) ?  $custom['pyda_se_image_url'][0] : '';
	
	$slider_image_url_openblank = isset($custom['pyda_se_image_url_openblank']) ?  $custom['pyda_se_image_url_openblank'][0] : '0';
	?>
	<label><?php _e('CDN Image URL', 'pyda-se'); ?>:</label>
	<input style="border: 1px solid #ccc; border-radius: 2px;  padding: 5px 0;" name="pyda_se_cdn_image_url" value="<?php echo $pyda_se_cdn_image_url; ?>" size="35"/><br /> <br />
	
	<label><?php _e('Image Link URL', 'pyda-se'); ?>: </label>
	<input style="border: 1px solid #ccc; border-radius: 2px;  padding: 5px 0;" name="pyda_se_image_url" value="<?php echo $slider_image_url; ?>" size="35"/> <br /><br />
	
	<small><em><?php _e('(optional - leave blank for no link)', 'pyda-se'); ?></em></small><br /><br />
	<label><input type="checkbox" name="pyda_se_image_url_openblank" <?php if($slider_image_url_openblank == 1){ echo ' checked="checked"'; } ?> value="1" /> <?php _e('Open link in new window?', 'pyda-se'); ?></label>
	<?php
}

function pyda_se_admin_init_slidereasy(){
	add_meta_box("pyda_se_image", "Image URL's", "pyda_se_image", "slidereasy", "advanced", "low");
}

add_action("add_meta_boxes", "pyda_se_admin_init_slidereasy");


//insert and update meta values

function pyda_se_mb_save(){
	
	global $post;
	
	if (isset($_POST["pyda_se_image_url"])) {
		
		$openblank = 0;
		
		if(isset($_POST["pyda_se_image_url_openblank"]) && $_POST["pyda_se_image_url_openblank"] == '1'){
			
			$openblank = 1;
		}
		
		update_post_meta($post->ID, "pyda_se_cdn_image_url", esc_url($_POST["pyda_se_cdn_image_url"]));
		
		update_post_meta($post->ID, "pyda_se_image_url", esc_url($_POST["pyda_se_image_url"]));
		
		update_post_meta($post->ID, "pyda_se_image_url_openblank", $openblank);
	}
}

add_action('save_post', 'pyda_se_mb_save');


/* Plugin settings link */

function pyda_se_admin_action_links($links, $file) {
    static $my_plugin;
    if (!$my_plugin) {
       
        $my_plugin = plugin_basename(__FILE__);
    }
    if ($file == $my_plugin) {
		
        $settings_link = '<a href="edit.php?post_type=slidereasy&page=slider.php">Settings</a>';
        
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'pyda_se_admin_action_links', 10, 2);



// Register slider widget 

class PydaSliderWidget extends WP_Widget {

	function PydaSliderWidget() {
		// Instantiate the parent object
		parent::__construct( false, 'SlideEASY Widget' );
	}

	function widget( $args, $instance ) {
		
		extract( $args );
		// these are the widget options
		$title = $instance['pyda_se_title'];
		$slider = $instance['pyda_se_slider'];

		echo $before_widget;

		echo '<h3 class="widget-title">'.$title.'</h3>';
	   
		echo do_shortcode('[se_slider per_page="10" image_content="0" thumb="0" cat="'.$slider.'"]');

		echo $after_widget;
  
	}

	function update( $new_instance, $old_instance ) {
		
		// Save widget options		
      $instance = $old_instance;
      // Fields
      $instance['pyda_se_title'] = strip_tags($new_instance['pyda_se_title']);
      $instance['pyda_se_slider'] = strip_tags($new_instance['pyda_se_slider']);
	  return $instance;
	}

	function form( $instance ) {
		
		// Check values
		if( $instance) {
			  $title = esc_attr($instance['pyda_se_title']);
			  $slider = esc_attr($instance['pyda_se_slider']);
		} else {
			 $title ='';
			 $slider ='';
		}
?>
		
		<p>
			<label for="<?php echo $this->get_field_id('pyda_se_title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>: 		

			<input type="text" value="<?php echo $title;?>" name="<?php echo $this->get_field_name('pyda_se_title');?>" id="<?php echo $this->get_field_name('pyda_se_title');?>">	
		</p>
	
		<p>
		<label for="<?php echo $this->get_field_id('pyda_se_slider'); ?>"><?php _e('Select Slider', 'wp_widget_plugin'); ?></label>: 		

		 <?php 
			$cat_args = array(
			'orderby'            => 'ID', 
			'name'               => $this->get_field_name('pyda_se_slider'),
			'class'              => 'postform',
			'selected'           =>  $slider  ,
			'taxonomy'           => 'pyda_se_cat',
			'hide_if_empty'      => false,
			); 
		 
		?>	
	<select name="<?php echo $this->get_field_name('pyda_se_slider');?>" id="<?php echo $this->get_field_name('pyda_se_slider');?>">
	  <?php $categories = get_categories($cat_args); 
	  
		  foreach ($categories as $category) {	  
			
			if($slider==$category->name) { $selected='selected="selected"'; }else{  $selected=''; }			  
			$option = '<option value="'.$category->name.'" '.$selected.'>';
			$option .= $category->cat_name;
			$option .= '</option>';
			echo $option;
		  }
	  ?> 
		 
	</select>
	</p>
		
<?php
	}
}

function pyda_se_register_widgets() {
	
	register_widget( 'PydaSliderWidget' );
}

add_action( 'widgets_init', 'pyda_se_register_widgets' );

?>
