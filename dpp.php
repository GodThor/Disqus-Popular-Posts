<?php

/*
	Plugin Name: Disqus Popular Posts
	Plugin URI: http://wphelpandtips.com/wordpress-plugin-disqus-popular-posts/
	Description: Shows the most popular posts sorted by comments in a widget or with a shortcode.
	Author: Thor
	Version: 2.0.4
	Author URI: http://wphelpandtips.com/
*/

if(!class_exists('dpp')) {
	class dpp {
		/**
		 * All the core stuff needed to run.
		 */
		function __construct() {
			# Actions
			add_action('widgets_init', array($this,'widget_init'));
			add_action('admin_menu', array($this,'menu'));

			# Ajax
			add_action('wp_ajax_dpp_render', array($this,'render')); // ajax for logged in users
			add_action('wp_ajax_nopriv_dpp_render', array($this,'render')); // ajax for not logged in users

			# Filters
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this,'add_action_link'));

			# Shortcodes
			add_shortcode('dpp', array($this,'shortcode'));

			define('DPP_DISQUS_API_URL','http://disqus.com/api/3.0/threads/listPopular.json?api_key=%s&forum=%s&interval=%s&limit=%d');
			define('DPP_FOLDER', plugin_basename(dirname(__FILE__)));
		}
		/**
		 * Register the widget with WordPress
		 */
		function widget_init() {
			register_widget('dpp_widget');
		}
		/**
		 * Shows the rate and review link on the plugins page.
		 * @param text $links
		 * @return array
		 */
		function add_action_link($links) {
			$path = admin_url();

			$mylinks = array(
				'<a href="https://wordpress.org/support/view/plugin-reviews/disqus-popular-posts" target="_blank">Rate and Review</a>',
				'<a href="' . $path . 'options-general.php?page=' . DPP_FOLDER . '">Shortcode Settings</a>',
				'<a href="' . $path . 'widgets.php">Create Widget</a>'
			);

			return array_merge($mylinks, $links);
		}
		/**
		 * Creates the menu in WP admin for the plugin.
		 */
		function menu() {
			$admin_menu = add_submenu_page('options-general.php','Disqus Popular Posts','Disqus Popular Posts','administrator', DPP_FOLDER, array($this,'admin'));
			add_action('load-' . $admin_menu, array($this,'help'), 20);
		}
		/**
		 * Creates the help tab on the settings page.
		 */
		function help() {
			$screen = get_current_screen();
			$screen->add_help_tab( array(
				'id' => 'dpp_help',            //unique id for the tab
				'title' => 'Shortcode Attributes',      //unique visible title for the tab
				'content' => 'If you would like to override any of the below settings in a shortcode then you can do so. The idea is this way you don\'t have to create a shortcode with a ton of attributes just to make it work. Instead, override anything you neeed as needed. Below are the attributes you can set in the shortcode to override values set in the settings. More detailed information can be found below in the settings area.
					<ul>
					<li><strong>id:</strong> Value is text, IE: myid, someotherid, etc. See note below.</li>
					<li><strong>title:</strong> Value is text, IE: Popular Articles</li>
					<li><strong>api_key:</strong> See note below if using Ajax Mode.</li>
					<li><strong>forum:</strong> Value is text, IE: myforum</li>
					<li><strong>ajax_mode:</strong> Value: 1 (true) or 0 (false)</li>
					<li><strong>hide_comments:</strong>Value: 1 (true) or 0 (false)</li>
					<li><strong>comments_text:</strong> Value is text, IE: Comments.</li>
					<li><strong>show_date:</strong> Value: 1 (true) or 0 (false)</li>
					<li><strong>featured_image:</strong> Value: 1 (true) or 0 (false)</li>
					<li><strong>size_w:</strong> Value is numeric, IE: 50</li>
					<li><strong>size_h:</strong> Value is numeric, IE: 50</li>
					<li><strong>align_image:</strong> Values: left, right or none</li>
					<li><strong>how_many:</strong> Value is numeric, IE: 5</li>
					<li><strong>interval:</strong> Value is text, IE: 90d</li>
					<li><strong>show_title:</strong> Value is text, IE: &lt;strong&gt;%%title%%&lt;/strong&gt;, where %%title%% is a placeholder for the post title. See note below.</li>
					<li><strong>save_results:</strong> Value: 1 (true) or 0 (false)</li>
					<li><strong>save_hours:</strong> Value is numeric, IE: 12</li>
					<li><strong>css_image_margin:</strong> Value is numeric, IE: 5</li>
					<li><strong>css_atitle_margin:</strong> Value is numeric, IE: 5</li>
					<li><strong>css_date_margin:</strong> Value is numeric, IE: 5</li>
					<li><strong>css_article_bottom_margin:</strong> Value is numeric, IE: 5</li>
					</ul>
					<p>
					To use these attributes you woud put them inside the shortcode. An example would be: [dpp title="My Popular Posts" ajax_mode="1" show_date="0" how_many="10"]<br />
					In this case the title, ajax_mode, show_date and how_many values are being overridden in the shortcode from what they are set to below.
					</p>
					<p>
					<strong>id Note:</strong> This attribute is only used in the rare situation that you are using more than one [dpp] shortcode on a page AND you are also saving results. By using the id attribute you are then able to save the results for each shortcode separately.
					</p>
					<p>
					<strong>api_key Note:</strong> The Disqus API key cannot be changed in the shortcode if using ajax mode and will always use the API key set below. This is for security reasons. You can change the API key in the shortcode if not using ajax mode though.
					</p>
					<p>
					<strong>show_title Note:</strong> WordPress has some limitations on HTML entered. If you find things aren\'t behaving then your best bet is to set how you want this to show up below in settings instead of trying to override it with this shortcode attribute.
					</p>'
			));
		}
		/**
		 * Disqus admin area.
		 * @return echo all the admin stuff.
		 */
		function admin() {
			require 'dpp_admin.php';
		}
		/**
		 * Makes the API call to Disqus.
		 * @param array $instance - The wdiget variables.
		 * @return array
		 */
		static function fetch($instance) {
			if(!$instance['interval']) $instance['interval'] = '90d'; # Set a default because of the 2.0.0 change to interval

			if(isset($instance['api_key']) && isset($instance['forum']) && isset($instance['interval']) && isset($instance['how_many'])) {
				$url_call = sprintf(DPP_DISQUS_API_URL, $instance['api_key'], $instance['forum'], $instance['interval'], $instance['how_many']);

				@$get_contents = file_get_contents($url_call);
				if($get_contents) $results = json_decode($get_contents);

				if($results && is_array($results->response)) {
					foreach($results->response as $key=>$fields) {
						$posts_by_count[$key] = $fields->posts;
					}
					if($posts_by_count && is_array($posts_by_count)) {
						arsort($posts_by_count);

						foreach($posts_by_count as $key=>$posts) {
							$sorted_results[$key] = $results->response[$key];
						}
					}
				}
				if($sorted_results) return $sorted_results;
				else return false;
			}
			else {
				return false;
			}
		}
		/**
		 * Does the actual displaying of the results.
		 * @param array $instance - The array of saved options for the widget.
		 * @param bool $widget_id - Used for saving results on each widget or shortcode instance
		 * @return string Returns the results as HTML if not in Ajax Mode, otherwise echos and exits.
		 */
		static function render($instance = false, $widget_id = false) {
			$dpp_db_version = 4; # Version used to indicate if saved results need updating.
			$code = '';
			$ajax = false;

			if(!$instance) { # Ajax
				$ajax = true;

				if(!$_POST['shortcode']) { # It's a widget
					$options = get_option('widget_dpp_widget');
					$get_id = $_POST['widget_id'];
					$get_id = explode('-', $get_id);
					$widget_id = $get_id[1];

					foreach($options as $key=>$fields) {
						if(is_numeric($key) && $key == $widget_id) {
							$instance = $fields;
							break;
						}
					}
				}
				else {
					$instance = unserialize(get_option('dpp_settings'));

					if($_POST['shortcode_atts']) {
						parse_str(html_entity_decode($_POST['shortcode_atts']), $settings);

						if($settings) {
							foreach($settings as $key=>$value) { # Override base settings
								$instance[$key] = $value;
							}
						}

						$code .= '<h4 class="dpp_title">' . $instance['title'] . '</h4>';
					}
				}
			}
			if($instance) {
				# Set the save_id for use in getting the right last run and result set.
				if(isset($instance['id'])) $save_id = 'shortcode_' . $instance['id']; # Shortcode with an ID
				elseif(!isset($instance['id']) && isset($_POST['shortcode'])) $save_id = 'shortcode_0'; # Shortcode without an ID
				elseif($widget_id) $save_id = 'widget_' . $widget_id; # Widget
				if(!$save_id) $save_id = 0; # Default ID to use

				$db_update = false;
				$ex_dpp_db_version = get_option('dpp_db_version');

				if($ex_dpp_db_version != $dpp_db_version) {
					$db_update = true;
					# Clear all previous data
					update_option('dpp_last_run','');
					update_option('dpp_results','');
				}

				$query_disqus = true;
				$now = date('Y-m-d H:i', current_time('timestamp')); # Use the local time

				$save_hours = $instance['save_hours'];

				if($instance['save_results'] && $save_hours > 0 && !$db_update) { # Only bother if I have proper variables and I'm not forcing a database update
					$last_run_check = get_option('dpp_last_run');

					if($last_run_check) { # It's run previously
						$last_run_check = unserialize($last_run_check);

						if($last_run_check && $last_run_check[$save_id]) { # Passed serialization and has a saved value
						    $datetime1 = date_create($now);
						    $datetime2 = date_create($last_run_check[$save_id]);
						    $interval = date_diff($datetime1, $datetime2);

						    if($interval->format('%h') < $save_hours) $query_disqus = false;
						}
					}
				}
				if(!$query_disqus) {
					$all_results = get_option('dpp_results'); # Get all the saved results

					if($all_results) {
						$all_results = unserialize($all_results);
						$sorted_results = $all_results[$save_id]; # Set the results we need based on the save_id
					}

					if(!$sorted_results) $query_disqus = true; # No results so get them
				}
				if($query_disqus && $instance['api_key'] && $instance['forum'] && $instance['interval'] && $instance['how_many']) {
					$sorted_results = dpp::fetch($instance);

					if(!$sorted_results && $instance['save_results']) { # No results so check for any saved results
						$all_results = get_option('dpp_results'); # Get all the saved results

						if($all_results) {
							$all_results = unserialize($all_results);
							$sorted_results = $all_results[$save_id]; # Set the results we need based on the save_id
						}
					}
				}
				if($sorted_results) {
					$show_title = $instance['show_title'];

					if(strpos($show_title, '%%title%%') === false) $show_title = '%%title%%'; # Make sure a title shows up if they removed it

					if($query_disqus && $instance['save_results'] && $save_hours) $updating_results = true;
					else $updating_results = false;

					$all_results = get_option('dpp_results'); # Get all the saved results

					if($all_results) {
						$all_results = unserialize($all_results);

						if($updating_results) $all_results[$save_id] = $sorted_results; # Just replace this result key
						else unset($all_results[$save_id]); # Remove this entry
					}
					else { # None previously saved or something messed up
						if($updating_results) $all_results[$save_id] = $sorted_results;
					}

					$last_run = get_option('dpp_last_run');

					if($last_run) {
						$last_run = unserialize($last_run);

						if($updating_results) $last_run[$save_id] = $now;
						else unset($last_run[$save_id]);
					}
					else {
						$last_run[$save_id] = $now;
					}
					if($updating_results) {
						update_option('dpp_last_run', serialize($last_run));
						update_option('dpp_results', serialize($all_results));
					}
					elseif(!$instance['save_results']) { # Unset the previous options if not saving
						if($last_run) $last_run = serialize($last_run);
						else $last_run = '';

						if($all_results) $all_results = serialize($all_results);
						else $all_results = '';

						update_option('dpp_last_run', $last_run);
						update_option('dpp_results', $all_results);
					}

					# Setup styles as needed
					if($instance['css_article_bottom_margin']) $article_css = ' margin-bottom: ' . $instance['css_article_bottom_margin'] . 'px;';
					else $article_css = '';

					if($instance['align_image'] != 'none') {
						$image_css = ' style="float: ' . $instance['align_image'] . ';';
						$image_css .= ' margin-' . (($instance['align_image'] == 'left') ? 'right' : 'left') . ': ';
						$image_css .= ($instance['css_image_margin']) ? $instance['css_image_margin'] . 'px;' : '0px;';
						$image_css .= '"'; # Close the quote on the opening style
					}

					if($instance['css_atitle_margin']) $atitle_css = ' style="margin-bottom: ' . $instance['css_atitle_margin'] . 'px;"';
					else $atitle_css = '';

					if($instance['css_date_margin']) $date_css = ' style="margin-bottom: ' . $instance['css_date_margin'] . 'px;"';
					else $date_css = '';

					$list = '';

					foreach($sorted_results as $key=>$fields) {
						$img_div = '';
						$post_array = '';
						$post_id = 0;

						# Post ID - Method #1
						$url = site_url();
						$page_path = trim(str_replace($url,'', $fields->link),'/');
						$post_id = url_to_postid($page_path);

						# Post ID - Method #2
						# Get the ID by the dsq_thread_id meta_value
						if(!$post_id && isset($fields->id)) {
							$post_array = get_posts(array('posts_per_page'=>1,'meta_key'=>'dsq_thread_id','meta_value'=>$fields->id));
							$post_id = (isset($post_array[0]->ID)) ? $post_array[0]->ID : '';
						}
						# Post ID - Method #3
						# Fallback method. Unreliable from Disqus using the identifier field.
						elseif((!$post_array || !$post_id) && $fields->identifiers[0]) {
							$page_string = $fields->identifiers[0];
							$page_parts = explode(' ', $page_string);
							$post_id = $page_parts[0];
						}

						if($post_id) $use_title = get_the_title($post_id);
						else $use_title = $fields->title;

						if($use_title) $display_title = str_replace('%%title%%', $use_title, $show_title);

						if($post_id && is_numeric($post_id) && $instance['featured_image'] && $instance['size_w'] && $instance['size_h']) {
							$image = get_the_post_thumbnail($post_id, array($instance['size_w'], $instance['size_h']), array('style'=>'float: none;'));

							if($image) {
								$img_div = '<div class="dpp_featured_image"' . $image_css . '><a href="' . $fields->link . '">' . $image . '</a></div>';
							}
						}
						if($display_title || ($instance['featured_image'] && $img_div)) {
							# Build the result
							$list .= '<div class="dpp_result" id="dpp_post_' . $post_id . '" style="clear: both; overflow: hidden;' . $article_css . '">';
							$list .= $img_div;
							$list .= '<div class="dpp_post_title"' . $atitle_css . '><a href="' . $fields->link . '">' . $display_title . '</a></div>';
							$list .= ($instance['show_date'] && $post_id) ? '<div class="dpp_post_date"' . $date_css . '>' . get_the_date('', $post_id ) . '</div>' : '';
							$list .= (!$instance['hide_comments']) ? '<div class="dpp_comments"><a href="' . $fields->link . '#disqus_thread">' . $fields->posts . ' ' . '</a> ' . $instance['comments_text'] . '</div>' : '';
							$list .= '</div>'; # Close dpp_result
						}

						unset($display_title, $post_array, $post_id, $image, $img_div);
					}

					$code .= $list;
				}

				if($db_update) update_option('dpp_db_version', $dpp_db_version);

				if(!$ajax) {
					return $code;
				}
				else {
					echo $code;
					exit;
				}
			}
			elseif(!$instance && $ajax) { # Something went wrong so exit for ajax to avoid 0 showing up.
				exit;
			}
		}
		/**
		 * Handles the shortcode.
		 * @param array $atts - Shortcode attributes.
		 * @param string $content - Shortcode content.
		 * @return string - Popular posts.
		 */
		function shortcode($atts, $content = '') {
			$settings = unserialize(get_option('dpp_settings'));

			if($atts) { # Override the base settings
				foreach($atts as $key=>$value) {
					$settings[$key] = $value;
				}
			}
			if($settings['ajax_mode']) {
				wp_enqueue_script('dpp', plugin_dir_url( __FILE__ ) . 'dpp.js', array('jquery'), false, true);
				wp_localize_script('dpp','dpp_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));

				# Create a string to be parsed later for attributes.
				if($atts) {
					foreach($atts as $key=>$value) {
						if($key != 'api_key') $settings_string .= ((!$settings_string) ? '' : '&') . $key . '=' . (($key == 'show_title') ? htmlentities($value) : $value);
					}
				}

				$unique_id = uniqid();

				$code .= '<span class="dpp_render_area" id="dpp_shortcode_render_area_' . $unique_id . '">' . (($settings_string) ? '<span id="dpp_shortcode_render_area_' . $unique_id . '_settings" style="display: none;">' . $settings_string . '</span>' : '') . '</span>';
			}
			else {


				$code .= '<h4 class="dpp_title">' . $settings['title'] . '</h4>' . dpp::render($settings);
			}

			return $code;
		}
	}
}

