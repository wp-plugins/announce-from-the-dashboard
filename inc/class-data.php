<?php

if ( !class_exists( 'Afd_Data' ) ) :

class Afd_Data
{

	function __construct() {
		
		if( is_admin() )
			add_action( 'plugins_loaded' , array( $this , 'init' ) , 20 );

	}

	function init() {
		
		global $Afd;
		
		if( !$Afd->Current['ajax'] ) {
			add_action( 'admin_init' , array( $this , 'dataUpdate' ) );
		}
	}

	function get_record( $record ) {
		
		global $Afd;
		
		$Data = array();

		if( !empty( $Afd->Current['multisite'] ) ) {
			
			$GetData = get_site_option( $record );

		} else {

			$GetData = get_option( $record );

		}
		
		if( !empty( $GetData ) )
			$Data = $GetData;
		
		return $Data;

	}

	function get_data_announces() {
		
		global $Afd;
		
		$Data = $this->get_record( $Afd->Plugin['record']['announce'] );
		
		return $Data;

	}

	function get_data_others() {
		
		global $Afd;
		
		$Data = $this->get_record( $Afd->Plugin['record']['other'] );
		
		return $Data;

	}
	
	function get_data_all_child() {
		
		global $Afd;
		
		$Data = array();
		
		$all_sites = wp_get_sites();

		foreach( $all_sites as $blog ) {
			
			$blog_details = get_blog_details( $blog['blog_id'] );
			$Data[$blog['blog_id']] = array( 'blog_id' => $blog['blog_id'] , 'name' => $blog_details->blogname , 'settings' => array() );

			switch_to_blog( $blog['blog_id'] );
			
			$child_data = get_option( $Afd->Plugin['record']['announce'] );
			if( !empty( $child_data ) )
				$Data[$blog['blog_id']]['settings'] = $child_data;
			
			restore_current_blog();
			
		}

		return $Data;

	}

	function get_user_data( $user_role = false ) {

		global $Afd;
		
		$GetData = $this->get_data_announces();
		$SettingsData = array();

		if( !empty( $GetData ) && !empty( $user_role ) ) {
			
			foreach( $GetData as $key => $announce ) {
				
				if( empty( $announce['role'] ) or !array_key_exists( $user_role , $announce['role'] ) ) {

					unset( $GetData[$key] );

				}

			}
			
			if( !empty( $GetData ) ) {

				foreach( $GetData as $key => $announce ) {
	
					$start = true;
					if( !empty( $announce['range']['start'] ) ) {
						$start = $Afd->specify_date_check( 'start' , $announce );
					}
					
					$end = true;
					if( !empty( $announce['range']['end'] ) ) {
						$end = $Afd->specify_date_check( 'end' , $announce );
					}
						
					if( !empty( $start ) && !empty( $end ) )
						$SettingsData[] = $announce;
	
				}
				
			}
			
			if( $Afd->Current['multisite'] && !empty( $SettingsData ) ) {
				
				foreach( $SettingsData as $key => $announce ) {
				
					if( $announce['standard'] == 'all' ) {
						if( !empty( $announce['subsites'] ) && array_key_exists( $Afd->Current['blog_id'] , $announce['subsites'] ) ) {

							unset( $SettingsData[$key] );

						}
					} elseif( $announce['standard'] == 'not' ) {
						if( empty( $announce['subsites'] ) or !array_key_exists( $Afd->Current['blog_id'] , $announce['subsites'] ) ) {

							unset( $SettingsData[$key] );

						}
					}
				
				}
				
			}
			
		}

		return $SettingsData;

	}

	function get_donate_key( $record ) {
		
		global $Afd;

		if( $Afd->Current['multisite'] ) {
			$donateKey = get_site_option( $record );
		} else {
			$donateKey = get_option( $record );
		}
		
		return $donateKey;

	}

	function get_donate_width() {
		
		global $Afd;
		
		$width = false;
		if( $Afd->Current['multisite'] ) {
			$GetData = get_site_option( $Afd->ClassInfo->DonateOptionRecord );
		} else {
			$GetData = get_option( $Afd->ClassInfo->DonateOptionRecord );
		}

		if( !empty( $GetData ) ) {
			$width = true;
		}

		return $width;

	}





