<?php

global $wp_version;

$Data = $this->get_data();
$AllTypes = $this->AllTypes();
$UserRoles = $this->get_user_role();
$Period = $this->get_date_period();

// include js css
$ReadedJs = array( 'jquery' , 'thickbox' );
wp_enqueue_script( $this->PageSlug ,  $this->Url . $this->PluginSlug . '.js', $ReadedJs , $this->Ver );
wp_enqueue_style('thickbox');

if( version_compare( $wp_version , "3.7.2" , '>' ) ) {
	wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '.css', array() , $this->Ver );
} else {
	wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '-3.7.css', array() , $this->Ver );
}

?>

<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'Announcement settings for Dashboard' , $this->ltd ); ?></h2>
	<?php echo $this->Msg; ?>
	<p>&nbsp;</p>


	<div class="metabox-holder columns-2" id="afd">

		<div id="postbox-container-1" class="postbox-container">

			<form id="afd_create_form" class="afd_form" method="post" action="<?php echo remove_query_arg( array( $this->MsgQ ) ); ?>">
				<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
				<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $this->RecordName; ?>" />

				<?php $mode = 'create'; ?>
				<div id="<?php echo $mode; ?>">
				
					<h3><?php _e( 'Create a new announce to the dashboard.' , $this->ltd ); ?></h3>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $mode; ?>_title"><?php _e( 'Announce title' , $this->ltd ); ?></label> *</th>
								<td><?php $this->fields_setting( $mode , 'title' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_content"><?php _e( 'Announce content' , $this->ltd ); ?></label> *</th>
								<td><?php $this->fields_setting( $mode , 'content' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_date_specifi"><?php _e( 'Date Range' , $this->ltd ); ?></label></th>
								<td>
									<?php $this->fields_setting( $mode , 'date' ); ?>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_type"><?php _e( 'Announce type' , $this->ltd ); ?></label></th>
								<td>
									<?php $this->fields_setting( $mode , 'type' ); ?>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_role"><?php _e( 'User Roles' ); ?></label></th>
								<td class="user_groups">
									<?php $this->fields_setting( $mode , 'userrole' ); ?>
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
				<span class="description"><?php _e( 'Thank you for your donation.' , $this->ltd ); ?></span>
			<?php else: ?>

				<div class="stuffbox" style="border-color: #FFC426; border-width: 3px;">
					<h3 style="background: #FFF2D0; border-color: #FFC426;"><span class="hndle"><?php _e( 'Have you want to customize?' , $this->ltd ); ?></span></h3>
					<div class="inside">
						<p style="float: right;">
							<img src="<?php echo $this->Schema; ?>www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=46" width="46" /><br />
							<a href="<?php echo $this->AuthorUrl; ?>contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank">gqevu6bsiz</a>
						</p>
						<p><?php _e( 'I am good at Admin Screen Customize.' , $this->ltd ); ?></p>
						<p><?php _e( 'Please consider the request to me if it is good.' , $this->ltd ); ?></p>
						<p>
							<a href="http://wpadminuicustomize.com/blog/category/example/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e ( 'Example Customize' , $this->ltd ); ?></a> :
							<a href="<?php echo $this->AuthorUrl; ?>contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Contact me' , $this->ltd ); ?></a></p>
					</div>
				</div>

				<div class="stuffbox" id="donationbox">
					<div class="inside">
						<p style="color: #FFFFFF; font-size: 20px;"><?php _e( 'Please donation.' , $this->ltd ); ?></p>
						<p style="text-align: center;">
							<a href="<?php echo $this->AuthorUrl; ?>please-donation/?utm_source=use_plugin&utm_medium=donate&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" class="button-primary" target="_blank"><?php _e( 'Please donation.' , $this->ltd ); ?></a>
						</p>
						<form id="donation_form" class="afd_form" method="post" action="<?php echo remove_query_arg( array( $this->MsgQ ) ); ?>">
							<h4 style="color: #FFF;"><?php _e( 'If you have already donated to.' , $this->ltd ); ?></h4>
							<p style="color: #FFF;"><?php _e( 'Please enter the \'Donate delete key\' that have been described in the \'Line Break First and End download page\'.' , $this->ltd ); ?></p>
							<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
							<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
							<input type="hidden" name="record_field" value="<?php echo $this->RecordName; ?>" />
							<label for="donate_key"><span style="color: #FFF; "><?php _e( 'Donate delete key' , $this->ltd ); ?></span></label>
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
				<h3><span class="hndle"><?php _e( 'About plugin' , $this->ltd ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Version checked' , $this->ltd ); ?> : 3.6.1 - 3.8.1</p>
					<ul>
						<li><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank"><?php _e( 'Plugin\'s site' , $this->ltd ); ?></a></li>
						<li><a href="<?php echo $this->AuthorUrl; ?>?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $this->ltd ); ?></a></li>
						<li><a href="http://wordpress.org/support/plugin/announce-from-the-dashboard" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
						<li><a href="http://wordpress.org/support/view/plugin-reviews/announce-from-the-dashboard" target="_blank"><?php _e( 'Reviews' , $this->ltd ); ?></a></li>
						<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
						<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
					</ul>
				</div>
			</div>

			<div class="stuffbox" id="usefulbox">
				<h3><span class="hndle"><?php _e( 'Useful plugins' , $this->ltd ); ?></span></h3>
				<div class="inside">
					<p><strong><a href="http://wpadminuicustomize.com/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
					<p class="description"><?php _e( 'Customize a variety of screen management.' , $this->ltd ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/post-lists-view-custom/" target="_blank">Post Lists View Custom</a></strong></p>
					<p class="description"><?php _e( 'Customize the list of the post and page. custom post type page, too. You can customize the column display items freely.' , $this->ltd ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/custom-options-plus-post-in/" target="_blank">Custom Options Plus Post in</a></strong></p>
					<p class="description"><?php _e( 'The plugin that allows you to add the value of the options. Option value that you have created, can be used in addition to the template tag, Short code can be used in the body of the article.' , $this->ltd ); ?></p>
					<p>&nbsp;</p>
					<p><a href="http://profiles.wordpress.org/gqevu6bsiz" target="_blank"><?php _e( 'All Plugins' ); ?></a></p>
				</div>
			</div>
		
		</div>

		<div class="clear"></div>

	</div>

	<div class="metabox-holder columns-1" id="afd-lists">

		<div id="postbox-container-1" class="postbox-container">

			<?php if( empty( $Data ) ) : ?>
	
				<p><strong><?php _e( 'Not created announce.' , $this->ltd ); ?></strong></p>
	
			<?php else : ?>

				<?php $mode = 'update'; ?>
				<div id="<?php echo $mode; ?>">

					<form id="afd_update_form" class="afd_form" method="post" action="<?php echo remove_query_arg( array( $this->MsgQ ) ); ?>">
						<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
						<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $this->RecordName; ?>" />
						<h3><?php _e( 'List of announce that you created' , $this->ltd ); ?></h3>

						<div class="tablenav top">
							<select name="action">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="submit" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
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
											<?php $title = strip_tags( $announce["title"] ); ?>
											<div class="edit">
												<?php _e( 'Announce title' , $this->ltd ); ?>:
												<?php $this->fields_setting( $mode , 'title' , $title , $key ); ?>
												<p>&nbsp;</p>
											</div>
											<div class="toggle">
												<strong><?php echo $title; ?></strong>
											</div>
											
											<div class="edit">
												<?php _e( 'Announce type' , $this->ltd ); ?>:
												<?php $this->fields_setting( $mode , 'type' , $type , $key ); ?>
												<p>&nbsp;</p>
											</div>
											<div class="toggle">
												<?php echo $AllTypes[$type]["label"]; ?>
												(<?php echo $AllTypes[$type]["color"]; ?> )
											</div>
											
											<div class="edit">
												<?php _e( 'Date Range' , $this->ltd ); ?>:
												<?php $range = array(); ?>
												<?php if( !empty( $announce["range"] ) ) $range = $announce["range"]; ?>
												<?php $sp_date = array(); ?>
												<?php if( !empty( $announce["date"] ) ) $sp_date = $announce["date"]; ?>
												<?php $this->fields_setting( $mode , 'date' , array( "range" => $range , "date" => $sp_date ) , $key ); ?>
											</div>
											<div class="toggle">
												<?php foreach( $Period as $name => $label ) : ?>
													<p>
														<strong><?php echo $label; ?>:</strong>
														<?php if( !empty( $announce["date"][$name] ) ) : ?>
															<code><?php echo mysql2date( get_option( 'date_format' ) . get_option( 'time_format' ) , $announce["date"][$name] ); ?></code>
														<?php endif; ?>
													</p>
												<?php endforeach; ?>
											</div>

											<ul class="toggle menu">
												<li><a class="menu_edit" href="#"><?php _e( 'Edit' ); ?></a> | </li>
												<li><a class="delete" href="<?php echo admin_url( 'options-general.php?page=' . $this->PageSlug ); ?>" id="delete_<?php echo $key; ?>"><?php _e('Delete'); ?></a></li>
											</ul>

										</td>
										<td class="content">
											<?php $content = stripslashes( $announce["content"] ); ?>
											<div class="edit">
												<?php $this->fields_setting( $mode , 'content' , $content , $key ); ?>
											</div>
											<div class="toggle"><?php echo $content; ?></div>
										</td>
										<td class="role">
											<?php $roles = $announce["role"]; ?>
											<div class="edit">
												<?php $this->fields_setting( $mode , 'userrole' , $roles , $key ); ?>
											</div>
											<div class="toggle">
												<?php if( !empty( $roles ) ) : ?>
													<ul>
														<?php foreach( $roles as $role => $val ) : ?>
															<li><?php echo $UserRoles[strip_tags( $role )]["label"]; ?></li>
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
							<input type="submit" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
					</form>
				</div>

				<div id="Confirm" style="display: none;">
					<div id="ConfirmSt">
						<p>&nbsp;</p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e( 'Cancel' ); ?></a>
						<a class="button-secondary" id="deletebtn" href="javascript:void(0);" title=""><?php _e( 'Continue' ); ?></a>
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
		$ConfDlg.children("p").html('<?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ) , '' ); ?>');
		$ConfDlg.children("p").children("strong").text($DelName);
		
		tb_show('<?php _e( 'Confirm Deletion' ); ?>', '#TB_inline?height=200&width=300&inlineId=Confirm', '');
		return false;
	});
	
	$("a#cancelbtn").click(function() {
		tb_remove();
	});

	$("a#deletebtn").click(function() {
		var $Form = $('<form action="<?php echo remove_query_arg( array( $this->MsgQ ) ); ?>" method="post"></form>');
		$Form.append('<input type="hidden" name="action" value="delete" />');
		$Form.append('<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>');
		$Form.append('<input type="hidden" name="data[delete][' + $(this).attr("title") + '][id]" value="1" />');
		$Form.append('<input type="hidden" name="record_field" value="<?php echo $this->RecordName; ?>" />' );
		
		$Form.submit();
		return false;
	});

	// Bulk
	$("input[type=submit].bulk").click(function() {
		$Form = $(this).parent().parent();
		$Action = $Form.find("select[name=action] option:selected").val();
		$Action2 = $Form.find("select[name=action2] option:selected").val();
		
		if( $Action != "" || $Action2 != "" ) {
			if( confirm( '<?php _e( 'Are you sure you want to bulk action?' , $this->ltd ); ?>' )){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	});
	
	// Date range check
	$(document).on('keyup blur change', '.date_range_setting input[type=text], .date_range_setting select', function( ev ) {
		
		var $DateRange = $(ev.target).parent().parent().parent().parent();
		
		var CheckStart = $DateRange.find('input.date_range_check').eq(0).prop('checked');
		var CheckEnd = $DateRange.find('input.date_range_check').eq(1).prop('checked');
		
		if( CheckStart && CheckEnd ) {

			var StartDate = {};
			var EndDate = {};

			StartDate.aa = $DateRange.find('.start input.date_aa').val();
			StartDate.mm = $DateRange.find('.start select.date_mm option:selected').val();
			StartDate.jj = $DateRange.find('.start input.date_jj').val();
			StartDate.hh = $DateRange.find('.start input.date_hh').val();
			StartDate.mn = $DateRange.find('.start input.date_mn').val();
			StartDate.date = new Date( StartDate.aa, StartDate.mm, StartDate.jj, StartDate.hh, StartDate.mn );
			StartDate.mic = StartDate.date.getTime();

			EndDate.aa = $DateRange.find('.end input.date_aa').val();
			EndDate.mm = $DateRange.find('.end select.date_mm option:selected').val();
			EndDate.jj = $DateRange.find('.end input.date_jj').val();
			EndDate.hh = $DateRange.find('.end input.date_hh').val();
			EndDate.mn = $DateRange.find('.end input.date_mn').val();
			EndDate.date = new Date( EndDate.aa, EndDate.mm, EndDate.jj, EndDate.hh, EndDate.mn );
			EndDate.mic = EndDate.date.getTime();

			if( StartDate.mic >= EndDate.mic ) {
				$DateRange.find('.date_range_error').fadeIn();
			} else {
				$DateRange.find('.date_range_error').hide();
			}

		} else {
			$DateRange.find('.date_range_error').hide();
		}
		
	});
	
});
</script>

			<?php endif; ?>

		</div>

	</div>

</div>
