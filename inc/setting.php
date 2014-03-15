<?php

global $wp_version;

$Data = $this->get_data();
$AllTypes = $this->AllTypes();
$UserRoles = $this->get_user_role();
$Period = $this->get_date_period();

// include js css
$ReadedJs = array( 'jquery' , 'jquery-ui-draggable' , 'jquery-ui-droppable' , 'jquery-ui-sortable' , 'thickbox' );
wp_enqueue_script( $this->PageSlug ,  $this->Url . $this->PluginSlug . '.js', $ReadedJs , $this->Ver );
wp_enqueue_style('thickbox');

// localize
$translation = array( 'msg' => array( 'delete_confirm' => __( 'Confirm Deletion' ) , 'bulk_delete_confirm' => __( 'Are you sure you want to bulk action?' , $this->ltd ) ) );
wp_localize_script( $this->PageSlug , $this->ltd , $translation );

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
							<select name="action" class="action_sel">
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
										<th class="check-column">
											<input type="checkbox" name="data[delete][<?php echo $key; ?>][id]" value="<?php echo $key; ?>" />
											<span class="spinner"></span>
										</th>
										<td class="title">
											<?php $title = strip_tags( $announce["title"] ); ?>
											<div class="edit">
												<?php _e( 'Announce title' , $this->ltd ); ?>:
												<?php $this->fields_setting( $mode , 'title' , $title , $key ); ?>
												<p>&nbsp;</p>
											</div>
											<div class="toggle announce_title">
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
							<select name="action2" class="action_sel">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="submit" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
					</form>
				</div>

				<div id="Confirm" style="display: none;">
					<div id="ConfirmSt">
						<p><?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ) , '' ); ?></p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e( 'Cancel' ); ?></a>
						<a class="button-secondary" id="deletebtn" href="javascript:void(0);" title=""><?php _e( 'Continue' ); ?></a>
					</div>
				</div>
				
				<div id="DeleteForm" style="display: none;">
					<form id="afd_delete_form" class="afd_form" method="post" action="<?php echo remove_query_arg( array( $this->MsgQ ) ); ?>">
						<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
						<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $this->RecordName; ?>" />
						<input type="hidden" name="action" value="delete" />
					</form>
				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
