<?php

if ( !class_exists( 'Afd_Manager' ) ) :

class Afd_Manager
{

	var $is_manager = false;
	
	function __construct() {
		
		if( is_admin() )
			add_action( 'plugins_loaded' , array( $this , 'set_manager' ) , 20 );
		
	}

	function get_manager_user_role() {

		global $Afd;

		$cap = false;

		if( is_multisite() ) {

			$cap = $Afd->Plugin['default_role']['network'];

		} else {

			$cap = $Afd->Plugin['default_role']['child'];

		}
		
		$other_data = $Afd->ClassData->get_data_others();
		if( !empty( $other_data['capability'] ) )
			$cap = strip_tags( $other_data['capability'] );
		
		return $cap;

	}

	function set_manager() {
		
		$cap = $this->get_manager_user_role();
		if( current_user_can( $cap ) )
			$this->is_manager = true;
		
	}

	function init() {
		
		global $Afd;
		
		if( $Afd->Current['admin'] && $this->is_manager && !$Afd->Current['ajax'] ) {
			
			$base_plugin = trailingslashit( $Afd->Plugin['plugin_slug'] ) . $Afd->Plugin['plugin_slug'] . '.php';
			
			if( $Afd->Current['multisite'] ) {

				add_filter( 'network_admin_plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'network_admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'network_admin_notices' , array( $this , 'update_notice' ) );

			} else {

				add_filter( 'plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'admin_notices' , array( $this , 'update_notice' ) );

			}
			
			add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );

		}
		
	}

	function plugin_action_links( $links ) {

		global $Afd;
		
		$link_setting = sprintf( '<a href="%1$s">%2$s</a>' , $Afd->Plugin['links']['setting'] , __( 'Settings' ) );
		$link_support = sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , $Afd->Plugin['links']['forum'] , __( 'Support Forums' ) );

		array_unshift( $links , $link_setting, $link_support );

		return $links;

	}

	function admin_menu() {
		
		global $Afd;

		$cap = $this->get_manager_user_role();

		if( $Afd->Current['multisite'] ) {

			add_menu_page( $Afd->Plugin['name'] , __( 'Announcement settings for Dashboard' , $Afd->Plugin['ltd'] ) , $cap , $Afd->Plugin['page_slug'] , array( $this , 'views') );

		} else {

			add_options_page( $Afd->Plugin['name'] , __( 'Announcement settings for Dashboard' , $Afd->Plugin['ltd'] ) , $cap , $Afd->Plugin['page_slug'] , array( $this , 'views' ) );

		}

	}

	function is_settings_page() {
		
		global $plugin_page;
		global $Afd;
		
		$is_settings_page = false;
		$setting_pages = array( $Afd->Plugin['page_slug'] );
		
		if( in_array( $plugin_page , $setting_pages ) )
			$is_settings_page = true;
		
		return $is_settings_page;
		
	}

	function admin_print_scripts() {
		
		global $plugin_page;
		global $wp_version;
		global $Afd;
		
		if( $this->is_settings_page() ) {
			
			$ReadedJs = array( 'jquery' , 'jquery-ui-draggable' , 'jquery-ui-droppable' , 'jquery-ui-sortable' , 'thickbox' );
			wp_enqueue_script( $Afd->Plugin['page_slug'] ,  $Afd->Plugin['url'] . $Afd->Plugin['ltd'] . '.js', $ReadedJs , $Afd->Plugin['ver'] );
			add_thickbox();
			
			wp_enqueue_style( $Afd->Plugin['page_slug'] , $Afd->Plugin['url'] . $Afd->Plugin['ltd'] . '.css', array() , $Afd->Plugin['ver'] );
			if( version_compare( $wp_version , '3.8' , '<' ) )
				wp_enqueue_style( $Afd->Plugin['page_slug'] . '-37' , $Afd->Plugin['url'] . $Afd->Plugin['ltd'] . '-3.7.css', array() , $Afd->Plugin['ver'] );

			$translation = array( 'msg' => array( 'delete_confirm' => __( 'Confirm Deletion' ) ) );
			wp_localize_script( $Afd->Plugin['page_slug'] , $Afd->Plugin['ltd'] , $translation );

		}
		
	}

	function views() {

		global $Afd;
		global $plugin_page;

		if( $this->is_settings_page() ) {
			
			$manage_page_path = $Afd->Plugin['dir'] . trailingslashit( 'inc' );
			
			if( $plugin_page == $Afd->Plugin['page_slug'] ) {
				
				if( !empty( $_GET ) && !empty( $_GET['tab'] ) && $_GET['tab'] == 'other' ) {
					
					include_once $manage_page_path . 'other.php';

				} else {
					
					include_once $manage_page_path . 'setting.php';
					
				}
				
			}
			
			add_filter( 'admin_footer_text' , array( $Afd->ClassInfo , 'admin_footer_text' ) );
			
		}
		
	}
	
	function get_action_link() {
		
		global $Afd;
		
		$url = remove_query_arg( array( $Afd->Plugin['msg_notice'] , 'donated' ) );
		
		return $url;

	}
	
	function update_notice() {
		
		global $Afd;

		if( $this->is_settings_page() ) {
			
			if( !empty( $_GET ) && !empty( $_GET[$Afd->Plugin['msg_notice']] ) ) {
				
				$update_nag = $_GET[$Afd->Plugin['msg_notice']];
				
				if( $update_nag == 'update' or $update_nag == 'delete' ) {

					printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Settings saved.' ) );

				}
				
			}
			
		}
		
	}
	
	function print_nav_tab_wrapper() {
		
		global $Afd;
		
		$current = 'default';
		
		if( !empty( $_GET ) && !empty( $_GET['tab'] ) && $_GET['tab'] == 'other' )
			$current = 'other';
		
		$url_base = $Afd->Plugin['links']['setting'];
		$tabs = array(
			'default' => array( 'url' => $url_base , 'label' => __( 'Announce settings' , $Afd->Plugin['ltd'] ) ),
			'other' => array( 'url' => add_query_arg( array( 'tab' => 'other' ) , $url_base ) , 'label' => __( 'Other Settings' , $Afd->Plugin['ltd'] ) ),
		);
		
		echo '<h3 class="nav-tab-wrapper">';

		foreach( $tabs as $tab_name => $tab ) {
			$class = '';
			if( $current == $tab_name ) $class = 'nav-tab-active';
			printf( '<a href="%1$s" class="nav-tab %2$s">%3$s</a>' , $tab['url'] , $class , $tab['label'] );
		}
		
		echo '</h3>';
		
	}
	
}

endif;
