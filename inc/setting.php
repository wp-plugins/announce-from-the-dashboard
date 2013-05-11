<?php

if( !empty( $_POST["donate_key"] ) ) {
	$this->DonatingCheck();
} elseif( !empty( $_POST["action"] ) or  !empty( $_POST["action2"] ) ) {
	$this->update_delete();
} elseif( !empty( $_POST["data"] ) ) {
	$this->update();
}

$Data = $this->get_data();
$User = wp_get_current_user();
$AllTypes = $this->AllTypes();
$UserRoles = $this->get_user_role();

// include js css
$ReadedJs = array( 'jquery' , 'thickbox' );
wp_enqueue_script( $this->Slug ,  $this->Url . dirname( dirname( plugin_basename( __FILE__ ) ) ) . '.js', $ReadedJs , $this->Ver );
wp_enqueue_style('thickbox');
wp_enqueue_style( $this->Slug , $this->Url . dirname( dirname( plugin_basename( __FILE__ ) ) ) . '.css', array() , $this->Ver );

?>

<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'Announcement settings for Dashboard' , $this->ltd ); ?></h2>
	<?php echo $this->Msg; ?>
	<p>&nbsp;</p>


	<div class="metabox-holder columns-2" id="afd">

		<div id="postbox-container-1" class="postbox-container">

			<form id="afd_form" method="post" action="">
				<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
				<?php wp_nonce_field(); ?>

				<?php $mode = 'create'; ?>
				<div id="<?php echo $mode; ?>">
				
					<h3><?php _e( 'Create a new announce to the dashboard.' , $this->ltd ); ?></h3>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $mode; ?>_title"><?php _e( 'Announce title' , $this->ltd ); ?></label> *</th>
								<td><input type="text" class="regular-text" id="<?php echo $mode; ?>_title" name="data[<?php echo $mode; ?>][title]"></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_content"><?php _e( 'Announce content' , $this->ltd ); ?></label> *</th>
								<td><?php wp_editor( "" , $mode . "_content" , array( 'textarea_name' => 'data[' . $mode . '][content]' , 'media_buttons' => false ) ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_type"><?php _e( 'Announce type' , $this->ltd ); ?></label></th>
								<td>
									<select name="data[<?php echo $mode; ?>][type]" id="<?php echo $mode; ?>_type">
										<option value="" selected="selected">- <?php _e( 'Select the type' , 'announce-from-the-dashboard' ); ?> -</option>
										<?php foreach( $AllTypes as $type ) : ?>
											<option value="<?php echo $type; ?>"><?php _e( $type , $this->ltd ); ?> (<?php echo $this->get_color( $type ); ?> )</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_role"><?php _e( 'User Roles' ); ?></label></th>
								<td>
									<?php foreach( $UserRoles as $role => $rolename ) : ?>
										<label><input type="checkbox" name="data[<?php echo $mode; ?>][role][<?php echo $role; ?>]" value="1" /> <?php echo $rolename; ?></label>
									<?php endforeach; ?>
								</td>
							</tr>
						</tbody>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" name="update" value="<?php _e( 'Save' ); ?>" />
					</p>
		
				</div>
	
			</form>

		</div>

		<div id="postbox-container-2" class="postbox-container">

			<?php $donatedKey = get_option( $this->ltd . '_donated' ); ?>
			<?php if( $donatedKey == $this->DonateKey ) : ?>
				<span class="description"><?php _e( 'Thank you for your donation.' , $this->ltd_p ); ?></span>
			<?php else: ?>

				<div class="stuffbox" id="donationbox">
					<div class="inside">
						<p style="color: #FFFFFF; font-size: 20px;"><?php _e( 'Please donation.' , $this->ltd_p ); ?></p>
						<p style="color: #FFFFFF;"><?php _e( 'You are contented with this plugin?<br />By the laws of Japan, Japan\'s new paypal user can not make a donation button.<br />So i would like you to buy this plugin as the replacement for the donation.' , $this->ltd_p ); ?></p>
						<p>&nbsp;</p>
						<p style="text-align: center;">
							<a href="http://gqevu6bsiz.chicappa.jp/line-break-first-and-end/?utm_source=use_plugin&utm_medium=donate&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" class="button-primary" target="_blank">Line Break First and End</a>
						</p>
						<p>&nbsp;</p>
						<div class="donation_memo">
							<p><strong><?php _e( 'Features' , $this->ltd_p ); ?></strong></p>
							<p><?php _e( 'Line Break First and End plugin is In the visual editor TinyMCE, It is a plugin that will help when you will not be able to enter a line break.' , $this->ltd_p ); ?></p>
						</div>
						<div class="donation_memo">
							<p><strong><?php _e( 'The primary use of donations' , $this->ltd_p ); ?></strong></p>
							<ul>
								<li>- <?php _e( 'Liquidation of time and value' , $this->ltd_p ); ?></li>
								<li>- <?php _e( 'Additional suggestions feature' , $this->ltd_p ); ?></li>
								<li>- <?php _e( 'Maintain motivation' , $this->ltd_p ); ?></li>
								<li>- <?php _e( 'Ensure time as the father of Sunday' , $this->ltd_p ); ?></li>
							</ul>
						</div>

						<form id="donation_form" class="afd_form" method="post" action="">
							<h4 style="color: #FFF;"><?php _e( 'If you have already donated to.' , $this->ltd_p ); ?></h4>
							<p style="color: #FFF;"><?php _e( 'Please enter the \'Donation delete key\' that have been described in the \'Line Break First and End download page\'.' , $this->ltd_p ); ?></p>
							<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
							<?php wp_nonce_field(); ?>
							<label for="donate_key"><span style="color: #FFF; "><?php _e( 'Donation delete key' , $this->ltd_p ); ?></span></label>
							<input type="text" name="donate_key" id="donate_key" value="" class="small-text" />
							<input type="submit" class="button-secondary" name="update" value="<?php _e( 'Submit' ); ?>" />
						</form>

					</div>
				</div>

			<?php endif; ?>

			<?php if( $donatedKey == $this->DonateKey ) : ?>
				<div class="toggle-plugin"><p class="icon"><a href="#"><?php echo esc_html__( 'Collapse' ); ?></a></p></div>
			<?php endif; ?>

			<div class="stuffbox" id="aboutbox">
				<h3><span class="hndle"><?php _e( 'About plugin' , $this->ltd_p ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Version check' , $this->ltd_p ); ?> : 3.4.2 - 3.5.1</p>
					<ul>
						<li><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank"><?php _e( 'Plugin\'s site' , $this->ltd_p ); ?></a></li>
						<li><a href="http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=side&utm_content=utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $this->ltd_p ); ?></a></li>
						<li><a href="http://wordpress.org/support/plugin/announce-from-the-dashboard" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
						<li><a href="http://wordpress.org/support/view/plugin-reviews/announce-from-the-dashboard" target="_blank"><?php _e( 'Reviews' , $this->ltd_p ); ?></a></li>
						<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
						<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
					</ul>
				</div>
			</div>

			<div class="stuffbox" id="usefulbox">
				<h3><span class="hndle"><?php _e( 'Useful plugins' , $this->ltd_p ); ?></span></h3>
				<div class="inside">
					<p><strong><a href="http://wpadminuicustomize.com/?utm_source=use_plugin&utm_medium=side&utm_content=utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
					<p class="description"><?php _e( 'Customize a variety of screen management.' , $this->ltd_p ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/post-lists-view-custom/" target="_blank">Post Lists View Custom</a></strong></p>
					<p class="description"><?php _e( 'Customize the list of the post and page. custom post type page, too. You can customize the column display items freely.' , $this->ltd_p ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/custom-options-plus-post-in/" target="_blank">Custom Options Plus Post in</a></strong></p>
					<p class="description"><?php _e( 'The plugin that allows you to add the value of the options. Option value that you have created, can be used in addition to the template tag, Short code can be used in the body of the article.' , $this->ltd_p ); ?></p>
					<p>&nbsp;</p>
				</div>
			</div>
		
		</div>

		<div class="clear"></div>

	</div>

	<div class="metabox-holder columns-1" id="afd-lists">

		<div id="postbox-container-1" class="postbox-container">

			<?php if( empty( $Data ) ) : ?>
	
				<p><?php _e( 'Not created announce.' , $this->ltd ); ?></p>
	
			<?php else : ?>

				<?php $mode = 'update'; ?>
				<div id="<?php echo $mode; ?>">

					<form id="afd_form" method="post" action="">
						<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
						<?php wp_nonce_field(); ?>
						<h3><?php _e( 'List of announce that you created' , $this->ltd ); ?></h3>

						<div class="tablenav top">
							<select name="action">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="submit" class="button-secondary action" value="<?php _e( 'Apply' ); ?>" />
						</div>
						<table cellspacing="0" class="widefat fixed">
							<thead>
								<tr>
									<th class="check-column"><input type="checkbox" /></th>
									<th class="title"><strong><?php _e( 'Announce title' , $this->ltd ); ?></strong> / <?php _e( 'Announce type' , $this->ltd ); ?></th>
									<th class="content"><?php _e( 'Announce content' , $this->ltd ); ?></th>
									<th class="role"><?php _e( 'User Roles' ); ?></th>
									<th class="operation">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th class="check-column"><input type="checkbox" /></th>
									<th class="title"><strong><?php _e( 'Announce title' , $this->ltd ); ?></strong> / <?php _e( 'Announce type' , $this->ltd ); ?></th>
									<th class="content"><?php _e( 'Announce content' , $this->ltd ); ?></th>
									<th class="role"><?php _e( 'User Roles' ); ?></th>
									<th class="operation">&nbsp;</th>
								</tr>
							</tfoot>
							<tbody>
								<?php foreach( $Data as $key => $announce ) : ?>

									<?php $type = strip_tags( $announce["type"] ); ?>
									<tr id="tr_<?php echo $key; ?>" class="<?php echo $type; ?>">
										<th class="check-column"><input type="checkbox" name="data[delete][<?php echo $key; ?>][id]" value="<?php echo $key; ?>" /></th>
										<td class="title">

											<div class="edit">
												<?php $title = strip_tags( $announce["title"] ); ?>
												<input type="text" value="<?php echo $title; ?>" name="data[<?php echo $mode; ?>][<?php echo $key; ?>][title]" />
											</div>
											<div class="toggle">
												<strong><?php echo $title; ?></strong>
											</div>
											
											<div class="edit">
												<select name="data[<?php echo $mode; ?>][<?php echo $key; ?>][type]">
													<?php foreach($AllTypes as $Types) : ?>
														<?php $Selected = ''; ?>
														<?php if($Types == $type) : ?>
															<?php $Selected = 'selected="selected"'; ?>
														<?php endif; ?>
														<option value="<?php echo $Types; ?>" <?php echo $Selected; ?>><?php _e( $Types , $this->ltd ); ?>(<?php echo $this->get_color($Types); ?>)</option>
													<?php endforeach; ?>
												</select>
											</div>
	
											<div class="toggle">
												<?php echo _e( $type , $this->ltd ); ?>
												(<?php echo $this->get_color( $type ); ?> )
											</div>

											<ul class="toggle menu">
												<li><a class="menu_edit" href="#"><?php _e( 'Edit' ); ?></a> | </li>
												<li><a class="delete" href="<?php echo admin_url( 'options-general.php?page=' . $this->Slug ); ?>" id="delete_<?php echo $key; ?>"><?php _e('Delete'); ?></a></li>
											</ul>

										</td>
										<td class="content">
											<div class="edit">
												<?php $content = stripslashes( $announce["content"] ); ?>
												<?php wp_editor( $content , $mode.'_'.$key.'_content', array( 'textarea_name' => 'data[' . $mode . ']['.$key.'][content]' , 'media_buttons' => false ) ); ?>
											</div>
											<div class="toggle"><?php echo $content; ?></div>
										</td>
										<td class="role">
											<div class="edit">
												<?php $roles = $announce["role"]; ?>
												<?php foreach( $UserRoles as $role => $rolename ) : ?>
													<?php $Checked = ''; ?>
													<?php if( array_key_exists( $role , $roles ) ) : ?>
														<?php $Checked = 'checked="checked"'; ?>
													<?php endif; ?>
													<label><input type="checkbox" name="data[<?php echo $mode; ?>][<?php echo $key; ?>][role][<?php echo $role; ?>]" value="<?php echo $role; ?>" <?php echo $Checked; ?> /> <?php echo $rolename; ?></label>
												<?php endforeach; ?>
											</div>
											<div class="toggle">
												<?php if( !empty( $roles ) ) : ?>
													<ul>
														<?php foreach( $roles as $role => $val ) : ?>
															<li><?php echo $UserRoles[strip_tags( $role )]; ?></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</div>
										</td>
										<td class="operation">
											<div class="edit">
												<p class="submit">
													<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
												</p>
											</div>
										</td>
									</tr>
								
								<?php endforeach; ?>
							</tbody>
						</table>
						<div class="tablenav top">
							<select name="action2">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="submit" class="button-secondary action" value="<?php _e( 'Apply' ); ?>" />
						</div>
					</form>
				</div>

				<div id="Confirm" style="display: none;">
					<div id="ConfirmSt">
						<p>&nbsp;</p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e('Cancel'); ?></a>
						<a class="button-secondary" id="deletebtn" href="javascript:void(0);" title=""><?php _e('Continue'); ?></a>
					</div>
				</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	// delete
	$("a.delete").click(function() {
		var $DelName = $(".toggle strong", $(this).parent().parent().parent()).text();
		var DeleteID = $(this).attr("id").replace( 'delete_' , '' );
		
		var $ConfDlg = $("#Confirm #ConfirmSt");

		$ConfDlg.children("a#deletebtn").attr( "title" , DeleteID );
		$ConfDlg.children("p").html('<?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ), '' ); ?>');
		$ConfDlg.children("p").children("strong").text($DelName);
		
		tb_show('<?php _e('Confirm Deletion'); ?>', '#TB_inline?height=100&width=240&inlineId=Confirm', '');
		return false;
	});
	
	$("a#cancelbtn").click(function() {
		tb_remove();
	});

	$("a#deletebtn").click(function() {
		var $Form = $('<form action="" method="post"></form>');
		$Form.append('<input type="hidden" name="action" value="delete" />');
		$Form.append('<input type="hidden" name="data[delete][' + $(this).attr("title") + '][id]" value="1" />');
		
		$Form.submit();
		return false;
	});

});
</script>

			<?php endif; ?>

		</div>

	</div>

</div>
