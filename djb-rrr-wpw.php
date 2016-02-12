<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://danieljblumenfeld.com/rrr-wpw/
 * @since             1.0.0
 * @package           djb-rrr-wpw
 *
 * @wordpress-plugin
 * Plugin Name:       Western PA Wheelmen RCubed Extension
 * Plugin URI:        http://danieljblumenfeld.com/rrr-wpw/
 * Description:       Extends the RCubed plugin to include information specific to the Western PA Wheelmen club
 * Version:           1.0.0
 * Author:            Dan Blumenfeld
 * Author URI:        http://danieljblumenfeld.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       djb-rrr-wpw
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_djb_rrr_wpw() {
	
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_djb_rrr_wpw() {
	
}

register_activation_hook( __FILE__, 'activate_djb_rrr_wpw' );
register_deactivation_hook( __FILE__, 'deactivate_djb_rrr_wpw' );

/**
 * The core plugin class 
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-djb-rrr-wpw.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_djb_rrr_wpw() {

	$plugin = new DJB_RRR_WPW();

}
run_djb_rrr_wpw();
