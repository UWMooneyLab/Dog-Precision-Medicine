<?php
/**
* Plugin Name: REDCapToWordPress Dog Project
* Description: Linking WordPress user accounts to their associated REDCap Record to forward patient-driven research.
* Version: 1.0
* Author: Tim Bergquist
* Plugin URI: https://github.com/UWMooneyLab/REDCapToWordpress
* License: GPL2
*/

/*
  The code that runs during plugin activation.
  This action is documented in includes/class-redcap-activator.php
 */

function activate_redcap() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-redcap-activator.php';
    redcap_activator::activate();
}


/*
  The code that runs during plugin uninstall.
  This action is documented in includes/class-redcap-uninstall.php
 */

function uninstall_redcap() {
    require_once plugin_dir_path( _FILE_ ) . 'includes/class-redcap-uninstall.php';
    redcap_uninstaller::redcap_uninstall();
}


register_activation_hook( __FILE__, 'activate_redcap' );
register_uninstall_hook(__FILE__, 'uninstall_redcap' );


add_action('admin_menu', 'redcap_to_wordpress_menu');

function redcap_to_wordpress_menu() {
    add_menu_page( 'REDCap', "REDCap", 'manage_options', 'redcap_plugin', 'redcap_init');
}

function redcap_init() {
    ?>
    <form method="post">
        <p>REDCap API Token</p>
        <input type='text' id='token'>
        <br><br>
        <p>REDCap API URL</p>
        <input type='text' id="url">
    </form>


    <?php
}


// deletes the user from the wp_redcap database when a user is deleted on the wordpress site.
// RedCap records are not deleted or altered.
function delete_user_from_redcap( $user_id ) {
	global $wpdb;
	$user_obj = get_userdata( $user_id );
	$email = $user_obj->user_email;
	print $email;
	$wpdb -> delete( 'wp_redcap', array('email' => $email ));
}
add_action( 'delete_user', 'delete_user_from_redcap' );


//Begins user session, adds a PHP session instance into the browser upon website load.
function StartSession() {
		session_start();
}
add_action('init', 'StartSession');


//Gives the wordpress logout function PHP session erasing functionality
function EndSession() {
	session_destroy();
}
add_action('wp_logout','EndSession');


//imports the other files used in this plugin
include 'includes/screening_forms.php';
include 'includes/redcap_api.php';




// used for tracking error messages
// adding messages to this instance will allow messages to be displayed when redcap_show_error_messages() is called
function redcap_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from redcap_errors()
//globals variables are also used to track progress through the website

function redcap_show_error_messages() {
	
	if (!isset($GLOBALS['submission_errors'])) {
		$GLOBALS['submission_errors']=FALSE;
	}
}
?>