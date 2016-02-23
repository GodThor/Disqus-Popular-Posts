<?php

/**
 * Shows the configuration form for a widget or the general settings used as a basis for shortcodes.
 * @param array $instance - Saved variables from the widget or general settings.
 * @return echos the form.
 */
function dpp_shortcode_config($instance = false) {
	if(isset($instance[ 'title' ])) $title = $instance['title'];
	else $title = __('Popular Posts','dpp_domain');

	if(isset($instance['how_many'])) $how_many = $instance['how_many'];
	else $how_many = __('5','dpp_domain');

	if(isset($instance['api_key'])) $api_key = $instance['api_key'];
	else $api_key = __('Disqus API Key','dpp_domain');

	if(isset($instance['forum'])) $forum = $instance['forum'];
	else $forum = __('Disqus Shortname','dpp_domain');

	if(isset($instance['interval'])) $interval = $instance['interval'];
	else $interval = __('90d','dpp_domain');

	if(isset($instance['size_w'])) $size_w = $instance['size_w'];
	else $size_w = __('100','dpp_domain');

	if(isset($instance['size_h'])) $size_h = $instance['size_h'];
	else $size_h = __('100','dpp_domain');

	if(isset($instance['align_image'])) $align_image = $instance['align_image'];
	else $align_image = __('left','dpp_domain');

	if(isset($instance['save_hours'])) $save_hours = $instance['save_hours'];
	else $save_hours = __('24','dpp_domain');

	if(isset($instance['show_title'])) $show_title = $instance['show_title'];
	else $show_title = __('%%title%%','dpp_domain');

	if(isset($instance['css_article_bottom_margin'])) $css_article_bottom_margin = $instance['css_article_bottom_margin'];
	else $css_article_bottom_margin = __('10','dpp_domain');

	if(isset($instance['css_image_margin'])) $css_image_margin = $instance['css_image_margin'];
	else $css_image_margin = __('5','dpp_domain');

	if(isset($instance['comments_text'])) $comments_text = $instance['comments_text'];
	else $comments_text = __('Comments','dpp_domain');

	$last_run = get_option('dpp_last_run');

	if($last_run) $show_last_run = unserialize($last_run);
	else $show_last_run = 'Results not set to save or no results saved yet.';

	if($instance['save_results'] == 1) {
		$disqus_data = get_option('dpp_results');

		if($disqus_data) {
			$disqus_data_type = "# Saved Results\n";
			$disqus_data = unserialize($disqus_data);
		}
	}
	if(!$disqus_data) {
		$disqus_data_type = "# Live Results\n";
		$disqus_data = dpp::fetch($instance);
	}

	echo '
		<form method="post" action="' . $PHP_SELF . '?page=' . DPP_FOLDER . '">';


	$count_list = array('1h','6h','12h','1d','3d','7d','30d','60d','90d');
	$show_count_list = '<select name="interval" id="interval">';
	$interval = esc_attr($interval);

	foreach($count_list as $value) {
		$show_count_list .= '<option value="' . $value . '"' . (($interval && $interval == $value) ? ' selected' : '') . '>' . $value . '</option>';
	}

	$show_count_list .= '</select>';

	echo '
		<style>
			.dpp_tfield {
				max-width: 400px;
			}
		</style>
		<p>
		If you haven\'t yet then you will need to register a new application with the <a href="https://disqus.com/api/applications/" target="_blank">Disqus API</a> to obtain an API key to enter below.
		</p>
		<p>
		<strong><label for="title">' . __( 'Title:' ) . '</label></strong><br />
		<input class="widefat dpp_tfield" id="title" name="title" type="text" value="' . esc_attr($title) . '">
		</p>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Settings</h3></a>
		</div>
		<div>
			<strong><label for="api_key">' . __('Disqus API Key:') . '</label></strong><br />
			<input class="widefat dpp_tfield" id="api_key" name="api_key" type="text" value="' . esc_attr($api_key) . '">
			<p>
			<strong><label for="forum">' . __('Disqus Shortname:') . '</label></strong><br />
			<input class="widefat dpp_tfield" id="forum" name="forum" type="text" value="' . esc_attr($forum) . '"><br />
			<em>You can find this by <a href="https://disqus.com/admin/" target="_blank">logging into Disqus</a> and going to Settings for your site.</em>
			</p>
		</div>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1);" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Options</h3></a>
		</div>
		<div style="display: none;">
			<input class="widefat dpp_tfield" id="ajax_mode" name="ajax_mode" type="checkbox" value="1"' . ((esc_attr($instance['ajax_mode']) == 1) ? " checked" : '') . '> <strong><label for="ajax_mode">' . __('Enable Ajax Mode:') . '</label></strong><br />
			<em>Results will be loaded by Ajax. If you use caching then this will prevent results from being cached.</em>
			</p>
			<p>
			<input class="widefat dpp_tfield" id="hide_comments" name="hide_comments" type="checkbox" value="1"' . ((esc_attr($instance['hide_comments']) == 1) ? " checked" : '') . '> <strong><label for="hide_comments">' . __( 'Hide Comment Count:' ) . '</label></strong>
			</p>
			<p>
				<strong><label for="comments_text">' . __('Comments Text:') . '</label></strong><br />
				<input class="widefat dpp_tfield" id="comments_text" name="comments_text" type="text" value="' . esc_attr($comments_text) . '"><br />
				<em>If you show the comment count then this is the text that appears beside the comment number. If I entered <strong>Comments</strong> in this field then it would appear on the site as: <strong>12 Comments</strong>.</em>
			</p>
			<p>
			<input class="widefat dpp_tfield" id="show_date" name="show_date" type="checkbox" value="1"' . ((esc_attr($instance['show_date']) == 1) ? " checked" : '') . '> <strong><label for="show_date">' . __('Show Post Date:') . '</label></strong>
			</p>
		</div>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Featured Image</h3></a>
		</div>
		<div style="display: none;">
			<p>
			<input class="widefat dpp_tfield" id="featured_image" name="featured_image" type="checkbox" value="1"' . ((esc_attr($instance['featured_image']) == 1) ? ' checked' : '') . '> <strong><label for="featured_image">' . __('Show Featured Image:') . '</label></strong>
			</p>
			<p>
			<strong>' . __( 'Featured Image Size:' ) . '</strong><br />
			<label for="size_w">' . __('Width (pixels):' ) . '</label>
			<input style="width: 50px;" id="size_w" name="size_w" type="text" value="' . esc_attr($size_w) . '">
			<label for="size_h">' . __('Height (pixels):' ) . '</label>
			<input style="width: 50px;" id="size_h" name="size_h" type="text" value="' . esc_attr($size_h) . '"><br />
			<em>Just enter the number.</em>
			</p>
			<p>
			<strong>' . __( 'Featured Image Alignment:' ) . '</strong><br />
			<input class="widefat dpp_tfield" id="align_image-left" name="align_image" type="radio" value="left"' . (($align_image == 'left') ? ' checked' : '') . '> <label for="align_image-left">Left</label> <input class="widefat dpp_tfield" id="align_image-right" name="align_image" type="radio" value="right"' . (($align_image == 'right') ? ' checked' : '') . '> <label for="align_image-right">Right</label> <input class="widefat dpp_tfield" id="align_image-none" name="align_image" type="radio" value="none"' . (($align_image == 'none') ? ' checked' : '') . '> <label for="align_image-none">None</label>
			</p>
		</div>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Posts</h3></a>
		</div>
		<div style="display: none;">
			<p>
			<strong><label for="how_many">' . __( 'How Many Posts to Show:' ) . '</label></strong>
			<input class="widefat dpp_tfield" id="how_many" name="how_many" type="text" value="' . esc_attr( $how_many ) . '" style="width: 50px;">
			</p>
			<p>
			<strong><label for="interval">' . __( 'Count Over How Many Days:' ) . '</label></strong>
			' . $show_count_list . ' <em>h = hours, d = days</em>
			</p>
			<p>
			<strong><label for="show_title">' . __('Display Article Title:') . '</label></strong>
			<input class="widefat dpp_tfield" id="show_title" name="show_title" type="text" value="' . esc_attr($show_title) . '"><br />
			<em>The %%title%% variable is used to display the article title. With this field you can adjust how that title shows, examples: &lt;h3&gt;%%title%%&lt;/h3&gt; or: &lt;strong&gt;%%title%%&lt;/strong&gt; or just: %%title%%. Whatever you like should work here as long as it\'s valid HTML.</em>
			</p>
			<p>
			<input class="widefat dpp_tfield" id="save_results" name="save_results" type="checkbox" value="1"' . ((esc_attr($instance['save_results']) == 1) ? " checked" : '') . '> <strong><label for="save_results">' . __('Save the Results:') . '</label></strong><br />
			<em>This will save the results so that it does not have to query Disqus every time it loads.</em>
			</p>
			<p>
			<strong><label for="save_hours">' . __('Save Results for How Many Hours?:') . '</label></strong>
			<input class="widefat dpp_tfield" id="save_hours" name="save_hours" type="text" value="' . esc_attr($save_hours) . '" style="width: 50px;"><br />
			<em>If you save the results then they will be saved for this many hours and rechecked when that time is met.</em>
			</p>
		</div>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Styling</h3></a>
		</div>
		<div style="display: none;">
			<p>
			<strong><label for="css_image_margin">' . __( 'How Much Space Between Featured Image and Article Info (in pixels):' ) . '</label></strong>
			<input class="widefat dpp_tfield" id="css_image_margin" name="css_image_margin" type="text" value="' . esc_attr( $css_image_margin ) . '" style="width: 50px;"><br />
			<em>Just enter the number.</em>
			</p>
			<p>
			<strong><label for="css_atitle_margin">' . __( 'How Much Space Below the Article Title (in pixels):' ) . '</label></strong>
			<input class="widefat dpp_tfield" id="css_atitle_margin" name="css_atitle_margin" type="text" value="' . esc_attr($instance['css_atitle_margin']) . '" style="width: 50px;"><br />
			<em>Just enter the number.</em>
			</p>
			<p>
			<strong><label for="css_date_margin">' . __( 'How Much Space Below the Article Date (in pixels):' ) . '</label></strong>
			<input class="widefat dpp_tfield" id="css_date_margin" name="css_date_margin" type="text" value="' . esc_attr( $instance['css_date_margin']) . '" style="width: 50px;"><br />
			<em>Just enter the number.</em>
			</p>
			<p>
			<strong><label for="css_article_bottom_margin">' . __( 'How Much Space Between Each Article (in pixels):' ) . '</label></strong>
			<input class="widefat dpp_tfield" id="css_article_bottom_margin" name="css_article_bottom_margin" type="text" value="' . esc_attr( $css_article_bottom_margin ) . '" style="width: 50px;"><br />
			<em>Just enter the number.</em>
			</p>
		</div>
		<div style="margin-bottom: 10px;">
			<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><h3 style="display: inline-block;">Debugging</h3></a>
		</div>
		<div style="display: none;">
			If you are having problems getting results to show up then make sure you have the Disqus API Key and Disqus Shortname set correctly above. Click on this link: <a href="' . sprintf(DPP_DISQUS_API_URL, $instance['api_key'], $instance['forum'], $instance['interval'], $instance['how_many']) . '" target="_blank">Disqus API Call URL</a> to see you get results or an error.
			<p>
			<textarea style="width: 100%; height: 200px;">';

	if($instance) { # Widget
		echo "# Shortcode\n";
		print_r($instance);
		echo "\n";
	}

	# Last Update
	echo "# Last Run\n ";
	print_r($show_last_run);
	echo "\n\n";


	# Disqus Data
	echo $disqus_data_type;
	print_r($disqus_data);

	echo '
			</textarea>
			</p>
			<p>
			<input type="checkbox" name="nuclear" id="nuclear" value="1"> <strong><label for="nuclear">Clear All Saved Results?</label></strong><br />
			<em>This will clear all saved data for all widgets and all shortcodes.</em>
			</p>
		</div>
		<p>
		Please <a href="https://wordpress.org/support/view/plugin-reviews/disqus-popular-posts" target="_blank">rate and review</a>. I\'d greatly appreciate it!
		</p>
		<p>
		<input type="submit" value="Save" class="button button-primary button-large">
		</p>
		</form>';
}

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

switch($mode) {
	default:
		echo '<h1>Disqus Popular Posts</h1>
			<p>
			If you are using a widget to show popular posts then all those settings are in the <a href="widgets.php">widget area</a>. However, if you plan to use shortcodes then these settings are for that.
			</p>
			<p>
			Below are all the settings you\'ll need to make the shortcode work. The shortcode you will use is: [dpp]
			</p>
			<p>
			For help with shortcode attributes, see the Help menu above.
			</p>
			<h2>Shortcode Settings</h2>';

		if($_POST) {
			foreach($_POST as $key=>$value) {
				if($key != 'show_title') $value = strip_tags($value);

				$settings[$key] = $value;
			}

			update_option('dpp_settings', serialize($settings));

			if($_POST['nuclear']) {
				# Clear all previous data
				update_option('dpp_last_run','');
				update_option('dpp_results','');
			}			
		}

		$instance = get_option('dpp_settings');

		if($instance) $instance = unserialize($instance);
		
		dpp_shortcode_config($instance);
	break;
}
?>