<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       http://www.prueba1.com
 * @since      1.0.0
 *
 * @package    Team_Vcard_Generator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
global $wpdb;
//Seleccionamos todos los custom post / miembros
$get_members_ids = $wpdb->get_results("SELECT ID from {$wpdb->prefix}posts where post_type='team_member' and post_status !='auto-draft'");
foreach ($get_members_ids as $member) {
    //por cada uno borramos primero todos sus metabox/postmeta y luego a el
    $wpdb->query("DELETE from {$wpdb->prefix}postmeta where post_id='" . $member->ID . "'");
    $wpdb->query("DELETE from {$wpdb->prefix}posts where ID='" . $member->ID . "'");
}
//Borramos los settings del plugin
$wpdb->query("DELETE from {$wpdb->prefix}options where option_name='bscs_settings'");
