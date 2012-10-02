<?php
/*
Plugin Name: WP-SmugMug
Plugin URI: http://tow.com/projects/wordpress/wp-smugmug/
Description: Integrate your SmugMug galleries into your WordPress blog.
Author: Adam Tow
Version: 2.0.9
Author URI: http://tow.com/
*/

@define('WP_SMUGMUG_QUERY_VAR', 'wpsm');
@define('WP_SMUGMUG_ACTION_QUERY_VAR', 'wpsm-action');
@define('WP_SMUGMUG_FILEPATH', '/wp-smugmug/wp-smugmug.php');
@define('WP_SMUGMUG_COMMENT', "\n\n<!-- WP-SmugMug Plugin: http://tow.com/projects/wordpress/ -->\n\n");
@define('WP_SMUGMUG_VERSION', '2.0.9');
@define('WP_SMUGMUG_CSS', 'div.wp-smugmug {
	clear:both;
	margin: 0 0 1em 0;
	padding: 0;
	width:100%;
}
div#wp-smugmug {
	margin: 5px 0 0 0;
	padding: 5px 0 0 0;
}

ul.thumbwrap {
	padding: 10px 0px 0 0px;
	margin: 0;
	text-indent: 0;
	width: 100%;
}
ul.thumbwrap li {
	float:left;
	margin: 0 8px 8px 0;
	padding: 0;
	
}
.mini ul.thumbwrap li {
	margin:0 5px 5px 0;
}


ul.thumbwrap a {
	display: block;
	text-decoration: none;
	cursor: pointer;
}

ul.thumbwrap img {
	vertical-align: middle;
	margin: 0;
	float: none;
	padding: 3px;
	background: #none;
	border: 1px solid #999;
	width:85px;
	height:85px;
}
.mini ul.thumbwrap img {
	width:70px;
	height:70px;
}
ul.thumbwrap a:hover img {
	background-color: #35332D;
	border: 1px solid #35332D
}
ul.thumbwrap a:hover {
	
}

');

@define('WP_SMUGMUG_CSS_IE', '');


if ( !empty($_REQUEST[WP_SMUGMUG_ACTION_QUERY_VAR]) ) {
	switch ( $_REQUEST[WP_SMUGMUG_ACTION_QUERY_VAR] ) {
		case 'css':
			header("Content-type: text/css");
			echo WP_SMUGMUG_CSS;
			die();
			break;
		case 'css_ie':
			echo WP_SMUGMUG_CSS_IE;
			die();
			break;
		default:
			die();
			break;
	}
}

