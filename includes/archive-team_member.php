<?php
//Redirige a compañia
global $wpdb;
$id_compañia=$wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts where post_excerpt='vcard_company'");
$enlace=get_permalink($id_compañia);
header('Location: '.$enlace);
exit();
?>
