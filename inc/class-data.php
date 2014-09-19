<?php

if ( !class_exists( 'Afd_Data' ) ) :

class Afd_Data
{

	function __construct() {
		
		if( is_admin() )
			add_action( 'wp_loaded' , array( $this , 'init' ) , 20 );

	}

	function init() {
		
		global $Afd;
		
		if( !$Afd->Current['ajax'] ) {
			add_action( 'admin_init' , array( $this , 'dataUpdate' ) );
		}

	}

	private function get_record( $record ) {
		
		global $Afd;
		
		$Data = array();

		if( $Afd->Current['multisite'] ) {
			
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
		
		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {

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





	function dataUpdate() {
		
		global $Afd;
		
		$RecordField = false;

		if( !empty( $_POST ) && !empty( $Afd->ClassManager->is_manager ) && !empty( $_POST[$Afd->Plugin['form']['field']] ) && $_POST[$Afd->Plugin['form']['field']] == $Afd->Plugin['UPFN']  ) {

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
		
					}
						
				}
					
			} elseif( !empty( $_POST[$Afd->Plugin['nonces']['field'] . '_import_child'] ) && check_admin_referer( $Afd->Plugin['nonces']['value'] . '_import_child' , $Afd->Plugin['nonces']['field'] . '_import_child' ) ) {
				
				if( $Afd->Current['multisite'] ) {

					$this->import_child_data();
					
				}
					
			}
				
		}

	}

	private function update_data_format( $list ) {
		
		global $Afd;

		$announce = array();

		$announce['title'] = '';
		if( !empty( $list['title'] ) )
			$announce['title'] = strip_tags( $list['title'] );

		$announce['content'] = '';
		if( !empty( $list['content'] ) )
			$announce['content'] = $list['content'];

		$announce['type'] = 'normal';
		if( !empty( $list['type'] ) )
			$announce['type'] = strip_tags( $list['type'] );

		$announce['role'] = array();
		if( !empty( $list['role'] ) ) {
			foreach( $list['role'] as $role_name ) {
				$role_name = strip_tags( $role_name );
				$announce['role'][$role_name] = 1;
			}
		}

		$announce['range'] = array();
		$announce['date'] = array();
		if( !empty( $list['range'] ) ) {
			foreach( $list['range'] as $range_type => $v ) {
				$range_type = strip_tags( $range_type );
				$announce['range'][$range_type] = 1;
				if( !empty( $list[$range_type]['date'] ) ) {
					$date = '';
					$date = $list[$range_type]['date']['aa'];
					$date .= '-' . zeroise( $list[$range_type]['date']['mm'], 2 );
					$date .= '-' . zeroise( $list[$range_type]['date']['jj'], 2 );
					$date .= ' ' . zeroise( $list[$range_type]['date']['hh'], 2 );
					$date .= ':' . zeroise( $list[$range_type]['date']['mn'], 2 );
					$date .= ':00';
					$announce['date'][$range_type] = $date;
				}
			}
		}
		
		if( $Afd->Current['multisite'] ) {

			$announce['standard'] = 'all';
			if( !empty( $list['standard'] ) )
				$announce['standard'] = strip_tags( $list['standard'] );
				
			$announce['subsites'] = array();
			if( !empty( $list['subsites'] ) ) {
				foreach( $list['subsites'] as $blog_id ) {
					$blog_id = intval( $blog_id );
					$announce['subsites'][$blog_id] = 1;
				}
			}

		}

		return $announce;

	}

	private function update_delete() {
		
		global $Afd;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		$delete_ids = array();

		if( empty( $PostData['delete'] ) )
			return false;

		foreach( $PostData['delete'] as $id => $v ) {
			$delete_ids[] = intval( $id );
		}
		
		$Data = $this->get_data_announces();

		foreach( $delete_ids as $id ) {
			if( !empty( $Data[$id] ) )
				unset( $Data[$id] );
		}

		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'delete' ) );
		exit;

	}
	
	private function update_add() {
		
		global $Afd;

		if( empty( $_POST['data'] ) )
			return false;
		
		$PostData = $_POST['data'];
		
		if( empty( $PostData ) or empty( $PostData['add'] ) or empty( $PostData['add']['content'] ) )
			return false;
		
		$Add_data = $this->update_data_format( $PostData['add'] );
		
		$Data = $this->get_data_announces();
		$Data[] = $Add_data;
		
		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	private function update_list() {
		
		global $Afd;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		
		if( empty( $PostData ) or empty( $PostData['update'] ) )
			return false;

		$Data = array();

		foreach( $PostData['update'] as $key => $list ) {

			$Data[$key] = $this->update_data_format( $list );

		}
		
		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {
			
			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['announce'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	function update_sort( $Data ) {
		
		global $Afd;

		if( $Afd->ClassManager->is_manager && check_ajax_referer( $Afd->Plugin['nonces']['value'] , $Afd->Plugin['nonces']['field'] ) ) {

			if( !empty( $Afd->Current['multisite'] ) ) {
						
				update_site_option( $Afd->Plugin['record']['announce'] , $Data );
						
			} else {
			
				update_option( $Afd->Plugin['record']['announce'] , $Data );

			}

		}

	}

	private function update_other() {
		
		global $Afd;

		if( empty( $_POST['data'] ) )
			return false;

		$PostData = $_POST['data'];
		
		if( empty( $PostData['other'] ) )
			return false;
		
		$OtherData = $PostData['other'];
		
		if( empty( $OtherData ) )
			return false;

		$Data = array();

		if( !empty( $OtherData['capability'] ) )
			$Data['capability'] = strip_tags( $OtherData['capability'] );
			
		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {
			
			update_site_option( $Afd->Plugin['record']['other'] , $Data );
			
		} else {

			update_option( $Afd->Plugin['record']['other'] , $Data );

		}

		wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'update' ) );
		exit;

	}
	
	private function import_child_data() {
		
		global $Afd;
		
		if( empty( $_POST['data'] ) )
			return false;

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

		if( $Afd->Current['multisite'] && $Afd->Current['network_admin'] ) {

			update_site_option( $Afd->Plugin['record']['announce'] , $Data );
			wp_redirect( $url );
			exit;
			
		}

	}
	
}

endif;