if ( !class_exists('wpSmugMug') ) { 

	class wpSmugMug 
	{
		var $wp_smugmug_domain = 'tow.com';
		var $wp_smugmug_is_setup = 0;
		var $settings;
		var $defaults = array(
								 'url' => ''
								,'title' => ''
								,'description' => ''
								,'thumbsize' => 'Th'		// Default thumbnail size
								,'size' => 'M'				// Default photo size
								,'imagecount' => 100		// Default retrieves 100 photos
								,'start' => 1				// The starting index of the photo to display
								,'num' => 100				// The number of photos to display
								,'link' => 'smugmug'		// Default link goes to SmugMug gallery page
								,'titletag' => 'h3'
								,'captions' => 'true'		// Display Captions
								,'sort' => 'true'			// Sort photos by EXIF date
								,'smugmug' => 'false'		// Display a link to the smugmug gallery
								,'window' => 'false'		// Open links in a new window
								,'version' => WP_SMUGMUG_VERSION
								,'installed' => true
								,'css' => ''
								,'css_ie' => ''
							);
										
		function wpSmugMug()
		{
			# Description:	Constructor for wpSmugMug class. Adds all the necessary
			#				hooks and filters into WordPress.
			#
			# History:		2008-12-02 - Initial version (adam)
			#				2008-12-31 - Remove filters used by WP-SmugMug version 1.x
			#
			
			if ( $this->wp_smugmug_is_setup )
			  return;
	
			load_plugin_textdomain($this->wp_smugmug_domain, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
	
			$this->wp_smugmug_load_settings();
			
			add_action('wp_head', array(&$this, 'wp_smugmug_wp_head'));
			add_action('admin_menu', array(&$this, 'wp_smugmug_admin_menu'));
			add_action('admin_menu', array(&$this, 'wp_smugmug_admin_panel'));
						
			add_filter('the_content', array(&$this, 'wp_smugmug_the_content'));
			add_filter('admin_print_scripts', array(&$this, 'wp_smugmug_admin_print_scripts'));
			
			add_shortcode('smugmug', array(&$this, 'wp_smugmug_shortcode'));
						
				// Remove the old WP-SmugMug filters and actions
			
			remove_filter('the_content', 'towsm_rss_gallery');
			remove_filter('the_content', 'towsm_wpimage_gallery');
			remove_filter('post_rewrite_rules', 'towsm_rewrite_rules_array');
			remove_filter('query_vars', 'towsm_query_vars');
			remove_action('template_redirect', 'towsm_template_redirect');
			remove_action('wp_head', 'towsm_css');
			remove_action('admin_menu', 'towsm_admin_panel');
		}
	
		function wp_smugmug_load_settings()
		{
			# Description:	Load the default settings to display in the WP-SmugMug editing pane.
			#				Also updates any new settings if a newer version of the plugin is
			#				installed.
			#
			# History:		2008-12-13 - Initial version (adam)
			#
			
				// Add the default settings if this is the first time WP-SmugMug is being loaded
			
			$version = get_option('wp_smugmug_version');
			
			if ( !get_option('wp_smugmug_installed') || version_compare($version, WP_SMUGMUG_VERSION, 'lt') ) {
				foreach($this->defaults as $k => $v) add_option('wp_smugmug_' . $k, $v);
				update_option('wp_smugmug_version', WP_SMUGMUG_VERSION);
			}
			
			$this->settings = array	(
										 'url' => ''
										,'title' => ''
										,'description' => ''
										,'thumbsize' => get_option('wp_smugmug_thumbsize')
										,'size' => get_option('wp_smugmug_size')
										,'imagecount' => get_option('wp_smugmug_imagecount')
										,'start' => get_option('wp_smugmug_start')
										,'num' => get_option('wp_smugmug_num')
										,'link' => get_option('wp_smugmug_link')
										,'titletag' => get_option('wp_smugmug_titletag')
										,'captions' => get_option('wp_smugmug_captions')
										,'sort' => get_option('wp_smugmug_sort')
										,'lightbox' => get_option('wp_smugmug_lightbox')
										,'smugmug' => get_option('wp_smugmug_smugmug')
										,'window' => get_option('wp_smugmug_window')
									);
			
		}
	
		function wp_smugmug_wp_head()
		{
			# Description:	Adds stylesheet link references and appends any custom CSS the user has added in the admin panel.
			#				We use a conditional comment to add all IE-specific styles.
			#
			# History:		2008-12-13 - Initial version (adam)
			#
			
			$wp = get_bloginfo('wpurl');
			$url = $wp . '/' . PLUGINDIR . '/' . plugin_basename(__FILE__);
			$query_var = WP_SMUGMUG_ACTION_QUERY_VAR;
			$css = get_option('wp_smugmug_css');
			$css_ie = get_option('wp_smugmug_css_ie');
			
			echo <<<EOF
	<link rel="stylesheet" type="text/css" href="{$url}?{$query_var}=css" />
	
EOF;
			if ( !empty($css) ) {
				echo <<<EOF
	<style type="text/css">
	{$css}
	</style>
	
EOF;
			}
			
			echo <<<EOF
	<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="{$url}?{$query_var}=css_ie" />
	
EOF;
			
			if ( !empty($css_ie) ) {
				echo <<<EOF
	<style type="text/css">
	{$css_ie}
	</style>
	
EOF;
			}
			echo "<![endif]-->\n";
		}
	
		function wp_smugmug_shortcode($atts, $content = null)
		{
			# Description:	Processes the shortcode text for WP-SmugMug
			#
			# Parameters:	atts - array of attribute names/value pairs
			#				content - the text between the shortcode tags (optional)
			#
			# History:		2008-12-02 - Initial version (adam)
			#				2008-12-31 - html_entity_decode fix for PHP4. Added #wp_smugmug html fragment to paged galleries
			#				2011-04-16 - use fetch_feed instead of fetch_rss. Support for new image paths in SmugMug. Removed
			#							 support for WordPress paged galleries.
			#
			#
			
			global $post;
			
				// Allow plugins/themes to override the default gallery template.
				
			$output = apply_filters('wp-smugmug', '', $attr);
			if ( !empty($output) )
				return $output;
			
			$attrs = shortcode_atts($this->settings, $atts);
	
			array_walk($attrs, array(&$this, 'fixTrueFalseAndEncoding'));
			extract($attrs);

			if ( !empty($url) ) {

				if ( !empty($num) && (int) $num < 1 )
					$num = 100;
	
				if ( !empty($imagecount) && $imagecount != 100 )
					$url .= '&ImageCount=' . $imagecount;
					
				if ( $imagecount > 100 ) {
					$url .= '&Paging=0';
				}
	
				if ( $window )
					$target = ' target="wp-smugmug-' . $post->ID . '"';
		
					// HTML Entity Decode that's PHP4 and PHP5 compatible
				
				$url = $this->decode_entities($url);
				$rss = fetch_feed($url);
				
					// If the RSS feed is empty or there has been a WordPress error, display a link to the gallery.

				if ( is_wp_error($rss) || empty($rss) || count($rss->get_items()) == 0 ) {
				
					preg_match("/.*?Data=(.*?)&.*/", $url, $rss_matches);
					$album_id = $rss_matches[1];
								
					$output .= '<p><a target="_blank" href="http://www.smugmug.com/gallery/' . $album_id . '"' . $target . '>' . __('View photos at SmugMug', $this->wp_smugmug_domain)  . '</a></p>';
					
				} else {
										
					$rss_items = $rss->get_items();
					
						// Sort the RSS feed unless sorting is checked off
					
					if ( $sort === true ) {
						usort($rss_items, array(&$this, 'wp_smugmug_compare_exif'));
					} else {
						$rss->enable_order_by_date(false);
					}
				
					$start = intval($start);
					
					if ( $start < 1 )
						$start = 1;
						
					$start--;
					$items = array_slice($rss->get_items(), $start, $num);
						
						// Generate the HTML code
		
					$output = apply_filters('wp_smugmug_style', WP_SMUGMUG_COMMENT . "<div class='wp-smugmug'>\n\n");
					
					if ( !empty($title) )
						$output .= '<' . get_option('wp_smugmug_titletag') . '>' . wptexturize($title) . '</' . get_option('wp_smugmug_titletag') . '>' . "\n\n";
					
					if ( !empty($description) )
						$output .= wpautop(wptexturize($description)) . "\n";
	
					$output .= '<ul class="thumbwrap">';
					
					foreach($items as $item) {
		
						$output .= '<li>';
						
							// Retrieve the URLs for the photo and thumbnail images
						
						$enclosures = $item->get_enclosures();

						if ( count($enclosures) > 1 ) {
							if ( strtolower($thumbsize) == "ti" ) {
								$thumb_url = $enclosures[0]->get_link();
							} else {
								$thumb_url = $enclosures[1]->get_thumbnail();
							}
							
							$size = strtolower($size);
							$num_enclosures = count($enclosures);
							
							switch ( $size ) {
								case "ti":
									$enclosure_index = 0;
									break;
								
								case "th":
									$enclosure_index = 1;
									break;
								
								case "s":
									$enclosure_index = 2;
									break;
								
								case "m":
									$enclosure_index = 3;
									break;
								
								default:
								case "l":
									$enclosure_index = 4;
									break;
								
								case "x1":
								case "xl":
									$size = "xl";
									$enclosure_index = 5;
									break;
								
								case "x2":
									$enclosure_index = 6;
									break;
								
								case "x3":
									$enclosure_index = 7;
									break;
								
								case "o":
									$enclosure_index = 8;
									break;
								
							}
	
								// Use the largest enclosure available
	
							if ( $num_enclosures > $enclosure_index ) {
								$photo_url = $enclosures[$enclosure_index]->get_link();
							} else {
								$photo_url = $enclosures[$num_enclosures - 1]->get_link();
							}
		
							$item_title = htmlspecialchars(strip_tags($item->get_title()), ENT_COMPAT, 'ISO-8859-1', false);	

							if ($item_title = "trhoppe's photo") {
								$item_title = "";
							}

							$rel = '';
							
								// Link variable determines what happens when the user clicks on an image link
							
							switch ( $link ) {
								case 'smugmug':
									$the_link = $item->get_link();
									break;
			
								case 'smugmug-lightbox':
									$the_link = $item->get_link() . '-' . $size . '-LB';
									break;
									
								case 'lightbox':
									$the_link = $photo_url;
									$rel = ' rel="lightbox[wp-smugmug-' . $post->ID . ']" class="thickbox"';
									break;
		
								case 'image':
								default:
									$the_link = $photo_url;
									break;
							}
							
							$anchor_tag = '<a href="' . $the_link . '" title="' . $item_title . '"' . $rel . $target . '>';
							
							$output .= $anchor_tag;
							$output .= '';
							$output .= '<img src="' . $thumb_url . '" ' . 'alt="' . $item_title . '" />';
							$output .= '';
												
							$output .= '</a>';
							$output .= '</li>';
						}
					}
					$output .= '</ul>';
					
					if ( $smugmug )
						$output .= '<p><a target="_blank" href="' . $rss->get_link() . '"' . $target . '>' . __('View photos on SmugMug instead', $this->wp_smugmug_domain) . '</a></p>';
				
					$output .= '<div style="clear: both;"></div>';
					$output .= '</div>';
					$output .= '<div style="clear: both;"></div>';
				}
				
				$rss->__destruct();
				unset($rss);
			}
				
			return $output;
		}
		
		function wp_smugmug_the_content($content = '')
		{
			 # Description:	Displays a paged gallery for advertisers. If the wpimage variable is
			 #				set in the URL string, we don't display the normal content and display
			 #				our paged gallery page instead
			 #
			 # Parameters:	content - the original content
			 #
			 #
			 # History:		2008-12-04 - Initial version (adam)
			 #				2008-12-19 - Backward compatibility with previous version of plugin
			 #				2008-12-31 - Added missing </a> tags to next/prev links. Added #wp-smugmug class id
			 #				2011-04-16 - Removed support for WordPress paged galleries
			 #
			 #
	
			global $post;
			global $wp;
			global $more;
			global $wp_query;
			
			$feed_url = get_post_meta($post->ID, 'wp-smugmug', true);
			$options = array();
			
			$output = '';
			
			if ( !empty($feed_url) && $more == 1) {
			
					// This handles the legacy WP-SmugMug plugin
			
				$query = get_post_meta($post->ID, 'wp-smugmug-options', true);
				parse_str($query, $options);
			
				$atts = $this->defaults;
				$atts['url'] = $feed_url;
				
				if ( $options['sort'] == 0 ) 
					$atts['sort'] == 'false';
					
				if ( !empty($options['title']) )
					$atts['title'] == urldecode($options['title']);
				
				if ( !empty($options['limit']) ) 
					$atts['imagecount'] = $options['limit'];
				
				$output = $this->wp_smugmug_shortcode($atts);
				$content .= $output;
			
			}
			
			return $content;
			
		}
		
		function wp_smugmug_admin_menu()
		{
			# Description:	Adds the WP-SmugMug forms to the Post/Page editing screens
			#
			# History:		2008-12-02 - Initial version (adam)
			#
			#
			
			add_meta_box('wp_smugmug', 'WP-SmugMug', array(&$this, 'wp_smugmug_insert_form'), 'post', 'normal');
			add_meta_box('wp_smugmug', 'WP-SmugMug', array(&$this, 'wp_smugmug_insert_form'), 'page', 'normal');
		}
	
		function wp_smugmug_insert_form()
		{
			# Description:	Inserts the WP-SmugMug form in the Post/Page edit screen
			#
			# History:		2008-12-02 - Initial version (adam)
			#
			#
		?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_url"><?php _e('RSS URL:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="40" style="width: 95%;" name="wpSmugMug[url]" id="wpSmugMug_url" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_title"><?php _e('Title:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="40" style="width: 95%;" name="wpSmugMug[title]" id="wpSmugMug_title" value="<?php echo get_option('wp_smugmug_title'); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_description"><?php _e('Description:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="40" style="width: 95%;" name="wpSmugMug[description]" id="wpSmugMug_description" value="<?php echo get_option('wp_smugmug_description'); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_imagecount"><?php _e('Number of photos to retrieve from gallery:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="5" name="wpSmugMug[imagecount]" value="<?php echo get_option('wp_smugmug_imagecount'); ?>" id="wpSmugMug_imagecount" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_start"><?php _e('Start at photo #:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="5" name="wpSmugMug[start]" value="<?php echo get_option('wp_smugmug_start'); ?>" id="wpSmugMug_start" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_num"><?php _e('Number of photos to display:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="text" size="5" name="wpSmugMug[num]" value="<?php echo get_option('wp_smugmug_num'); ?>" id="wpSmugMug_num" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_thumbsize"><?php _e('Thumbnail size:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="radio" name="wpSmugMug[thumbsize]" value="Th" id="wpSmugMug_link_thumbsize_regular"<?php if ( get_option('wp_smugmug_thumbsize') == 'Th' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_link_thumbsize_regular"><?php _e('Regular (150x150)', $this->wp_smugmug_domain); ?></label>
						<br />
						<input type="radio" name="wpSmugMug[thumbsize]" value="Ti" id="wpSmugMug_thumbsize_tiny"<?php if ( get_option('wp_smugmug_thumbsize') == 'Ti' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_thumbsize_tiny"><?php _e('Tiny (100x100)', $this->wp_smugmug_domain); ?></label>
						<br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_size"><?php _e('Photo size:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<select name="wpSmugMug[size]" id="wpSmugMug_size">
							<option value="S"<?php if ( get_option('wp_smugmug_size') == 'S' ) echo ' selected="selected"'; ?>><?php _e('Small', $this->wp_smugmug_domain); ?></option>
							<option value="M"<?php if ( get_option('wp_smugmug_size') == 'M' ) echo ' selected="selected"'; ?>><?php _e('Medium', $this->wp_smugmug_domain); ?></option>
							<option value="L"<?php if ( get_option('wp_smugmug_size') == 'L' ) echo ' selected="selected"'; ?>><?php _e('Large', $this->wp_smugmug_domain); ?></option>
							<option value="XL"<?php if ( get_option('wp_smugmug_size') == 'XL' ) echo ' selected="selected"'; ?>><?php _e('X-Large 1', $this->wp_smugmug_domain); ?></option>
							<option value="X2"<?php if ( get_option('wp_smugmug_size') == 'X2' ) echo ' selected="selected"'; ?>><?php _e('X-Large 2', $this->wp_smugmug_domain); ?></option>
							<option value="X3"<?php if ( get_option('wp_smugmug_size') == 'X3' ) echo ' selected="selected"'; ?>><?php _e('X-Large 3', $this->wp_smugmug_domain); ?></option>
							<option value="O"<?php if ( get_option('wp_smugmug_size') == 'O' ) echo ' selected="selected"'; ?>><?php _e('Original', $this->wp_smugmug_domain); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="wpSmugMug_link"><?php _e('Link destination:', $this->wp_smugmug_domain) ?></label>
					</th>
					<td>
						<input type="radio" name="wpSmugMug[link]" value="smugmug" id="wpSmugMug_link_smugmug"<?php if ( get_option('wp_smugmug_link') == 'smugmug' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_link_smugmug">SmugMug</label>
						<br />
						<input type="radio" name="wpSmugMug[link]" value="smugmug-lightbox" id="wpSmugMug_link_smugmug_lightbox"<?php if ( get_option('wp_smugmug_link') == 'smugmug-lightbox' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_link_smugmug_lightbox">SmugMug Lightbox</label>
						<br />
						<input type="radio" name="wpSmugMug[link]" value="image" id="wpSmugMug_link_image"<?php if ( get_option('wp_smugmug_link') == 'image' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_link_image"><?php _e('Image', $this->wp_smugmug_domain); ?></label>
						<br />
						<input type="radio" name="wpSmugMug[link]" value="lightbox" id="wpSmugMug_link_lightbox"<?php if ( get_option('wp_smugmug_link') == 'lightbox' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_link_image">Lightbox</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e('Options:', $this->wp_smugmug_domain) ?>
					</th>
					<td>
						<input type="hidden" name="wpSmugMug[captions]" id="wpSmugMug_captions_" value="false" />
						<input type="checkbox" name="wpSmugMug[captions]" id="wpSmugMug_captions" value="true"<?php if ( get_option('wp_smugmug_captions') == 'true' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_captions"><?php _e('Display captions', $this->wp_smugmug_domain); ?></label>
						<br />
						<input type="hidden" name="wpSmugMug[sort]" id="wpSmugMug_sort_" value="false" />
						<input type="checkbox" name="wpSmugMug[sort]" id="wpSmugMug_sort" value="true"<?php if ( get_option('wp_smugmug_sort') == 'true' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_sort"><?php _e('Sort photos by date', $this->wp_smugmug_domain); ?></label>
						<br />
						<input type="hidden" name="wpSmugMug[window]" id="wpSmugMug_window_" value="false" />
						<input type="checkbox" name="wpSmugMug[window]" id="wpSmugMug_window" value="true"<?php if ( get_option('wp_smugmug_window') == 'true' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_window"><?php _e('Open links in a new window', $this->wp_smugmug_domain); ?></label>
						<br />
						<input type="hidden" name="wpSmugMug[smugmug]" id="wpSmugMug_smugmug_" value="false" />
						<input type="checkbox" name="wpSmugMug[smugmug]" id="wpSmugMug_smugmug" value="true"<?php if ( get_option('wp_smugmug_smugmug') == 'true' ) echo ' checked="checked"'; ?> />
						<label for="wpSmugMug_smugmug"><?php _e('Display SmugMug gallery link at bottom', $this->wp_smugmug_domain); ?></label>
						<br />
					</td>
				</tr>
				<tr>
					<th colspan="2" class="submit">
						<input type="button" onclick="return wpSmugmugAdmin.sendToEditor(this.form);" value="<?php _e('Send to Editor &raquo;', $this->wp_smugmug_domain) ?>" />
					</th>
				</tr>
			</table>
		<?php 
		}
	
		 
		function wp_smugmug_admin_panel()
		{
			 # Description:	Registers the admin panel to WordPress
			 #
			 # History:		2006-12-13 - Initial version (atow)
			 #
	
			if (function_exists('add_options_page')) {
				add_options_page('WP-SmugMug', 'WP-SmugMug', 'manage_options', basename(__FILE__), array(&$this, 'wp_smugmug_panel'));
			}
		}
		 
		function wp_smugmug_panel()
		{ 
			 # Description:	The administrative panel for the WP-SmugMug plugin
			 #
			 #
			 # History:		2006-12-13 - Initial version (atow)
			 #
		?>
			<div class=wrap>
				<form method="post" action="options.php">
					<?php wp_nonce_field('update-options');Ê?>
					<h2>WP-SmugMug</h2>
					<p>
					<?php _e('Integrate your <a href="http://www.smugmug.com/">SmugMug</a> galleries into your WordPress posts and pages.', $this->wp_smugmug_domain); ?>
					</p>
				
					<h3><?php _e('Default Settings', $this->wp_smugmug_domain); ?></h3>
					<p>
					<?php _e('Customize the default settings shown in the WP-SmugMug panel in the Write Post or Write Page SubPanel.'); ?>
					</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_title"><?php _e('Title:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="40" style="width: 95%;" name="wp_smugmug_title" value="<?php echo get_option('wp_smugmug_title'); ?>" id="wp_smugmug_title" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_description"><?php _e('Description:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="40" style="width: 95%;" name="wp_smugmug_description" value="<?php echo get_option('wp_smugmug_description'); ?>" id="wp_smugmug_description" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_imagecount"><?php _e('Number of photos to retrieve from gallery:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="5" name="wp_smugmug_imagecount" value="<?php echo get_option('wp_smugmug_imagecount'); ?>" id="wp_smugmug_imagecount" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_start"><?php _e('Start at photo #:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="5" name="wp_smugmug_start" value="<?php echo get_option('wp_smugmug_start'); ?>" id="wp_smugmug_start" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_num"><?php _e('Number of photos to display:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="5" name="wp_smugmug_num" value="<?php echo get_option('wp_smugmug_num'); ?>" id="wp_smugmug_num" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_thumbsize"><?php _e('Thumbnail size:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="radio" name="wp_smugmug_thumbsize" value="Th" id="wp_smugmug_thumbsize_regular"<?php if ( get_option('wp_smugmug_thumbsize') == 'Th' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_link_thumbsize_regular"><?php _e('Regular (150x150)', $this->wp_smugmug_domain); ?></label>
								<br />
								<input type="radio" name="wp_smugmug_thumbsize" value="Ti" id="wp_smugmug_thumbsize_tiny"<?php if ( get_option('wp_smugmug_thumbsize') == 'Ti' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_thumbsize_tiny"><?php _e('Tiny (100x100)', $this->wp_smugmug_domain); ?></label>
								<br />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_size"><?php _e('Photo size:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<select name="wp_smugmug_size" id="wp_smugmug_size">
									<option value="S"<?php if ( get_option('wp_smugmug_size') == 'S' ) echo ' selected="selected"'; ?>><?php _e('Small', $this->wp_smugmug_domain); ?></option>
									<option value="M"<?php if ( get_option('wp_smugmug_size') == 'M' ) echo ' selected="selected"'; ?>><?php _e('Medium', $this->wp_smugmug_domain); ?></option>
									<option value="L"<?php if ( get_option('wp_smugmug_size') == 'L' ) echo ' selected="selected"'; ?>><?php _e('Large', $this->wp_smugmug_domain); ?></option>
									<option value="XL"<?php if ( get_option('wp_smugmug_size') == 'XL' ) echo ' selected="selected"'; ?>><?php _e('X-Large 1', $this->wp_smugmug_domain); ?></option>
									<option value="X2"<?php if ( get_option('wp_smugmug_size') == 'X2' ) echo ' selected="selected"'; ?>><?php _e('X-Large 2', $this->wp_smugmug_domain); ?></option>
									<option value="X3"<?php if ( get_option('wp_smugmug_size') == 'X3' ) echo ' selected="selected"'; ?>><?php _e('X-Large 3', $this->wp_smugmug_domain); ?></option>
									<option value="O"<?php if ( get_option('wp_smugmug_size') == 'O' ) echo ' selected="selected"'; ?>><?php _e('Original', $this->wp_smugmug_domain); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wpSmugMug_link"><?php _e('Link destination:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="radio" name="wp_smugmug_link" value="smugmug" id="wp_smugmug_link_smugmug"<?php if ( get_option('wp_smugmug_link') == 'smugmug' ) echo ' checked="checked"'; ?> />
								<label for="wp_smugmug_link_smugmug">SmugMug</label>
								<br />
								<input type="radio" name="wp_smugmug_link" value="smugmug-lightbox" id="wp_smugmug_link_smugmug_lightbox"<?php if ( get_option('wp_smugmug_link') == 'smugmug-lightbox' ) echo ' checked="checked"'; ?> />
								<label for="wp_smugmug_link_smugmug_lightbox">SmugMug Lightbox</label>
								<br />
								<input type="radio" name="wp_smugmug_link" value="image" id="wp_smugmug_link_image"<?php if ( get_option('wp_smugmug_link') == 'image' ) echo ' checked="checked"'; ?> />
								<label for="wp_smugmug_link_image"><?php _e('Image', $this->wp_smugmug_domain); ?></label>
								<br />
								<input type="radio" name="wp_smugmug_link" value="lightbox" id="wp_smugmug_link_lightbox"<?php if ( get_option('wp_smugmug_link') == 'lightbox' ) echo ' checked="checked"'; ?> />
								<label for="wp_smugmug_link_lightbox">Lightbox</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e('Options:', $this->wp_smugmug_domain) ?>
							</th>
							<td>
								<input type="checkbox" name="wp_smugmug_captions" id="wp_smugmug_captions" value="true"<?php if ( get_option('wp_smugmug_captions') == 'true' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_captions"><?php _e('Display captions', $this->wp_smugmug_domain); ?></label>
								<br />
								<input type="checkbox" name="wp_smugmug_sort" id="wp_smugmug_sort" value="true"<?php if ( get_option('wp_smugmug_sort') == 'true' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_sort"><?php _e('Sort photos by date', $this->wp_smugmug_domain); ?></label>
								<br />
								<input type="checkbox" name="wp_smugmug_window" id="wp_smugmug_window" value="true"<?php if ( get_option('wp_smugmug_window') == 'true' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_window"><?php _e('Open links in a new window', $this->wp_smugmug_domain); ?></label>
								<br />
								<input type="checkbox" name="wp_smugmug_smugmug" id="wp_smugmug_smugmug" value="true"<?php if ( get_option('wp_smugmug_smugmug') == 'true' ) echo ' checked="checked"'; ?> />
								<label for="wpSmugMug_smugmug"><?php _e('Display SmugMug gallery link at bottom', $this->wp_smugmug_domain); ?></label>
								<br />
							</td>
						</tr>
					</table>
					
					<h3><?php _e('Appearance'); ?></h3>
					<p>
					<?php _e('Customize the look and feel of your WP-SmugMug galleries with some custom CSS.'); ?>
					</p>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_titletag"><?php _e('XHTML tag used to<br />enclose the title:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<input type="text" size="2" name="wp_smugmug_titletag" value="<?php echo get_option('wp_smugmug_titletag'); ?>" id="wp_smugmug_titletag" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_css"><?php _e('Custom CSS:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<textarea name="wp_smugmug_css" id="wp_smugmug_css" style="width: 95%;" rows="5" cols="50"><?php echo get_option('wp_smugmug_css'); ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_smugmug_css_ie"><?php _e('Custom CSS for Internet Explorer 7 and below:', $this->wp_smugmug_domain) ?></label>
							</th>
							<td>
								<textarea name="wp_smugmug_css_ie" id="wp_smugmug_css_ie" style="width: 95%;" rows="5" cols="50"><?php echo get_option('wp_smugmug_css_ie'); ?></textarea>
							</td>
						</tr>
					</table>
					
					<p class="submit">
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="page_options" value="wp_smugmug_title,wp_smugmug_description,wp_smugmug_smugmug,wp_smugmug_window,wp_smugmug_sort,wp_smugmug_captions,wp_smugmug_link,wp_smugmug_size,wp_smugmug_thumbsize,wp_smugmug_num,wp_smugmug_start,wp_smugmug_css,wp_smugmug_titletag,wp_smugmug_css_ie" />
						<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes');Ê?>" />
					</p>
				</form>
			</div>
		<?php
		}
	
		function wp_smugmug_admin_print_scripts()
		{
			# Description:	Adds the necessary scripts to the editing page head
			#
			# History:		2008-12-02 - Initial version (adam)
			#
	
			if( $GLOBALS['editing']) {
				$wp = get_bloginfo('wpurl');		
				wp_enqueue_script('wpSmugMugAdmin', $wp . '/' . PLUGINDIR . '/'  . dirname(plugin_basename(__FILE__)) . '/wp-smugmug.js', array('jquery'), '1.0.0');
			}
		}
				
		function fixTrueFalseAndEncoding(&$value, $key) {
			$value = urldecode($value);
	
			if ($value == 'false') {
				$value = false;
			} elseif ($value == 'true') {
				$value = true;
			}
		}
		
		function decode_entities($text, $quote_style = ENT_COMPAT)
		{
			# Description:	Handles UTF-8 decoding in PHP4
			#
			# History:		http://us2.php.net/manual/en/function.html-entity-decode.php#68536
			#
			
			if ( function_exists('html_entity_decode') ) {
				$text = html_entity_decode($text, $quote_style, 'ISO-8859-1');
			} else { 
				$trans_tbl = get_html_translation_table(HTML_ENTITIES, $quote_style);
				$trans_tbl = array_flip($trans_tbl);
				$text = strtr($text, $trans_tbl);
			}
			$text = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $text); 
			$text = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $text);
			
			return $text;
		}
		
		function wp_smugmug_compare_exif($x, $y)
		{
			# Description:	Compare the exif date taken when sorting the array. This is used
			#				because the order of the RSS feed is not always by date.
			#
			# Parameters:	x - first item
			#				y - second item
			#
			# Returns:		comparison result
			#
			# History:		2006-12-13 - Initial version (atow)
			#				2011-04-16 - Use SimplePie
			#
			
			$xt = $x->get_item_tags('http://www.exif.org/specifications.html', 'DateTimeOriginal');
			$yt = $y->get_item_tags('http://www.exif.org/specifications.html', 'DateTimeOriginal');
					
			if( $xt && $yt ) {
				$xt = strtotime($xt[0]['data']);
				$yt = strtotime($yt[0]['data']);
			
				if( $xt == $yt ) {
					return 0;
				} else if( $xt < $yt ) {
					return -1;
				} else {
					return 1;
				}
			 }
			 return 0;
		}
	}
}

global $wp_smugmug;

if ( class_exists('wpSmugMug') ) {
	$wp_smugmug = new wpSmugMug();
}

?>
