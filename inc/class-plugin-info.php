<?php

if ( !class_exists( 'Afd_Plugin_Info' ) ) :

class Afd_Plugin_Info
{

	var $links = array();
	var $DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
	var $DonateRecord = '';
	var $DonateOptionRecord = '';

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'set_links' ) , 20 );
		add_action( 'plugins_loaded' , array( $this , 'setup' ) , 20 );
		
	}

	function set_links() {
		
		global $Afd;

		$this->links['author'] = 'http://gqevu6bsiz.chicappa.jp/';
		$this->links['forum'] = 'http://wordpress.org/support/plugin/' . $Afd->Plugin['plugin_slug'];
		$this->links['review'] = 'http://wordpress.org/support/view/plugin-reviews/' . $Afd->Plugin['plugin_slug'];
		$this->links['profile'] = 'http://profiles.wordpress.org/gqevu6bsiz';
		
		if( is_multisite() ) {

			$this->links['setting'] = network_admin_url( 'admin.php?page=' . $Afd->Plugin['page_slug'] );

		} else {

			$this->links['setting'] = admin_url( 'options-general.php?page=' . $Afd->Plugin['page_slug'] );

		}
		
	}

	function setup() {
		
		global $Afd;
		
		$this->DonateRecord = $Afd->Plugin['ltd'] . '_donated';
		$this->DonateOptionRecord = $Afd->Plugin['ltd'] . '_donate_width';
		
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

	function is_donate_key_check( $key ) {
		
		$check = false;
		$key = md5( strip_tags( $key ) );
		if( $this->DonateKey == $key )
			$check = $key;

		return $check;

	}

	function is_donated() {
		
		global $Afd;

		$donated = false;
		$donateKey = $Afd->ClassData->get_donate_key( $this->DonateRecord );

		if( !empty( $donateKey ) && $donateKey == $this->DonateKey ) {
			$donated = true;
		}

		return $donated;

	}

	function get_width_class() {
		
		global $Afd;

		$class = $Afd->Plugin['ltd'];
		
		if( $this->is_donated() ) {
			$width_option = $Afd->ClassData->get_donate_width();
			if( !empty( $width_option ) ) {
				$class .= ' full-width';
			}
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

	function donate_notice() {
		
		global $Afd;
		
		$is_donated = $this->is_donated();
		if( empty( $is_donated ) )
			printf( '<div class="updated"><p><strong><a href="%1$s" target="_blank">%2$s</a></strong></p></div>' , $this->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'footer' ) ) , __( 'Please consider making a donation.' , $Afd->Plugin['ltd'] ) );

	}
	
}

endif;
