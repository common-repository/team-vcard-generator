<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
//Tabla/Lista del plugin / pagina de administración
//Si no existe, importa el WP_List_Table de wordpress
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Prepara la tabla llenandola de datos y controla acciones (activar, desactivar, borrar miembros) 
 */
class Customers_List extends WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct([
            'singular' => __('Member', 'team-vcard-generator'), //singular name of the listed records
            'plural' => __('Members', 'team-vcard-generator'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ]);
    }

    // <editor-fold defaultstate="collapsed" desc=" SELECT Datos de miembros"> 
    /**
     * Select de los datos de la base de datos / miembros dependiendo si estamos buscando algo , orden o estado 
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_customers($per_page = 5, $page_number = 1) {
        global $wpdb;
        //estado sera la variable de tipo de post, activo-trash que retocamos a inactive para la url
        global $estado;
        if (isset($_GET['status'])) {
            $estado = $_GET["status"];
            if ($estado == "inactive") {
                $estado = "trash";
            }
        } else {
            $estado = 'publish';
        }
        //Selecciona miembros de la BD dependiendo del estado
        $sql = "SELECT * from {$wpdb->prefix}postmeta JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}postmeta.post_id={$wpdb->prefix}posts.ID where {$wpdb->prefix}posts.post_type='team_member' and {$wpdb->prefix}postmeta.meta_key='firstname' and {$wpdb->prefix}posts.post_status='" . $estado . "'";
        //Si buscamos algo nos añade el LIKE
        if (isset($_POST['busqueda-miembros'])) {
            $sql.= " AND {$wpdb->prefix}postmeta.meta_value LIKE '%" . $_POST['busqueda-miembros'] . "%'";
        }
        //Si pide order ordenamos la consulta
        if (!empty($_REQUEST['orderby'])) {
            $sql .= " AND {$wpdb->prefix}postmeta.meta_key='" . esc_sql($_REQUEST['orderby']) . "' ORDER BY wp_postmeta.meta_value";
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }

    /**
     * Cuenta el total de elementos en nuestra tabla
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb, $estado;
        if (isset($_GET['status'])) {
            $estado = $_GET["status"];
            if ($estado == "inactive") {
                $estado = "trash";
            }
        } else {
            $estado = 'publish';
        }
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type='team_member' and post_status='" . $estado . "'";
        if (isset($_POST['busqueda-miembros'])) {
            $sql.= 'WHERE firstname LIKE %' . $_POST['busqueda-miembros'] . '% or lastname LIKE %' . $_POST['busqueda-miembros'] . '%';
        }
        return $wpdb->get_var($sql);
    }

    /**
     * En caso de que no encuentre miembros
     */
    public function no_items() {
        _e('No members available', 'team-vcard-generator');
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc=" Contenido de las columnas"> 

    /**
     *  Columnas que queremos mostrar en la tabla, esta funcion llama a function column_$nombre-columna automaticamente(wp) cuando prepare_items();
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'firstname' => __('Firstname', 'team-vcard-generator'),
            'lastname' => __('Lastname', 'team-vcard-generator'),
            'mail_profesional' => __('Email address ', 'team-vcard-generator'),
            'qrcode' => 'QR-Code'
        );

        return $columns;
    }

    /**
     * En caso de no tener una columna especifica (column_firstname) este sera el contenido de la columna por defecto
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name) {
        $valores_post = json_decode(get_post_field('post_content', $item['ID'], 'raw'), true);
        $result = $valores_post[$column_name];
        return $result;
    }

    /**
     * La columna del primer nombre no queremos que sea igual que las demas asi que le añadimos las opciones de editar/ver y dependiendo del estado activar o borrar
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_firstname($item) {
        global $estado;
        $delete_nonce = wp_create_nonce('sp_delete_customer');
        if(isset($item['name'])){
          $title = '<strong>' .$item['name']. '</strong>';
        }else{
          $title = '<strong></strong>';
        }
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=edit_team_member&member=' . $item['ID'] . '">' . __('Edit', 'team-vcard-generator') . '</a>', $_REQUEST['page'], 'edit', $item['ID']),
            'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">' . __('Delete', 'team-vcard-generator') . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce));
        if ($estado == "trash") {
            $actions['active'] = sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">' . __('Activate', 'team-vcard-generator') . '</a>', esc_attr($_REQUEST['page']), 'active', absint($item['ID']), $delete_nonce);
        } else {
            $actions['desactive'] = sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">' . __('Deactivate', 'team-vcard-generator') . '</a>', esc_attr($_REQUEST['page']), 'desactive', absint($item['ID']), $delete_nonce);
            $actions['view'] = sprintf('<a href="' . get_permalink($item['ID']) . '">' . __('View', 'team-vcard-generator') . '</a>');
        }
        global $wpdb;
        $result = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id=" . $item['ID'] . " and meta_key='firstname'");
        return $result . " " . $this->row_actions($actions);
    }
    function column_qrcode($item){
        global $wpdb;
        $result = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id=" . $item['ID'] . " and meta_key='firstname'");
        $contenido ="<img class='preview_qrcode' src='".plugin_dir_url(__FILE__) ."vcf/".$result.'-'.$item['ID'] .".png'>";
        $contenido.="<a class='download_qrcode button' href='".plugin_dir_url(__FILE__)."vcf/".$result.'-'.$item['ID'].".png' download>".__('Download', 'team-vcard-generator')."</a>";
        return $contenido;
    }

    /**
     * 1a columna , mete el checkbox a los elementos de la tabla para selección multiple
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    /**
     * Para que puedan ordenarse los resultados a través de las columnas
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'firstname' => array('firstname', true),
            'lastname' => array('lastname', false),
            'mail_profesional' => array('mail', false)
        );

        return $sortable_columns;
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc=" BULK / Modifica Miembros (activar, desactivar, borrar)"> 

    /**
     * Devuelve un array asociativo que contiene las opciones del checkbox (borrar, activar...)
     *
     * @return array
     */
    public function get_bulk_actions() {
        global $estado;
        $actions = array();
        if ($estado == "trash") {
            $actions['bulk-active'] = __('Activate', 'team-vcard-generator');
            $actions['bulk-delete'] = __('Delete', 'team-vcard-generator');
        } else {
            $actions['bulk-trash'] = __('Deactivate', 'team-vcard-generator');
        }
        return $actions;
    }

    /**
     * En caso de que hagamos una accion con el bulk se encarga de redirigir el codigo para que lo haga (borrar,activar...)
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                //borra
                self::delete_customer(absint($_GET['customer']));
            }
        } else if ('active' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                //activa
                self::active_customer(absint($_GET['customer']));
            }
        } else if ('desactive' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                //desactiva
                self::trash_customer(absint($_GET['customer']));
            }
        }
        // If the delete bulk action is triggered
        if (( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' )
        ) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            if ($delete_ids > 0) {
                //borra todos los elementos
                foreach ($delete_ids as $id) {
                    self::delete_customer($id);
                }
                //actualiza la pagina
                echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . esc_url(add_query_arg()) . "'>";
            }
        } else if (( isset($_POST['action']) && $_POST['action'] == 'bulk-trash' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-trash' )
        ) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            if ($delete_ids > 0) {
                //desactiva todos los elementos
                foreach ($delete_ids as $id) {
                    self::trash_customer($id);
                }
                //actualiza la pagina
                echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . esc_url(add_query_arg()) . "'>";
            }
        } else if (( isset($_POST['action']) && $_POST['action'] == 'bulk-active' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-active')
        ) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            if ($delete_ids > 0) {
                foreach ($delete_ids as $id) {
                    //activa todos los elementos
                    self::active_customer($id);
                }
                //actualiza la pagina
                echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . esc_url(add_query_arg()) . "'>";
            }
        }
    }

    /**
     * Borra el member y todas sus metabox
     *
     * @param int $id customer ID
     */
    public static function delete_customer($id) {
        global $wpdb;
        if(isset($id)){
        $borrar = $wpdb->delete($wpdb->prefix . "postmeta", array('post_id' => $id));
        $borrar = $wpdb->delete($wpdb->prefix . "posts", array('ID' => $id));
        }
    }

    /**
     * Nos pone al member en trash/inactivo
     * 
     * @global type $wpdb
     * @param type $id
     * @return type
     */
    public static function trash_customer($id) {
        global $wpdb;
        if (!$post = get_post($id))
            return;
        $wpdb->update($wpdb->posts, array('post_status' => 'trash'), array('ID' => $post->ID));
        clean_post_cache($post->ID);
        $old_status = $post->post_status;
        $post->post_status = 'trash';
        wp_transition_post_status('trash', $old_status, $post);
        wp_transition_post_status('publish', "'" . get_post_status($id) . "'", $id);
    }

    /**
     * Activa el member
     * 
     * @global type $wpdb
     * @param type $id
     * @return type
     */
    public static function active_customer($id) {
        global $wpdb;
        if (!$post = get_post($id))
            return;
        $wpdb->update($wpdb->posts, array('post_status' => 'publish'), array('ID' => $post->ID));
        clean_post_cache($post->ID);
        $old_status = $post->post_status;
        $post->post_status = 'publish';
        wp_transition_post_status('publish', $old_status, $post);
        wp_transition_post_status('trash', "'" . get_post_status($id) . "'", $id);
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc=" Construye la tabla y la llena"> 
    /**
     * Handles data query and filter, sorting, and pagination.
     * Genera la tabla
     */
    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();
        $per_page = $this->get_items_per_page('customers_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        $this->items = self::get_customers($per_page, $current_page);
    }

    // </editor-fold>
}

/**
 * CREA EL HTML DE LA PAGINA con el contenido de Customer_List
 */
class SP_Plugin {

    // class instance
    static $instance;
    // customer WP_List_Table object
    public $customers_obj;

    /**
     * class constructor
     */
    public function __construct() {
        add_filter('set-screen-option', __CLASS__ . 'set_screen', 10, 3);
        add_action('admin_menu', array($this, 'plugin_menu'));
    }

    /**
     * Valores / Opciones de la tabla
     */
    public function screen_option() {
        $option = 'per_page';
        $args = array(
            'label' => 'Customers',
            'default' => 10,
            'option' => 'customers_per_page'
        );

        add_screen_option($option, $args);

        $this->customers_obj = new Customers_List();
    }

    public static function set_screen($status, $option, $value) {
        return $value;
    }

    public function plugin_menu() {
        $hook = add_submenu_page('team_member', __('Team members', 'team-vcard-generator'), __('Team members', 'team-vcard-generator'), 'manage_options', 'manage_team_member', array($this, 'plugin_settings_page'));
        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * Codigo HTML de la lista/tabla
     * 1.- prepare_items() para que monte los datos que puedan ser utilizados en 2.- y poder hacer el display de la tabla luego
     * 2.- HTML encima de la tabla/lista - Botones añadir nuevo,numero de miembros, ver todos, estados, cuadro de busqueda...
     * 3.- display() y cierre HTML
     */
    public function plugin_settings_page() {
        $this->customers_obj->prepare_items();
        ?>
        <div class="wrap" id="team_member">
            <h2><?php _e('Members', 'team-vcard-generator') ?> 
                <a class="add-new-h2" href="?page=new_team_member"><?php _e('Add new', 'team-vcard-generator') ?></a></h2>
            <ul class="subsubsub"><li class="all"><a href="admin.php?page=manage_team_member" <?php
                    if (!isset($_GET["status"])) {
                        echo "class='current'";
                    }
                    ?>><?php _e('Active', 'team-vcard-generator') ?> <span class="count"> (<?php
                    global $wpdb;
                    echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type='team_member' and post_status='publish' and post_excerpt!='vcard_company'");
                    ?>)</span></a> |</li>
                <li class="publish"><a href="admin.php?page=manage_team_member&status=inactive" <?php
                    if (isset($_GET['status'])&& $_GET["status"] == "inactive") {
                        echo "class='current'";
                    }
                    ?>><?php _e('Inactive', 'team-vcard-generator') ?><span class="count"> (<?php
                                       global $wpdb;
                                       echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type='team_member' and post_status='trash' and post_excerpt!='vcard_company'");
                                       ?>)</span></a></li></ul>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <form method="post"><p class="search-box">
                            <label class="screen-reader-text" for="post-search-input">Buscar entradas:</label>
                            <input type="search" id="post-search-input" name="busqueda-miembros" value="">
                            <input type="submit" id="search-submit" class="button" value=<?php _e('Search members', 'team-vcard-generator') ?>"></p>
                            <?php
                            $this->customers_obj->display();
                            ?>
                                   </form>
                                   <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">

                        </div>
                </div>
            </div>
            <br class="clear">
        </div>
        </div>
        <?php
    }

    /**
     * En caso de no tener una instance la inizializa
     * 
     * @return type
     */
    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

/**
 * Cuando carga el plugin llama a la instancia, si no esta genereda crea una
 */
add_action('plugins_loaded', function () {
    SP_Plugin::get_instance();
});

/**
 * Se encarga de ordenarnos el submenu del panel de administración para que la lista este el primero
 * 
 * @global array $submenu
 * @param type $menu_ord
 * @return type
 */
function wpse_73006_submenu_order($menu_ord) {
    global $submenu;

    $arr = array();
    $arr[] = $submenu['team_member'][4]; //lista
    $arr[] = $submenu['team_member'][2]; //empresa
    $arr[] = $submenu['team_member'][1]; //nuevo
    $arr[] = $submenu['team_member'][3]; //configuracion
    $submenu['team_member'] = $arr;

    return $menu_ord;
}

add_filter('custom_menu_order', 'wpse_73006_submenu_order');
?>