/**
 * @package Main
 */
class dpp_widget extends WP_Widget {
	/**
	 * @var Tracks the database version of the stored results
	 */
	function __construct() {
		parent::__construct(
			'dpp_widget', // Base ID
			__('Disqus Popular Posts', 'dpp_domain'), // Name
			array( 'description' => __( 'Shows the most popular posts by comments.', 'dpp_domain' ), ) // Args
		);
	}
	/**
	 * Renders the widget on the site.
	 * @param array $args Any arguments passed to the widget.
	 * @param array $instance The saved variables from the widget setup.
	 */
	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		$get_id = $this->id;
		$get_id = explode('-', $get_id);
		$widget_id = $get_id[1];

		$code = $args['before_widget'];

		if(!empty($title)) $code .= $args['before_title'] . $title . $args['after_title'];

		if($instance['ajax_mode']) {
			wp_enqueue_script('dpp', plugin_dir_url( __FILE__ ) . 'dpp.js', array('jquery'), false, true);
			wp_localize_script('dpp','dpp_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
			$code .= '<span class="dpp_render_area" id="dpp_render_area-' . $widget_id . '"></span>';
		}
		else {
			$code .= dpp::render($instance, $widget_id);
		}

		$code .= $args['after_widget'];

		echo $code;
	}
	/**
	 * The form shown in the Admin->Widgets area.
	 * @param array $instance The variables for the widget which are saved.
	 */
	public function form($instance) {
		$debug_settings = $instance;

		if($debug_settings['api_key']) $debug_settings['api_key'] = 'hidden';

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

		$count_list = array('1h','6h','12h','1d','3d','7d','30d','60d','90d');
		$show_count_list = '<select name="' . $this->get_field_name('interval') . '" id="' . $this->get_field_id('interval') . '">';
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
			<strong><label for="' . $this->get_field_id('title') . '">' . __( 'Title:' ) . '</label></strong><br />
			<input class="widefat dpp_tfield" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr( $title ) . '">
			</p>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Settings</strong></a>
			</div>
			<div>
				<strong><label for="' . $this->get_field_id('api_key') . '">' . __('Disqus API Key:') . '</label></strong><br />
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('api_key') . '" name="' . $this->get_field_name('api_key') . '" type="text" value="' . esc_attr($api_key) . '">
				<p>
				<strong><label for="' . $this->get_field_id('forum') . '">' . __('Disqus Shortname:') . '</label></strong><br />
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('forum') . '" name="' . $this->get_field_name('forum') . '" type="text" value="' . esc_attr($forum) . '"><br />
				<em>You can find this by <a href="https://disqus.com/admin/" target="_blank">logging into Disqus</a> and going to Settings for your site.</em>
				</p>
			</div>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1);" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Options</strong></a>
			</div>
			<div style="display: none;">
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('ajax_mode') . '" name="' . $this->get_field_name('ajax_mode') . '" type="checkbox" value="1"' . ((esc_attr($instance['ajax_mode']) == 1) ? " checked" : '') . '> <strong><label for="' . $this->get_field_id('ajax_mode') . '">' . __('Enable Ajax Mode:') . '</label></strong><br />
				<em>Results will be loaded by Ajax. If you use caching then this will prevent results from being cached.</em>
				</p>
				<p>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('hide_comments') . '" name="' . $this->get_field_name('hide_comments') . '" type="checkbox" value="1"' . ((esc_attr($instance['hide_comments']) == 1) ? " checked" : '') . '> <strong><label for="' . $this->get_field_id('hide_comments') . '">' . __( 'Hide Comment Count:' ) . '</label></strong>
				</p>
				<p>
					<strong><label for="' . $this->get_field_id('comments_text') . '">' . __('Comments Text:') . '</label></strong><br />
					<input class="widefat dpp_tfield" id="' . $this->get_field_id('comments_text') . '" name="' . $this->get_field_name('comments_text') . '" type="text" value="' . esc_attr($instance['comments_text']) . '"><br />
					<em>If you show the comment count then this is the text that appears beside the comment number. If I entered <strong>Comments</strong> in this field then it would appear on the site as: <strong>12 Comments</strong>.</em>
				</p>
				<p>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('show_date') . '" name="' . $this->get_field_name('show_date') . '" type="checkbox" value="1"' . ((esc_attr($instance['show_date']) == 1) ? " checked" : '') . '> <strong><label for="' . $this->get_field_id('show_date') . '">' . __('Show Post Date:') . '</label></strong>
				</p>
			</div>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Featured Image</strong></a>
			</div>
			<div style="display: none;">
				<p>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('featured_image') . '" name="' . $this->get_field_name('featured_image') . '" type="checkbox" value="1"' . ((esc_attr($instance['featured_image']) == 1) ? ' checked' : '') . '> <strong><label for="' . $this->get_field_id('featured_image') . '">' . __('Show Featured Image:') . '</label></strong>
				</p>
				<p>
				<strong>' . __( 'Featured Image Size:' ) . '</strong><br />
				<label for="' . $this->get_field_id('size_w') . '">' . __('Width (pixels):' ) . '</label>
				<input style="width: 50px;" id="' . $this->get_field_id('size_w') . '" name="' . $this->get_field_name('size_w') . '" type="text" value="' . esc_attr($size_w) . '">
				<label for="' . $this->get_field_id('size_h') . '">' . __('Height (pixels):' ) . '</label>
				<input style="width: 50px;" id="' . $this->get_field_id('size_h') . '" name="' . $this->get_field_name('size_h') . '" type="text" value="' . esc_attr($size_h) . '"><br />
				<em>Just enter the number.</em>
				</p>
				<p>
				<strong>' . __( 'Featured Image Alignment:' ) . '</strong><br />
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('align_image') . '-left" name="' . $this->get_field_name('align_image') . '" type="radio" value="left"' . (($align_image == 'left') ? ' checked' : '') . '> <label for="' . $this->get_field_id('align_image') . '-left">Left</label> <input class="widefat dpp_tfield" id="' . $this->get_field_id('align_image') . '-right" name="' . $this->get_field_name('align_image') . '" type="radio" value="right"' . (($align_image == 'right') ? ' checked' : '') . '> <label for="' . $this->get_field_id('align_image') . '-right">Right</label> <input class="widefat dpp_tfield" id="' . $this->get_field_id('align_image') . '-none" name="' . $this->get_field_name('align_image') . '" type="radio" value="none"' . (($align_image == 'none') ? ' checked' : '') . '> <label for="' . $this->get_field_id('align_image') . '-none">None</label>
				</p>
			</div>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Posts</strong></a>
			</div>
			<div style="display: none;">
				<p>
				<strong><label for="' . $this->get_field_id('how_many') . '">' . __( 'How Many Posts to Show:' ) . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('how_many') . '" name="' . $this->get_field_name('how_many') . '" type="text" value="' . esc_attr( $how_many ) . '" style="width: 50px;">
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('interval') . '">' . __( 'Count Over How Many Days:' ) . '</label></strong>
				' . $show_count_list . ' <em>h = hours, d = days</em>
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('show_title') . '">' . __('Display Article Title:') . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('show_title') . '" name="' . $this->get_field_name('show_title') . '" type="text" value="' . esc_attr($show_title) . '"><br />
				<em>The %%title%% variable is used to display the article title. With this field you can adjust how that title shows, examples: &lt;h3&gt;%%title%%&lt;/h3&gt; or: &lt;strong&gt;%%title%%&lt;/strong&gt; or just: %%title%%. Whatever you like should work here as long as it\'s valid HTML.</em>
				</p>
				<p>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('save_results') . '" name="' . $this->get_field_name('save_results') . '" type="checkbox" value="1"' . ((esc_attr($instance['save_results']) == 1) ? " checked" : '') . '> <strong><label for="' . $this->get_field_id('save_results') . '">' . __('Save the Results:') . '</label></strong><br />
				<em>This will save the results so that it does not have to query Disqus every time it loads.</em>
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('save_hours') . '">' . __('Save Results for How Many Hours?:') . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('save_hours') . '" name="' . $this->get_field_name('save_hours') . '" type="text" value="' . esc_attr($save_hours) . '" style="width: 50px;"><br />
				<em>If you save the results then they will be saved for this many hours and rechecked when that time is met.</em>
				</p>
			</div>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Styling</strong></a>
			</div>
			<div style="display: none;">
				<p>
				<strong><label for="' . $this->get_field_id('css_image_margin') . '">' . __( 'How Much Space Between Featured Image and Article Info (in pixels):' ) . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('css_image_margin') . '" name="' . $this->get_field_name('css_image_margin') . '" type="text" value="' . esc_attr( $css_image_margin ) . '" style="width: 50px;"><br />
				<em>Just enter the number.</em>
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('css_atitle_margin') . '">' . __( 'How Much Space Below the Article Title (in pixels):' ) . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('css_atitle_margin') . '" name="' . $this->get_field_name('css_atitle_margin') . '" type="text" value="' . esc_attr($instance['css_atitle_margin']) . '" style="width: 50px;"><br />
				<em>Just enter the number.</em>
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('css_date_margin') . '">' . __( 'How Much Space Below the Article Date (in pixels):' ) . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('css_date_margin') . '" name="' . $this->get_field_name('css_date_margin') . '" type="text" value="' . esc_attr( $instance['css_date_margin']) . '" style="width: 50px;"><br />
				<em>Just enter the number.</em>
				</p>
				<p>
				<strong><label for="' . $this->get_field_id('css_article_bottom_margin') . '">' . __( 'How Much Space Between Each Article (in pixels):' ) . '</label></strong>
				<input class="widefat dpp_tfield" id="' . $this->get_field_id('css_article_bottom_margin') . '" name="' . $this->get_field_name('css_article_bottom_margin') . '" type="text" value="' . esc_attr( $css_article_bottom_margin ) . '" style="width: 50px;"><br />
				<em>Just enter the number.</em>
				</p>
			</div>
			<div style="margin-bottom: 10px;">
				<a href="javascript:void(1)" onclick="jQuery(this).parent().next(\'div\').slideToggle();" style="background: url(\'images/arrows.png\') no-repeat; padding-left: 15px;"><strong>Debugging</strong></a>
			</div>
			<div style="display: none;">
				If you are having problems getting results to show up then make sure you have the Disqus API Key and Disqus Shortname set correctly above. Click on this link: <a href="' . sprintf(DPP_DISQUS_API_URL, $instance['api_key'], $instance['forum'], $instance['interval'], $instance['how_many']) . '" target="_blank">Disqus API Call URL</a> to see you get results or an error.
				<p>
				<textarea style="width: 100%; height: 200px;">';

