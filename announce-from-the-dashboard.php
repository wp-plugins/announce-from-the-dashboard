<?php
/*
Plugin Name: Announce from the Dashboard
Description: Announcement to the dashboard screen for users.
Plugin URI: http://wordpress.org/extend/plugins/announce-from-the-dashboard/
Version: 1.4
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=afd&utm_campaign=1_4
Text Domain: afd
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



if ( !class_exists( 'Afd' ) ) :

class Afd
{

	var $Plugin = array();
	var $Current = array();
	var $ThirdParty = array();

	var $ClassConfig;
	var $ClassData;
	var $ClassManager;
	var $ClassInfo;

	function __construct() {

		$inc_path = plugin_dir_path( __FILE__ );

		include_once $inc_path . 'inc/class-config.php';
		include_once $inc_path . 'inc/class-data.php';
		include_once $inc_path . 'inc/class-manager.php';
		include_once $inc_path . 'inc/class-plugin-info.php';

		$this->ClassConfig = new Afd_Config();
		$this->ClassData = new Afd_Data();
		$this->ClassManager = new Afd_Manager();
		$this->ClassInfo = new Afd_Plugin_Info();

		add_action( 'plugins_loaded' , array( $this , 'init' ) , 100 );

	}

	function init() {
		
		load_plugin_textdomain( $this->Plugin['ltd'] , false , $this->Plugin['plugin_slug'] . '/languages' );

		$this->ClassManager->init();

		add_action( 'wp_ajax_afd_sort_settings' , array( $this , 'wp_ajax_afd_sort_settings' ) );

		add_action( 'wp_ajax_afd_donation_toggle' , array( $this , 'wp_ajax_afd_donation_toggle' ) );
		
		add_action( 'load-index.php' , array( $this , 'FilterStart' ) );

	}

	// SetList
	function fields_setting( $mode = 'add' , $field = false , $val = '' , $key = false ) {
		
		global $wp_locale;
		global $Afd;

		if( !empty( $mode ) && !empty( $field ) ) {

			$f_name = sprintf( 'data[%s]' , $mode );
			if( $mode == 'update' )
				$f_name = sprintf( 'data[%1$s][%2$s]' , $mode , $key );

			$f_id = sprintf( '%s_' , $mode );
			if( $mode == 'update' )
				$f_id = sprintf( '%1$s_%2$s_' , $mode , $key );

			if( $field == 'title' ) {

				$f_name .= '[title]';
				$f_id .= 'title';
				printf( '<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" />' , $f_id , $f_name , $val );

			} elseif( $field == 'content' ) {

				$f_name .= '[content]';
				$f_id .= 'content';
				echo wp_editor( $val , $f_id , array( 'textarea_name' => $f_name , 'media_buttons' => false ) );

			} elseif( $field == 'type' ) {
				
				$f_name .= '[type]';
				$f_id .= 'type';
				printf( '<select id="%1$s" name="%2$s">' , $f_id , $f_name );
				
				$show_all_types = $this->ClassConfig->get_show_all_types();
				foreach( $show_all_types as $type_id => $type_set ) {
					printf( '<option value="%1$s" %2$s>%3$s (%4$s)</option>' , $type_id ,  selected( $type_id , $val , false ) , $type_set['label'] , $type_set['color'] );
				}
				echo '</select>';
				
			} elseif( $field == 'userrole' ) {
				
				$all_user_roles = $this->ClassConfig->get_all_user_roles();
				
				$f_name .= '[role][]';
				$f_id .= 'role';

				printf( '<select id="%1$s" name="%2$s" multiple="multiple" class="multiple_select">' , $f_id , $f_name );

				foreach( $all_user_roles as $role_name => $user_role ) {
					$role_val = false;
					if( !empty( $val[$role_name] ) )
						$role_val = $role_name;
					printf( '<option value="%1$s" %2$s /> %3$s</option>' , $role_name , selected( $role_name , $role_val , false ) , $user_role['label'] );
				}

				echo '</select>';
				
				printf( '<p class="description">%s</p>' , __( 'Hold the CTRL key and click the items in a list to choose them.' , $Afd->Plugin['ltd'] ) );

			} elseif( $field == 'date' ) {
				
				printf( '<p class="date_range_error">%s</p>' , __( 'Please <strong>End</strong> date is later than the <strong>Start</strong> date.' , $Afd->Plugin['ltd'] ) );

				$date_periods = $this->ClassConfig->get_date_periods();
				foreach( $date_periods as $name => $label ) {

					echo '<div class="date_range">';
					
					echo '<p>';
					printf( '<span class="description">%s</span> ' , $label );

					$range = 0;
					if( is_array( $val ) && !empty( $val['range'][$name] ) ) {
						$range = intval( $val['range'][$name] );
					}

					$range_check_name = $f_name . '[range][' . $name . ']';
					$range_check_id = $f_id . 'range_' . $name;
					printf( '<label><input type="checkbox" name="%1$s" id="%2$s" value="1" class="date_range_check"%3$s />%4$s</label>' , $range_check_name , $range_check_id , checked( $range , 1 , false , false ) , __( 'Specify' , $Afd->Plugin['ltd'] ) );
					echo '</p>';

					printf( '<div class="date_range_setting %s">' , $name );
					
					if( is_array( $val ) && !empty( $val['range'][$name] ) && $val['date'][$name] ) {
						$date[$name] = $val['date'][$name];
					} else {
						$date[$name] = current_time( 'mysql' );
					}
					
					$date_mm_name = $f_name . '[' . $name . '][date][mm]';
					$f_month = sprintf( '<select name="%s" class="date_mm">' , $date_mm_name );

					$mm = mysql2date( 'm', $date[$name], false );
					for ( $i = 1; $i < 13; $i = $i +1 ) {
						$monthnum = zeroise($i, 2);
						$month_label = sprintf( __( '%1$s-%2$s' ), $monthnum , $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) );
						$f_month .= sprintf( '<option value="%1$s" %2$s>%3$s</option>' , $monthnum , selected( $mm , $monthnum , false ) , $month_label );
					}
					$f_month .= '</select>';

					$date_aa_name = $f_name . '[' . $name . '][date][aa]';
					$f_year = sprintf( '<input type="text" name="%1$s" value="%2$s" size="4" maxlength="4" autocomplete="off" class="date_aa" />' , $date_aa_name , mysql2date( 'Y', $date[$name], false ) );

					$date_jj_name = $f_name . '[' . $name . '][date][jj]';
					$f_day = sprintf( '<input type="text" name="%1$s" value="%2$s" size="2" maxlength="2" autocomplete="off" class="date_jj" />' , $date_jj_name , mysql2date( 'd', $date[$name], false ) );

					$date_hh_name = $f_name . '[' . $name . '][date][hh]';
					$f_hour = sprintf( '<input type="text" name="%1$s" value="%2$s" size="2" maxlength="2" autocomplete="off" class="date_hh" />' , $date_hh_name , mysql2date( 'H', $date[$name], false ) );

					$date_mn_name = $f_name . '[' . $name . '][date][mn]';
					$f_minute = sprintf( '<input type="text" name="%1$s" value="%2$s" size="2" maxlength="2" autocomplete="off" class="date_mn" />' , $date_mn_name , mysql2date( 'i', $date[$name], false ) );

					echo '<p>';
					printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $f_month, $f_day, $f_year, $f_hour, $f_minute );
					echo '</p>';
					
					printf( '<p class="description">%1$s: %2$s</p>' , __( 'Now' , $Afd->Plugin['ltd'] ) , mysql2date( get_option( 'date_format' ) . get_option( 'time_format' ) , current_time( 'timestamp' ) ) );

					echo '</div>';
					
					echo '</div>';
					
				}

			} elseif( $field == 'standard' ) {
				
				$standard_name = $f_name . '[standard]';
				$standard_id = $f_id . 'standard';
				
				echo '<div class="select_default_subsites">';

				printf( '<select id="%1$s" name="%2$s" class="default_show">' , $standard_id , $standard_name );
				
				$standards = $this->ClassConfig->get_multisite_show_standard();

				$select_standard = 'all';
				if( !empty( $val['standard'] ) )
					$select_standard = strip_tags( $val['standard'] );
				
				foreach( $standards as $id => $label ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>' , $id ,  selected( $id , $select_standard , false ) , $label );
				}
				echo '</select>';
				
				printf( '<p class="show_subsite_description all">%s</p>' , __( 'Choose the site if you want to <strong>hide announce</strong>.' , $Afd->Plugin['ltd'] ) );
				printf( '<p class="show_subsite_description not">%s</p>' , __( 'Choose the site if you want to <strong>show announce</strong>.' , $Afd->Plugin['ltd'] ) );
				
				$susbiste_name = $f_name . '[subsites][]';
				$susbiste_id = $f_id . 'subsites';
				
				printf( '<select id="%1$s" name="%2$s" multiple="multiple" class="multiple_select">' , $susbiste_id , $susbiste_name );
					
				$all_sites = wp_get_sites();
				$select_subsited = array();
				if( !empty( $val['subsites'] ) )
					$select_subsited = $val['subsites'];
					
				foreach( $all_sites as $blog ) {
					$child_blog = get_blog_details( array( 'blog_id' => $blog['blog_id'] ) );
					$label = sprintf( '[%1$s] %2$s' , $blog['blog_id'] , $child_blog->blogname );
					printf( '<option value="%1$s" %2$s>%3$s</option>' , $blog['blog_id'] ,  selected( array_key_exists( $blog['blog_id'] , $select_subsited ) , 1 , false ) , $label );
				}
				echo '</select>';

				printf( '<p class="description">%s</p>' , __( 'Hold the CTRL key and click the items in a list to choose them.' , $Afd->Plugin['ltd'] ) );

				echo '</div>';

				
			}

		}
		
	}

	// SetList
	function update_data_format( $list ) {
		
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

	// Ajax
	function wp_ajax_afd_sort_settings() {

		if( !empty( $_POST['afd_sort'] ) && is_array( $_POST['afd_sort'] ) ) {
			
			$Data = $this->ClassData->get_data_announces();
			$NewData = array();
			
			foreach( $_POST['afd_sort'] as $key => $id ) {
				$NewData[$id] = $Data[$id];
			}
			
			if( $Data !== $NewData ) {
				
				$this->ClassData->update_sort( $NewData );
				wp_send_json_success( array( 'msg' => __( 'Saved' ) ) );

			}

		}
		
		die();

	}

	// Ajax
	function wp_ajax_afd_donation_toggle() {
		
		if( isset( $_POST['f'] ) ) {

			$val = intval( $_POST['f'] );
			$this->ClassData->update_donate_toggle( $val );

		}
		
		die();
		
	}



	// SetList
	function specify_date_check( $range_type , $data ) {

		$available = false;
		
		if( $range_type == 'start' && $data['date']['start'] ) {
			if( current_time( 'timestamp' ) > strtotime( $data['date']['start'] ) ) {
				$available = true;
			}
		}
		
		if( $range_type == 'end' && $data['date']['end'] ) {
			if( current_time( 'timestamp' ) < strtotime( $data['date']['end'] ) ) {
				$available = true;
			}
		}
		
		return $available;

	}



	// FilterStart
	function FilterStart() {

		global $Afd;
		
		if( !$Afd->Current['network_admin'] && $Afd->Current['admin'] && !$Afd->Current['ajax'] ) {
			
			$Data = $Afd->ClassData->get_user_data( $Afd->Current['user_role'] );
			if( !empty( $Data ) ) {

				add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );
				add_filter( 'admin_notices' , array( $this , 'admin_notices' ) , 99 );
				add_action( 'wp_dashboard_setup' , array( $this , 'wp_dashboard_setup' ) );
				
				// filter add
				add_filter( 'afd_apply_content', 'wptexturize'        );
				add_filter( 'afd_apply_content', 'convert_smilies'    );
				add_filter( 'afd_apply_content', 'convert_chars'      );
				add_filter( 'afd_apply_content', 'wpautop'            );
				add_filter( 'afd_apply_content', 'shortcode_unautop'  );
				add_filter( 'afd_apply_content', 'prepend_attachment' );
			
			}

		}
		
	}

	// FilterStart
	function admin_print_scripts() {
		
		global $wp_version;
		global $Afd;
		
		wp_enqueue_style( $Afd->Plugin['ltd'] , $Afd->Plugin['url'] . 'admin/assets/dashboard.css', array() , $Afd->Plugin['ver'] );
		if( version_compare( $wp_version , '3.8' , '<' ) )
			wp_enqueue_style( $Afd->Plugin['ltd'] . '-37' , $Afd->Plugin['url'] . 'admin/assets/dashboard-3.7.css', array() , $Afd->Plugin['ver'] );
		
	}

	// FilterStart
	function admin_notices() {

		global $Afd;

		$Data = $Afd->ClassData->get_user_data( $Afd->Current['user_role'] );
		
		if( !empty( $Data ) ) {
			
			foreach( $Data as $key => $announce ) {
				
				$type = strip_tags( $announce['type'] );
				if( !empty( $type ) && $type == 'updated' or $type == 'error' or $type == 'normal' or $type == 'nonstyle' ) {
	
					$class = 'announce updated ' . $type;
					echo sprintf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>' , $class , strip_tags( $announce['title'] ) , $this->afd_apply_content( $announce['content'] ) );

				}
			}
			
		}
		
	}

	// FilterStart
	function wp_dashboard_setup() {

		global $Afd;

		$Data = $Afd->ClassData->get_user_data( $Afd->Current['user_role'] );
		
		if( !empty( $Data ) ) {

			foreach( $Data as $key => $announce ) {
				
				$type = strip_tags( $announce['type'] );
				if( !empty( $type ) && $type == 'metabox' ) {

					add_meta_box( $Afd->Plugin['page_slug'] . '-' . $key , strip_tags( $announce['title'] ) , array( $this , 'dashboard_do_metabox' ) , 'dashboard' , 'normal' , '' , array( 'announce' => $key ) );

				}
			}

		}
		
	}

	// FilterStart
	function dashboard_do_metabox( $post , $metabox ) {
		
		global $Afd;

		if( isset( $metabox['args']['announce'] ) ) {
			
			$Data = $Afd->ClassData->get_user_data( $Afd->Current['user_role'] );
			if( !empty( $Data[$metabox['args']['announce']] ) ) {
				echo $this->afd_apply_content( $Data[$metabox['args']['announce']]['content'] );
			}
			
		}

	}

	// FilterStart
	function afd_apply_content( $Content ) {

		$Content = apply_filters( 'afd_apply_content' , stripslashes( $Content ) );
		
		return $Content;

	}

}

$GLOBALS['Afd'] = new Afd();

endif;
?>