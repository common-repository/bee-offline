<?php
/*
Plugin Name: Bee Offline
Plugin URI: http://rolies.i-cornershop.com
Description: This plugin can set your wordpress to offline mode if you want to maintenance or change layout of your website. 
Version: 1.4
Author: Rolies Debby
Author URI: http://www.roliesdabee.com
License: GPL
*/

$prefix = 'bee_';

function bee_offline_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('BeeOffline Configuration'), __('BeeOffline'), 'manage_options', 'bee-offline-key-config', 'bee_offline_conf');

}

function bee_offline_conf() {

global $wp_roles, $current_user, $prefix, $blog_id;
      
get_currentuserinfo();

if ( isset($_POST['submit']) ) {

	if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE == true ) {
		update_option($prefix . 'role_conf_' . $blog_id, $_POST['role_name']);
		update_option($prefix . 'offline_set_' . $blog_id, $_POST['offline-set']);
	} else {
		update_option($prefix . 'role_conf', $_POST['role_name']);
		update_option($prefix . 'offline_set', $_POST['offline-set']);
	}
}

if ( !empty($_POST ) ) : ?>

	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>

<?php endif; ?>

<div class="wrap">

	<h2><?php _e('Bee Offline Configuration'); ?></h2>

<div class="narrow">

<form action="" method="post" id="bee-offline-conf" style="margin: auto; width: 400px; ">

	<p><?php echo "For many people editing web is like hell, because sometime you don't now what you want to do but you don't want to display the error messages on your site while you editing or upgrading your site. This plugins give you a room for edit and upgrade your WP while your site display the offline screen.<br><br>While offline you still can check your site with after you are loged in to your account and you have an Administrator access.<br><br>By <a href=\"http://rolies.i-cornershop.com\" target=\"_blank\">Rolies Debby</a>."; ?></p>

<p>

<?php 

echo $user_identity;

if ( $construction == 1 || $_POST['offline-set'] == 1 ) {

	$conf_cur = "1";
	$conf_say = "Yes, Show Under Construction Page";

} else {

	$conf_cur = "0";
	$conf_say = "No, Show My Current Web";	

}

if ( empty($_POST ) ) {
?>

<fieldset style="border:1px #666666; margin-top:50px;">

	<fieldset style="display:block;">
	<label>Offline Site : </label><br><br>
	<select id="offline-set" name="offline-set" style="font-family: 'Trebuchet MS', Arial, Verdana; font-size: 1em;">
		<option value="<?php echo $conf_cur; ?>"><?php echo $conf_say; ?></option>
		<option value=""></option>
		<option value="0">No, Show My Current Web</option>
		<option value="1">Yes, Show Under Construction Page</option>
	</select>
	</fieldset>
	
	<fieldset style="display:block; margin-top:20px">
	<label>Select user role for access site while offline ( except Administrator ) : </label><br><br>
	<?php 
		
		if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE == true ) {
			$role_config = (get_option($prefix . 'role_conf_' . $blog_id)) ? get_option($prefix . 'role_conf_' . $blog_id) : array();
		} else {
			$role_config = (get_option($prefix . 'role_conf')) ? get_option($prefix . 'role_conf') : array();
		}
		$role_select = '<input type="hidden" name="role_name[]" value="administrator" />';
		
		foreach( $wp_roles->role_names as $role => $name ) {
			
		  $name = translate_with_context($name);
		
			if ( $role != 'administrator' ) {

				if ( array_search($role, $role_config) ) {
						$checked = 'checked="checked"';
				} else {
						$checked = '';
				}

			  $role_select .= '<input style="margin:5px;" ' . $checked . ' type="checkbox" name="role_name[]" value="'.$role .'" /><label style="margin:5px;">'. $name . '</label><br />';
	
			}	
		}
		echo $role_select;
	?>
	</fieldset>

	<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>

</fieldset>

<?php } else { ?>

<h3>Your setting have been saved.</h3>

<a href="./plugins.php?page=bee-offline-key-config">Change Your Setting.</a>

<?php } ?>
</form>

</div>

</div>

<?php

}

function redirect() {

	global $current_user, $blog_id, $prefix;

	if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE == true ) {
		$role_config = (get_option($prefix . 'role_conf_' . $blog_id)) ? get_option($prefix . 'role_conf_' . $blog_id) : array();
		$construction = (get_option($prefix . 'offline_set_' . $blog_id)) ? get_option($prefix . 'offline_set_' . $blog_id) : array();
	} else {
		$role_config = (get_option($prefix . 'offline_set')) ? get_option($prefix . 'offline_set') : array();
		$construction = (get_option($prefix . 'role_conf')) ? get_option($prefix . 'role_conf') : array();
	}
      
	get_currentuserinfo();
	
	$user_object = new WP_User($current_user->ID);

	$roles = $user_object->roles;

	$display = false;
	
	foreach( $roles as $cur_role ) {
		//if ( array_search($cur_role, $role_config) ) {
		if ( in_array($cur_role, $role_config) ) {
				$display = true;
		}
	}

	if ( $construction) {
		
		if ( !$current_user->ID || ( $display == false ) ) {
	
			include("./wp-content/plugins/bee-offline/construction.php");
		
			exit;
	
		} else {
		
			echo '<div class="offline-msg" style="width: 100%; padding: 10px; background: #FFCC00; text-align: center;">Your site is under construction mode, you can change this setting at BeeOffline configuration under the plugins section.</div>';
		
		}
	}

}

add_action('admin_menu', 'bee_offline_config_page');
add_action('wp_head', 'redirect');

?>