		if($debug_settings) { # Widget
			echo "# Widget\n";
			print_r($debug_settings);
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
				<input type="checkbox" name="' . $this->get_field_name('nuclear') . '" id="' . $this->get_field_id('nuclear') . '" value="1"> <strong><label for="' . $this->get_field_id('nuclear') . '">Clear All Saved Results?</label></strong><br />
				<em>This will clear all saved data for all widgets and all shortcodes.</em>
				</p>
			</div>
			<p>
			Please <a href="https://wordpress.org/support/view/plugin-reviews/disqus-popular-posts" target="_blank">rate and review</a>. I\'d greatly appreciate it!
			</p>';	
	}
	/**
	 * Handles updating the widget variables.
	 * @param array $new_instance The new variables.
	 * @param array $old_instance The old variables.
	 * @return array The variables to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['ajax_mode'] = (!empty($new_instance['ajax_mode'])) ? strip_tags($new_instance['ajax_mode']) : '';
		$instance['how_many'] = (!empty($new_instance['how_many'])) ? strip_tags($new_instance['how_many']) : '';
		$instance['api_key'] = (!empty( $new_instance['api_key'])) ? strip_tags($new_instance['api_key']) : '';
		$instance['forum'] = (!empty($new_instance['forum'])) ? strip_tags($new_instance['forum']) : '';
		$instance['interval'] = (!empty($new_instance['interval'])) ? strip_tags($new_instance['interval']) : '';
		$instance['hide_comments'] = (!empty($new_instance['hide_comments'])) ? strip_tags($new_instance['hide_comments']) : '';
		$instance['featured_image'] = (!empty($new_instance['featured_image'])) ? strip_tags($new_instance['featured_image']) : '';
		$instance['size_w'] = (!empty($new_instance['size_w'])) ? strip_tags($new_instance['size_w']) : '';
		$instance['size_h'] = (!empty($new_instance['size_h'])) ? strip_tags($new_instance['size_h']) : '';
		$instance['show_date'] = (!empty($new_instance['show_date'])) ? strip_tags($new_instance['show_date']) : '';
		$instance['align_image'] = (!empty($new_instance['align_image'])) ? strip_tags($new_instance['align_image']) : '';
		$instance['save_results'] = (!empty($new_instance['save_results'])) ? strip_tags($new_instance['save_results']) : '';
		$instance['save_hours'] = (!empty($new_instance['save_hours'])) ? strip_tags(ceil($new_instance['save_hours'])) : '';
		$instance['show_title'] = (!empty($new_instance['show_title'])) ? $new_instance['show_title'] : '';
		$instance['css_article_bottom_margin'] = (!empty($new_instance['css_article_bottom_margin'])) ? strip_tags($new_instance['css_article_bottom_margin']) : '';
		$instance['css_image_margin'] = (!empty($new_instance['css_image_margin'])) ? strip_tags($new_instance['css_image_margin']) : '';
		$instance['css_atitle_margin'] = (!empty($new_instance['css_atitle_margin'])) ? strip_tags($new_instance['css_atitle_margin']) : '';
		$instance['css_date_margin'] = (!empty($new_instance['css_date_margin'])) ? strip_tags($new_instance['css_date_margin']) : '';
		$instance['comments_text'] = (!empty($new_instance['comments_text'])) ? strip_tags($new_instance['comments_text']) : '';

		if($new_instance['nuclear']) {
			# Clear all previous data
			update_option('dpp_last_run','');
			update_option('dpp_results','');
		}

		return $instance;
	}
}

$dpp = new dpp();

?>
