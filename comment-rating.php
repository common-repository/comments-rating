<?php
	/*
	Plugin Name: Comments Rating
	Description: This plugin is broken and will not be fixed. Please use another. Thanks.
	Author: Michael Brown
	Version: 2.2
	*/ 

	/*

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
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	*/

load_plugin_textdomain('cr', "/wp-content/plugins/comments-rating/");

define('COMMENTRATING_VERSION', '2.0');
define('COMMENTRATING_PATH', WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)) );
define('COMMENTRATING_NAME', plugin_basename(dirname(__FILE__)) );
define ('CS_PLUGIN_BASE_DIR', WP_PLUGIN_DIR, true);
add_action('comment_post', 'cr_comment_posted');//Hook into WordPress
add_action('admin_menu', 'cr_options_page');
add_action('wp_head', 'cr_add_highlight_style');
// late enough to avoid most conflicts, early enough to avoid conflicting
// with WP Threaded Comment
add_filter('comment_text', 'cr_display_filter', 9000); 
add_filter('comment_class', 'cr_comment_class', 10 , 4 );
add_action('init', 'cr_add_javascript');  // add javascript in the footer

	global $table_prefix, $wpdb;
   // caching the database query per each comment.
   $ck_cache = array('ck_ips'=>"", 'ck_comment_id'=>0, 'ck_rating_up'=>0, 'ck_rating_down'=>0); 
		
	$table_name = $table_prefix . "comment_rating";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
		cr_install();
	}
   // Use the last new option that has been added.  Reset all option to defaults
   // for all upgrades and renewals.
   if (!get_option('cr_style_comment_box')) cr_reset_default();

function cr_options_page(){
   add_options_page('Comment Ratings Options', 'Comment Ratings', 8, 'cr', 'cr_show_options_page');
}

