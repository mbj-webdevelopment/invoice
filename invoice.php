<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Invoice
 * Plugin URI:        http://localleadminer.com/
 * Description:       Easily add PayPal Invoice to your WordPress website.
 * Version:           1.0.0
 * Author:            mbj-webdevelopment
 * Author URI:        http://localleadminer.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       invoice
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if (!defined('PI_PLUGIN_DIR')) {
    define('PI_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('PI_PLUGIN_DIR_BASE')) {
    define('PI_PLUGIN_DIR_BASE', plugin_basename(__FILE__));
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-invoice-activator.php
 */
function activate_invoice() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-invoice-activator.php';
	Invoice_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-invoice-deactivator.php
 */
function deactivate_invoice() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-invoice-deactivator.php';
	Invoice_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_invoice' );
register_deactivation_hook( __FILE__, 'deactivate_invoice' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-invoice.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_invoice() {

	$plugin = new Invoice();
	$plugin->run();

}
run_invoice();
