<?php

global $Afd;

$Data = $Afd->ClassData->get_data_announces();
$show_all_types = $Afd->ClassConfig->get_show_all_types();
$all_user_roles = $Afd->ClassConfig->get_all_user_roles();
$date_periods = $Afd->ClassConfig->get_date_periods();
$show_standards = $Afd->ClassConfig->get_multisite_show_standard();
?>

<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'Announcement settings for Dashboard' , $Afd->Plugin['ltd'] ); ?></h2>
	<?php $this->print_nav_tab_wrapper(); ?>

	<?php $class = $Afd->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<?php include_once $Afd->Plugin['dir'] . 'inc/information.php'; ?>
		
		</div>

		<div id="postbox-container-2" class="postbox-container">

			<form id="<?php echo $Afd->Plugin['ltd']; ?>_create_form" class="<?php echo $Afd->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Afd->Plugin['form']['field']; ?>" value="Y">
				<?php wp_nonce_field( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Afd->Plugin['record']['announce']; ?>" />

				<?php $mode = 'add'; ?>
				<input type="hidden" name="mode" value="<?php echo $mode; ?>">
				
				<h3><?php _e( 'Create a new announce to the dashboard.' , $Afd->Plugin['ltd'] ); ?></h3>

				<?php $class = ''; ?>
				<?php if( !empty( $Data ) ) : ?>
				
					<p class="submit">
						<input type="button" class="button button-primary announce_add_btn" value="<?php _e( 'Create Announcement' , $Afd->Plugin['ltd'] ); ?>" />
					</p>
					<?php $class = 'hide_add'; ?>

				<?php endif; ?>

				<div id="<?php echo $mode; ?>" class="<?php echo $class; ?>">
				
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="<?php echo $mode; ?>_title"><?php _e( 'Announce title' , $Afd->Plugin['ltd'] ); ?></label></th>
								<td><?php $Afd->fields_setting( $mode , 'title' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_content"><?php _e( 'Announce content' , $Afd->Plugin['ltd'] ); ?></label> *</th>
								<td><?php $Afd->fields_setting( $mode , 'content' ); ?></td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_date_specifi"><?php _e( 'Date Range' , $Afd->Plugin['ltd'] ); ?></label></th>
								<td>
									<?php $Afd->fields_setting( $mode , 'date' ); ?>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_type"><?php _e( 'Announce type' , $Afd->Plugin['ltd'] ); ?></label></th>
								<td>
									<?php $Afd->fields_setting( $mode , 'type' ); ?>
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $mode; ?>_role"><?php _e( 'User Roles' ); ?></label></th>
								<td class="user_groups">
									<?php $Afd->fields_setting( $mode , 'userrole' ); ?>
								</td>
							</tr>
							<?php if( $Afd->Current['multisite'] ): ?>
								<tr>
									<th><label for="<?php echo $mode; ?>_standard"><?php _e( 'Default show for announce of Child-sites.' , $Afd->Plugin['ltd'] ); ?></label> *</th>
									<td class="user_groups">
										<?php $Afd->fields_setting( $mode , 'standard' ); ?>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>

					<?php submit_button( __( 'Save' ) ); ?>
		
				</div>
	
			</form>

		</div>

		<div class="clear"></div>

	</div>

	<div class="metabox-holder columns-1" id="afd-lists">

		<div id="postbox-container-1" class="postbox-container">

			<?php if( empty( $Data ) ) : ?>
	
				<p><strong><?php _e( 'Not created announce.' , $Afd->Plugin['ltd'] ); ?></strong></p>
	
			<?php else : ?>

				<?php $mode = 'update'; ?>
				<div id="<?php echo $mode; ?>">

					<form id="<?php echo $Afd->Plugin['ltd']; ?>_update_form" class="<?php echo $Afd->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">

						<input type="hidden" name="<?php echo $Afd->Plugin['form']['field']; ?>" value="Y">
						<?php wp_nonce_field( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Afd->Plugin['record']['announce']; ?>" />
						<input type="hidden" name="mode" value="<?php echo $mode; ?>">

						<h3><?php _e( 'List of announce that you created' , $Afd->Plugin['ltd'] ); ?></h3>
						<p><?php _e( 'It is will show in order from the top.' , $Afd->Plugin['ltd'] ); ?></p>

						<div class="tablenav top">
							<select name="action" class="action_sel">
								<option value=""><?php _e( 'Bulk Actions' ); ?></option>
								<option value="delete"><?php _e( 'Delete' ); ?></option>
							</select>
							<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
						<table cellspacing="0" class="widefat fixed">
							<?php $arr = array( 'thead' , 'tfoot' ); ?>
							<?php foreach( $arr as $tag ) : ?>
								<<?php echo $tag; ?>>
									<tr>
										<th class="check-column">
											<input type="checkbox" />
										</th>
										<th class="title">
											<?php _e( 'Announce title' , $Afd->Plugin['ltd'] ); ?> / 
											<?php _e( 'Announce type' , $Afd->Plugin['ltd'] ); ?>
										</th>
										<th class="content">
											<?php _e( 'Announce content' , $Afd->Plugin['ltd'] ); ?>
										</th>
										<th class="role">
											<?php _e( 'User Roles' ); ?>
											<?php if( $Afd->Current['multisite'] ): ?>
											/ <?php _e( 'Child-sites' , $Afd->Plugin['ltd'] ); ?>
											<?php endif; ?>
										</th>
										<th class="operation">&nbsp;</th>
									</tr>
								</<?php echo $tag; ?>>
							<?php endforeach; ?>
							<tbody>
								<?php foreach( $Data as $key => $announce ) : ?>

									<?php $type = strip_tags( $announce['type'] ); ?>
									<tr id="tr_<?php echo $key; ?>" class="<?php echo $Afd->Plugin['ltd']; ?>_list_tr <?php echo $type; ?>">
										<th class="check-column">
											<input type="checkbox" name="data[update][<?php echo $key; ?>][id]" value="<?php echo $key; ?>" />
											<span class="spinner"></span>
										</th>
										<td class="title">
											<?php $title = strip_tags( $announce['title'] ); ?>
											<div class="edit">
												<?php _e( 'Announce title' , $Afd->Plugin['ltd'] ); ?>:
												<?php $Afd->fields_setting( $mode , 'title' , $title , $key ); ?>
												<p>&nbsp;</p>
											</div>
											<div class="toggle announce_title">
												<p><strong><?php echo $title; ?></strong></p>
											</div>
											
											<div class="edit">
												<?php _e( 'Announce type' , $Afd->Plugin['ltd'] ); ?>:
												<?php $Afd->fields_setting( $mode , 'type' , $type , $key ); ?>
												<p>&nbsp;</p>
											</div>
											<div class="toggle">
												<p>
													<?php echo $show_all_types[$type]['label']; ?>
													(<?php echo $show_all_types[$type]['color']; ?> )
												</p>
											</div>
											
											<div class="edit">
												<?php _e( 'Date Range' , $Afd->Plugin['ltd'] ); ?>:
												<?php $range = array(); ?>
												<?php if( !empty( $announce['range'] ) ) $range = $announce['range']; ?>
												<?php $sp_date = array(); ?>
												<?php if( !empty( $announce['date'] ) ) $sp_date = $announce['date']; ?>
												<?php $Afd->fields_setting( $mode , 'date' , array( 'range' => $range , 'date' => $sp_date ) , $key ); ?>
											</div>
											<div class="toggle">
												<?php foreach( $date_periods as $name => $label ) : ?>
													<p>
														<strong><?php echo $label; ?>:</strong>
														<?php if( !empty( $announce['date'][$name] ) ) : ?>
															<code><?php echo mysql2date( get_option( 'date_format' ) . get_option( 'time_format' ) , $announce['date'][$name] ); ?></code>
														<?php endif; ?>
													</p>
												<?php endforeach; ?>
											</div>

										</td>
										<td class="content">
											<?php $content = stripslashes( $announce['content'] ); ?>
											<div class="edit">
												<?php $Afd->fields_setting( $mode , 'content' , $content , $key ); ?>
											</div>
											<div class="toggle"><?php echo $content; ?></div>
										</td>
										<td class="role">
											<?php $roles = $announce['role']; ?>
											<?php if( $Afd->Current['multisite'] ): ?>
												<?php $standard = $announce['standard']; ?>
												<?php $subsites = $announce['subsites']; ?>
											<?php endif; ?>
											<div class="edit">
												<?php $Afd->fields_setting( $mode , 'userrole' , $roles , $key ); ?>
												<?php if( $Afd->Current['multisite'] ): ?>
													<?php $Afd->fields_setting( $mode , 'standard' , array( 'standard' => $standard , 'subsites' => $subsites ) , $key ); ?>
												<?php endif; ?>
											</div>
											<div class="toggle">
												<?php if( !empty( $roles ) ) : ?>
													<ul>
														<?php foreach( $roles as $role => $val ) : ?>
															<li><?php echo $all_user_roles[$role]['label']; ?></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
												<?php if( $Afd->Current['multisite'] ): ?>
													<p class="show_default_<?php echo $standard; ?>"><strong><?php echo $show_standards[$standard]; ?></strong></p>
													<?php if( !empty( $subsites ) ): ?>
														<ul>
															<?php foreach( $subsites as $blog_id => $v ) : ?>
																<?php $child_blog = get_blog_details( array( 'blog_id' => $blog_id ) ); ?>
																<li>[<?php echo $blog_id; ?>] <?php echo $child_blog->blogname; ?></li>
															<?php endforeach; ?>
														</ul>
													<?php endif; ?>
												<?php endif; ?>
											</div>
										</td>
										<td class="operation">
											<ul class="toggle menu">
												<li><a class="menu_edit" href="#"><?php _e( 'Edit' ); ?></a> | </li>
												<li><a class="delete" href="<?php echo admin_url( 'options-general.php?page=' . $Afd->Plugin['page_slug'] ); ?>" id="delete_<?php echo $key; ?>"><?php _e('Delete'); ?></a></li>
											</ul>
											<div class="edit">
												<?php submit_button( __( 'Save' ) ); ?>
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
							<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
						</div>
					</form>
				</div>

				<div id="<?php echo $Afd->Plugin['ltd']; ?>_confirm">
					<div id="ConfirmSt">
						<p><?php echo sprintf( __( 'You are about to delete <strong>%s</strong>.' ) , '' ); ?></p>
						<a class="button-secondary" id="cancelbtn" href="javascript:void(0);"><?php _e( 'Cancel' ); ?></a>
						<a class="button-secondary" id="deletebtn" href="javascript:void(0);" title=""><?php _e( 'Continue' ); ?></a>
					</div>
				</div>
				
				<div id="<?php echo $Afd->Plugin['ltd']; ?>_delete">
					<form id="<?php echo $Afd->Plugin['ltd']; ?>_delete_form" class="<?php echo $Afd->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
						<input type="hidden" name="<?php echo $Afd->Plugin['form']['field']; ?>" value="Y">
						<?php wp_nonce_field( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Afd->Plugin['record']['announce']; ?>" />
						<input type="hidden" name="action" value="delete" />
					</form>
				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
