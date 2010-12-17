<?php
// Share Widget
//
// Copyright (c) 2010 Share Widget.
// http://www.share-widget.com
//
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// *****************************************************************


/**
* Plugin Name: Share-Widget
* Plugin URI: http://www.share-widget.com
* Description: Add the share-widget button on the footer of your post. Users can share your content through the most popular social networking and bookmarking site.
* Version: 1.0.1
*
* Author: Register.it
* Author URI: http://www.share-widget.com
*/

if (!defined('SHAREWIDGET_INIT')) define('SHAREWIDGET_INIT', 1);
else return;

load_plugin_textdomain('share-widget');

$sharewidget_mode = array();
$sharewidget_mode['normal-ico'] = array(
		'img'=>'<img src="http://img.adv.dadapro.net/myshare/shared/buttons/share001ico.gif" width="20" height="20" alt="Share" style="border:0;vertical-align:middle;margin:0 5px 0 0;"/> SHARE', 
		'opt' => '',
		'txt' => '');
$sharewidget_mode['normal-btn'] = array(
		'img'=>'<img src="http://img.adv.dadapro.net/myshare/shared/buttons/share001btn.gif" width="60" height="17" alt="Share" style="border:0"/>', 
		'opt' => '',
		'txt' => '');
$sharewidget_mode['short-ico'] = array(
		'img'=>'<img src="http://img.adv.dadapro.net/myshare/shared/buttons/short001ico.gif" width="20" height="20" alt="Share" style="border:0;vertical-align:middle;margin:0 5px 0 0;"/> SHARE', 
		'opt' => 'var _mysshort=true;',
		'txt' => '(link shorted with X.nu)');
$sharewidget_mode['short-btn'] = array(
		'img'=>'<img src="http://img.adv.dadapro.net/myshare/shared/buttons/short001btn.gif" width="60" height="17" alt="Share" style="border:0"/>', 
		'opt' => 'var _mysshort=true;',
		'txt' => '(link shorted with X.nu)');

/**
* Appends Share-Widget button to post content.
*/
function sharewidget_social_widget($content) {
	global $sharewidget_mode;

	$type = get_option('sharewidget_type');
	if (empty($sharewidget_mode[$type]))
		$type = 'short-btn';
	$sharewidget_img = $sharewidget_mode[$type]['img'];
	$sharewidget_opt = $sharewidget_mode[$type]['opt']; 
   
    if (is_feed()) return $content;
    else if (is_search()) return $content;
    else if (is_page()) return $content;
    else if (is_archive()) return $content;
    else if (is_category()) return $content;

    $link  = get_permalink();
    $title = the_title('', '', false);

    $content .= "\n<!-- Share-Widget Button BEGIN -->\n";
    $content .= <<<EOF
<a href="http://www.share-widget.com/myshare.php5" myshare_id="mys_shareit" myshare_url="$link" myshare_title="$title" onclick="return false;" style="text-decoration:none; color:#0098cc; font-size:11px; line-height:20px;">
$sharewidget_img</a>
<script type="text/javascript">
function _myssw()
{if(typeof(_mysst)=="undefined")
{var ns=document.createElement('script');ns.src="http://www.share-widget.com/content_js2.php5";ns.onload=ns.onreadystatechange=function()
{if(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")
{var _mys_obj=new _mysst();_mys_obj.shareview();}}
document.body.appendChild(ns);}else
{_mys_obj.shareview();}}
if(typeof(_mysst)=="undefined"&&!_myssmw)
{if(window.addEventListener){window.addEventListener('load',_myssw,false);}else if(window.attachEvent){window.attachEvent("onload",_myssw);}else{setTimeout("_myssw()",2000);}}
var _myssmw=true;$sharewidget_opt</script>
EOF;
    $content .= "\n<!-- Share-Widget Button END -->";

    return $content;
}

function sharewidget_admin_menu() {
   add_options_page(__('Share-Widget Options', 'share-widget'), __('Share-Widget', 'share-widget'), 8, __FILE__, 'sharewidget_plugin_options');
}

/**
*	admin plugin options
*/
function sharewidget_plugin_options() {
	global $sharewidget_mode;
	add_option('sharewidget_type');	
	$type = get_option('sharewidget_type');
?>
 <div class="wrap">
	<h2><?php _e('Share-Widget Options', 'share-widget');?></h2>
	<p><?php _e('Select your favourite button. You can choose if you want to share a normal link or a "x.nu" shorted link:', 'share-widget');?></p>
    <?php 
	  if( isset($_POST['op']) && $_POST['op'] =='sw_update_opt') {
	    // Save the posted value in the database
	    update_option('sharewidget_type', $_POST[ 'sharewidget_type' ] );
		$type = get_option('sharewidget_type');
		echo '<div style="padding:10px;border:1px solid #aaa;background-color:#9fde33;text-align:center;display:none;" id="st_updated">';
		_e('Option succesfully updated', 'share-widget');
		echo '</div>';
	}
    ?>
	<form id="sw_share" name="sw_share" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input name="op" type="hidden" value="sw_update_opt" />	
	<?php
		foreach ($sharewidget_mode as $k => $v){
			echo '<div style="margin:4px 0;vertical-align:middle;"><input type="radio" name="sharewidget_type" value="'.$k.'" id="sw-'.$k.'" '.($type == $k ? " checked":"").'>';
			echo '<label for="sw-'.$k.'"><a>'.$v['img'].'</a></label> '.__($v['txt'], 'share-widget').' </div>';
		}
	?>
	<p class="submit">
    	<input type="submit" name="Submit" value="<?php _e('Save Changes', 'share-widget') ?>" />
    </p>
   </form>
 </div>
<?php
}
add_filter('the_content', 'sharewidget_social_widget');
add_filter('admin_menu', 'sharewidget_admin_menu');
?>
