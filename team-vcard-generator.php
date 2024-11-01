<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.prueba1.com
 * @since             1.0.0
 * @package           Team_Vcard_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Team VCard generator
 * Plugin URI:        www.prueba.com
 * Description:       Team Vcard Generator lets you create a list of all members of the company and then get a personalized URL for each and a QR-Code. Visitors to your site can download the contact on their Android, Iphone or Outlook in VCF format.
 * Version:           1.1.0
 * Author:            bluminson
 * Author URI:        http://www.prueba1.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       team-vcard-generator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-team-vcard-generator-activator.php
 */
function activate_team_vcard_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-team-vcard-generator-activator.php';
	Team_Vcard_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-team-vcard-generator-deactivator.php
 */
function deactivate_team_vcard_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-team-vcard-generator-deactivator.php';
	Team_Vcard_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_team_vcard_generator' );
register_deactivation_hook( __FILE__, 'deactivate_team_vcard_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-team-vcard-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_team_vcard_generator() {

	$plugin = new Team_Vcard_Generator();
	$plugin->run();

}
run_team_vcard_generator();