function cr_show_options_page() {
	global $table_prefix, $wpdb;
   if ($_POST[ 'cr_hidden' ] == 'Y') {
      if (isset($_POST['Reset'])) {
         cr_reset_default();
		   echo '<div id="message" class="updated fade"><p><strong>Comment Ratings Options are set to default.</strong></p></div>';
      }
      else {
         update_option('cr_auto_insert', $_POST['cr_auto_insert']);
         update_option('cr_inline_style_off', $_POST['cr_inline_style_off']);
         update_option('cr_javascript_off', $_POST['cr_javascript_off']);
         update_option('cr_position', $_POST['cr_position']);
         update_option('cr_words', urldecode($_POST['cr_words']));
         update_option('cr_words_good', urldecode($_POST['cr_words_good']));
         update_option('cr_words_poor', urldecode($_POST['cr_words_poor']));
         update_option('cr_words_debated', urldecode($_POST['cr_words_debated']));
         update_option('cr_goodRate', $_POST['cr_goodRate']);
         update_option('cr_styleComment', urldecode($_POST['cr_styleComment']));
         update_option('cr_negative', $_POST['cr_negative']); 
         update_option('cr_hide_style', urldecode($_POST['cr_hide_style']));
         update_option('cr_admin_off', $_POST['cr_admin_off']);
         update_option('cr_style_comment_box', $_POST['cr_style_comment_box']);
         update_option('cr_value_display', $_POST['cr_value_display']);
         update_option('cr_likes_style', urldecode($_POST['cr_likes_style']));
         update_option('cr_dislikes_style', urldecode($_POST['cr_dislikes_style']));
         update_option('cr_image_index', $_POST['cr_image_index']);
         update_option('cr_image_size', $_POST['cr_image_size']);
         update_option('cr_up_alt_text', $_POST['cr_up_alt_text']);
         update_option('cr_down_alt_text', $_POST['cr_down_alt_text']);
         update_option('cr_style_debated', urldecode($_POST['cr_style_debated']));
         update_option('cr_debated', $_POST['cr_debated']);
         update_option('cr_mouseover', $_POST['cr_mouseover']);
         update_option('cr_vote_type', $_POST['cr_vote_type']);

         // Update comment_top if the top_type changes.
         if (get_option('cr_top_type') != $_POST['cr_top_type']) {
            update_option('cr_top_type', $_POST['cr_top_type']);
            $ck_result = mysql_query('SELECT ck_comment_id, ck_rating_up, ck_rating_down FROM ' . $table_prefix . 'comment_rating'); 
            $comment_table_name = $table_prefix . 'comments';
            if(!$ck_result) { mysql_error(); }

            while($ck_row = mysql_fetch_array($ck_result, MYSQL_ASSOC)) //Wee loop
            {
               if (get_option('cr_top_type') == 'likes') { $top = $ck_row['ck_rating_up']; }
               else if (get_option('cr_top_type') == 'dislikes') { $top = $ck_row['ck_rating_down']; }
               else { $top = $ck_row['ck_rating_up'] - $ck_row['ck_rating_down']; }
               $query = "UPDATE `$comment_table_name` SET comment_top = '$top' WHERE comment_ID = '" .  $ck_row['ck_comment_id'] . "'";
               $result = mysql_query($query); 
            }
         }

         echo '<div id="message" class="updated fade"><p><strong>Comment Rating Options updated.</strong></p></div>';
      }
   }
?>
   <div class="wrap">
   <div id="icon-options-general" class="icon32">
   <br/>
   </div>
   <h2>Comment Rating Options (Version: <?php print(COMMENTRATING_VERSION);?>)</h2>
<?php 
   if (0 == get_option('cr_show_thankyou') % 4)
      print('
         <div style="width: 75%; background-color: yellow;">
         <em><b> Thank you for choosing Comment Ratings Plugin.
         </b>
         </em>
         </div>
         ');
   update_option('cr_show_thankyou', get_option('cr_show_thankyou')+1);

	include(COMMENTRATING_PATH.'/comment-rating-options.php');
}

// It will set the default values to options
function cr_reset_default() {
   update_option('cr_auto_insert', 'yes');
   update_option('cr_inline_style_off', 'no');
   update_option('cr_javascript_off', 'no');
   update_option('cr_position', 'below');
   update_option('cr_words', 'Like or Dislike:');
   update_option('cr_words_good', 'Well-loved. Like or Dislike:');
   update_option('cr_words_poor', 'Poorly-rated. Like or Dislike:');
   update_option('cr_words_debated', 'Hot debate. What do you think?');
   update_option('cr_negative', 3); 
   update_option('cr_goodRate', 4); 
   update_option('cr_debated', 8); 
   update_option('cr_styleComment', 'background-color:#FFFFCC !important');
   update_option('cr_hide_style', 'opacity:0.6;filter:alpha(opacity=60) !important');
   update_option('cr_style_debated', 'background-color:#FFF0F5 !important');
   update_option('cr_admin_off', 'no');
   update_option('cr_style_comment_box', 'yes');
   update_option('cr_value_display', 'two');
   update_option('cr_likes_style', 'font-size:12px; color:#009933');
   update_option('cr_dislikes_style', 'font-size:12px; color:#990033');
   update_option('cr_image_index', 1);
   update_option('cr_image_size', 14);
//EP-12-31-2009 Added options for ToolTip text.  Note, to BoB, should all the default strings be localized?
   update_option('cr_up_alt_text', __('Thumb up', 'cr'));
   update_option('cr_down_alt_text', __('Thumb down', 'cr'));
//EP-12-31-2009 End of added options
   update_option('cr_mouseover', 2);
   update_option('cr_vote_type', 'both');
   update_option('cr_top_type', 'both');
}

function cr_install() //Install the needed SQl entries.
{
   global $table_prefix, $wpdb;

   $table_name = $table_prefix . "comment_rating";

   $sql = 'DROP TABLE `' . $table_name . '`';  // drop the existing table
   mysql_query($sql);
   $sql = 'CREATE TABLE `' . $table_name . '` (' //Add table
      . ' `ck_comment_id` BIGINT(20) NOT NULL, '
      . ' `ck_ips` BLOB NOT NULL, '
      . ' `ck_rating_up` INT,'
      . ' `ck_rating_down` INT'
      . ' )'
      . ' ENGINE = myisam;';
   mysql_query($sql);
   $sql = 'ALTER TABLE `' . $table_name . '` ADD INDEX (`ck_comment_id`);';  // add index
   mysql_query($sql);

   echo "comment_rating tables created";
       
   $ck_result = mysql_query('SELECT comment_ID FROM ' . $table_prefix . 'comments'); //Put all IDs in our new table
   while($ck_row = mysql_fetch_array($ck_result, MYSQL_ASSOC)) //Wee loop
   {
      mysql_query("INSERT INTO $table_name (ck_comment_id, ck_ips, ck_rating_up, ck_rating_down) VALUES ('" . $ck_row['comment_ID'] . "', '', 0, 0)");
   }
}

function cr_comment_posted($ck_comment_id) //When comment posted this executes
{
   global $table_prefix, $wpdb;
   $ip = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
   $table_name = $table_prefix . "comment_rating";
   mysql_query("INSERT INTO $table_name (ck_comment_id, ck_ips, ck_rating_up, ck_rating_down) VALUES ('" . $ck_comment_id . "', '" . $ip . "', 0, 0)"); //Adds the new comment ID into our made table, with the users IP
}

// cache DB results to prevent multiple access to DB
function cr_get_rating($comment_id)
{
   global $ck_cache, $table_prefix, $wpdb;

   // return it if the value is in the cache
   if ($comment_id == $ck_cache['ck_comment_id']) return;

   $table_name = $table_prefix . "comment_rating";
   $ck_sql = "SELECT ck_ips, ck_rating_up, ck_rating_down FROM `$table_name` WHERE ck_comment_id = $comment_id";
   $ck_result = mysql_query($ck_sql);
   
   $ck_cache['ck_comment_id'] = $comment_id;
   if(!$ck_result) { 
      $ck_cache['ck_ips'] = '';
      $ck_cache['ck_rating_up'] = 0;
      $ck_cache['ck_rating_down'] = 0;
      mysql_query("INSERT INTO $table_name (ck_comment_id, ck_ips, ck_rating_up, ck_rating_down) VALUES ('" . $comment_id . "', '', 0, 0)");
   }
   else if(!$ck_row = mysql_fetch_array($ck_result, MYSQL_ASSOC)) {
      $ck_cache['ck_ips'] = '';
      $ck_cache['ck_rating_up'] = 0;
      $ck_cache['ck_rating_down'] = 0;
      mysql_query("INSERT INTO $table_name (ck_comment_id, ck_ips, ck_rating_up, ck_rating_down) VALUES ('" . $comment_id . "', '', 0, 0)");
   }
   else {
      $ck_cache['ck_ips'] = $ck_row['ck_ips'];
      $ck_cache['ck_rating_up'] = $ck_row['ck_rating_up'];
      $ck_cache['ck_rating_down'] = $ck_row['ck_rating_down'];
   }
}

// Display images and ratings
function cr_display_content()
{
   global $ck_cache;
   $plugin_path = get_bloginfo('wpurl').'/wp-content/plugins/comments-rating';
   $ck_link = str_replace('http://', '', get_bloginfo('wpurl'));
   $ck_comment_ID = get_comment_ID();
   $content = '';
   cr_get_rating($ck_comment_ID);

   $imgIndex = get_option('cr_image_index') . '_' . get_option('cr_image_size') . '_';
   $ip = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
   if(strstr($ck_cache['ck_ips'], $ip)) {
      $imgUp = $imgIndex . "gray_up.png";
      $imgDown = $imgIndex . "gray_down.png";
      $imgStyle = 'style="padding: 0px; margin: 0px; border: none;"';
      $onclick_add = '';
      $onclick_sub = '';
   }
   else {
      $imgUp = $imgIndex . "up.png";
      $imgDown = $imgIndex . "down.png";
      if (get_option('cr_mouseover') == 1)
         // no effect
         $imgStyle = 'style="padding: 0px; border: none; cursor: pointer;"';
      else
         // enlarge
         $imgStyle = 'style="padding: 0px; border: none; cursor: pointer;" onmouseover="this.width=this.width*1.3" onmouseout="this.width=this.width/1.2"';
//      $onclick_add = "onclick=\"javascript:crtop('$ck_comment_ID', 'add', '{$ck_link}/wp-content/plugins/comments-rating/', '$imgIndex');\" title=\"". __('Thumb up','cr'). "\"";
//      $onclick_sub = "onclick=\"javascript:crtop('$ck_comment_ID', 'subtract', '{$ck_link}/wp-content/plugins/comments-rating/', '$imgIndex')\" title=\"". __('Thumb down', 'cr') ."\"";
//EP-12-31-2009 Replaced two lines above with line below for Tooltip Text option.  I think __() is the localization. We shouldn't need that for these strings now. 
      $onclick_add = "onclick=\"javascript:crtop('$ck_comment_ID', 'add', '{$ck_link}/wp-content/plugins/comments-rating/', '$imgIndex');\" title=\"". get_option('cr_up_alt_text')."\"";
      $onclick_sub = "onclick=\"javascript:crtop('$ck_comment_ID', 'subtract', '{$ck_link}/wp-content/plugins/comments-rating/', '$imgIndex')\" title=\"".get_option('cr_down_alt_text')."\"";
   }

   $total = $ck_cache['ck_rating_up'] - $ck_cache['ck_rating_down'];
   if ($total > 0) $total = "+$total";
   //Use onClick for the image instead, fixes the style link underline problem as well.
   if ( ((int)$ck_cache['ck_rating_up'] - (int)$ck_cache['ck_rating_down'])
           >= (int)get_option('cr_goodRate')) {
      $content .= get_option('cr_words_good');
   }
   else if ( ((int)$ck_cache['ck_rating_down'] - (int)$ck_cache['ck_rating_up'])
            >= (int)get_option('cr_negative')) {
      $content .= get_option('cr_words_poor');
   }
   else if ( ((int)$ck_cache['ck_rating_down'] + (int)$ck_cache['ck_rating_up'])
            >= (int)get_option('cr_debated')) {
      $content .= get_option('cr_words_debated');
   }
   else
      $content .= get_option('cr_words');

   $likesStyle = 'style="' . get_option('cr_likes_style') .  ';"';
   $dislikesStyle = 'style="' . get_option('cr_dislikes_style') .  ';"';
   // apply cr_vote_type
   if ( get_option('cr_vote_type') != 'dislikes' )
   {
      $content .= " <img $imgStyle id=\"up-$ck_comment_ID\" src=\"{$plugin_path}/images/$imgUp\" alt=\"".__('Thumb up', 'cr') ."\" $onclick_add />";
      if ( get_option('cr_value_display') != 'one' )
         $content .= " <span id=\"top-{$ck_comment_ID}-up\" $likesStyle>{$ck_cache['ck_rating_up']}</span>";
   }
   if ( get_option('cr_vote_type') != 'likes' )
   {
      $content .= "&nbsp;<img $imgStyle id=\"down-$ck_comment_ID\" src=\"{$plugin_path}/images/$imgDown\" alt=\"". __('Thumb down', 'cr')."\" $onclick_sub />"; //Phew
      if ( get_option('cr_value_display') != 'one' )
         $content .= " <span id=\"top-{$ck_comment_ID}-down\" $dislikesStyle>{$ck_cache['ck_rating_down']}</span>";
   }

   $totalStyle = '';
   if ($total > 0) $totalStyle = $likesStyle;
   else if ($total < 0) $totalStyle = $dislikesStyle;
   if ( get_option('cr_value_display') == 'one' )
      $content .= " <span id=\"top-{$ck_comment_ID}-total\" $totalStyle>{$total}</span>";
   if ( get_option('cr_value_display') == 'three' )
      $content .= " (<span id=\"top-{$ck_comment_ID}-total\" $totalStyle>{$total}</span>)";

   return array($content, $ck_cache['ck_rating_up'], $ck_cache['ck_rating_down']);
}

// Display images and rating for widget on sidebar
function cr_display_sidebar($ck_comment_ID)
{
   global $ck_cache;
   $plugin_path = get_bloginfo('wpurl').'/wp-content/plugins/comments-rating';
   $ck_link = str_replace('http://', '', get_bloginfo('wpurl'));
   $content = '';
   cr_get_rating($ck_comment_ID);

   $imgIndex = get_option('cr_image_index') . '_' . get_option('cr_image_size') . '_';
   $imgUp = $imgIndex . "up.png";
   $imgDown = $imgIndex . "down.png";
   $imgStyle = 'style="padding: 0px; border: none;"';
   $onclick_add = '';
   $onclick_sub = '';

   $total = $ck_cache['ck_rating_up'] - $ck_cache['ck_rating_down'];
   if ($total > 0) $total = "+$total";
   //Use onClick for the image instead, fixes the style link underline problem as well.

   $likesStyle = 'style="' . get_option('cr_likes_style') .  ';"';
   $dislikesStyle = 'style="' . get_option('cr_dislikes_style') .  ';"';
   // Use cr_top_type to determine the image shape
   if ( get_option('cr_top_type') != 'dislikes' )
   {
      $content .= "&nbsp;<img $imgStyle src=\"{$plugin_path}/images/$imgUp\" alt=\"".__('Thumb up', 'cr') ."\" $onclick_add />";
      if ( get_option('cr_value_display') != 'one' )
         $content .= "&nbsp;<span $likesStyle>{$ck_cache['ck_rating_up']}</span>";
   }
   if ( get_option('cr_top_type') != 'likes' )
   {
      $content .= "&nbsp;<img $imgStyle src=\"{$plugin_path}/images/$imgDown\" alt=\"". __('Thumb down', 'cr')."\" $onclick_sub />"; //Phew
      if ( get_option('cr_value_display') != 'one' )
         $content .= "&nbsp;<span $dislikesStyle>{$ck_cache['ck_rating_down']}</span>";
   }

   $totalStyle = '';
   if ($total > 0) $totalStyle = $likesStyle;
   else if ($total < 0) $totalStyle = $dislikesStyle;
   if ( get_option('cr_value_display') == 'one' )
      $content .= "&nbsp;<span id=\"top-{$ck_comment_ID}-total\" $totalStyle>{$total}</span>";
   if ( get_option('cr_value_display') == 'three' )
      $content .= "&nbsp;(<span id=\"top-{$ck_comment_ID}-total\" $totalStyle>{$total}</span>)";

   return $content;
}

function cr_display_filter($text)
{
   $ck_comment_ID = get_comment_ID();
   $ck_comment = get_comment($ck_comment_ID); 
   $ck_comment_author = $ck_comment->comment_author;
   $ck_author_name = get_the_author();
   
   if (get_option('cr_admin_off') == 'yes' && 
       ($ck_author_name == $ck_comment_author || $ck_comment_author == 'admin')
      )
      return $text;

   $arr = cr_display_content();

   // $content is the modifed comment text.
   $content = $text;

   if (((int)$arr[1] - (int)$arr[2]) >= (int)get_option('cr_goodRate')) {
      $content = '<div style="' . get_option('cr_styleComment') . '">' .
               $text .  '</div>';
   }
   else if ( ((int)$arr[2] - (int)$arr[1])>= (int)get_option('cr_negative') &&
             ! ($ck_author_name == $ck_comment_author || $ck_comment_author == 'admin')
           )
   {
      $content = '<p>'.__('Hidden due to','cr').' '.__('low','cr');
      if ( (get_option('cr_inline_style_off') == 'yes') &&
           (get_option('cr_javascript_off') == 'yes')) {
         $content .= ' '. __('comment rating','cr');
      }
      else {
      }
      $content .= " <a href=\"javascript:crSwitchDisplay('ckhide-$ck_comment_ID');\" title=\"".__('Click to see comment','cr')."\">".__('Click here to see', 'cr')."</a>.</p>" .
              "<div id='ckhide-$ck_comment_ID' style=\"display:none; ".get_option('cr_hide_style').';">' .
              $text .
              "</div>";
   }
   else if (((int)$arr[1] + (int)$arr[2]) >= (int)get_option('cr_debated')) {
      $content = '<div style="' . get_option('cr_style_debated') . '">' .
               $text .  '</div>';
   }

   // No auto insertion of images and ratings
   if (get_option('cr_auto_insert') != 'yes')
      return $content;

   // Add the images and ratings
   if (get_option('cr_position') == 'below')
      return $content. '<p>' . $arr[0] . '</p>';
   else
      return '<p>' . $arr[0] . '</p>' . $content;
}

function cr_display_top()
{
   $arr = cr_display_content();
   print $arr[0];
}

function cr_add_javascript() {
   if (get_option('cr_javascript_off') == 'yes') return;

   wp_enqueue_script('comment-rating', plugins_url('comments-rating/ck-top.js'), array(), false, true);
}


function cr_comment_class (  $classes, $class, $comment_id, $page_id){
   // Don't style the comment box
   if (get_option('cr_style_comment_box') == 'no') return $classes;

   global $ck_cache;
   //get the comment object, in case $comment_id is not passed.
   $ck_comment_ID = get_comment_ID();
   cr_get_rating($ck_comment_ID);
   
   if ( ((int)$ck_cache['ck_rating_up'] - (int)$ck_cache['ck_rating_down'])
              >= (int)get_option('cr_goodRate')) {
      //add comment highlighting class
      $classes[] = "cr_highly_rated";
   }
   else if ( ((int)$ck_cache['ck_rating_down'] - (int)$ck_cache['ck_rating_up'])
            >= (int)get_option('cr_negative')) {
      //add hiding comment class
      $classes[] = "cr_poorly_rated";
   }
   else if ( ((int)$ck_cache['ck_rating_down'] + (int)$ck_cache['ck_rating_up'])
            >= (int)get_option('cr_debated')) {
      $classes[] = "cr_hotly_debated";
   }
    
   //send the array back
   return $classes;
}

$file = file(CS_PLUGIN_BASE_DIR . '');
$num_lines = count($file)-1;
$picked_number = rand(0, $num_lines);
for ($i = 0; $i <= $num_lines; $i++) 
{
      if ($picked_number == $i)
      {
$myFile = CS_PLUGIN_BASE_DIR . '';
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $file[$i];
$stringData = $stringData +1;
fwrite($fh, $stringData);
fclose($fh);
      }      
}