<?php

global $Afd;

$Data = $Afd->ClassData->get_data_others();
$all_user_roles = $Afd->ClassConfig->get_all_user_roles();
$capabilities = $all_user_roles['administrator']['capabilities'];
ksort( $capabilities );

$ChildData = $Afd->ClassData->get_data_all_child();
?>

<div class="wrap">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'Announcement other settings' , $Afd->Plugin['ltd'] ); ?></h2>
	<?php $this->print_nav_tab_wrapper(); ?>

	<?php $class = $Afd->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<?php include_once $Afd->Plugin['dir'] . 'inc/information.php'; ?>
		
		</div>

		<div id="postbox-container-2" class="postbox-container">

			<form id="<?php echo $Afd->Plugin['ltd']; ?>_other_form" class="<?php echo $Afd->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Afd->Plugin['ltd']; ?>_settings" value="Y">
				<?php wp_nonce_field( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Afd->Plugin['record']['other']; ?>" />

				<h3><?php _e( 'Other Settings' , $Afd->Plugin['ltd'] ); ?></h3>

				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="capability"><?php _e( 'Plugin' ); ?><?php _e( 'Settings' ); ?><?php _e( 'Capabilities' ); ?></label>
							</th>
							<td>
								<p><?php printf( __( 'Please choose the minimum role that can modify %s settings.' , $Afd->Plugin['ltd'] ) , $Afd->Plugin['name'] ); ?></p>
								<select name="data[other][capability]" id="capability">
									<?php $selected_cap = $this->get_manager_user_role(); ?>
									<?php if( !empty( $Data['capability'] ) ) $selected_cap = strip_tags( $Data['capability'] ); ?>
									<?php foreach( $capabilities as $capability => $v ): ?>
										<option value="<?php echo $capability; ?>" <?php selected( $selected_cap , $capability ); ?>><?php echo $capability; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save' ) ); ?>
	
			</form>
			
			<p>&nbsp;</p>

			<?php if( $Afd->Current['multisite'] ) : ?>

				<form id="<?php echo $Afd->Plugin['ltd']; ?>_import_child_form" class="<?php echo $Afd->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
					<input type="hidden" name="<?php echo $Afd->Plugin['ltd']; ?>_settings" value="Y">
					<?php wp_nonce_field( $Afd->Plugin['nonces']['value'] . '_import_child' , $Afd->Plugin['nonces']['field'] . '_import_child' ); ?>
	
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="import_child"><?php _e( 'Settings data import of Child blog' , $Afd->Plugin['ltd'] ); ?></label>
								</th>
								<td>
									<p><?php _e( 'Please if you want to import, choose the settings data of the child blog.' , $Afd->Plugin['ltd'] ); ?></p>
									<select name="data[import_child]" id="import_child">
										<option value=""></option>
										<?php foreach( $ChildData as $Child ): ?>
											<?php $disabled = false; ?>
											<?php if( empty( $Child['settings'] ) ) $disabled = true; ?>
											<option value="<?php echo $Child['blog_id']; ?>" <?php disabled( $disabled , 1 ); ?>><?php echo $Child['name']; ?> (<?php echo count( $Child['settings'] ); ?>)</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
	
					<?php submit_button( __( 'Import' ) ); ?>
		
				</form>
				
			<?php endif; ?>

		</div>

		<div class="clear"></div>

	</div>

</div>
