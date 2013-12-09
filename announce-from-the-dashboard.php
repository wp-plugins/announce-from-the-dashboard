<?php
/*
Plugin Name: Announce from the Dashboard
Description: Announcement to the dashboard screen for users.
Plugin URI: http://wordpress.org/extend/plugins/announce-from-the-dashboard/
Version: 1.2.4
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=afd&utm_campaign=1_2_4
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





class Afd
{

	var $Ver,
		$Name,
		$Dir,
		$Url,
		$AuthorUrl,
		$ltd,
		$RecordName,
		$PageSlug,
		$PluginSlug,
		$Nonces,
		$Schema,
		$UPFN,
		$Msg,
		$MsgQ;


	function __construct() {
		$this->Ver = '1.2.4';
		$this->Name = 'Announce from the Dashboard';
		$this->Dir = plugin_dir_path( __FILE__ );
		$this->Url = plugin_dir_url( __FILE__ );
		$this->AuthorUrl = 'http://gqevu6bsiz.chicappa.jp/';
		$this->ltd = 'afd';
		$this->RecordName = 'announce_from_the_dashboard';
		$this->PageSlug = 'announce_from_the_dashboard';
		$this->PluginSlug = dirname( plugin_basename( __FILE__ ) );
		$this->Nonces = array( "field" => $this->ltd . '_form_field' , "value" => $this->ltd . '_form_value');
		$this->Schema = is_ssl() ? 'https://' : 'http://';
		$this->UPFN = 'Y';
		$this->DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
		$this->Msg = '';
		$this->MsgQ = $this->ltd . '_msg';

		$this->PluginSetup();
		add_action( 'load-index.php' , array( $this , 'FilterStart' ) );
	}

	// PluginSetup
	function PluginSetup() {
		// load text domain
		load_plugin_textdomain( $this->ltd , false , $this->PluginSlug . '/languages' );

		// plugin links
		add_filter( 'plugin_action_links' , array( $this , 'plugin_action_links' ) , 10 , 2 );

		// add menu
		add_action( 'admin_menu' , array( $this , 'admin_menu' ) );

		// data update
		add_action( 'admin_init' , array( $this , 'dataUpdate') );

		// get donation toggle
		add_action( 'wp_ajax_afd_get_donation_toggle' , array( $this , 'wp_ajax_afd_get_donation_toggle' ) );

		// set donation toggle
		add_action( 'wp_ajax_afd_set_donation_toggle' , array( $this , 'wp_ajax_afd_set_donation_toggle' ) );
	}

	// PluginSetup
	function plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {
			$link = '<a href="' . self_admin_url( 'options-general.php?page=' . $this->PageSlug ) . '">' . __( 'Settings' ) . '</a>';
			$support_link = '<a href="http://wordpress.org/support/plugin/announce-from-the-dashboard" target="_blank">' . __( 'Support Forums' ) . '</a>';
			array_unshift( $links, $link , $support_link  );

		}
		return $links;
	}

	// PluginSetup
	function admin_menu() {
		add_options_page( $this->Name , __( 'Announcement settings for Dashboard' , $this->ltd ), 'administrator' , $this->PageSlug , array( $this , 'setting' ) );
	}




	// SettingPage
	function setting() {
		$this->display_msg();
		add_filter( 'admin_footer_text' , array( $this , 'layout_footer' ) );
		$this->DisplayDonation();
		include_once 'inc/setting.php';
	}




	// SetList
	function wp_ajax_afd_get_donation_toggle() {
		echo get_option( $this->ltd . '_donate_width' );
		die();

	}

	// SetList
	function wp_ajax_afd_set_donation_toggle() {
		update_option( $this->ltd . '_donate_width' , strip_tags( $_POST["f"] ) );
		die();
	}

	// SetList
	function AllTypes() {
		global $wp_version;

		$Displaytype = array();
		$Displaytype["normal"] = array( "color" => __( 'Gray' , $this->ltd ) , "label" => __( 'Normal' , $this->ltd ) );
		$Displaytype["updated"] = array( "color" => __( 'Yellow' , $this->ltd ) , "label" => __( 'Update' ) );

		if( version_compare( $wp_version , "3.7.2" , '>' ) ) {
			$Displaytype["updated"]["color"] = __( 'Green' , $this->ltd );
		} else {
			$mp6 = is_plugin_active('mp6/mp6.php');
			if( $mp6 ) {
				$Displaytype["updated"]["color"] = __( 'Yellowish Green' , $this->ltd );
			}
		}

		$Displaytype["error"] = array( "color" => __( 'Red' , $this->ltd ) , "label" => __( 'Error' ) );
		$Displaytype["metabox"] = array( "color" => __( 'Gray' , $this->ltd ) , "label" => __( 'Metabox' , $this->ltd ) );
		$Displaytype["nonstyle"] = array( "color" => "" , "label" => __( 'Non Styles' , $this->ltd ) );

		return $Displaytype;
	}

	// SetList
	function get_user_role() {
		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $details ) {
			$UserRole[$role]["label"] = translate_user_role( $details['name'] );
		}

		return $UserRole;
	}

	// SetList
	function get_user_role_group() {
		$UserRole = '';
		$User = wp_get_current_user();
		if( !empty( $User->roles ) ) {
			foreach( $User->roles as $role ) {
				$UserRole = $role;
				break;
			}
		}
		return $UserRole;
	}




	// GetData
	function get_data( $user = false ) {
		$GetData = get_option( $this->RecordName );

		$Data = array();
		if( !empty( $GetData ) ) {
			if( !empty( $user ) ) {
				$SettData = array();
				foreach( $GetData as $k => $sett ) {
					if( array_key_exists( $user , $sett["role"] ) ) {
						$SettData[] = $sett;
					}
				}
				$Data = $SettData;
			} else {
				$Data = $GetData;
			}
		}

		return $Data;
	}




	// DataUpdate
	function dataUpdate() {

		$RecordField = false;
		
		if( !empty( $_POST["record_field"] ) ) {
			$RecordField = strip_tags( $_POST["record_field"] );
		}
		
		if( !empty( $RecordField ) && $RecordField == $this->RecordName ) {

			if( !empty( $_POST["data"]["create"] ) ) {
				$this->update();
			}

			if( !empty( $_POST["data"]["update"] ) && empty( $_POST["action"] ) && empty( $_POST["action2"] ) ) {
				$this->update();
			}

			$del = "";
			if( !empty( $_POST["action"] ) ) {
				$del = strip_tags( $_POST["action"] );
			} elseif( !empty( $_POST["action2"] ) ) {
				$del = strip_tags( $_POST["action2"] );
			}
			if( $del == 'delete' ) {
				$this->update_delete();
			}

			if( !empty( $_POST["donate_key"] ) ) {
				$this->DonatingCheck();
			}
			
		}

	}

	// DataUpdate
	function DonatingCheck() {
		$Update = $this->update_validate();

		if( !empty( $Update ) ) {
			if( !empty( $_POST["donate_key"] ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
				$SubmitKey = md5( strip_tags( $_POST["donate_key"] ) );
				if( $this->DonateKey == $SubmitKey ) {
					update_option( $this->ltd . '_donated' , $SubmitKey );
					wp_redirect( add_query_arg( $this->MsgQ , 'donated' , remove_query_arg( array( $this->MsgQ ) ) ) );
					exit;
				}
			}
		}
	}

	// DataUpdate
	function update_validate() {
		$Update = array();

		if( !empty( $_POST[$this->UPFN] ) ) {
			$UPFN = strip_tags( $_POST[$this->UPFN] );
			if( $UPFN == $this->UPFN ) {
				$Update["UPFN"] = strip_tags( $_POST[$this->UPFN] );
			}
		}

		return $Update;
	}

	// DataUpdate
	function update() {
		$Update = $this->update_validate();
		if( !empty( $Update ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {

			if( !empty( $_POST["data"]["create"] ) ) {
				
				$Create = $_POST["data"]["create"];
				if( !empty( $Create["title"] ) && !empty( $Create["content"] ) ) {
					
					$title = strip_tags ( $Create["title"] );
					$Content = $Create["content"];
					if( !empty( $Create["type"] ) ) {
						$Type = $Create["type"];
					} else {
						$Type = 'normal';
					}
					$Roles = array();
					if( !empty( $Create["role"] ) ) {
						foreach( $Create["role"] as $name => $val ) {
							$Roles[strip_tags( $name )] = intval( $val );
						}
					}
					$Update = $this->get_data();
					
					$Update[] = array( "title" => $title , "content" => $Content , "type" => $Type , "role" => $Roles );
					
				}
				
			} elseif( !empty( $_POST["data"]["update"] ) ) {
				
				foreach( $_POST["data"]["update"] as $key => $Announce ) {

					$title = strip_tags ( $Announce["title"] );
					$Content = $Announce["content"];
					if( !empty( $Announce["type"] ) ) {
						$Type = $Announce["type"];
					} else {
						$Type = 'normal';
					}
					$Roles = array();
					if( !empty( $Announce["role"] ) ) {
						foreach( $Announce["role"] as $name => $val ) {
							$Roles[strip_tags( $name )] = intval( $val );
						}
					}
					$Update[$key] = array( "title" => $title , "content" => $Content , "type" => $Type , "role" => $Roles );

				}
			}
			
			if( !empty( $Update ) ) {
				unset( $Update["UPFN"] );

				if( !empty( $Update ) ) {
					update_option( $this->RecordName , $Update );
					wp_redirect( add_query_arg( $this->MsgQ , 'update' , remove_query_arg( array( $this->MsgQ ) ) ) );
					exit;
				}

			}

		}
	}

	// DataUpdate
	function update_delete() {
		$Update = $this->get_data();
		$del = false;

		if( check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			if( !empty( $_POST["data"]["delete"] ) ) {
				foreach( $_POST["data"]["delete"] as $ID => $v ) {
					$DeleteID = intval( $ID );
					unset( $Update[$DeleteID] );
				}
				$del = true;
			}
	
			if( $del ) {
				update_option( $this->RecordName , $Update );
				wp_redirect( add_query_arg( $this->MsgQ , 'delete' , remove_query_arg( array( $this->MsgQ ) ) ) );
				exit;
			}
		}
	}






	// FilterStart
	function FilterStart() {
		if ( is_admin() ) {
			// notice
			add_filter( 'admin_notices' , array( $this , 'admin_notices' ) , 99 );
			
			// metabox
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

	// FilterStart
	function admin_notices() {
		global $wp_version;

		$UserRole = $this->get_user_role_group();
		$Data = $this->get_data( $UserRole );

		if( !empty( $Data ) ) {

			if( version_compare( $wp_version , "3.7.2" , '>' ) ) {
				wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '.css', array() , $this->Ver );
			} else {
				wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '-3.7.css', array() , $this->Ver );
			}

			foreach( $Data as $key => $sett ) {
				
				$type = $sett["type"];
				if( !empty( $type ) && $type == 'updated' or $type == 'error' or $type == 'normal' or $type == 'nonstyle' ) {

					$class = 'announce updated ' . $type;
					echo sprintf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>' , $class , strip_tags( $sett["title"] ) , $this->afd_apply_content( $sett["content"] ) );

				}
			}

		}
		
	}

	// FilterStart
	function wp_dashboard_setup() {

		$UserRole = $this->get_user_role_group();
		$Data = $this->get_data( $UserRole );

		if( !empty( $Data ) ) {

			foreach( $Data as $key => $sett ) {
				
				$type = $sett["type"];
				if( !empty( $type ) && $type == 'metabox' ) {

					add_meta_box( $this->PageSlug . '-' . $key , strip_tags( $sett["title"] ) , array( $this , 'dashboard_do_metabox' ) , 'dashboard' , 'normal' , '' , array( "announce" => $key ) );

				}
			}

		}
		
	}

	// FilterStart
	function dashboard_do_metabox( $post , $metabox ) {
		
		if( isset( $metabox["args"]["announce"] ) ) {
			
			$User = wp_get_current_user();
			$Userrole = $User->roles[0];
			
			$Data = $this->get_data( $Userrole );
			if( !empty( $Data[$metabox["args"]["announce"]] ) ) {
				echo $this->afd_apply_content( $Data[$metabox["args"]["announce"]]["content"] );
			}
			
		}

	}

	// FilterStart
	function afd_apply_content( $Content ) {
		$Content = apply_filters( 'afd_apply_content' , stripslashes( $Content ) );
		
		return $Content;
	}



	// FilterStart
	function display_msg() {
		if( !empty( $_GET[$this->MsgQ] ) ) {
			$msg = strip_tags(  $_GET[$this->MsgQ] );
			if( $msg == 'update' or $msg == 'delete' ) {
				$this->Msg .= '<div class="updated"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
			} elseif( $msg == 'donated' ) {
				$this->Msg .= '<div class="updated"><p><strong>' . __( 'Thank you for your donation.' , $this->ltd ) . '</strong></p></div>';
			}
		}
	}

	// FilterStart
	function layout_footer( $text ) {
		$text = '<img src="' . $this->Schema . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=18" width="18" /> Plugin developer : <a href="' . $this->AuthorUrl . '?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">gqevu6bsiz</a>';
		return $text;
	}

	// FilterStart
	function DisplayDonation() {
		$donation = get_option( $this->ltd . '_donated' );
		if( $this->DonateKey != $donation ) {
			$this->Msg .= '<div class="updated"><p><strong>' . __( 'Please consider a donate if you are satisfied with this plugin.' , $this->ltd ) . '</strong> <a href="' . $this->AuthorUrl . 'please-donation/?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">' . __( 'Please donation.' , $this->ltd ) . '</a></p></div>';
		}
	}

}

if( class_exists( 'Afd' ) ) {
	$Afd = new Afd();
}

?>