	function dataUpdate() {
		
		global $Afd;
		
		$RecordField = false;
		
		if( !empty( $_POST ) && !empty( $_POST[$Afd->Plugin['ltd'] . '_settings'] ) && $_POST[$Afd->Plugin['ltd'] . '_settings'] == $Afd->Plugin['UPFN'] ) {

			$can_capability = $Afd->ClassManager->get_manager_user_role();
			if( current_user_can( $can_capability ) ) {

				if( !empty( $_POST['record_field'] ) ) {
	
					$RecordField = strip_tags( $_POST['record_field'] );
					
					if( !empty( $_POST[$Afd->Plugin['nonces']['field']] ) && check_admin_referer( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ) ) {
						
						if( $RecordField == $Afd->Plugin['record']['announce'] ) {
							
							if( !empty( $_POST['data']['delete'] ) ) {
		
								$this->update_delete();
		
							} elseif( !empty( $_POST['data']['add'] ) ) {
								
								$this->update_add();
		
							} elseif( !empty( $_POST['data']['update'] ) ) {
								
								$this->update_list();
		
							}
							
						} elseif( $RecordField == $Afd->Plugin['record']['other'] ) {
								
							$this->update_other();
		
						} elseif( $RecordField == 'donate' ) {
								
							$this->update_donate();
		
						}
						
					}
					
				} elseif( $Afd->Current['multisite'] && !empty( $_POST[$Afd->Plugin['ltd'] . '_field_import_child'] ) && check_admin_referer( $Afd->Plugin['nonces']['value'] . '_import_child' , $Afd->Plugin['nonces']['field'] . '_import_child' ) ) {
					
					$this->import_child_data();
					
				}
				
			}

		}

	}

	function update_delete() {
		
		global $Afd;

		$PostData = $_POST['data'];
		$delete_ids = array();

		foreach( $PostData['delete'] as $id => $v ) {
			$delete_ids[] = intval( $id );
		}
		
		$Data = $this->get_data_announces();
		foreach( $delete_ids as $id ) {
			if( !empty( $Data[$id] ) )
				unset( $Data[$id] );
		}

		if( !empty( $Afd->Current['multisite'] ) ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'delete' ) );
		exit;

	}
	
	function update_add() {
		
		global $Afd;

		$PostData = $_POST['data'];
		$Add_data = $Afd->update_data_format( $PostData['add'] );
		
		$Data = $this->get_data_announces();
		$Data[] = $Add_data;
		
		if( !empty( $Afd->Current['multisite'] ) ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	function update_list() {
		
		global $Afd;

		$PostData = $_POST['data'];
		$Data = array();
		foreach( $PostData['update'] as $key => $list ) {
			$Data[$key] = $Afd->update_data_format( $list );
		}
		
		if( !empty( $Afd->Current['multisite'] ) ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	function update_sort( $Data ) {
		
		global $Afd;

		if( !empty( $Afd->Current['multisite'] ) ) {
					
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
					
		} else {
		
			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

	}

	function update_other() {
		
		global $Afd;

		$PostData = $_POST['data'];
		
		if( empty( $PostData['other'] ) )
			return false;
		
		$OtherData = $PostData['other'];

		$Data = array();

		if( !empty( $OtherData['capability'] ) )
			$Data['capability'] = strip_tags( $OtherData['capability'] );
			
		if( !empty( $Afd->Current['multisite'] ) ) {
			
			update_site_option( $Afd->Plugin['record']['other'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['other'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	function import_child_data() {
		
		global $Afd;
		
		$PostData = $_POST['data'];

		if( empty( $PostData['import_child'] ) )
			return false;

		$blog_id = intval( $PostData['import_child'] );

		switch_to_blog( $blog_id );
			
		$child_datas = get_option( $Afd->Plugin['record']['announce'] );
		if( empty( $child_datas ) )
			return false;

		restore_current_blog();
		
		$Data = $this->get_data_announces();

		foreach( $child_datas as $key => $child_data ) {
			
			$child_data['standard'] = 'not';
			$child_data['subsites'] = array( $blog_id => 1 );

			array_push( $Data , $child_data );

		}

		$url = add_query_arg( $Afd->Plugin['msg_notice'] , 'update' );
		$url = remove_query_arg( array( 'tab' ) , $url );

		update_site_option( $Afd->Plugin['record']['announce'] , $Data );
		wp_redirect( $url );
		exit;

	}
	
	function update_donate() {
		
		global $Afd;

		$is_donate_check = false;
		$submit_key = false;
		if( !empty( $_POST['donate_key'] ) ) {
			$is_donate_check = $Afd->ClassInfo->is_donate_key_check( $_POST['donate_key'] );
			if( !empty( $is_donate_check ) ) {

				if( !empty( $Afd->Current['multisite'] ) ) {
							
					update_site_option( $Afd->ClassInfo->DonateRecord , $is_donate_check );
							
				} else {
				
					update_option( $Afd->ClassInfo->DonateRecord , $is_donate_check );
		
				}

				wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'donated' ) );

			}
		}
		
	}
	
	function update_donate_toggle( $Data ) {
		
		global $Afd;

		if( !empty( $Afd->Current['multisite'] ) ) {
					
			update_site_option( $Afd->ClassInfo->DonateOptionRecord , $Data );
					
		} else {
		
			update_option( $Afd->ClassInfo->DonateOptionRecord , $Data );

		}

	}

}

endif;
