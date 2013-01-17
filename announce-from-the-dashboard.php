<?php
/*
Plugin Name: Announce from the Dashboard
Description: Announcement to the dashboard by User Role.
Plugin URI: http://gqevu6bsiz.chicappa.jp
Version: 1.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/author/admin/
Text Domain: announce-from-the-dashboard
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain('announce-from-the-dashboard', false, basename(dirname(__FILE__)).'/languages');

define ('ANNOUNCE_FROM_THE_DASHBOARD_VER', '1.1');
define ('ANNOUNCE_FROM_THE_DASHBOARD_PLUGIN_NAME', 'Announce from the Dashboard');
define ('ANNOUNCE_FROM_THE_DASHBOARD_MANAGE_URL', admin_url('options-general.php').'?page=announce_from_the_dashboard');
define ('ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME', 'announce_from_the_dashboard');
define ('ANNOUNCE_FROM_THE_DASHBOARD_PLUGIN_DIR', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/');
?>
<?php
function announce_from_the_dashboard_add_menu() {
	// add menu
	add_options_page(__('Dashboard announce setting', 'announce-from-the-dashboard'), __('Announce Dashboard', 'announce-from-the-dashboard'), 'administrator', 'announce_from_the_dashboard', 'announce_from_the_dashboard_setting');

	// plugin links
	add_filter('plugin_action_links', 'announce_from_the_dashboard_plugin_setting', 10, 2);
}



// plugin setup
function announce_from_the_dashboard_plugin_setting($links, $file) {
	if(plugin_basename(__FILE__) == $file) {
		$settings_link = '<a href="'.ANNOUNCE_FROM_THE_DASHBOARD_MANAGE_URL.'">'.__('Settings').'</a>'; 
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_action('admin_menu', 'announce_from_the_dashboard_add_menu');



// setting
function announce_from_the_dashboard_setting() {
	$UPFN = 'sett';
	$Msg = '';

	// get type
	$Displaytype = announce_from_the_dashboard_typeload();

	// get role
	$UserRole = announce_from_the_dashboard_roleload();


	if(isset($_GET["delete"])) {
		// delete
		$id = $_GET["delete"];
		$Data = get_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME);
		unset($Data[$id]);
		update_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME, $Data);
		echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';
	} else if(!empty($_POST[$UPFN])) {
		// update
		if($_POST[$UPFN] == 'Y') {
			unset($_POST[$UPFN]);

			$Update = array();
			if(!empty($_POST["update"])) {
				foreach ($_POST["update"] as $key => $val) {
					$type = '';
					if(!empty($val["type"])) {
						$type = strip_tags($val["type"]);
					} else {
						$type = $Displaytype[0];
					}
	
					$role = array();
					if(!empty($val["role"])) {
						foreach($val["role"] as $tmp) {
							$role[$tmp] = strip_tags($tmp);
						}
					} else {
						$role = $UserRole;
					}

					$Update[$key] = array(
						"title" => strip_tags($val["title"]),
						"content" => $val["content"],
						"type" => $type,
						"role" => $role
					);
				}
			}
			if(!empty($_POST["create"]) && !empty($_POST["create"]["title"]) && !empty($_POST["create"]["content"])) {
				$type = '';
				if(!empty($_POST["create"]["type"])) {
					$type = strip_tags($_POST["create"]["type"]);
				} else {
					$type = $Displaytype[0];
				}

				$role = array();
				if(!empty($_POST["create"]["role"])) {
					foreach($_POST["create"]["role"] as $val) {
						$role[$val] = strip_tags($val);
					}
				} else {
					$role = $UserRole;
				}
				$Update[] = array(
					"title" => strip_tags($_POST["create"]["title"]),
					"content" => $_POST["create"]["content"],
					"type" => $type,
					"role" => $role
				);
			}

			if(!empty($Update)) {
				update_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME, $Update);
				$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';
			}
		}
	}
	
	// get data
	$Data = get_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME);

	// include js css
	$ReadedJs = array('jquery', 'thickbox');
	wp_enqueue_script('announce-from-the-dashboard', ANNOUNCE_FROM_THE_DASHBOARD_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.js', $ReadedJs, ANNOUNCE_FROM_THE_DASHBOARD_VER);
	wp_enqueue_style('thickbox');
	wp_enqueue_style('announce-from-the-dashboard', ANNOUNCE_FROM_THE_DASHBOARD_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), ANNOUNCE_FROM_THE_DASHBOARD_VER);
?>
<div class="wrap">
	<div class="icon32" id="icon-themes"></div>
	<h2><?php _e('Dashboard announce setting', 'announce-from-the-dashboard'); ?></h2>
	<?php echo $Msg; ?>
	<p>&nbsp;</p>

	<form id="announce_from_the_dashboard" method="post" action="<?php echo ANNOUNCE_FROM_THE_DASHBOARD_MANAGE_URL; ?>">
		<input type="hidden" name="<?php echo $UPFN; ?>" value="Y">
		<?php wp_nonce_field(-1, '_wpnonce', false); ?>

		<div id="create">
			<h3><?php _e('Create a new announce to the dashboard.', 'announce-from-the-dashboard'); ?></h3>
			<?php $mode = 'create'; ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="<?php echo $mode; ?>_title"><?php _e('Announce title', 'announce-from-the-dashboard'); ?></label> *</th>
						<td>
							<input type="text" class="regular-text" id="<?php echo $mode; ?>_title" name="<?php echo $mode; ?>[title]">
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $mode; ?>_content"><?php _e('Announce content', 'announce-from-the-dashboard'); ?></label> *</th>
						<td>
							<?php wp_editor("", $mode."_content", array('textarea_name' => $mode.'[content]', 'media_buttons' => false)); ?>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $mode; ?>_type"><?php _e('Announce type', 'announce-from-the-dashboard'); ?></label></th>
						<td>
							<select name="<?php echo $mode; ?>[type]" id="<?php echo $mode; ?>_type">
								<option value="" selected="selected">- <?php _e('Select the type', 'announce-from-the-dashboard'); ?> -</option>
								<?php foreach($Displaytype as $val) : ?>
									<option value="<?php echo $val; ?>"><?php _e($val, 'announce-from-the-dashboard'); ?> (<?php announce_from_the_dashboard_typecolor($val); ?> )</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo $mode; ?>_role"><?php _e('User Roles'); ?></label></th>
						<td>
							<?php foreach($UserRole as $role => $rolename) : ?>
								<label><input type="checkbox" name="<?php echo $mode; ?>[role][<?php echo $role; ?>]" value="<?php echo $role; ?>" /> <?php echo $rolename; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="button" class="button-primary" value="<?php _e('Save'); ?>" />
			</p>
		</div>

		<div id="update">
			<h3><?php _e('List of announce that you created', 'announce-from-the-dashboard'); ?></h3>
			<?php if(!empty($Data)) : ?>
				<?php $mode = 'update'; ?>

				<table cellspacing="0" class="widefat fixed">
					<thead>
						<tr>
							<th class="title"><strong><?php _e('Announce title', 'announce-from-the-dashboard'); ?></strong> / <?php _e('Announce type', 'announce-from-the-dashboard'); ?></th>
							<th class="content"><?php _e('Announce content', 'announce-from-the-dashboard'); ?></th>
							<th class="role"><?php _e('User Roles'); ?></th>
							<th class="operation">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($Data as $key => $content) : ?>
							<tr id="tr_<?php echo $key; ?>" class="<?php echo $content["type"]; ?>">
								<td class="title">
									<input type="text" value="<?php echo strip_tags($content["title"]); ?>" name="<?php echo $mode; ?>[<?php echo $key; ?>][title]">
									<span><strong><?php echo strip_tags($content["title"]); ?></strong></span>
									<?php $val = strip_tags($content["type"]); ?>

									<select name="<?php echo $mode; ?>[<?php echo $key; ?>][type]">
										<?php foreach($Displaytype as $type) : ?>
											<?php $Selected = ''; ?>
											<?php if($type == $val) : ?>
												<?php $Selected = 'selected="selected"'; ?>
											<?php endif; ?>
											<option value="<?php echo $type; ?>" <?php echo $Selected; ?>><?php _e($type, 'announce-from-the-dashboard'); ?>(<?php announce_from_the_dashboard_typecolor($type); ?>)</option>
										<?php endforeach; ?>
									</select>
									<span class="description">
										<?php echo _e(strip_tags($content["type"]), 'announce-from-the-dashboard'); ?>
										(<?php announce_from_the_dashboard_typecolor(strip_tags($content["type"])); ?> )
									</span>
								</td>
								<td class="content">
									<?php wp_editor(stripslashes($content["content"]), $mode.'_'.$key.'_content', array('textarea_name' => $mode.'['.$key.'][content]', 'media_buttons' => false)); ?>
									<span><?php echo stripslashes(esc_html($content["content"])); ?></span>
								</td>
								<td class="role">
									<?php $val = $content["role"]; ?>
									<?php foreach($UserRole as $role => $rolename) : ?>
										<?php $Checked = ''; ?>
										<?php if(array_key_exists($role, $val)) : ?>
											<?php $Checked = 'checked="checked"'; ?>
										<?php endif; ?>
										<label><input type="checkbox" name="<?php echo $mode; ?>[<?php echo $key; ?>][role][<?php echo $role; ?>]" value="<?php echo $role; ?>" <?php echo $Checked; ?> /> <?php echo $rolename; ?></label>
									<?php endforeach; ?>
									<span>
										<?php if(!empty($content["role"])) : ?>
											<ul>
												<?php foreach($content["role"] as $role => $tmp) : ?>
													<li><?php echo $UserRole[strip_tags($role)]; ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</span>
								</td>
								<td class="operation">
									<span>
										<a class="edit" href="javascript:void(0)"><?php _e('Edit'); ?></a>
										&nbsp;|&nbsp;
										<a class="delete" href="<?php echo ANNOUNCE_FROM_THE_DASHBOARD_MANAGE_URL; ?>&delete=<?php echo $key; ?>"><?php _e('Delete'); ?></a>
									</span>
									<p class="submit">
										<input type="button" class="button-primary" value="<?php _e('Save'); ?>" />
									</p>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div id="Confirm">
					<div id="ConfirmSt">
						<p>&nbsp;</p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e('Cancel'); ?></a>
						<a class="button-secondary" id="deletebtn" href=""><?php _e('Continue'); ?></a>
					</div>
				</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	// delete
	$("a.delete").click(function() {
		var $DelUrl = $(this).attr("href");
		var $DelName = $(this).parent().parent().parent().children('td.title').children('span').children('strong').text();
		var $ConfDlg = $("#Confirm #ConfirmSt");
		$ConfDlg.children("a#deletebtn").attr("href", $DelUrl);
		$ConfDlg.children("p").html('<?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ), '' ); ?>');
		$ConfDlg.children("p").children("strong").text($DelName);
		
		tb_show('<?php _e('Confirm Deletion'); ?>', '#TB_inline?height=100&width=240&inlineId=Confirm', '');
		return false;
	});
	
	$("a#cancelbtn").click(function() {
		tb_remove();
	});
});
</script>

			<?php else : ?>

				<p><?php _e('Not created announce.', 'announce-from-the-dashboard'); ?></p>

			<?php endif; ?>
		</div>

	</form>
</div>
<?php
}



// get type
function announce_from_the_dashboard_typeload() {
	$Displaytype = array('normal', 'error', 'updated', 'metabox');

	return $Displaytype;
}



// get role
function announce_from_the_dashboard_roleload() {
	$UserRole = array();
	$editable_roles = get_editable_roles();
	foreach ( $editable_roles as $role => $details ) {
		$UserRole[$role] = translate_user_role($details['name'] );
	}

	return $UserRole;
}



// get color
function announce_from_the_dashboard_typecolor($type) {
	$color = '';
	if($type == 'normal') {
		$color = __('gray', 'announce-from-the-dashboard');
	} else if($type == 'updated') {
		$color = __('yellow', 'announce-from-the-dashboard');
	} else if($type == 'error') {
		$color = __('red', 'announce-from-the-dashboard');
	} else if($type == 'metabox') {
		$color = __('gray', 'announce-from-the-dashboard');
	}
	echo $color;
}


// announce show
function announce_from_the_dashboard_show() {
	$screen = get_current_screen();

	if($screen->base == 'dashboard') {
		// get data
		$Data = get_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME);

		if(!empty($Data)) {
			// get type
			$Displaytype = announce_from_the_dashboard_typeload();
	
			// get role
			$UserRole = announce_from_the_dashboard_roleload();

			wp_enqueue_style('announce-from-the-dashboard', ANNOUNCE_FROM_THE_DASHBOARD_PLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), ANNOUNCE_FROM_THE_DASHBOARD_VER);

			$User = wp_get_current_user();
			$Userroles = $User->roles[0];

			$Msg = '';
			foreach($Data as $key => $an) {
				$type = $an["type"];
				if( !empty( $type ) && $type == 'updated' or $type == 'error' or $type == 'normal' ) {
					if(!empty($an["role"][$Userroles])) {
						$Msg .= '<div class="announce updated '.$an["type"].'"><p><strong>'.strip_tags($an["title"]).'</strong>'.apply_filters('the_content', stripslashes($an["content"])).'</p></div>';
					}
				}
			}

			if(!empty($Msg)) {
				echo $Msg;
			}

		}

	}
}
add_filter('admin_notices', 'announce_from_the_dashboard_show', 99);



// announce show metaxo
function announce_from_the_dashboard_show_metabox() {
	// get data
	$Data = get_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME);

	if(!empty($Data)) {
		// get type
		$Displaytype = announce_from_the_dashboard_typeload();
	
		// get role
		$UserRole = announce_from_the_dashboard_roleload();

		$User = wp_get_current_user();
		$Userroles = $User->roles[0];

		foreach($Data as $key => $an) {
			$type = $an["type"];
			if( !empty( $type ) && $type == 'metabox' ) {
				if(!empty($an["role"][$Userroles])) {
					add_meta_box( 'announce_from_the_dashboard-' . $key , strip_tags($an["title"]) , "nnounce_from_the_dashboard_callback" , 'dashboard' , 'normal' , '' , array( "announce" => $key ) );
				}
			}
		}

	}

}
function nnounce_from_the_dashboard_callback( $post , $metabox ) {
	
	if( !empty( $metabox["args"]["announce"] ) ) {

		// get data
		$Data = get_option(ANNOUNCE_FROM_THE_DASHBOARD_RECORD_NAME);

		if( !empty( $Data[$metabox["args"]["announce"]] ) ) {
			echo apply_filters('the_content', stripslashes($Data[$metabox["args"]["announce"]]["content"]));
		}

	}
}
add_action('wp_dashboard_setup', 'announce_from_the_dashboard_show_metabox');

?>