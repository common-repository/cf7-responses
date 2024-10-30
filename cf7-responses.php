<?php
/*
	Plugin Name: Contact Form 7 Responses
	Plugin URI: https://pluginrox.com/plugin/contact-form-7-responses/
	Description: Easy solutions to view responses from Csssontact Corm 7
	Version: 1.0.5
	Author: PluginRox
	Author URI: https://pluginrox.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class CF7_Responses{
	
	public function __construct(){

	    $this->load_defines();
        $this->load_scripts();
        $this->load_classes();
        $this->load_functions();
	}

	function load_functions() {

        require_once( CF7R_PLUGIN_DIR . 'includes/functions.php');
        require_once( CF7R_PLUGIN_DIR . 'includes/functions-settings.php');
    }

	function load_classes() {

        require_once( CF7R_PLUGIN_DIR . 'includes/classes/class-post-types.php');
        require_once( CF7R_PLUGIN_DIR . 'includes/classes/class-post-meta.php');
        require_once( CF7R_PLUGIN_DIR . 'includes/classes/class-pick-settings.php');
        require_once( CF7R_PLUGIN_DIR . 'includes/classes/class-responses-list.php');
    }

	function admin_scripts() {

        wp_enqueue_style('cf7r_admin_css', CF7R_PLUGIN_URL.'assets/admin/css/style.css');

        wp_enqueue_style('icofont', CF7R_PLUGIN_URL.'assets/fonts/icofont.min.css');
        wp_enqueue_style('tool-tip', CF7R_PLUGIN_URL.'assets/tool-tip.min.css');

        wp_enqueue_script('jquery');
        wp_enqueue_script('cf7r_admin_js', plugins_url( 'assets/admin/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
        wp_localize_script('cf7r_admin_js', 'cf7r', array(
            'cf7r_ajaxurl' => admin_url( 'admin-ajax.php')
        ) );
    }

	function load_scripts() {

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    function load_defines(){

        $this->define('CF7R_PLUGIN_URL',WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
        $this->define('CF7R_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define('CF7R_PLUGIN_FILE', plugin_basename( __FILE__ ) );
        $this->define('CF7R_TEXTDOMAIN', 'cf7-responses' );
    }

    private function define( $name, $value ){
	    if( ! defined( $name ) ) define( $name, $value );
    }

} new CF7_Responses();

