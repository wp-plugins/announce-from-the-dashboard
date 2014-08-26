<?php

if ( !class_exists( 'Afd_Plugin_Info' ) ) :

class Afd_Plugin_Info
{

	var $links = array();
	var $DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
	var $DonateRecord = '';
	var $DonateOptionRecord = '';

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'setup' ) , 20 );
		
	}

	function setup() {
		
		global $Afd;
		
		$this->DonateRecord = $Afd->Plugin['ltd'] . '_donated';
		$this->DonateOptionRecord = $Afd->Plugin['ltd'] . '_donate_width';

		if( $Afd->Current['admin'] && $Afd->ClassManager->is_manager ) {

			if( !$Afd->Current['ajax'] ) {

				if( $Afd->Current['multisite'] ) {

					add_action( 'network_admin_notices' , array( $this , 'donate_notice' ) );

				} else {

					add_action( 'admin_notices' , array( $this , 'donate_notice' ) );

				}

				add_action( 'admin_init' , array( $this , 'dataUpdate' ) );

			} else {

				add_action( 'wp_ajax_afd_donation_toggle' , array( $this , 'wp_ajax_donation_toggle' ) );

			}

		}

	}

	function wp_ajax_donation_toggle() {
		
		if( isset( $_POST['f'] ) ) {

			$val = intval( $_POST['f'] );
			$this->update_donate_toggle( $val );

		}
		
		die();
		
	}

	function is_donated() {
		
		global $Afd;

		$donated = false;
		$donateKey = $this->get_donate_key( $this->DonateRecord );

		if( !empty( $donateKey ) && $donateKey == $this->DonateKey ) {
			$donated = true;
		}

		return $donated;

	}

	function donate_notice() {
		
		global $Afd;
		
		$setting_page = $Afd->ClassManager->is_settings_page();
		
		if( !empty( $setting_page ) ) {
		
			if( !empty( $_GET ) && !empty( $_GET[$Afd->Plugin['msg_notice']] ) && $_GET[$Afd->Plugin['msg_notice']] == 'donated' ) {

				printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Thank you for your donation.' , $Afd->Plugin['ltd'] ) );

			} else {

				$is_donated = $this->is_donated();
	
				if( empty( $is_donated ) )
					printf( '<div class="updated"><p><strong><a href="%1$s" target="_blank">%2$s</a></strong></p></div>' , $this->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'footer' ) ) , __( 'Please consider making a donation.' , $Afd->Plugin['ltd'] ) );
					
			}
				
		}

	}
	
	function version_checked() {

		global $Afd;

		$readme = file_get_contents( $Afd->Plugin['dir'] . 'readme.txt' );
		$items = explode( "\n" , $readme );
		$version_checked = '';
		foreach( $items as $key => $line ) {
			if( strpos( $line , 'Requires at least: ' ) !== false ) {
				$version_checked .= str_replace( 'Requires at least: ' , '' ,  $line );
				$version_checked .= ' - ';
			} elseif( strpos( $line , 'Tested up to: ' ) !== false ) {
				$version_checked .= str_replace( 'Tested up to: ' , '' ,  $line );
				break;
			}
		}
		
		return $version_checked;
		
	}

	function author_url( $args ) {
		
		global $Afd;

		$url = 'http://gqevu6bsiz.chicappa.jp/';
		
		if( !empty( $args['translate'] ) ) {
			$url .= 'please-translation/';
		} elseif( !empty( $args['donate'] ) ) {
			$url .= 'please-donation/';
		} elseif( !empty( $args['contact'] ) ) {
			$url .= 'contact-us/';
		}
		
		$url .= $this->get_utm_link( $args );

		return $url;

	}

	function get_utm_link( $args ) {
		
		global $Afd;

		$url = '?utm_source=' . $args['tp'];
		$url .= '&utm_medium=' . $args['lc'];
		$url .= '&utm_content=' . $Afd->Plugin['ltd'];
		$url .= '&utm_campaign=' . str_replace( '.' , '_' , $Afd->Plugin['ver'] );

		return $url;

	}

	private function is_donate_key_check( $key ) {
		
		$check = false;
		$key = md5( strip_tags( $key ) );
		if( $this->DonateKey == $key )
			$check = $key;

		return $check;

	}

	function get_width_class() {
		
		global $Afd;

		$class = $Afd->Plugin['ltd'];
		
		if( $this->is_donated() ) {

			$width_option = $this->get_donate_width();

			if( !empty( $width_option ) )
				$class .= ' full-width';

		}
		
		return $class;

	}
	
	function get_gravatar_src( $size = 40 ) {
		
		global $Afd;

		$img_src = $Afd->Current['schema'] . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=' . $size;

		return $img_src;

	}

	function admin_footer_text() {
		
		$author_url = $this->author_url( array( 'tp' => 'use_plugin' , 'lc' => 'footer' ) );
		$text = sprintf( '<a href="%1$s" target="_blank"><img src="%2$s" width="18" /></a>' ,  $author_url , $this->get_gravatar_src( '18' ) );
		$text .= sprintf( 'Plugin developer : <a href="%s" target="_blank">gqevu6bsiz</a>' , $author_url );

		return $text;
		
	}

	private function get_donate_key( $record ) {
		
		global $Afd;

		if( $Afd->Current['multisite'] ) {
			$donateKey = get_site_option( $record );
		} else {
			$donateKey = get_option( $record );
		}
		
		return $donateKey;

	}

	private function get_donate_width() {
		
		global $Afd;
		
		$width = false;
		if( $Afd->Current['multisite'] ) {
			$GetData = get_site_option( $this->DonateOptionRecord );
		} else {
			$GetData = get_option( $this->DonateOptionRecord );
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

				if( !empty( $_POST[$Afd->Plugin['nonces']['field'] . '_donate'] ) && check_admin_referer( $Afd->Plugin['nonces']['value'] . '_donate' , $Afd->Plugin['nonces']['field'] . '_donate' ) ) {
					
					$this->update_donate();
					
				}
				
			}

		}

	}
	
	private function update_donate() {
		
		global $Afd;

		$is_donate_check = false;
		$submit_key = false;

		if( !empty( $_POST['donate_key'] ) ) {

			$is_donate_check = $this->is_donate_key_check( $_POST['donate_key'] );

			if( !empty( $is_donate_check ) ) {

				if( !empty( $Afd->Current['multisite'] ) ) {
							
					update_site_option( $this->DonateRecord , $is_donate_check );
							
				} else {
				
					update_option( $this->DonateRecord , $is_donate_check );
		
				}

				wp_redirect( add_query_arg( $Afd->Plugin['msg_notice'] , 'donated' ) );

			}

		}

	}

	private function update_donate_toggle( $Data ) {
		
		global $Afd;

		if( $Afd->ClassManager->is_manager ) {

			if( $Afd->Current['multisite'] ) {
						
				update_site_option( $this->DonateOptionRecord , $Data );
						
			} else {
			
				update_option( $this->DonateOptionRecord , $Data );

			}
			
		}

	}

}

endif;
