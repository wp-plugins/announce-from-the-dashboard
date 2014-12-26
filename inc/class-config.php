<?php

if ( !class_exists( 'Afd_Config' ) ) :

class Afd_Config
{

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'setup_config' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_record' ) );
		add_action( 'init' , array( $this , 'setup_site_env' ) );
		add_action( 'init' , array( $this , 'setup_current_env' ) );
		add_action( 'init' , array( $this , 'setup_current_user' ) );
		add_action( 'init' , array( $this , 'setup_links' ) );
		add_action( 'init' , array( $this , 'setup_third_party' ) );
		
	}

	function setup_config() {
		
		global $Afd;
		
		$Afd->Plugin['plugin_slug']  = 'announce-from-the-dashboard';
		$Afd->Plugin['dir']          = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$Afd->Plugin['name']         = 'Announce from the Dashboard';
		$Afd->Plugin['page_slug']    = str_replace( '-' , '_' , $Afd->Plugin['plugin_slug'] );
		$Afd->Plugin['url']          = plugin_dir_url( dirname( __FILE__ ) );
		$Afd->Plugin['ltd']          = 'afd';
		$Afd->Plugin['nonces']       = array( 'field' => $Afd->Plugin['ltd'] . '_field' , 'value' => $Afd->Plugin['ltd'] . '_value' );
		$Afd->Plugin['UPFN']         = 'Y';
		$Afd->Plugin['form'] = array( 'field' => $Afd->Plugin['ltd'] . '_settings' );
		$Afd->Plugin['msg_notice']   = $Afd->Plugin['ltd'] . '_msg';
		$Afd->Plugin['default_role'] = array( 'child' => 'manage_options' , 'network' => 'manage_network' );

		$Afd->Plugin['dir_admin_assets'] = $Afd->Plugin['url'] . trailingslashit( 'admin' ) . trailingslashit( 'assets' );
		
	}

	function setup_record() {
		
		global $Afd;
		
		$Afd->Plugin['record']['announce'] = $Afd->Plugin['page_slug'];
		$Afd->Plugin['record']['other'] = $Afd->Plugin['ltd'] . '_other';

	}
	
	function setup_site_env() {
		
		global $Afd;

		$Afd->Current['multisite'] = is_multisite();
		$Afd->Current['blog_id'] = get_current_blog_id();

		$Afd->Current['main_blog'] = false;

		if( $Afd->Current['blog_id'] == 1 ) {

			$Afd->Current['main_blog'] = true;

		}
		
	}

	function setup_current_env() {
		
		global $Afd;
		
		$Afd->Current['admin']         = is_admin();
		$Afd->Current['network_admin'] = is_network_admin();

		$Afd->Current['ajax']          = false;

		if( defined( 'DOING_AJAX' ) )
			$Afd->Current['ajax'] = true;
			
		$Afd->Current['schema'] = is_ssl() ? 'https://' : 'http://';

	}
	
	function setup_current_user() {
		
		global $Afd;
		
		$Afd->Current['user_login']    = is_user_logged_in();
		$Afd->Current['user_role']     = false;

		$User = wp_get_current_user();

		if( !empty( $User->roles ) ) {

			$current_roles = $User->roles;

			foreach( $current_roles as $role ) {

				$Afd->Current['user_role'] = $role;

				break;

			}

		}

		$Afd->Current['superadmin']    = false;

		if( $Afd->Current['multisite'] )
			$Afd->Current['superadmin'] = is_super_admin();


	}
	
	function setup_links() {
		
		global $Afd;
		
		$Afd->Plugin['links']['author'] = 'http://gqevu6bsiz.chicappa.jp/';
		$Afd->Plugin['links']['forum'] = 'http://wordpress.org/support/plugin/' . $Afd->Plugin['plugin_slug'];
		$Afd->Plugin['links']['review'] = 'http://wordpress.org/support/view/plugin-reviews/' . $Afd->Plugin['plugin_slug'];
		$Afd->Plugin['links']['profile'] = 'http://profiles.wordpress.org/gqevu6bsiz';
		
		if( $Afd->Current['multisite'] ) {

			$Afd->Plugin['links']['setting'] = network_admin_url( 'admin.php?page=' . $Afd->Plugin['page_slug'] );

		} else {

			$Afd->Plugin['links']['setting'] = admin_url( 'options-general.php?page=' . $Afd->Plugin['page_slug'] );

		}

	}

	function setup_third_party() {
		
		global $Afd;

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$check_plugins = array();
		
		if( !empty( $check_plugins ) ) {
			foreach( $check_plugins as $name => $base_name ) {
				if( is_plugin_active( $base_name ) )
					$Afd->ThirdParty[$name] = true;
			}
		}
		
	}

	function get_show_all_types() {

		global $Afd;

		$show_all_types = array();

		$show_all_types['normal']  = array( 'color' => __( 'Gray' , $Afd->Plugin['ltd'] ) , 'label' => __( 'Normal' , $Afd->Plugin['ltd'] ) );
		$show_all_types['updated'] = array( 'color' => __( 'Green' , $Afd->Plugin['ltd'] ) , 'label' => __( 'Update' ) );
		$show_all_types['error']  = array( 'color' => __( 'Red' , $Afd->Plugin['ltd'] ) , 'label' => __( 'Error' ) );
		$show_all_types['metabox']  = array( 'color' => __( 'Gray' , $Afd->Plugin['ltd'] ) , 'label' => __( 'Metabox' , $Afd->Plugin['ltd'] ) );
		$show_all_types['nonstyle']  = array( 'color' => '' , 'label' => __( 'Non Styles' , $Afd->Plugin['ltd'] ) );

		return $show_all_types;

	}

	function get_all_user_roles() {

		global $Afd;
		global $wp_roles;

		$UserRole = array();
		$all_user_roles = $wp_roles->roles;
		foreach ( $all_user_roles as $role => $user ) {
			$user['label'] = translate_user_role( $user['name'] );
			$UserRole[$role] = $user;
		}
		
		if( !empty( $Afd->Current['multisite'] ) && !empty( $Afd->Current['network_admin'] ) && !empty( $Afd->Current['superadmin'] ) ) {
			
			$add_caps = array( 'manage_network' , 'manage_network_users' , 'manage_network_themes' , 'manage_network_plugins' , 'manage_network_options' );
			foreach( $add_caps as $cap ) {
				$UserRole[$Afd->Current['user_role']]['capabilities'][$cap] = 1;
			}
			
		}

		return $UserRole;

	}

	function get_date_periods() {

		global $Afd;

		$Period = array( 'start' => __( 'Start' , $Afd->Plugin['ltd'] ) , 'end' => __( 'End' , $Afd->Plugin['ltd'] ) );
		
		return $Period;

	}

	function get_multisite_show_standard() {
		
		global $Afd;

		$Standard = array(
			'all' => __( 'Default show to all child-sites' , $Afd->Plugin['ltd'] ),
			'not' => __( 'Default show to not all child-sites' , $Afd->Plugin['ltd'] )
		);
		
		return $Standard;

	}
	
}

endif;
