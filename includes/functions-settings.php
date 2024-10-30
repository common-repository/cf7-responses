<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


if( ! function_exists( 'wpcf7_add_plugin_menu_page' ) ) {
    function wpcf7_add_plugin_menu_page(){

        $subpage_responses = array(
            'page_nav' 	=> __( 'All Responses', 'text-domain' ),
            'show_submit' => false,
        );

//        $subpage_options = array(
//            'page_nav' 	=> __( 'Options', 'text-domain' ),
//            'show_submit' => true,
//        );

        $subpage_export = array(
            'page_nav' 	=> __( 'Export', 'text-domain' ),
            'show_submit' => false,
            'page_settings' => array(
                'export_section' => array(
                    'title' 	=> 	__('Export Contact form 7 responses','text-domain'),
                    'options' 	=> array(
                        array(
                            'id'		=> 'cf7r_export_list',
                            'title'		=> __('Select Form','text-domain'),
                            'details'	=> __('Required: Select a form of which you want to export responses','text-domain'),
                            'type'		=> 'select',
                            'args'		=> 'PICK_POSTS_%wpcf7_contact_form%',
                        ),
                        array(
                            'id'		=> 'cf7r_export_date_1',
                            'title'		=> __('Date Range','text-domain'),
                            'details'	=> __('Optional: Export from this date. Leave empty to ignore','text-domain'),
                            'type'		=> 'datepicker',
                            'autocomplete' => 'off',
                            'placeholder' => 'December 10, 2018',
                        ),
                        array(
                            'id'		=> 'cf7r_export_date_2',
                            'details'	=> __('Optional:  From upper date to this date. Leave empty to ignore','text-domain'),
                            'type'		=> 'datepicker',
                            'autocomplete' => 'off',
                            'placeholder' => 'December 18, 2018',
                        ),
                        array(
                            'id'		=> 'cf7r_export_button',
                        ),
                    ),
                )
            ),
        );


        $args = array(
            'add_in_menu'       => true,
            'menu_type'         => 'submenu',
            'menu_title'        => __( 'Responses', 'text-domain' ),
            'page_title'        => __( 'Contact Form 7 - Responses', 'text-domain' ),
            'menu_page_title'   => __( 'Contact Form 7 - Responses', 'text-domain' ),
            'capability'        => "manage_options",
            'parent_slug'       => "wpcf7",
            'menu_slug'         => "cf7-responses",
            'pages'             => array(
                'responses' => $subpage_responses,
//                'options'    => $subpage_options,
                'export'    => $subpage_export,
            ),
        );

        $plugin_page = new Pick_settings( $args );

        global $pagenow;

        if( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && sanitize_text_field( $_GET['post_type'] ) == 'wpcf7_responses' ) {

            wp_safe_redirect( admin_url( 'admin.php?page=' . $plugin_page->get_menu_slug() ) );
            exit();
        }
    }
}
add_action( 'plugins_loaded', 'wpcf7_add_plugin_menu_page' );



if( ! function_exists( 'cf7r_pick_settings_cf7r_export_button' ) ) {
    function cf7r_pick_settings_cf7r_export_button() {


        wp_nonce_field('cf7r_export_nonce', 'cf7r_export_nonce_value');

        echo "<input type='hidden' name='action' value='cf7r_process_download_csv' />";
        echo "<input type='submit' class='button' value='Export' />";
    }
}
add_action( 'pick_settings_cf7r_export_button', 'cf7r_pick_settings_cf7r_export_button' );


if( ! function_exists( 'cf7r_pick_settings_after_page_export' ) ) {
    function cf7r_pick_settings_after_page_export() {

        echo "</form>";
    }
}
add_action( 'pick_settings_after_page_export', 'cf7r_pick_settings_after_page_export' );



if( ! function_exists( 'cf7r_pick_settings_before_page_export' ) ) {
    function cf7r_pick_settings_before_page_export() {

        $action_url = admin_url( 'admin-ajax.php' );

        echo "<form action='$action_url' method='get'>";
    }
}
add_action( 'pick_settings_before_page_export', 'cf7r_pick_settings_before_page_export' );



if( ! function_exists( 'cf7r_pick_settings_page_responses' ) ) {
    function cf7r_pick_settings_page_responses() {

        $list_table = new CF7R_Responses_list_table();
        $list_table->prepare_items();

        echo '<form method="get" action="">';
        echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';

        $list_table->display();

        echo '</form>';
    }
}
add_action( 'pick_settings_page_responses', 'cf7r_pick_settings_page_responses' );



if( ! function_exists( 'cf7r_set_screen_options' ) ) {
    function cf7r_set_screen_options($result, $option, $value){
        $wpcf7_screens = array(
            'cf7r_response_list_items_per_page',
        );

        if (in_array($option, $wpcf7_screens)) {
            $result = $value;
        }

        return $result;
    }
}
add_filter( 'set-screen-option', 'cf7r_set_screen_options', 10, 3 );



if( ! function_exists( 'cf7r_load_responses_list_admin' ) ) {
    function cf7r_load_responses_list_admin(){

        $current_screen = get_current_screen();
        $action         = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

        if (!class_exists('CF7R_Responses_list_table')) {
            require_once(CF7R_PLUGIN_DIR . 'includes/classes/class-responses-list.php');
        }

        if( $action == 'delete' ) {

            $posts      = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : array();
            $is_deleted = true;

            foreach( $posts as $post_id ) {
                $is_deleted = wp_trash_post( $post_id );
            }

            if( $is_deleted ) cf7r_print_notices( sprintf( __('%s Response%s deleted successfully !', CF7R_TEXTDOMAIN ), count( $posts ), count( $posts ) > 1 ? 's' : '' ) );
        }

        add_filter('manage_' . $current_screen->id . '_columns', array('CF7R_Responses_list_table', 'define_columns'), 10, 0);

        add_screen_option('per_page', array('default' => 20, 'option' => 'cf7r_response_list_items_per_page'));
    }
}



if( ! function_exists( 'cf7r_load_responses_list' ) ) {
    function cf7r_load_responses_list( $hook_suffix ) {

        add_action( 'load-' . $hook_suffix, 'cf7r_load_responses_list_admin', 10, 0 );
    }
}
add_action( 'pick_settings_submenu_added_cf7-responses', 'cf7r_load_responses_list' );



if( ! function_exists( 'cf7r_update_response_data' ) ) {
    function cf7r_update_response_data( $response_id ) {
        foreach( get_posts( 'post_type=wpcf7_responses&post_per_page=-1&fields=ids' ) as $response_id ) {
            $form_id        = get_post_meta( $response_id, 'cf7r_form_id', true );
            $contact_form   = WPCF7_ContactForm::get_instance( $form_id );
            $post_content   = "";
            foreach( $contact_form->collect_mail_tags() as $form_key ) {
                $form_key_value = get_post_meta( $response_id, $form_key, true );
                if( ! empty( $form_key_value ) ) $post_content .= $form_key_value . '<br>';
            }
            wp_update_post( array( 'ID' => $response_id, 'post_content' => $post_content ) );
        }
    }
}