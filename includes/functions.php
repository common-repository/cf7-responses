<?php
/*
* @Author 		Pluginrox
* Copyright: 	2018 Pluginrox
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


if( ! function_exists( 'cf7r_show_notices_for_response_status') ) {
    function cf7r_show_notices_for_response_status(){

        $unread_responses = get_posts( array(
            'post_type'         => 'wpcf7_responses',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'       => 'cf7r_response_status',
                    'value'     => 'unread',
                    'compare'   => '=',
                )
            ),
            'fields' => 'ids'
        ) );

        if( count( $unread_responses ) > 0 ) {

            cf7r_print_notices( sprintf(
                __('You have <strong>%s unread</strong> responses. You should respond quickly to them. <a href="%s"><strong>View all unread responses</strong></a>', CF7R_TEXTDOMAIN),
                count( $unread_responses ), esc_url('admin.php?page=cf7-responses&status=unread') ), 'info');
            return;
        }
    }
}
add_action('admin_notices', 'cf7r_show_notices_for_response_status');



if( ! function_exists( 'cf7r_add_unread_class_to_admin_menu' ) ) {
    function cf7r_add_unread_class_to_admin_menu() {

        $unread_responses = get_posts( array(
            'post_type'         => 'wpcf7_responses',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'       => 'cf7r_response_status',
                    'value'     => 'unread',
                    'compare'   => '=',
                )
            ),
            'fields' => 'ids'
        ) );

        if( count( $unread_responses ) > 0 ) {

            global $menu;

            if( ! $menu || ! is_array( $menu ) ) {
            	return;
            }

            foreach ($menu as $key => $value) {
                if('Contact' == $value[0] ) $menu[$key][4] .= " cf7r-unread-responses";
            }
        }
    }
}
add_action( 'admin_init','cf7r_add_unread_class_to_admin_menu' );



if( ! function_exists( 'cf7r_process_download_csv' ) ){
    function cf7r_process_download_csv() {

        $export_nonce = isset( $_REQUEST['cf7r_export_nonce_value'] ) ? $_REQUEST['cf7r_export_nonce_value'] : '';

        if( ! wp_verify_nonce( $export_nonce, 'cf7r_export_nonce' ) ) return;

        $cf7r_export_list = isset( $_REQUEST['cf7r_export_list'] ) ? sanitize_text_field( $_REQUEST['cf7r_export_list'] ) : '';
        $cf7r_export_date_1 = isset( $_REQUEST['cf7r_export_date_1'] ) ? sanitize_text_field( $_REQUEST['cf7r_export_date_1'] ) : '';
        $cf7r_export_date_2 = isset( $_REQUEST['cf7r_export_date_2'] ) ? sanitize_text_field( $_REQUEST['cf7r_export_date_2'] ) : '';

        if( empty( $cf7r_export_list ) ) return;

        if( empty( $cf7r_export_date_1 ) ) $cf7r_export_date_2 = '';
        if( empty( $cf7r_export_date_2 ) ) $cf7r_export_date_1 = date( 'F j, Y' );

        $args = array(
            'post_type'         => 'wpcf7_responses',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'       => 'cf7r_form_id',
                    'value'     => $cf7r_export_list,
                    'compare'   => '=',
                )
            ),
        );

        if( ! empty( $cf7r_export_date_1 ) && ! empty( $cf7r_export_date_2 ) ) {

            $args['date_query'][] = array(
                'after'     => $cf7r_export_date_1,
                'before'    => $cf7r_export_date_2 . ' + 1 Day',
                'inclusive' => true,
            );
        }

        $responses      = get_posts( $args );
        $contact_form   = wpcf7_contact_form( $cf7r_export_list );

        if( ! $contact_form ) return;

        $filename = "Contact_Form_7_Responses_" . date("Y_m_d_H_i_s");

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$filename.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $out = fopen('php://output', 'w');

        fputcsv( $out,  $contact_form->collect_mail_tags() );

        foreach( $responses as $response_id ) {
            $form_key_values = array_map( function( $form_key ) use ($response_id) { return get_post_meta( $response_id, $form_key, true ); }, $contact_form->collect_mail_tags() );
            fputcsv( $out, $form_key_values );
        }

        fclose($out);
        die();
    }
}
add_action( 'wp_ajax_cf7r_process_download_csv', 'cf7r_process_download_csv' );



if( ! function_exists( 'cf7r_save_submit_response' ) ) {
    function cf7r_save_submit_response( $contact_form ) {

        $contact_form_id    = isset($_POST['_wpcf7']) ? sanitize_text_field($_POST['_wpcf7']) : '';
        $cf7r_submitted_by  = isset($_POST['_cf7r_user_id']) ? sanitize_text_field($_POST['_cf7r_user_id']) : '';
        $post_content       = "";

        if( $contact_form_id != $contact_form->id() ) return;

        $response_ID = wp_insert_post( array(
            'post_title'    => 'Response',
            'post_content'  => '',
            'post_type'     => 'wpcf7_responses',
            'post_status'   => 'publish',
        ), true );

        if( is_wp_error( $response_ID ) ) return;

        foreach ($contact_form->collect_mail_tags() as $form_key) {

            $form_key_value = isset($_POST[$form_key]) ? sanitize_text_field($_POST[$form_key]) : '';
            $post_content  .= $form_key_value . '<br>';

            update_post_meta( $response_ID, $form_key, $form_key_value );
        }

        wp_update_post( array(
            'ID'            => $response_ID,
            'post_title'    => '#' . __('Response', CF7R_TEXTDOMAIN) . '-' . $response_ID,
            'post_content'  => $post_content,
        ) );

        update_post_meta( $response_ID, 'cf7r_form_id', $contact_form->id() );
        update_post_meta( $response_ID, 'cf7r_submitted_by', $cf7r_submitted_by );
        update_post_meta( $response_ID, 'cf7r_response_status', 'unread' );
    }
}
add_action( 'wpcf7_submit', 'cf7r_save_submit_response', 10, 1 );



if( ! function_exists( 'cf7r_print_notices' ) ) {
    function cf7r_print_notices($message = '', $notice_type = 'success', $is_dismissible = true) {

        // $message         {String}    : ''
        // $notice_type     {String}    : success | error | warning
        // $is_dismissible  {Bool}      : true | false

        printf('<div class="notice notice-%s %s"><p>%s</p></div>', $notice_type, $is_dismissible ? 'is-dismissible' : '', $message);
    }
}



if( ! function_exists( 'cf7r_add_form_hidden_fields' ) ) {
    function cf7r_add_form_hidden_fields( $fields ) {

        $fields['_cf7r_user_id'] = get_current_user_id();

        return $fields;
    }
}
add_filter( 'wpcf7_form_hidden_fields', 'cf7r_add_form_hidden_fields', 10, 1 );



if( ! function_exists( 'cf7r_show_notices_for_dependencies') ) {
	function cf7r_show_notices_for_dependencies(){

		// Check Contact Form 7

		if (!defined('WPCF7_VERSION')) {
			cf7r_print_notices( sprintf(
				__('<strong>Contact Form 7</strong> Plugin is Missing. <a href="%s" target="_blank">Get Contact Form 7</a>', CF7R_TEXTDOMAIN),
				esc_url('https://wordpress.org/plugins/contact-form-7/') ), 'error');
			deactivate_plugins( CF7R_PLUGIN_FILE );
			return;
		}
	}
}
add_action('admin_notices', 'cf7r_show_notices_for_dependencies');



if( ! function_exists( 'cf7r_add_plugin_meta' ) ) {
	function cf7r_add_plugin_meta( $links, $file ){

//		if( CF7R_PLUGIN_FILE === $file ) {
//			$row_meta = array(
//				'docs'      => sprintf( __('<a href="%s"><i class="icofont-search-document"></i> Docs</a>', CF7R_TEXTDOMAIN), 'url' ),
//				'support'   => sprintf( __('<a href="%s"><i class="icofont-live-support"></i> Forum Supports</a>', CF7R_TEXTDOMAIN), 'url' ),
//				'buypro'    => sprintf( __('<a class="woc-plugin-meta-buy" href="%s"><i class="icofont-cart-alt"></i> Get Pro</a>', CF7R_TEXTDOMAIN), 'url' ),
//			);
//			return array_merge( $links, $row_meta );
//		}
		return (array) $links;
	}
}
add_filter('plugin_row_meta', 'cf7r_add_plugin_meta', 10, 2);



if( ! function_exists( 'cf7r_add_plugin_actions' ) ) {
	function cf7r_add_plugin_actions( $links ){

		$action_links = array(
			'responses' => sprintf( __('<a href="%s">View Responses</a>', CF7R_TEXTDOMAIN), admin_url('admin.php?page=cf7-responses')),
			'export' => sprintf( __('<a href="%s">Export</a>', CF7R_TEXTDOMAIN), admin_url('admin.php?page=cf7-responses&tab=export')),
		);

		return array_merge( $action_links, $links );
	}
}
add_filter('plugin_action_links_' . CF7R_PLUGIN_FILE, 'cf7r_add_plugin_actions', 10, 1);