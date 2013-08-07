<?php
/*
Plugin Name: Announce from the Dashboard
Description: Announcement to the dashboard screen for users.
Plugin URI: http://wordpress.org/extend/plugins/announce-from-the-dashboard/
Version: 1.2.3.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=afd&utm_campaign=1_2_3_1
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
		$ltd_p,
		$RecordName,
		$PageSlug,
		$PluginSlug,
		$Nonces,
		$Schema,
		$UPFN,
		$Msg;


	function __construct() {
		$this->Ver = '1.2.3.1';
		$this->Name = 'Announce from the Dashboard';
		$this->Dir = plugin_dir_path( __FILE__ );
		$this->Url = plugin_dir_url( __FILE__ );
		$this->AuthorUrl = 'http://gqevu6bsiz.chicappa.jp/';
		$this->ltd = 'afd';
		$this->ltd_p = $this->ltd . '_plugin';
		$this->RecordName = 'announce_from_the_dashboard';
		$this->PageSlug = 'announce_from_the_dashboard';
		$this->PluginSlug = dirname( plugin_basename( __FILE__ ) );
		$this->Nonces = array( "field" => $this->ltd . '_form_field' , "value" => $this->ltd . '_form_value');
		$this->Schema = is_ssl() ? 'https://' : 'http://';
		$this->UPFN = 'Y';
		$this->DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
		$this->Msg = '';

		$this->PluginSetup();
		add_action( 'load-index.php' , array( $this , 'FilterStart' ) );
	}

	// PluginSetup
	function PluginSetup() {
		// load text domain
		load_plugin_textdomain( $this->ltd , false , $this->PluginSlug . '/languages' );
		load_plugin_textdomain( $this->ltd_p , false , $this->PluginSlug . '/languages' );

		// plugin links
		add_filter( 'plugin_action_links' , array( $this , 'plugin_action_links' ) , 10 , 2 );

		// add menu
		add_action( 'admin_menu' , array( $this , 'admin_menu' ) );

		// get donation toggle
		add_action( 'wp_ajax_afd_get_donation_toggle' , array( $this , 'wp_ajax_afd_get_donation_toggle' ) );

		// set donation toggle
		add_action( 'wp_ajax_afd_set_donation_toggle' , array( $this , 'wp_ajax_afd_set_donation_toggle' ) );
	}

	// Translation File Check
	function TransFileCk() {
		$file = false;
		$moFile = $this->Dir . 'languages/' . $this->ltd . '-' . get_locale() . '.mo';
		if( file_exists( $moFile ) ) {
			$file = true;
		}
		return $file;
	}

	// PluginSetup
	function plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {

			$mofile = $this->TransFileCk();
			if( $mofile == false ) {
				$translation_link = '<a href="' . $this->AuthorUrl . 'please-translation/?utm_source=use_plugin&utm_medium=side&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">Please translate</a>'; 
				array_unshift( $links, $translation_link );
			}
			$support_link = '<a href="http://wordpress.org/support/plugin/announce-from-the-dashboard" target="_blank">' . __( 'Support Forums' ) . '</a>';
			array_unshift( $links, $support_link );
			array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=' . $this->PageSlug ) . '">' . __('Settings') . '</a>' );

		}
		return $links;
	}

	// PluginSetup
	function admin_menu() {
		add_options_page( $this->Name , __( 'Announcement settings for Dashboard' , $this->ltd ), 'administrator' , $this->PageSlug , array( $this , 'setting' ) );
	}




	// SettingPage
	function setting() {
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
		$Displaytype = array( 'normal' , 'updated' , 'error' , 'metabox' , 'nonstyle' );
	
		return $Displaytype;
	}

	// SetList
	function get_color( $type ) {
		$color = '';
		if($type == 'normal') {
			$color = __( 'gray' , $this->ltd );
		} else if($type == 'updated') {
			$mp6 = is_plugin_active('mp6/mp6.php');
			if( $mp6 ) {
				$color = __( 'yellowish green' , $this->ltd );
			} else {
				$color = __( 'yellow' , $this->ltd );
			}
		} else if($type == 'error') {
			$color = __( 'red' , $this->ltd );
		} else if($type == 'metabox') {
			$color = __( 'gray' , $this->ltd );
		}

		return $color;
	}

	// SetList
	function get_user_role() {
		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $details ) {
			$UserRole[$role] = translate_user_role( $details['name'] );
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
	function DonatingCheck() {
		$Update = $this->update_validate();

		if( !empty( $Update ) ) {
			if( !empty( $_POST["donate_key"] ) ) {
				$SubmitKey = md5( strip_tags( $_POST["donate_key"] ) );
				if( $this->DonateKey == $SubmitKey ) {
					update_option( $this->ltd . '_donated' , $SubmitKey );
					$this->Msg .= '<div class="updated"><p><strong>' . __( 'Thank you for your donate.' , $this->ltd_p ) . '</strong></p></div>';
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

				update_option( $this->RecordName , $Update );
				$this->Msg = '<div class="updated"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';

			}

		}
	}

	// DataUpdate
	function update_delete() {

		$Update = $this->get_data();
		$del = false;

		if( check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			if( !empty( $_POST["action"] ) or !empty( $_POST["action2"] ) ) {
				if( $_POST["action"] == 'delete' or $_POST["action2"] == 'delete' ) {
					if( !empty( $_POST["data"]["delete"] ) ) {
						foreach( $_POST["data"]["delete"] as $ID => $v ) {
							$DeleteID = intval( $ID );
							unset( $Update[$DeleteID] );
						}
						$del = true;
					}
				}
			}
	
			if( $del ) {
				update_option( $this->RecordName , $Update );
				$this->Msg = '<div class="updated"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
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

		$UserRole = $this->get_user_role_group();
		$Data = $this->get_data( $UserRole );

		if( !empty( $Data ) ) {

			wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '.css' , array() , $this->Ver );

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
	function layout_footer( $text ) {
		$text = '<img src="' . $this->Schema . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=18" width="18" /> Plugin developer : <a href="' . $this->AuthorUrl . '?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">gqevu6bsiz</a>';
		return $text;
	}

	// FilterStart
	function DisplayDonation() {
		$donation = get_option( $this->ltd . '_donated' );
		if( $this->DonateKey != $donation ) {
			$this->Msg .= '<div class="error"><p><strong>' . __( 'Please consider a donate if you are satisfied with this plugin.' , $this->ltd_p ) . '</strong> <a href="' . $this->AuthorUrl . 'please-donation/?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">' . __( 'Please donate.' , $this->ltd_p ) . '</a></p></div>';
		}
	}

}

if( class_exists( 'Afd' ) ) {
	$Afd = new Afd();
}

?>