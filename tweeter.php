<?php
/*
Plugin Name: Tweeter!
Plugin URI: http://pathartl.me/4/tweeter/
Description: Another Twitter widget, this time with some style!
Author: Pat Hartl
Version: 1.1.0
Author URI: http://pathartl.me/

Copyright 2009  Pat Hartl  (email : pat@pathartl.me)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
function tweeter_options() {
	add_options_page('Tweeter!', 'Tweeter!', 'manage_options', 'tweeter', 'tweeter_options_page');
	add_submenu_page(basename(__FILE__), 'Settings', 'Settings', 8, basename(__FILE__), 'tweeter_options_page');
}
?>
<?php function tweeter_options_page() { ?>

<div class="wrap">
    
    <div class="icon32" id="icon-options-general"><br/></div><h2>Settings for Tweeter!</h2>

    <form method="post" action="options.php">

	    <?php
	        if(function_exists('settings_fields')){
	            settings_fields('tweeter-options');
	        } else {
	            wp_nonce_field('update-options');
	            ?>
	            <input type="hidden" name="action" value="update" />
	            <input type="hidden" name="page_options" value="username,tweetCount,delay,replies" />
	            <?php
	        }
	    ?>
	
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="tweeter_username">Twitter Username</label></th>
				<td>
					<input type="text" name="tweeter_username" value="<?php echo get_option('tweeter_username'); ?>" size="20" />
					<span class="description">The username of your Twitter account.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tweeter_tweetCount">Number of Tweets</label></th>
				<td>
					<input type="text" name="tweeter_tweetCount" value="<?php echo get_option('tweeter_tweetCount'); ?>" size="10" />
					<span class="description">How many Tweets you want to display at one time.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tweeter_delay">Refresh Delay</label></th>
				<td>
					<input type="text" name="tweeter_delay" value="<?php echo get_option('tweeter_delay'); ?>" size="10" />
					<span class="description">The amount of time (in milliseconds) in which the feed is refreshed.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tweeter_replies">Replies?</label></th>
				<td>
					<select name="tweeter_replies">
                		<option <?php if (get_option('tweeter_replies') == 'false') echo 'selected="selected"'; ?> value="false">Yes</option>
                		<option <?php if (get_option('tweeter_replies') == 'true') echo 'selected="selected"'; ?> value="true">No</option> 
                	</select>
                	<span class="description">Should @replies from your Twitter feed be displayed?</span>
				</td>
			</tr>
		</table>
		<p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" class="button-primary" />
        </p>

    </form>
    
    <p>I created this little plugin just for fun. If you liked it as much as I do, why not follow me? My Twitter username is <a href="http://twitter.com/pathartl">@pathartl</a>. Who knows, if you're interesting enough I may follow you too!</p>

</div>
<?php } ?>
<?php

// Checks to see if the user is an admin, then adds the options
if(is_admin()){
    add_action('admin_menu', 'tweeter_options');
    add_action('admin_init', 'tweeter_init');
}

//default options
function tweeter_activate(){
    add_option('tweeter_username', 'wordpress');
    add_option('tweeter_tweetCount', 3);
    add_option('tweeter_delay', 30000);
    add_option('tweeter_replies', 'true');
}


function displayTweeter()
{
?>
	<script type="text/javascript">
		function getTweets() {
			getTwitters('tweeter_feed', { 
				id: '<?php echo get_option('tweeter_username');?>',
				count: '<?php echo get_option('tweeter_tweetCount');?>', 
				enableLinks: true, 
				ignoreReplies: <?php echo get_option('tweeter_replies');?>, 
				clearContents: true,
				template: '<div class="tweeter_tweet"><span class="tweeter_text"><a href="http://twitter.com/%user_screen_name%/statuses/%id%/">%text%</a></span><br /><span class="tweeter_time">%time%</span></div>'
			});
		}
		jQuery.noConflict();
		jQuery(document).ready(function(){
		getTweets();                          //Get initial tweets
		setInterval ( "getTweets()", '<?php echo get_option('tweeter_delay');?>' ); //Set tweets to refresh every 30 seconds
		});
	</script>
		<div id="tweeter_container">
  			<div id="tweeter_feed"></div>
  			<a href="http://twitter.com/<?php echo get_option('tweeter_username');?>"><div id="tweeter_bottom"></div></a>
  		</div>
<?php
}

function widget_tweeter($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>Tweeter!<?php echo $after_title;
  displayTweeter();
  echo $after_widget;
}

function tweeter_init()
{
  if(function_exists('register_setting')){
      register_setting('tweeter-options', 'tweeter_username');
      register_setting('tweeter-options', 'tweeter_tweetCount');
      register_setting('tweeter-options', 'tweeter_delay'); 
      register_setting('tweeter-options', 'tweeter_replies'); 
  }
  wp_register_script('tweeter_twitter', 'http://twitterjs.googlecode.com/svn/trunk/src/twitter.min.js');
  wp_enqueue_script('tweeter_twitter');
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-tabs');
  wp_register_style('tweeter_css', WP_PLUGIN_URL .'/tweeter-a-twitter-widget/styles.css', false, '1.0.0', 'screen');
  wp_enqueue_style('tweeter_css');
  register_sidebar_widget(__('Tweeter!'), 'widget_tweeter');   
}
add_action("plugins_loaded", "tweeter_init");
?>