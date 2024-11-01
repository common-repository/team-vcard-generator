<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.prueba1.com
 * @since      1.0.0
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/admin
 * @author     bluminson <developer.bluminson@outlook.com>
 */
class Team_Vcard_Generator_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Inizializa la clase y establece propiedades
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Registra el css para las paginas de administracion del plugin
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name . "bootstrap", plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "mini-colors", plugin_dir_url(__FILE__) . 'css/jquery.minicolors.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "bootstrap-select", plugin_dir_url(__FILE__) . 'css/bootstrap-select.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "jquery-ui", plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/team-vcard-generator-admin.css', array(), $this->version, 'all');
    }

    /**
     * Registra el JS para las paginas de administracion del plugin
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script($this->plugin_name . "bootstrapjs", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "mini-colors", plugin_dir_url(__FILE__) . 'js/jquery.minicolors.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "jquery-ui", plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "bootstrap-select", plugin_dir_url(__FILE__) . 'js/bootstrap-select.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/team-vcard-generator-admin.js', array('jquery'), $this->version, false);
    }

}
