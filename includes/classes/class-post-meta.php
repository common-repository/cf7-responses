<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class CF7R_Post_meta{
	
	public function __construct(){
		
		add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_boxes' ) );
	}

    public function display_info_box( $post ) {

        include CF7R_PLUGIN_DIR . 'templates/admin/meta-box-info.php';
    }

	public function display_meta_box( $post ) {

	    update_post_meta( $post->ID, 'cf7r_response_status', current_time('mysql') );

        include CF7R_PLUGIN_DIR . 'templates/admin/meta-box-response.php';
    }
	
	public function add_boxes( $post_type ) {

		$post_types = array( 'wpcf7_responses' );

		if( in_array( $post_type, $post_types ) ) {

			add_meta_box('cf7r_meta_box',  __( 'Response details', CF7R_TEXTDOMAIN ), array( $this, 'display_meta_box' ), $post_type,'normal','high' );
			add_meta_box('cf7r_info_box',  __( 'Response Infomation', CF7R_TEXTDOMAIN ), array( $this, 'display_info_box' ), $post_type,'side','high' );
		}
	}

	public function remove_publish_box() {
        remove_meta_box( 'submitdiv', 'wpcf7_responses', 'side' );
    }

} new CF7R_Post_meta();