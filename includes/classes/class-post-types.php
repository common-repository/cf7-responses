<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class CF7R_Post_types{
	
	public function __construct(){
		add_action( 'init', array( $this, 'register_postype_wpcf7_responses' ) );

		add_filter( 'parent_file', array( $this, 'hightlight_menu' ), 10, 1 );
		add_filter( 'submenu_file', array( $this, 'hightlight_submenu' ), 10, 1 );
	}

	function hightlight_submenu( $submenu_file ) {

        global $post;

        if( $post && $post->post_type == 'wpcf7_responses' ) return 'cf7-responses';

        return $submenu_file;
    }

	function hightlight_menu( $parent_file ) {

        global $post;

        if( $post && $post->post_type == 'wpcf7_responses' ) return 'wpcf7';

        return $parent_file;
    }
	
	public function register_postype_wpcf7_responses(){

	    if( post_type_exists( "wpcf7_responses" ) ) return;

		$singular  = __( 'Response', CF7R_TEXTDOMAIN );
		$plural    = __( 'Responses', CF7R_TEXTDOMAIN );

		register_post_type( "wpcf7_responses",
			apply_filters( "register_post_type_woc", array(
				'labels' => array(
					'name' 					=> sprintf( __( 'Contact Form 7 - All %s', CF7R_TEXTDOMAIN ), $plural ),
					'singular_name' 		=> $singular,
					'menu_name'             => __( $singular, CF7R_TEXTDOMAIN ),
					'all_items'             => sprintf( __( '%s', CF7R_TEXTDOMAIN ), $plural ),
					'add_new' 				=> __( 'Add New', CF7R_TEXTDOMAIN ),
					'add_new_item' 			=> sprintf( __( 'Add %s', CF7R_TEXTDOMAIN ), $singular ),
					'edit' 					=> __( 'Edit', CF7R_TEXTDOMAIN ),
					'edit_item' 			=> sprintf( __( 'Contact Form 7 %s Details', CF7R_TEXTDOMAIN ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', CF7R_TEXTDOMAIN ), $singular ),
					'view' 					=> sprintf( __( 'View %s', CF7R_TEXTDOMAIN ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', CF7R_TEXTDOMAIN ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', CF7R_TEXTDOMAIN ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', CF7R_TEXTDOMAIN ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', CF7R_TEXTDOMAIN ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', CF7R_TEXTDOMAIN ), $singular )
				),
				'description' => sprintf( __( 'This is where you can create and manage %s.', CF7R_TEXTDOMAIN ), $plural ),
				'public' 				=> false,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> false,
				'exclude_from_search' 	=> true,
				'hierarchical' 			=> false,
				'rewrite' 				=> false,
				'capabilities' 		    => array( 'create_posts' => 'do_not_allow' ),
				'query_var' 			=> true,
				'supports' 				=> array(''),
				'show_in_nav_menus' 	=> false,
                'show_in_menu'          => false,
			) )
		);
	}
	
} new CF7R_Post_types();