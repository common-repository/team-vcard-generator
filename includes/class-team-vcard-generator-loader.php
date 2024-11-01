<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Register all actions and filters for the plugin
 *
 * @link       http://www.prueba1.com
 * @since      1.0.0
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Team_Vcard_Generator
 * @subpackage Team_Vcard_Generator/includes
 * @author     bluminson <developer.bluminson@outlook.com>
 */
class Team_Vcard_Generator_Loader {

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {

        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {

        //<editor-fold defaultstate="collapsed" desc="Crea el menu del plugin(Administracion) y Registra el custom_post team_member + crea el post company si no existe">
        /**
         * Construye el menu del plugin y los submenus en el panel de administracion
         */
        function bscs_add_admin_menu() {
            //manage_options -> quién puede verlo, dashicons-groups ->icono propio menu wordpress 99->despues de que elemento va en la barra de administración:
            //1-Dashboard 4-Separator 5-Posts 10-Media 15-Links 20-Pages 25-Comments 59-Separator 60-Appearance 6-Plugins 70-Users 75-Tools 80-Settings 99-Separator
            add_menu_page(__('Team member', 'team-vcard-generator'), 'VCard', 'manage_options', 'team_member', null, 'dashicons-groups', 99);

            add_submenu_page('team_member', __('Add new', 'team-vcard-generator'), __('Add new', 'team-vcard-generator'), 'manage_options', 'new_team_member', 'bscs_new_member');

            add_submenu_page('team_member', __('Company', 'team-vcard-generator'), __('Company', 'team-vcard-generator'), 'manage_options', 'company_team_member', 'bscs_company');
            //null porque no queremos un submenu editar en nuestro menu, solo accedemos al por url desde la lista al editar
            add_submenu_page(null, __('Edit', 'team-vcard-generator'), __('Edit', 'team-vcard-generator'), 'manage_options', 'edit_team_member', 'bscs_edit_member');

            add_submenu_page('team_member', __('Settings', 'team-vcard-generator'), __('Settings', 'team-vcard-generator'), 'manage_options', 'settings_team_member', 'bscs_options_page');

            //Elimina el submenu de administracion del post custom que nos crea wordpress por defecto, ya que tenemos uno propio
            remove_submenu_page('team_member', 'team_member');
        }

        add_action('admin_menu', 'bscs_add_admin_menu');

        /**
         * Registra el custom post team_member
         */
        function register_post_custom() {
            $url = 'team_member';
            $options = get_option('bscs_settings');
            if ($options['url'] != "") {
                $url = $options['url'];
            }
            $args = array(
                'supports' => array('title', 'thumbnail'),
                'public' => true,
                'show_in_menu' => false,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-groups',
                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'has_archive' => true,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => array('slug' => $url, 'with_front' => false),
                'capability_type' => 'post');
            register_post_type('team_member', $args);
            flush_rewrite_rules();
        }

        add_action('init', 'register_post_custom');

        //QUITA JS Y ESTILOS DEL PLUGIN(tarjeta/frontend) en las demas paginas, quita JSyCSS externos en la tarjeta
        function remove_default_styles() {
            if (get_post_type(get_queried_object_id()) == "team_member") {
                //recoge todos los css
                global $wp_styles, $wp_scripts;
                // loop que borra los que no son los que queremos
                foreach ($wp_styles->registered as $handle => $data) {
                    if (!in_array($handle, array('admin-bar', 'open-sans', 'dashicons', 'team-vcard-generator-bootstrap', 'team-vcard-generator-design', 'team-vcard-generator-ripples', 'team-vcard-generator-jquery-mobile', 'team-vcard-generator-font-awesome', 'team-vcard-generator-plugin', 'team-vcard-generator-plugin2'))) {
                        wp_deregister_style($handle);
                        wp_dequeue_style($handle);
                    }
                }
                foreach ($wp_scripts->queue as $handle => $data) {
                    if (!in_array($handle, array('team-vcard-generatorjquery-mobile', 'team-vcard-generator-design', 'team-vcard-generator-ripples', 'team-vcard-generator'))) {
                        wp_deregister_script($handle);
                        wp_dequeue_script($handle);
                    }
                }
            } else {
                global $wp_styles, $wp_scripts;
                foreach ($wp_styles->registered as $handle => $data) {
                    if (in_array($handle, array('admin-bar', 'open-sans', 'dashicons', 'team-vcard-generator-bootstrap', 'team-vcard-generator-design', 'team-vcard-generator-ripples', 'team-vcard-generator-jquery-mobile', 'team-vcard-generator-font-awesome', 'team-vcard-generator-plugin', 'team-vcard-generator-plugin2'))) {
                        wp_deregister_style($handle);
                        wp_dequeue_style($handle);
                    }
                }
                foreach ($wp_scripts->registered as $handle => $data) {
                    if (!in_array($handle, array('team-vcard-generatorjquery-mobile', 'team-vcard-generator-design', 'team-vcard-generator-ripples', 'team-vcard-generator'))) {
                        wp_deregister_script($handle);
                        wp_dequeue_script($handle);
                    }
                }
            }
        }

        add_action('wp_print_styles', 'remove_default_styles', 99999);
        
        //LO mismo que la anterior pero para el panel de administracion
        function remove_default_styles_admin() {
            if (!in_array($_GET['page'], array('new_team_member','settings_team_member','edit_team_member','manage_team_member','company_team_member'))) {
                //recoge todos los css
                global $wp_styles, $wp_scripts;
                // loop que borra los que no son los que queremos
                foreach ($wp_styles->registered as $handle => $data) {
                    if (in_array($handle, array('team-vcard-generatorbootstrap', 'team-vcard-generatormini-colors', 'team-vcard-generatorbootstrap-select', 'team-vcard-generatorjquery-ui', 'team-vcard-generator'))) {
                        wp_deregister_style($handle);
                        wp_dequeue_style($handle);
                    }
                }
                foreach ($wp_scripts->queue as $handle => $data) {
                    if (in_array($handle, array('team-vcard-generatorbootstrapjs', 'team-vcard-generatormini-colors', 'team-vcard-generatorbootstrap-select', 'team-vcard-generatorjquery-ui', 'team-vcard-generator'))) {
                        wp_deregister_script($handle);
                        wp_dequeue_script($handle);
                    }
                }
            }
        }

        add_action('admin_enqueue_scripts', 'remove_default_styles_admin', 99999);

        //Si no existe el post de company nos lo crea vacio
        function registra_company() {
            global $wpdb;
            $existe_company = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_excerpt='vcard_company'");
            if (!$existe_company > 0) {
                $actual_id_usuario = wp_get_current_user()->ID;
                $post = array(
                    'post_title' => 'company',
                    'post_content' => '{"firstname":"company","lastname":"","mail_profesional":"","mail_otro":"","telefono_profesional":"","telefono_otro":"","direccion":{"calle":"","numero":"","ciudad":"","provincia":"","zip":""},"nota":"","cargo":"","links":[],"textos":[],"widget":{"twitter":"","facebook":""},"photo":"","horario":[],"usar_padre":[],"tipo":"vcard_company"}',
                    'post_status' => 'publish',
                    'post_author' => $actual_id_usuario,
                    'post_type' => 'team_member',
                    'post_excerpt' => 'vcard_company'
                );
                // Nos insterta el post y nos crea el QR-Code
                include 'phpqrcode/qrlib.php';
                $id_empresa = wp_insert_post($post);
                QRcode::png(get_permalink($id_empresa), plugin_dir_path(__FILE__) . 'vcf/company-' . $id_empresa . '.png', 'L', 30, 10);
            }
        }

        add_action('init', 'registra_company');

        //</editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Redirecciona a nuestras plantillas en caso de que el post_type sea team_member"> 
        add_filter('single_template', 'my_custom_template');

        /**
         * Asigna la plantilla que tenemos en el plugin a los POST tipo Team_Member
         * 
         * @return type
         */
        function my_custom_template($single) {
            global $wp_query, $post;
            if ($post->post_type == "team_member") {
                $options = get_option('bscs_settings');
                $template = "/single-team_member.php";
                if (file_exists(dirname(__FILE__) . $template)) {
                    return dirname(__FILE__) . $template;
                }
            }
            return $single;
        }

        add_filter('archive_template', 'my_custom_template2');

        /**
         * Asigna la plantilla a el archivo POST(localhost/team_member/) para que nos muestre el listado del miembros
         * 
         * @return type
         */
        function my_custom_template2($archive) {
            global $wp_query, $post;
            /* Checks for single template by post type */
            if ($post->post_type == "team_member") {
                $options = get_option('bscs_settings');
                $template = "/archive-team_member.php";
                if (file_exists(dirname(__FILE__) . $template)) {
                    return dirname(__FILE__) . $template;
                }
            }
            return $archive;
        }

        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Contenido paginas Editar / Nuevo / Company de la administración del plugin"> 
        /**
         * Contenido de la página NUEVO miembro en Administración
         */
        function bscs_new_member($post) {
            global $wpdb;
            //En caso de que ejecutemos el form nos guarda los datos
            if (isset($_POST["firstname"]) && $_POST["firstname"] != "" && ($_POST["mail_profesional"] != "" || $_POST["mail_personal"] != "" || $_POST["mail_otro"] != "")) {
                $numero_miembros = $wpdb->get_var("SELECT count(*) from {$wpdb->prefix}posts where post_type='team_member' and post_status !='auto-draft' and post_excerpt='vcard_individual'");
                if ($numero_miembros <= 10) {
                    //Definimos los datos del json
                    if (isset($_POST['links'])) {
                        $array_links_insertar = array();
                        foreach ($_POST['links'] as $link) {
                            //echo "key:".$key." val:".$val;
                            //print_r($link);
                            if (sanitize_text_field($link['direccion']) != "" && sanitize_text_field($link['sitio']) != "no") {
                                array_push($array_links_insertar, array('sitio' => sanitize_text_field($link['sitio']), 'direccion' => sanitize_text_field($link['direccion'])));
                            }
                        }
                    } else {
                        $array_links_insertar = array();
                    }

                    if (isset($_POST['textos'])) {
                        $array_textos_insertar = array();
                        foreach ($_POST['textos'] as $texto) {
                            //echo "key:".$key." val:".$val;
                            if (sanitize_text_field($texto['titulo']) != "" && (sanitize_text_field($texto['texto']) != "" || sanitize_text_field($texto['imagen']) != "")) {
                                array_push($array_textos_insertar, array('titulo' => sanitize_text_field($texto['titulo']), 'texto' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($texto['texto'], true))), 'imagen' => sanitize_text_field($texto['imagen'])));
                            }
                        }
                    } else {
                        $array_textos_insertar = array();
                    }
                    if (isset($_POST['horario'])) {
                        $horario = array();
                        foreach ($_POST['horario'] as $texto => $value) {
                            $inicio = $value[inicio];
                            $cierre = $value[cierre];
                            if (count($value[dias]) > 0) {
                                foreach ($value[dias] as $dia) {
                                    array_push($horario, array($dia, $inicio, $cierre));
                                }
                            }
                        }
                    } else {
                        $horario = array();
                    }
                    if (isset($_POST['usar_padre'])) {
                        $padres = array();
                        foreach ($_POST['usar_padre'] as $key => $value) {
                            array_push($padres, $key);
                        }
                    } else {
                        $padres = array();
                    }
                    $numero_miembros = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type='team_member'");
                    //valores para crear el post, directamente publicado
                    $titulo = sanitize_text_field($_POST["firstname"]) . " " . sanitize_text_field($_POST["lastname"]);
                    $padre;
                    $tipo = sanitize_text_field($_POST['tipo_miembro']);
                    if ($tipo == "individual") {
                        $padre = (int) sanitize_text_field($_POST['tipo_miembro_padre']);
                        $tipo = "vcard_individual";
                    } else {
                        $padre = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts where post_excerpt='vcard_company'");
                        $tipo = "vcard_office";
                    }
                    $_POST['widget_twitter'] = preg_replace("/\r|\n/", "", $_POST['widget_twitter']);
                    $widget_twitter_arreglado = str_replace("</a>rn<script>", "</a><script>", $_POST['widget_twitter']);
                    $actual_id_usuario = wp_get_current_user()->ID;
                    //JSON DATOS
                    $arr = array('firstname' => sanitize_text_field($_POST['firstname']),
                        'lastname' => sanitize_text_field($_POST['lastname']),
                        'mail_profesional' => sanitize_text_field($_POST['mail_profesional']),
                        'mail_personal' => sanitize_text_field($_POST['mail_personal']),
                        'mail_otro' => sanitize_text_field($_POST['mail_otro']),
                        'telefono_profesional' => sanitize_text_field($_POST['telefono_profesional']),
                        'telefono_personal' => sanitize_text_field($_POST['telefono_personal']),
                        'telefono_otro' => sanitize_text_field($_POST['telefono_otro']),
                        'direccion' => array('calle' => sanitize_text_field($_POST['address_calle']), 'numero' => sanitize_text_field($_POST['address_numero']), 'ciudad' => sanitize_text_field($_POST['address_ciudad']), 'provincia' => sanitize_text_field($_POST['address_provincia']), 'zip' => sanitize_text_field($_POST['address_zip'])),
                        'nota' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($_POST['nota'], true))),
                        'cargo' => sanitize_text_field($_POST['cargo']),
                        'links' => $array_links_insertar,
                        'textos' => $array_textos_insertar,
                        'widget' => array('twitter' => $widget_twitter_arreglado, 'facebook' => sanitize_text_field($_POST['widget_facebook'])),
                        'photo' => sanitize_text_field($_POST['photo']),
                        'horario' => $horario,
                        'usar_padre' => $padres,
                        'tipo' => $tipo
                    );
                    $descripcion_json = json_encode($arr, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
                    $post = array(
                        'post_title' => $titulo,
                        'post_name' => $titulo,
                        'post_content ' => $descripcion_json,
                        'post_status' => 'publish',
                        'post_author' => $actual_id_usuario,
                        'post_type' => 'team_member',
                        'post_parent' => $padre,
                        'post_excerpt' => $tipo
                    );
                    // Intento de insetar el nuevo post, si falla nos muestra un error
                    if ($id = wp_insert_post($post)) {
                        // Si lo inserta crea todos los metabox asociados y les mete el contenido y genera el QR Code
                        include 'phpqrcode/qrlib.php';
                        QRcode::png(get_permalink($id), plugin_dir_path(__FILE__) . 'vcf/' . sanitize_text_field($_POST['firstname']) . '-' . $id . '.png', 'L', 30, 10);
                        //metabox
                        $update_args = array(
                            'ID' => $id,
                            'post_content' => $descripcion_json);
                        $result = wp_update_post($update_args);
                        //global $wpdb;
                        update_post_meta($id, 'firstname', sanitize_text_field($_POST['firstname']));
                        update_post_meta($id, 'lastname', sanitize_text_field($_POST['lastname']));
                        update_post_meta($id, 'mail', sanitize_text_field($_POST['mail_profesional']));
                        echo "<div class='alert alert-success fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Member created!', 'team-vcard-generator') . "</strong> " . __("The member was created correctly", "team-vcard-generator") . "</div>";
                    } else
                    //en caso de que no funcione el insert
                        echo "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Member uncreated!', 'team-vcard-generator') . "</strong> " . __("Couldn't create the member because an error", "team-vcard-generator") . "</div>";
                }else {
                    echo "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Member uncreated!', 'team-vcard-generator') . "</strong> " . __("You have reached the maximum allowed members (10)", "team-vcard-generator") . "</div>";
                }
            } else if (isset($_POST['firstname'])) {
                echo "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Member uncreated!', 'team-vcard-generator') . "</strong> " . __("The minimum required data to register are the first name and a email", "team-vcard-generator") . "</div>";
            }
            //HTML de la pagina
            ?>
            <div class="wrap" id="team_member">
                <form action='' method='post' class="bscs" name="formulario">
                    <h2><?php _e('New member', 'team-vcard-generator'); ?>
                        <a class="add-new-h2" href="?page=manage_team_member"><?php _e('Return', 'team-vcard-generator') ?></a>
                        <a class="add-new-h2 submit_formulario" href="#"><?php _e('Save', 'team-vcard-generator') ?></a>
                    </h2>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1"><?php _e('Details', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-2"><?php _e('Content ', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-3"><?php _e('Links / Social networks', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-4"><?php _e('Schedule', 'team-vcard-generator'); ?></a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="tipo_tarjeta">
                                <p><?php _e('Type of member', 'team-vcard-generator') ?></p>
                                <input type="radio" name="tipo_miembro" value="individual" checked><?php _e('Individual', 'team-vcard-generator') ?>
                                <input type="radio" name="tipo_miembro" value="office"><?php _e('Branch Office ', 'team-vcard-generator') ?>
                                <p id="lista_padres"><?php _e('Select the parent: ', 'team-vcard-generator'); ?>
                                    <select name="tipo_miembro_padre"> 
                                        <?php
                                        $posibles_padres_company = $wpdb->get_results("SELECT ID,post_title,post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' AND post_excerpt='vcard_company'");
                                        foreach ($posibles_padres_company as $opcion) {
                                            echo "<option value='" . $opcion->ID . "' selected>" . __('Company', 'team-vcard-generator') . ": " . $opcion->post_title . "</option>";
                                        }
                                        $posibles_padres_offices = $wpdb->get_results("SELECT ID,post_title,post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' AND post_excerpt='vcard_office'");
                                        foreach ($posibles_padres_offices as $opcion) {
                                            echo "<option value='" . $opcion->ID . "'>" . __('Office', 'team-vcard-generator') . ": " . $opcion->post_title . "</option>";
                                        }
                                        echo "<option value='none'>" . __('None', 'team-vcard-generator') . "</option>";
                                        ?>
                                    </select></p>
                            </div>
                            <?php
                            $options = get_option('bscs_settings');
                            ?>
                            <div class='row'>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Details', 'team-vcard-generator'); ?></h3>
                                    <p>
                                        <label for="firstname"><?php _e('Firstname: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="firstname" id="firstname" />
                                    </p>
                                    <p>
                                        <label for="lastname"><?php _e('Lastname: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="lastname" id="lastname"/>
                                    </p>
                                    <p>
                                        <label for="mail_profesional"><?php _e('Email address (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_profesional" id="mail_profesional"/>
                                    </p>
                                    <p>
                                        <label for="mail_personal"><?php _e('Email address (Home): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_personal" id="mail_personal"/>
                                    </p>
                                    <p>
                                        <label for="mail_otro"><?php _e('Email address (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_otro" id="mail_otro"/>
                                    </p>
                                    <p>
                                        <label for="telefono_profesional"><?php _e('Phone number (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_profesional" id="telefono_profesional"/>
                                    </p>
                                    <p>
                                        <label for="telefono_personal"><?php _e('Phone number (Home): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_personal" id="telefono_personal"/>
                                    </p>
                                    <p>
                                        <label for="telefono_otro"><?php _e('Phone number (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_otro" id="telefono_otro" />
                                    </p>
                                    <p>
                                        <label for="cargo"><?php _e('Charge: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="cargo" id="cargo"/> 
                                    </p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Address', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[direccion]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p><label for="address_calle"><?php _e('Street: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_calle" id="address_calle" value="<?php echo esc_attr($options['bscs_calle']); ?>"/></p>
                                        <p><label for="address_numero"><?php _e('Number: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_numero" id="address_numero" value="<?php echo esc_attr($options['bscs_numero']); ?>"/></p>
                                        <p><label for="address_ciudad"><?php _e('City: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_ciudad" id="address_ciudad" value="<?php echo esc_attr($options['bscs_ciudad']); ?>"/></p>
                                        <p><label for="address_provincia"><?php _e('State: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_provincia" id="address_provincia" value="<?php echo esc_attr($options['bscs_provincia']); ?>"/></p>
                                        <p><label for="address_zip"><?php _e('Zip code: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_zip" id="address_zip" value="<?php echo esc_attr($options['bscs_zip']); ?>"/></p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Note', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[nota]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p>
                                            <textarea name='nota' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e('Note in contact download', 'team-vcard-generator') ?>'><?php echo esc_textarea($options[bscs_nota]); ?></textarea>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Photo', 'team-vcard-generator'); ?></h3>
                                    <p>
                                        <input type="text" name="photo" id="photo" readonly/><a href="#" class="button" id="insert-my-media"><?php _e('Select', 'team-vcard-generator'); ?></a>
                                    </p>
                                    <p><img src="<?php
                                        if ($options['usar_imagen'] == 'true') {
                                            echo esc_attr($options['imagen']);
                                        }
                                        ?>" alt="" id="preview"></img></p>
                                </div>
                            </div>
                        </div>
                        <div id='tabs-3'>
                            <div class='row'>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Links', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[links]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p class='inputlink'><label><select name='links[0][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[0][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                        <p class='inputlink'><label><select name='links[1][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[1][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                        <p class='inputlink'><label><select name='links[2][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[2][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                        <p><button id="morelinks"><?php _e('Add links', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Widgets', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[widget]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p class="widget"><label class='titulo_widget'>Twitter </label><a class="add-new-h2" target="_blank" href="https://twitter.com/settings/widgets/new"><?php _e('CREATE', 'team-vcard-generator'); ?></a></p>
                                        <span><?php _e('Create and paste the html inside the below input', 'team-vcard-generator'); ?></span>
                                        <textarea name='widget_twitter' wrap="soft" cols="20" rows="6"></textarea>
                                        <p></p>
                                        <p class="widget"><label class='titulo_widget'>Facebook</label><input name='widget_facebook' type='text'></p>
                                        <span><?php _e('Put only the link, example: https://www.facebook.com/Microsoft/ ', 'team-vcard-generator'); ?></span>
                                    </div></div>
                            </div>
                        </div><div id='tabs-2'>
                            <div class='row'>
                                <div class="col-lg-6 col-md-6">
                                    <h3><?php _e('Content', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[textos]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[1][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e(' Content - max 2000 characters', 'team-vcard-generator') ?>'></textarea><img class='fotos_textos_preview'></p>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[2][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e(' Content - max 2000 characters', 'team-vcard-generator') ?>'></textarea><img class='fotos_textos_preview'></p>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[3][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e(' Content - max 2000 characters', 'team-vcard-generator') ?>'></textarea><img class='fotos_textos_preview'></p>
                                        <p><button id="moretexts"><?php _e('Add content', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <h3><?php _e('Offers', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[ofertas]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[1][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select  image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[2][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[3][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                        <p><button id="moreofertas"><?php _e('Add offer', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id='tabs-4'>
                            <div class='row'>
                                <div class="col-lg-12 col-md-12">
                                    <h3><?php _e('Schedule', 'team-vcard-generator'); ?></h3>
                                    <?php
                                    _e('You can define the days in groups and personalize the horary of each group of days, in each group you can set multiples time slots (+)', 'team-vcard-generator');
                                    echo "<p></p>";
                                    ?>
                                    <p><input type='checkbox' name='usar_padre[horario]' class='cambiar_como_padre'><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre'>
                                        <?php for ($x = 0; $x < 7; $x++) { ?>
                                            <p class="lista_dia"<?php
                                            if ($x > 0) {
                                                echo "style='display:none'";
                                            }
                                            ?>>
                                                <select class="selectpicker" data-style="btn-primary" name='horario[<?php echo $x; ?>][dias][]' multiple data-width="fit" title="<?php _e('Select the days', 'team-vcard-generator'); ?>">
                                                    <option value="0"><?php _e('Monday', 'team-vcard-generator'); ?></option>
                                                    <option value="1"><?php _e('Tuesday', 'team-vcard-generator'); ?></option>
                                                    <option value="2"><?php _e('Wednesday', 'team-vcard-generator'); ?></option>
                                                    <option value="3"><?php _e('Thursday', 'team-vcard-generator'); ?></option>
                                                    <option value="4"><?php _e('Friday', 'team-vcard-generator'); ?></option>
                                                    <option value="5"><?php _e('Saturday', 'team-vcard-generator'); ?></option>
                                                    <option value="6"><?php _e('Sunday', 'team-vcard-generator'); ?></option>
                                                </select>
                                                <span class="calendar_franja"><input type="time" class="calendar_inicio" name="horario[<?php echo $x; ?>][inicio][]" value="00:00"> <?php _e('to', 'team-vcard-generator'); ?> <input type="time" class="calendar_final" name="horario[<?php echo $x; ?>][cierre][]" value="00:00"></span><button class="btn btn-default btn-danger lesshour">-</button><button class="btn btn-default btn-success morehour">+</button><button class="btn btn-default lesscalendar"><?php _e('Delete this group', 'team-vcard-generator'); ?></button></p>
            <?php } //@todo Festivos(cerrado/abierto)?    ?>
                                        <button class="btn btn-default" id="morecalendar"><?php _e('Add group', 'team-vcard-generator'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  
            </div>
            <?php
        }

        /**
         * Pagina para editar usuarios, parecida a la de nuevo pero lee variables creadas para llenar los metabox del formulario
         */
        function bscs_edit_member($post) {
            $id = $_GET['member'];
            global $wpdb;
            //Si se ejecuta el form actualizamos datos en BD
            if ($_POST["firstname"] != "" && ($_POST["mail_profesional"] != "" || $_POST["mail_personal"] != "" || $_POST["mail_otro"] != "")) {
                //Definimos los datos del json
                if (isset($_POST['links'])) {
                    $array_links_insertar = array();
                    foreach ($_POST['links'] as $link) {
                        //echo "key:".$key." val:".$val;
                        //print_r($link);
                        if (sanitize_text_field($link['direccion']) != "" && sanitize_text_field($link['sitio']) != "no") {
                            array_push($array_links_insertar, array('sitio' => sanitize_text_field($link['sitio']), 'direccion' => sanitize_text_field($link['direccion'])));
                        }
                    }
                }
                if (isset($_POST['textos'])) {
                    $array_textos_insertar = array();
                    foreach ($_POST['textos'] as $texto) {
                        if (sanitize_text_field($texto['titulo']) != "" && (sanitize_text_field($texto['texto']) != "" || sanitize_text_field($texto['imagen']) != "")) {
                            array_push($array_textos_insertar, array('titulo' => sanitize_text_field($texto['titulo']), 'texto' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($texto['texto'], true))), 'imagen' => sanitize_text_field($texto['imagen'])));
                        }
                    }
                }
                if (isset($_POST['ofertas'])) {
                    $array_ofertas_insertar = array();
                    foreach ($_POST['ofertas'] as $oferta) {
                        if (sanitize_text_field($oferta['titulo']) != "" && (sanitize_text_field($oferta['texto']) != "" || sanitize_text_field($oferta['imagen']) != "")) {
                            array_push($array_ofertas_insertar, array('titulo' => sanitize_text_field($oferta['titulo']), 'texto' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($oferta['texto'], true))), 'imagen' => sanitize_text_field($oferta['imagen'])));
                        }
                    }
                }
                if (isset($_POST['horario'])) {
                    $horario = array();
                    foreach ($_POST['horario'] as $texto => $value) {
                        //echo "key:".$key." val:".$val;
                        $inicio = $value[inicio];
                        $cierre = $value[cierre];
                        if (count($value[dias]) > 0) {
                            foreach ($value[dias] as $dia) {
                                array_push($horario, array($dia, $inicio, $cierre, $dia));
                            }
                        }
                    }
                }
                if (isset($_POST['usar_padre'])) {
                    $padres = array();
                    foreach ($_POST['usar_padre'] as $key => $value) {
                        array_push($padres, $key);
                    }
                } else {
                    $padres = array();
                }
                $padre;
                $tipo = sanitize_text_field($_POST['tipo_miembro']);
                if ($tipo == "individual") {
                    $padre = (int) sanitize_text_field($_POST['tipo_miembro_padre']);
                    $tipo = "vcard_individual";
                } else {
                    $padre = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts where post_excerpt='vcard_company'");
                    $tipo = "vcard_office";
                }
                $widget_twitter_arreglado = str_replace("</a>rn<script>", "</a><script>", $_POST['widget_twitter']);
                $arr = array('firstname' => sanitize_text_field($_POST['firstname']),
                    'lastname' => sanitize_text_field($_POST['lastname']),
                    'mail_profesional' => sanitize_text_field($_POST['mail_profesional']),
                    'mail_personal' => sanitize_text_field($_POST['mail_personal']),
                    'mail_otro' => sanitize_text_field($_POST['mail_otro']),
                    'telefono_profesional' => sanitize_text_field($_POST['telefono_profesional']),
                    'telefono_personal' => sanitize_text_field($_POST['telefono_personal']),
                    'telefono_otro' => sanitize_text_field($_POST['telefono_otro']),
                    'direccion' => array('calle' => sanitize_text_field($_POST['address_calle']), 'numero' => sanitize_text_field($_POST['address_numero']), 'ciudad' => sanitize_text_field($_POST['address_ciudad']), 'provincia' => sanitize_text_field($_POST['address_provincia']), 'zip' => sanitize_text_field($_POST['address_zip'])),
                    'nota' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($_POST['nota'], true))),
                    'cargo' => sanitize_text_field($_POST['cargo']),
                    'links' => $array_links_insertar,
                    'textos' => $array_textos_insertar,
                    'ofertas' => $array_ofertas_insertar,
                    'widget' => array('twitter' => $widget_twitter_arreglado, 'facebook' => sanitize_text_field($_POST['widget_facebook'])),
                    'photo' => sanitize_text_field($_POST['photo']),
                    'horario' => $horario,
                    'usar_padre' => $padres,
                    'tipo' => $tipo
                );
                $descripcion_json = json_encode($arr, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);

                //Actualiza el titulo y valores del post
                $titulo = sanitize_text_field($_POST["firstname"]) . " " . sanitize_text_field($_POST["lastname"]);
                $update_args = array(
                    'ID' => $id,
                    'post_name' => $titulo,
                    'post_title' => $titulo,
                    'post_content' => $descripcion_json,
                    'post_parent' => $padre,
                    'post_excerpt' => $tipo
                );

                include 'phpqrcode/qrlib.php';
                QRcode::png(get_permalink($id), plugin_dir_path(__FILE__) . 'vcf/' . sanitize_text_field($_POST["firstname"]) . '-' . $id . '.png', 'L', 30, 10);

                $result = wp_update_post($update_args);
                $actualizar = $wpdb->update($wpdb->prefix . "postmeta", array('meta_value' => sanitize_text_field($_POST['firstname'])), array('post_id' => $id, 'meta_key' => 'firstname'));
                $actualizar = $wpdb->update($wpdb->prefix . "postmeta", array('meta_value' => sanitize_text_field($_POST['lastname'])), array('post_id' => $id, 'meta_key' => 'lastname'));
                $actualizar = $wpdb->update($wpdb->prefix . "postmeta", array('meta_value' => sanitize_text_field($_POST['mail_profesional'])), array('post_id' => $id, 'meta_key' => 'mail_profesional'));
            } else if (isset($_POST["firstname"])) {
                echo "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Unsaved changes', 'team-vcard-generator') . "</strong> " . __("The minimum required data to register are the full name and a email", "team-vcard-generator") . "</div>";
            }
            //Mete el json en una variable
            $valores_post = json_decode(get_post_field('post_content', $id, 'raw'), true);
            //HTML de la pagina, las variables las mete en los inputs
            ?>

            <?php //@todo quitar esta mierda de aqui y de nuevo    ?>
            <div class="wrap" id="team_member">
                <h2><?php _e('Edit member', 'team-vcard-generator'); ?>
                    <a class="add-new-h2" href="?page=manage_team_member"><?php _e('Return', 'team-vcard-generator') ?></a>
                    <a class="add-new-h2 submit_formulario" href="#"><?php _e('Save changes', 'team-vcard-generator') ?></a>
                    <a class="add-new-h2" href="<?php echo get_permalink($id); ?>"><?php _e('View', 'team-vcard-generator') ?></a>
                </h2>
                <br>
                <form action='' method='post' class="bscs" name="formulario">
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class="tipo_tarjeta">
                                <p><?php _e('Type of member', 'team-vcard-generator') ?></p>
                                <input type="radio" name="tipo_miembro" value="individual" <?php
                                       if ($valores_post['tipo'] == "vcard_individual") {
                                           echo " checked";
                                       }
                                       ?>><?php _e('Individual', 'team-vcard-generator') ?>
                                <input type="radio" name="tipo_miembro" value="office" <?php
                           if ($valores_post['tipo'] != "vcard_individual") {
                               echo " checked";
                           }
                                       ?>><?php _e('Branch Office ', 'team-vcard-generator') ?>
                                <p id="lista_padres"><?php _e('Select the parent: ', 'team-vcard-generator'); ?>
                                    <select name="tipo_miembro_padre"> 
                                        <?php
                                        $comprueba_padre_id = wp_get_post_parent_id($id);
                                        $posibles_padres_company = $wpdb->get_results("SELECT ID,post_title,post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' AND post_excerpt='vcard_company'");
                                        foreach ($posibles_padres_company as $opcion) {
                                            if ($opcion->ID == $comprueba_padre_id) {
                                                $es_padre = ' selected';
                                            } else {
                                                $es_padre = "";
                                            }
                                            echo "<option value='" . $opcion->ID . "'" . $es_padre . ">" . __('Company', 'team-vcard-generator') . ": " . $opcion->post_title . "</option>";
                                            $es_padre = "";
                                        }
                                        $posibles_padres_offices = $wpdb->get_results("SELECT ID,post_title,post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' AND post_excerpt='vcard_office'");
                                        foreach ($posibles_padres_offices as $opcion) {
                                            if ($opcion->ID == $comprueba_padre_id) {
                                                $es_padre = ' selected';
                                            } else {
                                                $es_padre = "";
                                            }
                                            echo "<option value='" . $opcion->ID . "'" . $es_padre . ">" . __('Office', 'team-vcard-generator') . ": " . $opcion->post_title . "</option>";
                                            $es_padre = "";
                                        }
                                        if ($comprueba_padre_id == 0) {
                                            $es_padre = " selected";
                                        }
                                        echo "<option value='none'" . $es_padre . ">" . __('None', 'team-vcard-generator') . "</option>";
                                        ?>
                                    </select></p>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            <img id='preview_qrcode' src="<?php echo plugin_dir_url(__FILE__) . 'vcf/' . $valores_post['firstname'] . '-' . $id . '.png'; ?>">
                            <a id='download_qrcode' href="<?php echo plugin_dir_url(__FILE__) . 'vcf/' . $valores_post['firstname'] . '-' . $id . '.png'; ?>" class='button' download>
            <?php _e('Download QR-Code', 'team-vcard-generator'); ?></a></div>
                    </div>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1"><?php _e('Details', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-2"><?php _e('Content ', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-3"><?php _e('Links / Social networks', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-4"><?php _e('Schedule', 'team-vcard-generator'); ?></a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class='row'>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Details', 'team-vcard-generator'); ?></h3>
                                    <p>
                                        <label for="firstname"><?php _e('Firstname: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="firstname" id="firstname" value="<?php echo esc_attr($valores_post['firstname']); ?>" />
                                    </p>
                                    <p>
                                        <label for="lastname"><?php _e('Lastname: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="lastname" id="lastname" value="<?php echo esc_attr($valores_post['lastname']); ?>" />
                                    </p>
                                    <p>
                                        <label for="mail_profesional"><?php _e('Email address (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_profesional" id="mail_trabajo" value="<?php echo esc_attr($valores_post['mail_profesional']); ?>" />
                                    </p>
                                    <p>
                                        <label for="mail_personal"><?php _e('Email address (Home): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_personal" id="mail_casa" value="<?php echo esc_attr($valores_post['mail_personal']); ?>" />
                                    </p>
                                    <p>
                                        <label for="mail_otro"><?php _e('Email address (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_otro" id="mail_otro" value="<?php echo esc_attr($valores_post['mail_otro']); ?>" />
                                    </p>
                                    <p>
                                        <label for="telefono_profesional"><?php _e('Phone number (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_profesional" value="<?php echo esc_attr($valores_post['telefono_profesional']); ?>" />
                                    </p>
                                    <p>
                                        <label for="telefono_personal"><?php _e('Phone number (Home): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_personal" value="<?php echo esc_attr($valores_post['telefono_personal']); ?>" />
                                    </p>
                                    <p>
                                        <label for="telefono_otro"><?php _e('Phone number (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_otro" value="<?php echo esc_attr($valores_post['telefono_otro']); ?>" />
                                    </p>
                                    <p>
                                        <label for="cargo"><?php _e('Charge: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="cargo" id="cargo" value="<?php echo esc_attr($valores_post['cargo']); ?>"/> 
                                    </p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Address', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[direccion]' class='cambiar_como_padre' <?php
                                    if (in_array("direccion", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                             if (in_array("direccion", $valores_post['usar_padre'])) {
                                 echo 'true';
                             }
                             ?>'>
                                        <p><label for="address_calle"><?php _e('Street: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_calle" id="address_calle" value="<?php echo esc_attr($valores_post['direccion']['calle']); ?>"/></p>
                                        <p><label for="address_numero"><?php _e('Number: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_numero" id="address_numero" value="<?php echo esc_attr($valores_post['direccion']['numero']); ?>"/></p>
                                        <p><label for="address_ciudad"><?php _e('City: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_ciudad" id="address_ciudad" value="<?php echo esc_attr($valores_post['direccion']['ciudad']); ?>"/></p>
                                        <p><label for="address_provincia"><?php _e('State: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_provincia" id="address_provincia" value="<?php echo esc_attr($valores_post['direccion']['provincia']); ?>"/></p>
                                        <p><label for="address_zip"><?php _e('Zip code: ', 'team-vcard-generator'); ?></label>
                                            <input type="text" name="address_zip" id="address_zip" value="<?php echo esc_attr($valores_post['direccion']['zip']); ?>"/></p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Note', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[nota]' class='cambiar_como_padre' <?php
                                    if (in_array("nota", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                             if (in_array("nota", $valores_post['usar_padre'])) {
                                 echo 'true';
                             }
                             ?>'>
                                        <p>
                                            <textarea name='nota' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e('Note in contact download', 'team-vcard-generator') ?>'><?php echo esc_textarea($valores_post['nota']); ?></textarea>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php
                                        $photo = $valores_post['photo'];
                                        _e('Photo', 'team-vcard-generator');
                                        //Si no tenemos foto asignada y el ajuste usar_imagen esta activado nos muestra esa por defecto
                                        $options = get_option('bscs_settings');
                                        if ($options['usar_imagen'] == 'true' && $photo == "") {
                                            $photo = $options['imagen'];
                                        }
                                        ?></h3>
                                    <p>
                                        <input type="text" name="photo" id="photo" value="<?php echo esc_attr($photo); ?>" readonly/><a href="#" id="insert-my-media" class="button"><?php _e('Select', 'team-vcard-generator'); ?></a>
                                    </p>
                                    <p><img src="<?php echo esc_attr($photo); ?>" alt="" id="preview"></img></p>
                                </div>
                            </div>
                        </div>
                        <div id="tabs-3">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Links', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[links]' class='cambiar_como_padre' <?php
                                    if (in_array("links", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                                             if (in_array("links", $valores_post['usar_padre'])) {
                                                 echo 'true';
                                             }
                                             ?>'>
                                             <?php
                                             $opciones_links = array(__('Choose', 'team-vcard-generator'), 'Facebook', 'Linkedin', 'Twitter', 'Google+', 'Instagram', 'Tumblr', 'VK', 'Web Page', __('Other..', 'team-vcard-generator'));
                                             if (count($valores_post['links']) > 0) {
                                                 $array_links = $valores_post['links'];
                                                 $numero = 0;
                                                 foreach ($array_links as $key => $val) {
                                                     $devuelve = "<p class='inputlink'><label><select name='links[" . $numero . "][sitio]'>";
                                                     foreach ($opciones_links as $opcion) {
                                                         if ($opcion == $val['sitio']) {
                                                             $devuelve.="<option value='" . $opcion . "' selected>" . $opcion . "</option>";
                                                         } else {
                                                             $devuelve.="<option value='" . $opcion . "'>" . $opcion . "</option>";
                                                         }
                                                     }
                                                     $devuelve.="</select></label><input type='text' name='links[" . $numero . "][direccion]' value='" . esc_attr($val['direccion']) . "'><span class='dashicons dashicons-no removelink'></span>";
                                                     echo $devuelve;
                                                     $numero++;
                                                 }
                                             } else {
                                                 ?>
                                            <p class='inputlink'><label><select name='links[0][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[0][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                            <p class='inputlink'><label><select name='links[1][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[1][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                            <p class='inputlink'><label><select name='links[2][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[2][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
            <?php } ?>
                                        <p><button id="morelinks"><?php _e('Add links', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Widgets', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[widget]' class='cambiar_como_padre' <?php
                                    if (in_array("widget", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                             if (in_array("widget", $valores_post['usar_padre'])) {
                                 echo 'true';
                             }
                             ?>'>
                                        <p class="widget"><label class='titulo_widget'>Twitter </label><a class="add-new-h2" target="_blank" href="https://twitter.com/settings/widgets/new"><?php _e('CREATE', 'team-vcard-generator'); ?></a></p>
                                        <span><?php _e('Create and paste the html inside the below input', 'team-vcard-generator'); ?></span>
                                        <textarea name='widget_twitter' wrap="soft" cols="20" rows="6"><?php echo esc_html($valores_post['widget']['twitter']); ?></textarea>
                                        <p></p>
                                        <p class="widget"><label class='titulo_widget'>Facebook</label><input name='widget_facebook' type='text' value="<?php echo esc_attr($valores_post['widget']['facebook']); ?>"></p>
                                        <span><?php _e('Put only the link, example: https://www.facebook.com/Microsoft/ ', 'team-vcard-generator'); ?></span>
                                    </div></div>
                            </div>
                        </div>
                        <div id="tabs-2">
                            <div class='row'>
                                <div class="col-lg-6 col-md-6">
                                    <h3><?php _e('Content', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[textos]' class='cambiar_como_padre' <?php
                                    if (in_array("textos", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                                             if (in_array("textos", $valores_post['usar_padre'])) {
                                                 echo 'true';
                                             }
                                             ?>'>
                                             <?php
                                             if (count($valores_post['textos']) > 0) {
                                                 $array_textos = $valores_post['textos'];
                                                 foreach ($array_textos as $key => $val) {
                                                     $devuelve = "<p class='inputtexto'><label class='titulo_texto'><input name='textos[" . $key . "][titulo]' value='" . esc_attr($val['titulo']) . "'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[" . $key . "][imagen]' class='imagen_textos_url' value='" . esc_attr($val['imagen']) . "' readonly/><a href='#' class='button add_imagen_textos'>" . __('Select image', 'team-vcard-generator') . "</a></span>";
                                                     $devuelve.="<textarea name='textos[" . $key . "][texto]' wrap='soft' cols='30' rows='10' maxlength='2000'>" . esc_textarea($val['texto']) . "</textarea><img class='fotos_textos_preview' src='" . esc_html($val['imagen']) . "'></p>";
                                                     echo $devuelve;
                                                 }
                                             } else {
                                                 ?>
                                            <p class='inputtexto'><label class='titulo_texto'><input name='textos[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[1][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_textos_preview'></p>
                                            <p class='inputtexto'><label class='titulo_texto'><input name='textos[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[2][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_textos_preview'></p>
                                            <p class='inputtexto'><label class='titulo_texto'><input name='textos[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[3][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_textos_preview'></p>

            <?php } ?>
                                        <p><button id="moretexts"><?php _e('Add content', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <h3><?php _e('Offers', 'team-vcard-generator'); ?></h3>
                                    <p><input type='checkbox' name='usar_padre[ofertas]' class='cambiar_como_padre' <?php
                                    if (in_array("ofertas", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                                             if (in_array("ofertas", $valores_post['usar_padre'])) {
                                                 echo 'true';
                                             }
                                             ?>'>
                                             <?php
                                             if (count($valores_post['ofertas']) > 0) {
                                                 $array_ofertas = $valores_post['ofertas'];
                                                 foreach ($array_ofertas as $key => $val) {
                                                     $devuelve = "<p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[" . $key . "][titulo]' value='" . esc_attr($val['titulo']) . "'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[" . $key . "][imagen]' class='imagen_ofertas_url' value='" . esc_attr($val['imagen']) . "' readonly/><a href='#' class='button add_imagen_ofertas'>" . __('Select image', 'team-vcard-generator') . "</a></span>";
                                                     $devuelve.="<textarea name='ofertas[" . $key . "][texto]' wrap='soft' cols='30' rows='10' maxlength='2000'>" . esc_textarea($val['texto']) . "</textarea><img class='fotos_ofertas_preview' src='" . esc_html($val['imagen']) . "'></p>";
                                                     echo $devuelve;
                                                 }
                                             } else {
                                                 ?>
                                            <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[1][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                            <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[2][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                            <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[3][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
            <?php } ?>
                                        <p><button id="moreofertas"><?php _e('Add content', 'team-vcard-generator'); ?></button></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tabs-4">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <h3><?php _e('Schedule', 'team-vcard-generator'); ?></h3>
                                        <?php
                                        _e('You can define the days in groups and personalize the horary of each group of days, in each group you can set multiples time slots (+)', 'team-vcard-generator');
                                        echo "<p></p>";
                                        ?>
                                    <p><input type='checkbox' name='usar_padre[horario]' class='cambiar_como_padre' <?php
                                    if (in_array("horario", $valores_post['usar_padre'])) {
                                        echo 'checked';
                                    }
                                    ?>><?php _e('Same as parent', 'team-vcard-generator'); ?></p>
                                    <div class='como_padre <?php
                                             if (in_array("horario", $valores_post['usar_padre'])) {
                                                 echo 'true';
                                             }
                                             ?>'>
                                             <?php
                                             if (count($valores_post['horario']) > 0) {
                                                 $array_horarios_montar = array();
                                                 foreach ($valores_post['horario'] as $key => $horario) {
                                                     if ($key == 0) {
                                                         array_push($array_horarios_montar, array(array($horario[0]), $horario[1], $horario[2], $horario[3]));
                                                     } else {
                                                         $horario_insertado = false;
                                                         foreach ($array_horarios_montar as $llave => $horario_final) {
                                                             if ($horario_final[1] == $horario[1] && $horario_final[2] == $horario[2]) {
                                                                 array_push($array_horarios_montar[$llave][0], $horario[0]);
                                                                 if (isset($array_horarios_montar[$llave][3])) {
                                                                     if ((int) $array_horarios_montar[$llave][3] > (int) $horario[3]) {
                                                                         $array_horarios_montar[$llave][3] = $horario[3];
                                                                     }
                                                                 } else {
                                                                     $array_horarios_montar[$llave][3] = $horario[3];
                                                                 }
                                                                 $horario_insertado = true;
                                                             }
                                                         }
                                                         if (!$horario_insertado) {
                                                             array_push($array_horarios_montar, array(array($horario[0]), $horario[1], $horario[2], $horario[3]));
                                                         }
                                                         $horario_insertado = false;
                                                     }
                                                 }
                                                 $price = array();
                                                 foreach ($array_horarios_montar as $key => $row) {
                                                     $price[$key] = $row[3];
                                                 }
                                                 array_multisort($price, SORT_ASC, $array_horarios_montar);

                                                 $cuenta_horarios = 0;
                                                 foreach ($array_horarios_montar as $key => $horarios_montar) {
                                                     $dia_cada_caso=$key;
                                                     $linea_horario = "<p class='lista_dia'><select class='selectpicker' data-style='btn-primary' name='horario[" . $key . "][dias][]' multiple data-width='fit' title='" . __('Select the days', 'team-vcard-generator') . "'>";
                                                     $linea_horario.="<option value='0'";
                                                     if (in_array('0', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Monday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='1'";
                                                     if (in_array('1', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Tuesday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='2'";
                                                     if (in_array('2', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Wednesday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='3'";
                                                     if (in_array('3', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Thursday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='4'";
                                                     if (in_array('4', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Friday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='5'";
                                                     if (in_array('5', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Saturday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="<option value='6'";
                                                     if (in_array('6', $horarios_montar[0])) {
                                                         $linea_horario.="selected";
                                                     }
                                                     $linea_horario.=">" . __('Sunday', 'team-vcard-generator') . "</option>";
                                                     $linea_horario.="</select>";
                                                     if (count($horarios_montar[1]) > 0) {
                                                         foreach ($horarios_montar[1] as $key => $hora) {
                                                             $linea_horario.="<span class='calendar_franja'><input type='time' class='calendar_inicio' name='horario[" . $dia_cada_caso . "][inicio][]' value='" . $horarios_montar[1][$key] . "'>" . __('to', 'team-vcard-generator') . "<input type='time' class='calendar_final' name='horario[" . $dia_cada_caso . "][cierre][]' value='" . $horarios_montar[2][$key] . "'></span>";
                                                             if ($key != (count($horarios_montar[1]) - 1)) {
                                                                 $linea_horario.="<span class='separa_franjas'> // </span>";
                                                             }
                                                         }
                                                     }   else {
                                                         $linea_horario.="<input type='time' class='calendar_inicio' name='horario[".$dia_cada_caso."][inicio][]' value='00:00'>" . __('to', 'team-vcard-generator') . "<input type='time' class='calendar_final' name='horario[0][cierre][]' value='00:00'>";
                                                     }
                                                     $linea_horario.= "</span><button class='btn btn-default btn-danger lesshour'>-</button><button class='btn btn-default btn-success morehour'>+</button><button class='btn btn-default lesscalendar'>" . __('Delete this group', 'team-vcard-generator') . "</button></p>";
                                                     echo $linea_horario;
                                                     $cuenta_horarios++;
                                                 }
                                             }
                                             if (isset($cuenta_horarios)) {
                                                 $x = $cuenta_horarios;
                                             } else {
                                                 $x = 0;
                                             }
                                             for ($x; $x < 7; $x++) {
                                                 ?>
                                            <p class="lista_dia"<?php
                                                 if ($x > 0) {
                                                     echo "style='display:none'";
                                                 }
                                                 ?>>
                                                <select class="selectpicker" data-style="btn-primary" name='horario[<?php echo $x; ?>][dias][]' multiple data-width="fit" title="<?php _e('Select the days', 'team-vcard-generator'); ?>">
                                                    <option value="0"><?php _e('Monday', 'team-vcard-generator'); ?></option>
                                                    <option value="1"><?php _e('Tuesday', 'team-vcard-generator'); ?></option>
                                                    <option value="2"><?php _e('Wednesday', 'team-vcard-generator'); ?></option>
                                                    <option value="3"><?php _e('Thursday', 'team-vcard-generator'); ?></option>
                                                    <option value="4"><?php _e('Friday', 'team-vcard-generator'); ?></option>
                                                    <option value="5"><?php _e('Saturday', 'team-vcard-generator'); ?></option>
                                                    <option value="6"><?php _e('Sunday', 'team-vcard-generator'); ?></option>
                                                </select>
                                                <span class="calendar_franja"><input type="time" class="calendar_inicio" name="horario[<?php echo $x; ?>][inicio][]" value="00:00"> <?php _e('to', 'team-vcard-generator'); ?> <input type="time" class="calendar_final" name="horario[<?php echo $x; ?>][cierre][]" value="00:00"></span><button class="btn btn-default btn-danger lesshour">-</button><button class="btn btn-default btn-success morehour">+</button><button class="btn btn-default lesscalendar"><?php _e('Delete this group', 'team-vcard-generator'); ?></button></p>
                        <?php } //@todo Festivos(cerrado/abierto)?   ?> 
                                        <button class="btn btn-default" id="morecalendar"><?php _e('Add group', 'team-vcard-generator'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

            <?php //@todo limpiar esta mierda de enlaces  ?>
                    </div>
                </form>
            </div> 
            <?php
        }

        /**
         * Pagina de la empresa
         */
        function bscs_company($post) {
            $id_empresa = wp_insert_post($post);
            global $wpdb;
            $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_excerpt='vcard_company'");
            //Si se ejecuta el form actualizamos datos en BD
            if (isset($_POST["firstname"]) && $_POST["firstname"] != "") {
                //Definimos los datos del json
                if (isset($_POST['links'])) {
                    $array_links_insertar = array();
                    foreach ($_POST['links'] as $link) {
                        //echo "key:".$key." val:".$val;
                        //print_r($link);
                        if (sanitize_text_field($link['direccion']) != "" && sanitize_text_field($link['sitio']) != "no") {
                            array_push($array_links_insertar, array('sitio' => sanitize_text_field($link['sitio']), 'direccion' => sanitize_text_field($link['direccion'])));
                        }
                    }
                }
                if (isset($_POST['textos'])) {
                    $array_textos_insertar = array();
                    foreach ($_POST['textos'] as $texto) {
                        if (sanitize_text_field($texto['titulo']) != "" && (sanitize_text_field($texto['texto']) != "" || sanitize_text_field($texto['imagen']) != "")) {
                            array_push($array_textos_insertar, array('titulo' => sanitize_text_field($texto['titulo']), 'texto' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($texto['texto'], true))), 'imagen' => sanitize_text_field($texto['imagen'])));
                        }
                    }
                }
                if (isset($_POST['ofertas'])) {
                    $array_ofertas_insertar = array();
                    foreach ($_POST['ofertas'] as $oferta) {
                        if (sanitize_text_field($oferta['titulo']) != "" && (sanitize_text_field($oferta['texto']) != "" || sanitize_text_field($oferta['imagen']) != "")) {
                            array_push($array_ofertas_insertar, array('titulo' => sanitize_text_field($oferta['titulo']), 'texto' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($oferta['texto'], true))), 'imagen' => sanitize_text_field($oferta['imagen'])));
                        }
                    }
                }
                if (isset($_POST['horario'])) {
                    $horario = array();
                    foreach ($_POST['horario'] as $texto => $value) {
                        //echo "key:".$key." val:".$val;
                        $inicio = $value[inicio];
                        $cierre = $value[cierre];
                        if (count($value[dias]) > 0) {
                            foreach ($value[dias] as $dia) {
                                array_push($horario, array($dia, $inicio, $cierre, $dia));
                            }
                        }
                    }
                }
                $arr = array('firstname' => sanitize_text_field($_POST['firstname']),
                    'mail_profesional' => sanitize_text_field($_POST['mail_profesional']),
                    'mail_otro' => sanitize_text_field($_POST['mail_otro']),
                    'telefono_profesional' => sanitize_text_field($_POST['telefono_profesional']),
                    'telefono_otro' => sanitize_text_field($_POST['telefono_otro']),
                    'direccion' => array('calle' => sanitize_text_field($_POST['address_calle']), 'numero' => sanitize_text_field($_POST['address_numero']), 'ciudad' => sanitize_text_field($_POST['address_ciudad']), 'provincia' => sanitize_text_field($_POST['address_provincia']), 'zip' => sanitize_text_field($_POST['address_zip'])),
                    'nota' => trim(preg_replace('/(\r\n)|\n|\r/', '\\n', wp_kses($_POST['nota'], true))),
                    'links' => $array_links_insertar,
                    'textos' => $array_textos_insertar,
                    'ofertas' => $array_ofertas_insertar,
                    'widget' => array('twitter' => $_POST['widget_twitter'], 'facebook' => sanitize_text_field($_POST['widget_facebook'])),
                    'photo' => sanitize_text_field($_POST['photo']),
                    'horario' => $horario,
                    'photo' => sanitize_text_field($_POST['photo']),
                    'tipo' => "vcard_company"
                );
                $descripcion_json = json_encode($arr, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);

                //Actualiza el titulo y valores del post
                $update_args = array(
                    'ID' => $id,
                    'post_name' => sanitize_text_field($_POST["firstname"]),
                    'post_title' => sanitize_text_field($_POST["firstname"]),
                    'post_content' => $descripcion_json);
                $result = wp_update_post($update_args);
                include 'phpqrcode/qrlib.php';
                QRcode::png(get_permalink($id), plugin_dir_path(__FILE__) . 'vcf/' . sanitize_text_field($_POST['firstname']) . '-' . $id . '.png', 'L', 30, 10);
            } else if (isset($_POST["firstname"])) {
                echo "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close' title='close'>x</a><strong>" . __('Unsaved changes', 'team-vcard-generator') . "</strong> " . __("The minimum required data is the name of the company", "team-vcard-generator") . "</div>";
            }
            //Mete el json en una variable
            $valores_post = json_decode(get_post_field('post_content', $id, 'raw'), true);

            //HTML de la pagina, las variables las mete en los inputs
            ?>
            <div class="wrap" id="team_member">

                <h2><?php _e('My company', 'team-vcard-generator'); ?>
                    <a class="add-new-h2 submit_formulario_admin" href="#"><?php _e('Save changes', 'team-vcard-generator') ?></a>
                    <a class="add-new-h2" href="<?php echo get_permalink($id); ?>"><?php _e('View', 'team-vcard-generator') ?></a>
                </h2>
                <img id='preview_qrcode' src="<?php echo plugin_dir_url(__FILE__) . 'vcf/' . $valores_post['firstname'] . '-' . $id . '.png'; ?>">
                <a id='download_qrcode' href="<?php echo plugin_dir_url(__FILE__) . 'vcf/' . $valores_post['firstname'] . '-' . $id . '.png'; ?>" class='button' download>
            <?php _e('Download QR-Code', 'team-vcard-generator'); ?>
                </a>
                <form action='' method='post' class="bscs" name="formulario">
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1"><?php _e('Details', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-2"><?php _e('Content ', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-3"><?php _e('Links / Social networks', 'team-vcard-generator'); ?></a></li>
                            <li><a href="#tabs-4"><?php _e('Schedule', 'team-vcard-generator'); ?></a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Details', 'team-vcard-generator'); ?></h3>
                                    <p>
                                        <label for="name"><?php _e('Name: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="firstname" id="firstname" value="<?php echo esc_attr($valores_post['firstname']); ?>" />
                                    </p>
                                    <p>
                                        <label for="mail_profesional"><?php _e('Email address (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_profesional" id="mail_trabajo" value="<?php echo esc_attr($valores_post['mail_profesional']); ?>" />
                                    </p>
                                    <p>
                                        <label for="mail_otro"><?php _e('Email address (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="mail_otro" id="mail_otro" value="<?php echo esc_attr($valores_post['mail_otro']); ?>" />
                                    </p>
                                    <p>
                                        <label for="telefono_profesional"><?php _e('Phone number (Work): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_profesional" value="<?php echo esc_attr($valores_post['telefono_profesional']); ?>" />
                                    </p>
                                    <p>
                                        <label for="telefono_otro"><?php _e('Phone number (Other): ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="telefono_otro" value="<?php echo esc_attr($valores_post['telefono_otro']); ?>" />
                                    </p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Address', 'team-vcard-generator'); ?></h3>
                                    <p><label for="address_calle"><?php _e('Street: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="address_calle" id="address_calle" value="<?php echo esc_attr($valores_post['direccion']['calle']); ?>"/></p>
                                    <p><label for="address_numero"><?php _e('Number: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="address_numero" id="address_numero" value="<?php echo esc_attr($valores_post['direccion']['numero']); ?>"/></p>
                                    <p><label for="address_ciudad"><?php _e('City: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="address_ciudad" id="address_ciudad" value="<?php echo esc_attr($valores_post['direccion']['ciudad']); ?>"/></p>
                                    <p><label for="address_provincia"><?php _e('State: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="address_provincia" id="address_provincia" value="<?php echo esc_attr($valores_post['direccion']['provincia']); ?>"/></p>
                                    <p><label for="address_zip"><?php _e('Zip code: ', 'team-vcard-generator'); ?></label>
                                        <input type="text" name="address_zip" id="address_zip" value="<?php echo esc_attr($valores_post['direccion']['zip']); ?>"/></p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Note', 'team-vcard-generator'); ?></h3>
                                    <p>
                                        <textarea name='nota' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e('Note in contact download', 'team-vcard-generator') ?>'><?php echo esc_textarea($valores_post['nota']); ?></textarea>
                                    </p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php
                                        $photo = $valores_post['photo'];
                                        _e('Logo', 'team-vcard-generator');
                                        //Si no tenemos foto asignada y el ajuste usar_imagen esta activado nos muestra esa por defecto
                                        $options = get_option('bscs_settings');
                                        if ($options['usar_imagen'] == 'true' && $photo == "") {
                                            $photo = $options['imagen'];
                                        }
                                        ?></h3>
                                    <p>
                                        <input type="text" name="photo" id="photo" value="<?php echo esc_attr($photo); ?>" readonly/><a href="#" id="insert-my-media" class="button"><?php _e('Select', 'team-vcard-generator'); ?></a>
                                    </p>
                                    <p><img src="<?php echo esc_attr($photo); ?>" alt="" id="preview"></img></p>
                                </div>
                            </div>
                        </div><div id='tabs-3'>
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Links', 'team-vcard-generator'); ?></h3><?php
                                    $opciones_links = array(__('Choose', 'team-vcard-generator'), 'Facebook', 'Linkedin', 'Twitter', 'Google+', 'Instagram', 'Tumblr', 'VK', 'Web Page', __('Other..', 'team-vcard-generator'));
                                    if (isset($valores_post['links'])) {
                                        $array_links = $valores_post['links'];
                                        $numero = 0;
                                        foreach ($array_links as $key => $val) {
                                            $devuelve = "<p class='inputlink'><label><select name='links[" . $numero . "][sitio]'>";
                                            foreach ($opciones_links as $opcion) {
                                                if ($opcion == $val['sitio']) {
                                                    $devuelve.="<option value='" . $opcion . "' selected>" . $opcion . "</option>";
                                                } else {
                                                    $devuelve.="<option value='" . $opcion . "'>" . $opcion . "</option>";
                                                }
                                            }
                                            $devuelve.="</select></label><input type='text' name='links[" . $numero . "][direccion]' value='" . esc_attr($val['direccion']) . "'><span class='dashicons dashicons-no removelink'></span>";
                                            echo $devuelve;
                                            $numero++;
                                        }
                                    } else {
                                        ?>
                                        <p class='inputlink'><label><select name='links[0][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[0][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                        <p class='inputlink'><label><select name='links[1][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[1][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
                                        <p class='inputlink'><label><select name='links[2][sitio]'><option value='no'><?php _e('Choose', 'team-vcard-generator'); ?></option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[2][direccion]' placeholder="https://www..."><span class='dashicons dashicons-no removelink'></span></p>
            <?php } ?>
                                    <p><button id="morelinks"><?php _e('Add links', 'team-vcard-generator'); ?></button></p>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h3><?php _e('Widgets', 'team-vcard-generator'); ?></h3>
                                    <p class="widget"><label class='titulo_widget'>Twitter </label><a class="add-new-h2" target="_blank" href="https://twitter.com/settings/widgets/new"><?php _e('CREATE', 'team-vcard-generator'); ?></a></p>
                                    <span><?php _e('Create and paste the html inside the below input', 'team-vcard-generator'); ?></span>
                                    <textarea name='widget_twitter' wrap="soft" cols="20" rows="6" placeholder="<a class='twitter-timeline' href='https://twitter.com/Microsoft' data-widget-id='727112677152186369'>Tweets by @Microsoft</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>"><?php echo esc_html($valores_post['widget']['twitter']); ?></textarea>
                                    <p></p>
                                    <p class="widget"><label class='titulo_widget'>Facebook</label><input name='widget_facebook' type='text' value="<?php echo esc_attr($valores_post['widget']['facebook']); ?>"></p>
                                    <span><?php _e('Put only the link, example: https://www.facebook.com/Microsoft/ ', 'team-vcard-generator'); ?></span>
                                </div>
                            </div></div><div id='tabs-2'>
                            <div class='row'>
                                <div class="col-lg-6 col-md-6">
                                    <h3><?php _e('Content', 'team-vcard-generator'); ?></h3>
                                    <?php
                                    if (isset($valores_post['textos'])) {
                                        $array_textos = $valores_post['textos'];
                                        foreach ($array_textos as $key => $val) {
                                            $devuelve = "<p class='inputtexto'><label class='titulo_texto'><input name='textos[" . $key . "][titulo]' value='" . esc_attr($val['titulo']) . "'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[" . $key . "][imagen]' class='imagen_textos_url' value='" . esc_attr($val['imagen']) . "' readonly/><a href='#' class='button add_imagen_textos'>" . __('Select image', 'team-vcard-generator') . "</a></span>";
                                            $devuelve.="<textarea name='textos[" . $key . "][texto]' wrap='soft' cols='30' rows='10' maxlength='2000'>" . esc_textarea($val['texto']) . "</textarea><img class='fotos_textos_preview' src='" . esc_html($val['imagen']) . "'></p>";
                                            echo $devuelve;
                                        }
                                    } else {
                                        ?>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[1][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000" placeholder='<?php _e('Content - max 2000 characters', 'team-vcard-generator') ?>'></textarea><img class='fotos_textos_preview'></p>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[2][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_textos_preview'></p>
                                        <p class='inputtexto'><label class='titulo_texto'><input name='textos[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[3][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='textos[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_textos_preview'></p>

                                    <?php } ?>
                                    <p><button id="moretexts"><?php _e('Add content', 'team-vcard-generator'); ?></button></p>
                                </div>

                                <div class="col-lg-6 col-md-6"> 
                                    <h3><?php _e('Offers', 'team-vcard-generator'); ?></h3>
                                    <?php
                                    if (isset($valores_post['ofertas'])) {
                                        $array_ofertas = $valores_post['ofertas'];
                                        foreach ($array_ofertas as $key => $val) {
                                            $devuelve = "<p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[" . $key . "][titulo]' value='" . esc_attr($val['titulo']) . "'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[" . $key . "][imagen]' class='imagen_ofertas_url' value='" . esc_attr($val['imagen']) . "' readonly/><a href='#' class='button add_imagen_ofertas'>" . __('Select image', 'team-vcard-generator') . "</a></span>";
                                            $devuelve.="<textarea name='ofertas[" . $key . "][texto]' wrap='soft' cols='30' rows='10' maxlength='2000'>" . esc_textarea($val['texto']) . "</textarea><img class='fotos_ofertas_preview' src='" . esc_html($val['imagen']) . "'></p>";
                                            echo $devuelve;
                                        }
                                    } else {
                                        ?>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[1][titulo]' type='text' placeholder='<?php _e('Title', 'team-vcard-generator') ?>'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[1][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[1][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[2][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[2][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[2][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
                                        <p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[3][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[3][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'><?php _e('Select image', 'team-vcard-generator'); ?></a></span><textarea name='ofertas[3][texto]' wrap="soft" cols="30" rows="10" maxlength="2000"></textarea><img class='fotos_ofertas_preview'></p>
            <?php } ?>
                                    <p><button id="moreofertas"><?php _e('Add content', 'team-vcard-generator'); ?></button></p>
                                </div>
                            </div>
                        </div><div id='tabs-4'>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <h3><?php _e('Schedule', 'team-vcard-generator'); ?></h3>
                                    <?php
                                    _e('You can define the days in groups and personalize the horary of each group of days, in each group you can set multiples time slots (+)', 'team-vcard-generator');
                                    echo "<p></p>";
                                    if (count($valores_post['horario']) > 0) {
                                        $array_horarios_montar = array();
                                        foreach ($valores_post['horario'] as $key => $horario) {
                                            if ($key == 0) {
                                                array_push($array_horarios_montar, array(array($horario[0]), $horario[1], $horario[2], $horario[3]));
                                            } else {
                                                $horario_insertado = false;
                                                foreach ($array_horarios_montar as $llave => $horario_final) {
                                                    if ($horario_final[1] == $horario[1] && $horario_final[2] == $horario[2]) {
                                                        array_push($array_horarios_montar[$llave][0], $horario[0]);
                                                        if (isset($array_horarios_montar[$llave][3])) {
                                                            if ((int) $array_horarios_montar[$llave][3] > (int) $horario[3]) {
                                                                $array_horarios_montar[$llave][3] = $horario[3];
                                                            }
                                                        } else {
                                                            $array_horarios_montar[$llave][3] = $horario[3];
                                                        }
                                                        $horario_insertado = true;
                                                    }
                                                }
                                                if (!$horario_insertado) {
                                                    array_push($array_horarios_montar, array(array($horario[0]), $horario[1], $horario[2], $horario[3]));
                                                }
                                                $horario_insertado = false;
                                            }
                                        }
                                        $price = array();
                                        foreach ($array_horarios_montar as $key => $row) {
                                            $price[$key] = $row[3];
                                        }
                                        array_multisort($price, SORT_ASC, $array_horarios_montar);

                                        $cuenta_horarios = 0;
                                        foreach ($array_horarios_montar as $key => $horarios_montar) {
                                            $linea_horario = "<p class='lista_dia'><select class='selectpicker' data-style='btn-primary' name='horario[" . $key . "][dias][]' multiple data-width='fit' title='" . __('Select the days', 'team-vcard-generator') . "'>";
                                            $linea_horario.="<option value='0'";
                                            if (in_array('0', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Monday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='1'";
                                            if (in_array('1', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Tuesday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='2'";
                                            if (in_array('2', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Wednesday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='3'";
                                            if (in_array('3', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Thursday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='4'";
                                            if (in_array('4', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Friday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='5'";
                                            if (in_array('5', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Saturday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="<option value='6'";
                                            if (in_array('6', $horarios_montar[0])) {
                                                $linea_horario.="selected";
                                            }
                                            $linea_horario.=">" . __('Sunday', 'team-vcard-generator') . "</option>";
                                            $linea_horario.="</select>";
                                            if (count($horarios_montar[1]) > 0) {
                                                foreach ($horarios_montar[1] as $key => $hora) {
                                                    $linea_horario.="<span class='calendar_franja'><input type='time' class='calendar_inicio' name='horario[" . $key . "][inicio][]' value='" . $horarios_montar[1][$key] . "'>" . __('to', 'team-vcard-generator') . "<input type='time' class='calendar_final' name='horario[" . $key . "][cierre][]' value='" . $horarios_montar[2][$key] . "'></span>";
                                                }
                                            } else {
                                                $linea_horario.="<span class='calendar_franja'><input type='time' class='calendar_inicio' name='horario[0][inicio][]' value='00:00'>" . __('to', 'team-vcard-generator') . "<input type='time' class='calendar_final' name='horario[0][cierre][]' value='00:00'></span>";
                                            }
                                            $linea_horario.= "<button class='btn btn-default btn-danger lesshour'>-</button><button class='btn btn-default btn-success morehour'>+</button><button class='btn btn-default lesscalendar'>" . __('Delete this group', 'team-vcard-generator') . "</button></p>";
                                            echo $linea_horario;
                                            $cuenta_horarios++;
                                        }
                                    }
                                    if (isset($cuenta_horarios)) {
                                        $x = $cuenta_horarios;
                                    } else {
                                        $x = 0;
                                    }
                                    for ($x; $x < 7; $x++) {
                                        ?>
                                        <p class="lista_dia"<?php
                                        if ($x > 0) {
                                            echo "style='display:none'";
                                        }
                                        ?>>
                                            <select class="selectpicker" data-style="btn-primary" name='horario[<?php echo $x; ?>][dias][]' multiple data-width="fit" title="<?php _e('Select the days', 'team-vcard-generator'); ?>">
                                                <option value="0"><?php _e('Monday', 'team-vcard-generator'); ?></option>
                                                <option value="1"><?php _e('Tuesday', 'team-vcard-generator'); ?></option>
                                                <option value="2"><?php _e('Wednesday', 'team-vcard-generator'); ?></option>
                                                <option value="3"><?php _e('Thursday', 'team-vcard-generator'); ?></option>
                                                <option value="4"><?php _e('Friday', 'team-vcard-generator'); ?></option>
                                                <option value="5"><?php _e('Saturday', 'team-vcard-generator'); ?></option>
                                                <option value="6"><?php _e('Sunday', 'team-vcard-generator'); ?></option>
                                            </select>
                                            <span class="calendar_franja"><input type="time" class="calendar_inicio" name="horario[<?php echo $x; ?>][inicio][]" value="00:00"> <?php _e('to', 'team-vcard-generator'); ?> <input type="time" class="calendar_final" name="horario[<?php echo $x; ?>][cierre][]" value="00:00"></span><button class="btn btn-default btn-danger lesshour">-</button><button class="btn btn-default btn-success morehour">+</button><button class="btn btn-default lesscalendar"><?php _e('Delete this group', 'team-vcard-generator'); ?></button></p>
                <?php } //@todo Festivos(cerrado/abierto)?  ?> 
                                    <button class="btn btn-default" id="morecalendar"><?php _e('Add group', 'team-vcard-generator'); ?></button>
                                </div>
                            </div>
                        </div></div>
                </form>
            <?php //@todo limpiar esta mierda de enlaces  ?>
            </div>
            <?php
        }

        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Settings del Plugin + contenido de su propia pagina Settings"> 
        /**
         * Inizializa los Settings del plugin
         */
        function bscs_settings_init() {
            //Registra nuestro Setting y agrega fields, los fields llaman a una funcion que pone el contenido(columnas), asi que pueden tener varios campos dentro de cada field
            register_setting('pluginPage', 'bscs_settings');
            add_settings_section('bscs_pluginPage_section', '', 'bscs_settings_section_callback', 'pluginPage');
            add_settings_field('bscs_url', __('URL', 'team-vcard-generator'), 'bscs_url_render', 'pluginPage', 'bscs_pluginPage_section');
            add_settings_field('bscs_imagen', __('Default photo', 'team-vcard-generator'), 'bscs_imagen_render', 'pluginPage', 'bscs_pluginPage_section');
            add_settings_field('bscs_estilo_solo', __('Template', 'team-vcard-generator'), 'bscs_estilo_render', 'pluginPage', 'bscs_pluginPage_section');
            add_settings_field('bscs_colores_solo', __('Colors', 'team-vcard-generator'), 'bscs_colores_render', 'pluginPage', 'bscs_pluginPage_section');

            //SETTINGS en caso de que esten vacios al hacer el submit
            $mis_options = get_option('bscs_settings');
            if (strlen($mis_options['url']) == 0) {
                $mis_options['url'] = 'team_member';
                update_option('bscs_settings', $mis_options);
            }
            if (strlen($mis_options['usar_imagen']) == 0) {
                $mis_options['usar_imagen'] = 'true';
                update_option('bscs_settings', $mis_options);
            }
            if (strlen($mis_options['mail']) == 0) {
                $mis_options['mail'] = 'example@company.com';
                update_option('bscs_settings', $mis_options);
            }
            if (strlen($mis_options['estilo']) == 0) {
                $mis_options['estilo'] = 'estilo1';
                update_option('bscs_settings', $mis_options);
            }
            if (strlen($mis_options['color1']) == 0) {
                $mis_options['color1'] = '#2eb8db';
                update_option('bscs_settings', $mis_options);
            }
            if (strlen($mis_options['color2']) == 0) {
                $mis_options['color2'] = '#eb5596';
                update_option('bscs_settings', $mis_options);
            }
        }

        add_action('admin_init', 'bscs_settings_init');

        function bscs_url_render() {
            //recoge todas las opciones para poder sacarlas luego
            $options = get_option('bscs_settings');
            ?>
            <input type='text' name='bscs_settings[url_viejo]' value='<?php echo esc_attr($options['url_viejo']); ?>' style='display:none'>
            <input type='text' name='bscs_settings[url]' value='<?php echo esc_attr($options['url']); ?>'><p>
                <?php _e('Permalink where be all the cards', 'team-vcard-generator'); ?>
            </p><p>
                <?php
                _e('Example: http://your-domain-name/<span style="color:red">URL</span>/eli-wallace', 'team-vcard-generator');
                echo "</p>";
            }

            //imagen del usuario por defecto
            function bscs_imagen_render() {
                $options = get_option('bscs_settings');
                //recoge todas las opciones para poder sacarlas luego
                ?>
            <p>
                <input type="text" name="bscs_settings[imagen]" id="photo" value="<?php echo esc_attr($options['imagen']); ?>" readonly/><a href="#" class="button" id="insert-my-media"><?php _e('Select', 'team-vcard-generator'); ?></a>
            </p>
            <p><img src="<?php echo esc_attr($options['imagen']); ?>" alt="" id="preview"></img></p>
            <p id="usar_imagen"><label for="usar_imagen"><?php _e('Activate: ', 'team-vcard-generator'); ?></label>
                <label for="usar_imagen_true"><?php _e('Yes ', 'team-vcard-generator'); ?></label><input type="radio" name="bscs_settings[usar_imagen]" value="true" <?php
                if ($options['usar_imagen'] == 'true') {
                    echo "checked = 'checked'";
                }
                ?> />
                <label for="usar_imagen_false"><?php _e('No ', 'team-vcard-generator'); ?></label><input type="radio" name="bscs_settings[usar_imagen]" value="false" <?php
            if ($options['usar_imagen'] == 'false') {
                echo "checked = 'checked'";
            }
            ?> />
            </p>
            <?php
            _e('If the member dont have a photo and this option is activated, the photo of the user will be this image.', 'team-vcard-generator');
        }

        //imagen del usuario por defecto
        function bscs_estilo_render() {
            $options = get_option('bscs_settings');
            ?>
            <p id='usar_estilo'>
                <label for="estilo1"><?php _e('Default ', 'team-vcard-generator'); ?></label><input type="radio" name="bscs_settings[estilo]" value="estilo1" <?php
                if ($options['estilo'] == 'estilo1') {
                    echo "checked = 'checked'";
                }
                ?> /><img class='estilos_imagen' src='<?php echo plugin_dir_url(__FILE__) . "../admin/img/estilo1.png"; ?>'>
                <label for="estilo2"><?php _e('Secondary ', 'team-vcard-generator'); ?></label><input type="radio" name="bscs_settings[estilo]" value="estilo2" <?php
            if ($options['estilo'] == 'estilo2') {
                echo "checked = 'checked'";
            }
            ?> /><img class='estilos_imagen' src='<?php echo plugin_dir_url(__FILE__) . "../admin/img/estilo2.png"; ?>'>
            </p>

            <?php
        }

        function bscs_colores_render() {
            //recoge todas las opciones para poder sacarlas luego
            $options = get_option('bscs_settings');
            ?>
            <div class='row color'>
                <div class='col-md-2'>
                    <p><label for="1color"><?php _e('1º Color: ', 'team-vcard-generator'); ?></label>
                        <input type="text" class="form-control colores" name='bscs_settings[color1]' id='color1' value="<?php echo esc_attr($options['color1']); ?>">
                    </p>
                    <p><label for="2color"><?php _e('2º Color: ', 'team-vcard-generator'); ?></label>
                        <input type="text" class="form-control colores" name='bscs_settings[color2]' id='color2' value="<?php echo esc_attr($options['color2']); ?>">
                    </p>
                </div>
                <div class='col-md-4'>
                    <p><?php _e('Top Background preview: ', 'team-vcard-generator'); ?></p>
                    <div id='preview_colores' style='background: linear-gradient(272deg, <?php echo $options['color1'] . ", " . $options['color2']; ?> );background-size: 400% 400%;-webkit-animation: fondocolores 12s ease infinite;-moz-animation: fondocolores 12s ease infinite;-o-animation: fondocolores 12s ease infinite;animation: fondocolores 12s ease infinite;'></div>
                </div>

                <div class='col-md-3'>
                    <p><?php _e('Browser address bar(smartphone): ', 'team-vcard-generator'); ?></p>
                    <div id='layer_color_padre'>
                        <div id='layer_color' style='background-color:<?php echo $options['color1'] ?>'>
                            <img src='<?php echo plugin_dir_url(__FILE__) . "../admin/img/color.png" ?>'>
                        </div>
                    </div>
                </div>
                <div class='col-md-3'>
                    <p><?php _e('Go up button: ', 'team-vcard-generator'); ?></p>
            <?php echo "<button class='scroll-top' type='button' style='background: " . $options['color1'] . " url(" . plugin_dir_url(__FILE__) . "../public/img/top-arrow.svg) no-repeat center 50%!important;'></button>"; ?>
                </div>
            </div>
            <?php
        }

        //No se puede quitar o da error, podemos insertar html aqui que iria entre el titulo y los ajustes / info
        function bscs_settings_section_callback() {
            
        }

        //Crea el HTML de las settings recogiendo las columnas / fields
        function bscs_options_page() {
            ?>
            <div id='team_member_settings' class="wrap" >
                <form action='options.php' method='post'>
                    <h2><?php _e('Settings', 'team-vcard-generator') ?></h2>
                    <?php
                    //define el settings que estamos utilizando - pluginPage
                    settings_fields('pluginPage');
                    //crea las secciones (columnas) que tenemos puestas - lo mete en una tabla automaticamente
                    do_settings_sections('pluginPage');
                    //nos mete el boton de submit por defecto de wordpress con traduccion propia
                    submit_button();
                    ?>
                </form>
            </div>

            <?php
        }

        // </editor-fold>

        /**
         * Para poder acceder al wp_media de wordpress
         */
        function load_wp_media_files() {
            wp_enqueue_media();
        }

        add_action('admin_enqueue_scripts', 'load_wp_media_files');

        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }

}
?>