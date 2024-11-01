<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.prueba1.com
 * @since      1.0.0
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/public
 * @author     bluminson <developer.bluminson@outlook.com>
 */
class Team_Vcard_Generator_Public {

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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Team_Vcard_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Team_Vcard_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $options = get_option('bscs_settings');
        if(get_post_type(get_queried_object_id())=="team_member"){
        wp_enqueue_style($this->plugin_name . "-bootstrap", plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-design", plugin_dir_url(__FILE__) . 'css/bootstrap-material-design.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-ripples", plugin_dir_url(__FILE__) . 'css/ripples.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-jquery-mobile", plugin_dir_url(__FILE__) . 'css/jquery-mobile.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-font-awesome", plugin_dir_url(__FILE__) . 'css/font-awesome/css/font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "-plugin", plugin_dir_url(__FILE__) . 'css/team-vcard-generator-public.css', array(), $this->version, 'all');
        if ($options['estilo'] == "estilo2") {
        wp_enqueue_style($this->plugin_name . "-plugin2", plugin_dir_url(__FILE__) . 'css/estilo2.css', array(), $this->version, 'all');
        }
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Team_Vcard_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Team_Vcard_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if(get_post_type(get_queried_object_id())=="team_member"){
        wp_enqueue_script($this->plugin_name . "jquery-mobile", plugin_dir_url(__FILE__) . 'js/jquery.mobile-1.4.5.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "-design", plugin_dir_url(__FILE__) . 'js/material.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . "-ripples", plugin_dir_url(__FILE__) . 'js/ripples.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/team-vcard-generator-public.js', array('jquery'), $this->version, false);
        }
    }

}
