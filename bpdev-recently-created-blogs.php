<?php
/*
Plugin name:BpDev Recently Created Blogs Widget
Author:Brajesh Singh
Author URI: http://buddydev.com
Plugin URI: http://buddydev.com/plugins/buddypress-free-plugins/bpdev-recently-created-blogs-widget-list-the-most-recently-created-blogs-on-your-wordpress-mubuddypress-site/
Description:Show a list of most recently created blogs on your wordpress MU site.
Version:1.1
Created on:10th Nov 2008
Modified On:3rd dec 2009
License:GPL

*/
/***
    Copyright (C) 2008-2009 Brajesh Singh(buddydev.com)

    This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or  any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses>.

    */
function bpdev_get_recent_blogs($number_blogs=5)
{
  global $wpdb;
  $blog_table=$wpdb->blogs;
/*fetch blog_id,domain,path from wp_blogs table ,where the blog is not spam,deleted or archived order by the date and time of registration */
  $query="select blog_id,domain,path from $blog_table where public='1' and archived='0' and spam='0' and deleted='0' order by registered desc limit 0,$number_blogs";
  
  $recent_blogs=$wpdb->get_results($wpdb->prepare($query));
	
return $recent_blogs;
 
 }
 
 /*Show a bulleted list of recently created blogs */
 function bpdev_show_recent_blogs($number_blogs=5,$description=true)
 {
 $recent_blogs=bpdev_get_recent_blogs($number_blogs);
					
foreach($recent_blogs as $recent_blog):
					$blog_url="";
				if( defined( "VHOST" ) && constant( "VHOST" ) == 'yes' )
					$blog_url="http://".$recent_blog->domain.$recent_blog->path;
					else
					$blog_url="http://".$recent_blog->domain.$recent_blog->path;
					$blog_name=get_blog_option($recent_blog->blog_id,"blogname");
?>
 <li>
		<a href="<?php echo $blog_url;?>"><?php _e( $blog_name)?> </a>
	<?php if($description):?>
	<span><?php _e( get_blog_option($recent_blog->blog_id,"blogdescription"));?></span>
	<?php endif;?>
</li>
 <?php endforeach;?>
<?php 
 }
 
 
 class BpDev_Recentblogs_Widget extends WP_Widget
 {
 	function bpdev_recentblogs_widget() {
		parent::WP_Widget( false, $name = __( 'Recently Created Blogs', 'bpdev' ) );
	}

	function widget($args, $instance) {
		extract( $args );
	
		 echo $before_widget; 
		echo $before_title
			. $instance['title']
			. $after_title; 
			echo "<ul id='recently-created-blogs'>";
			bpdev_show_recent_blogs($instance['count'],$instance['show_description']);
		echo "</ul>";
	
		 echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = absint($new_instance['count'] ) ;
		$instance['show_description'] = $new_instance['show_description'] ;

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'List Recently created blogs', 'count' => 5,'show_description'=>false ) );
		$title = strip_tags( $instance['title'] );
		$count = absint( $instance['count'] );
		$show_description =  $instance['show_description'] ;
	?>
		<p><label for="bpdev-widget-title"><?php _e('Title:', 'bpdev'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( stripslashes( $title ) ); ?>" /></label></p>
			<p>
				<label for="bpdev-widget-blog-count"><?php _e( 'How many Blogs' , 'bpdev'); ?>
					<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" class="widefat" value="<?php echo attribute_escape( absint( $count ) ); ?>" />
				</label>
			</p>
			<p>
				<label for="bpdev-widget-show_description"><?php _e( 'Show Description' , 'bpdev'); ?>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'show_description' ); ?>" name="<?php echo $this->get_field_name( 'show_description' ); ?>" class="widefat" value="1" <?php if( $show_description==true) echo "checked='checked'" ?>/>
				</label>
			</p>
	<?php
	}
 
 
 }
 function bpdev_register_recentlycreated_blogs_widgets() {
	add_action('widgets_init', create_function('', 'return register_widget("BpDev_Recentblogs_Widget");') );
	}
add_action( 'plugins_loaded', 'bpdev_register_recentlycreated_blogs_widgets' );

?>