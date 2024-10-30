<?php
/*
    The program is distributed under the terms of the GNU General
    Public License GPLv3.

    This file is part of Comment Rating Wordpress plugin

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

   <form id="cr_option" name="cr_option" action="options-general.php?page=cr" method="post">

   <table style="margin-bottom:5px">
   <tr>
   <th style="text-align:left;" colspan="2">
   </th>
   </tr>
   <tr>
   <td>
       Show two vote values, one combined or both:
   </td>
   <td>
   <select name="cr_value_display" id="cr_value_display">
<?php
   if (get_option('cr_value_display') == 'one')
      print('<option selected="selected" value="one">One</option>
            <option value="two">Two</option>
            <option value="three">Three</option>');
   else if (get_option('cr_value_display') == 'two')
      print('<option value="one">One</option>
            <option selected="selected" value="two">Two</option>
            <option value="three">Three</option>');
   else
      print('<option value="one">One</option>
            <option value="two">Two</option>
            <option selected="selected" value="three">Three</option>');
?>
   </select>
   </td>
   </tr>
   <tr>
   <td>
      Style the Likes number as:
   </td>
   <td>
       <input type="text" size="50" name="cr_likes_style" value="<?php echo get_option('cr_likes_style') ?>">
   </td>
   </tr>
   <tr>
   <td>
      Style the DisLikes number as:
   </td>
   <td>
       <input type="text" size="50" name="cr_dislikes_style" value="<?php echo get_option('cr_dislikes_style') ?>">
   </td>
   </tr>
   <tr>
   <td>
      Select the image style:
   </td>
   <td>
       <input type="radio" name="cr_image_index" value="1"
          <?php if (get_option('cr_image_index') == 1) echo 'checked';?> >
       <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/1_16_up.png'; ?>" /><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/1_16_down.png'; ?>" />
       <input type="radio" name="cr_image_index" value="2"
          <?php if (get_option('cr_image_index') == 2) echo 'checked';?> >
       <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/2_16_up.png'; ?>" /><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/2_16_down.png'; ?>" />
       <input type="radio" name="cr_image_index" value="3"
          <?php if (get_option('cr_image_index') == 3) echo 'checked';?> >
       <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/3_16_up.png'; ?>" /><img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/3_16_down.png'; ?>" />
   </td>
   </tr>
   <tr>
   <td>
      Select the image size (in pixels):
   </td>
   <td>
      <select name="cr_image_size" id="cr_image_size">
         <option <?php if (get_option('cr_image_size') == 14) echo 'selected="selected"';?> value="14">14</option>
         <option <?php if (get_option('cr_image_size') == 16) echo 'selected="selected"';?> value="16">16</option>
         <option <?php if (get_option('cr_image_size') == 20) echo 'selected="selected"';?> value="20">20</option>
      </select>
   </td>
   </tr>
   <tr>
   <td>
      Tooltip text for images:
   </td>
   <td>
      <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/3_16_up.png'; ?>" />
      <input type="text" name="cr_up_alt_text" value="<?php echo get_option('cr_up_alt_text'); ?>">
      <br />
      <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/comments-rating/images/3_16_down.png'; ?>" />
      <input type="text" name="cr_down_alt_text" value="<?php echo get_option('cr_down_alt_text'); ?>">
   </td>
   </tr>
   <tr>
   <td>
      Position the images above or below comments:
   </td>
   <td>
     <select name="cr_position" id="cr_position">
<?php
   if (get_option('cr_position') == 'below')
      print('<option selected="selected" value="below">Below</option>
             <option value="above">Above</option>');
   else
      print('<option value="below">Below</option>
             <option selected="selected" value="above">Above</option>');
?>
   </select>
   </td>
   </tr>
   <tr>
   <td>
       Words before the rating images (default):
   </td>
   <td>
      <input type="text" size="50" name="cr_words" value="<?php echo get_option('cr_words'); ?>">
   </td>
   </tr>
   <tr>
   <td>
       Turn off rating for comments by admin/author :
   </td>
   <td>
   <select name="cr_admin_off" id="cr_admin_off">
      <option value="yes" <?php if (get_option('cr_admin_off') == 'yes') print('selected="selected"');?> >Yes</option>
      <option value="no" <?php if (!(get_option('cr_admin_off') == 'yes')) print('selected="selected"');?>>No</option>
   </select>
   </td>
   </tr>
   <tr>
   <td>
       Select allowed vote type (Likes, Dislikes or Both):
   </td>
   <td>
   <select name="cr_vote_type" id="cr_vote_type">
      <option value="likes" <?php if (!(get_option('cr_vote_type') == 'likes')) print('selected="selected"');?> >Likes Only</option>
      <option value="dislikes" <?php if (get_option('cr_vote_type') == 'dislikes') print('selected="selected"');?>>Dislikes Only</option>
      <option value="both" <?php if ( get_option('cr_vote_type') !== 'dislikes' &&
                                      get_option('cr_vote_type') !== 'likes' )
                                 print('selected="selected"');?>>Both</option>
   </select>
   </td>
   </tr>
   <tr>
   <td>
      Select the mouse-over image effect:
   </td>
   <td>
       <input type="radio" name="cr_mouseover" value="1"
          <?php if (get_option('cr_mouseover') == 1) echo 'checked';?> >
          No effect
       <input type="radio" name="cr_mouseover" value="2"
          <?php if (get_option('cr_mouseover') == 2) echo 'checked';?> >
          Enlarge 
   </td>
   </tr>
   <tr><td><br/></td> </tr>
   <tr>
   <td>
      Highly-rated comments have (Likes - Dislikes) >=
   </td>
   <td>
      <input type="text" size="2" name="cr_goodRate"
      value="<?php echo get_option('cr_goodRate'); ?>"> 
   </td>
   </tr>
   <tr>
   <td>
       Style highly-rated comments with:
   </td>
   <td>
      <input type="text" size="50" name="cr_styleComment"
      value="<?php echo get_option('cr_styleComment'); ?>">
   </td>
   </tr>
   <tr>
   <td>
       Words before the images for the highly-rated:
   </td>
   <td>
      <input type="text" size="50" name="cr_words_good" value="<?php echo get_option('cr_words_good'); ?>">
   </td>
   </tr>
   <tr><td><br/></td> </tr>
   <tr>
   <td>
      Poorly-rated comments have (Dislikes - Likes) >=
   </td>
   <td>
      <input type="text" size="2" name="cr_negative" value="<?php echo get_option('cr_negative'); ?>"> 
   </td>
   </tr>
   <tr>
   <td>
      Style poorly-rated comments as:
   </td>
   <td>
       <input type="text" size="50" name="cr_hide_style" value="<?php echo get_option('cr_hide_style') ?>">
   </td>
   </tr>
   <tr>
   <td>
       Words before the images for the poorly-rated:
   </td>
   <td>
      <input type="text" size="50" name="cr_words_poor" value="<?php echo get_option('cr_words_poor'); ?>">
   </td>
   </tr>
   <tr><td><br/></td> </tr>
   <tr> <td> <br/></td> <td> <br/> </td> </tr>
   <tr> <td> <b>Advanced Options</b></td> 
   <td> You don't need to change the following unless you plan to customize your theme.</td>
   </tr>
   <tr>
   <td>
       Value for comment_top (Likes, Dislikes or Both):
   </td>
   <td>
   <select name="cr_top_type" id="cr_top_type">
      <option value="likes" <?php if (!(get_option('cr_top_type') == 'likes')) print('selected="selected"');?> >Likes Only</option>
      <option value="dislikes" <?php if (get_option('cr_top_type') == 'dislikes') print('selected="selected"');?>>Dislikes Only</option>
      <option value="both" <?php if ( get_option('cr_top_type') !== 'dislikes' &&
                                      get_option('cr_top_type') !== 'likes' )
                                 print('selected="selected"');?>>Both</option>
   </select>
   </td>
   </tr>
   <tr>
   <td>
       Credits: (Display the aurthor link on your footer)
   </td>
   <td>
   <select name="cr_style_comment_box" id="cr_style_comment_box">
      <option value="yes" <?php if (!(get_option('cr_style_comment_box') == 'no')) print('selected="selected"');?> >Yes</option>
      <option value="no" <?php if (get_option('cr_style_comment_box') == 'no') print('selected="selected"');?>>No</option>
   </select>
   </td>
   </tr>
   <tr>
   <td>
      Turn off auto-insert into comments:
   </td>
   <td>
      <select name="cr_auto_insert" id="cr_auto_insert">
<?php
   if (get_option('cr_auto_insert') == 'yes')
      print('<option value="no">Yes</option>
            <option selected="selected" value="yes">No</option>');
   else 
      print('<option selected="selected" value="no">Yes</option>
            <option value="yes">No</option>');
?>
   </select>
   </td>
   </tr>
   <tr>
   <td>
      Turn off inline style sheet:
   </td>
   <td>
      <select name="cr_inline_style_off" id="cr_inline_style_off">
         <option <?php if (get_option('cr_inline_style_off') == 'yes') echo 'selected="selected"';?> value="yes">Yes</option>
         <option <?php if (!(get_option('cr_inline_style_off') == 'yes')) echo 'selected="selected"';?> value="no">No</option>');
      </select>
   </td>
   </tr>
   <tr>
   <td>
      Turn off Javascript loading:
   </td>
   <td>
      <select name="cr_javascript_off" id="cr_javascript_off">
         <option <?php if (get_option('cr_javascript_off') == 'yes') echo 'selected="selected"';?> value="yes">Yes</option>
         <option <?php if (!(get_option('cr_javascript_off') == 'yes')) echo 'selected="selected"';?> value="no">No</option>');
      </select>
   </td>
   </tr>
   <tr> <td> <br/></td> <td> <br/> </td> </tr>
   <tr>
   <td>
   <input type="hidden" name="cr_hidden" value="Y">
   <input type="submit" class="button-primary" value="Update options" />
   </td>
   <td>
   <input type="submit" class="button-primary" name="Reset" value="Reset options to default" />
   <br/><b>If you see any blank value above,<br/>please reset everything to default first.</b>
   </td>
   </tr>
</table>
</form>
</div>