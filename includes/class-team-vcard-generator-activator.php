<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Fired during plugin activation
 *
 * @link       http://www.prueba1.com
 * @since      1.0.0
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/includes
 * @author     bluminson <developer.bluminson@outlook.com>
 */
class Team_Vcard_Generator_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        
        function crear_settings(){
            $opciones_por_defecto=array('mail' => 'example@company.com', 'imagen' => '', 'usar_imagen' => 'true', 'estilo' => 'estilo1', 'color1' => '#2eb8db', 'color2' => '#eb5596','url'=>'team_member');
            add_option('bscs_settings', $opciones_por_defecto, '', 'yes');
        }
        if (!get_option('bscs_settings')) {
            crear_settings();
        }
    }

}
