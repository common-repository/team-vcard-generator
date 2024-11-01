<?php
//Pagina personal de cada miembro con la opción de descargar el vcf
//Inizializa variables de la BD con los valores del miembro
$id = get_the_ID();
$valores_post = json_decode(get_post_field('post_content', $id, 'raw'), true);
$padre = wp_get_post_parent_id($id);
if ($padre > 0) {
    $valores_post_padre = json_decode(get_post_field('post_content', $padre, 'raw'), true);
    if ($valores_post['usar_padre'] > 0) {
        foreach ($valores_post['usar_padre'] as $cambio => $nombre) {
            $valores_post[$nombre] = $valores_post_padre[$nombre];
        }
    }
}
//Recoge todos los datos de la tarjeta en JSON
$options = get_option('bscs_settings');
//Opciones del plugin configuradas en Settings

/* FORMULARIO SUMBIT */
if (isset($_POST['comprueba'])) {
    if ($_POST['comprueba'] == "") {
        header('Location: #send');
    } else {
        header('Location: /#not-send');
    }
}
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <!-- Chrome, Firefox OS and Opera -->
        <meta name="theme-color" content="<?php echo esc_html($options['color1']); ?>" />
        <!-- Windows Phone -->
        <meta name="msapplication-navbutton-color" content="<?php echo esc_html($options['color1']); ?>">
        <!-- iOS Safari -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo esc_html($options['color1']); ?>">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
        <?php
        $options = get_option('bscs_settings');
        wp_head();
        ?>
    </head>
    <body>
        <?php
        $photo = $valores_post['photo'];
        if ($photo == "" && $options['usar_imagen'] == 'true') {
            $photo = $options['imagen'];
        }

        function headers($opcion, $titulo) {
            global $valores_post;
            if ($opcion == 1) {
                $header = "<div class='container container_header'><div class='row'>";
                $padre = wp_get_post_parent_id($id);
                if ($padre > 0) {
                    $valores_post_padre = json_decode(get_post_field('post_content', $padre, 'raw'), true);
                    if ($valores_post_padre['photo'] != '') {
                        $header.="<div class='col-xs-3 col-md-3'><img class='logo_padre' src='" . $valores_post_padre['photo'] . "'></div>";
                    } else {
                        $header.="<div class='col-xs-3 col-md-3'><span>" . $valores_post_padre['firstname'] . "</span></div>";
                    }
                    $nombre_header.=$valores_post['firstname'];
                    if (isset($valores_post['lastname'])) {
                        $nombre_header.=' ' . $valores_post['lastname'];
                    }
                    $header.="<div class='col-xs-9 col-md-9'><span>" . $nombre_header . "</span></div>";
                } else {
                    $nombre_header.=$valores_post['firstname'];
                    if (isset($valores_post['lastname'])) {
                        $nombre_header.=' ' . $valores_post['lastname'];
                    }
                    $header.="<div class='col-xs-12 col-md-12 solonombre'><span>" . $nombre_header . "</span></div>";
                }

                $header.="</div></div></div>";
                return $header;
            }
        }

        function footers($opcion) {
            global $valores_post, $photo;
            $footer = "<form method='post' action='" . plugin_dir_url(__FILE__) . "VCard.php' data-ajax='false'>
                    <input type='hidden' name='firstname' value='" . esc_attr($valores_post['firstname']) . "'>
                    <input type='hidden' name='lastname' value='" . esc_attr($valores_post['lastname']) . "'>
                    <input type='hidden' name='mail_profesional' value='" . esc_attr($valores_post['mail_profesional']) . "'>
                    <input type='hidden' name='mail_personal' value='" . esc_attr($valores_post['mail_personal']) . "'>
                    <input type='hidden' name='mail_otro' value='" . esc_attr($valores_post['mail_otro']) . "'>
                    <input type='hidden' name='telefono_profesional' value='" . esc_attr($valores_post['telefono_profesional']) . "'>
                    <input type='hidden' name='telefono_personal' value='" . esc_attr($valores_post['telefono_otro']) . "'>
                    <input type='hidden' name='telefono_otro' value='" . esc_attr($valores_post['telefono_otro']) . "'>
                    <input type='hidden' name='nota' value='" . preg_replace('/\r|\n/', ' ', $valores_post['nota']) . "'>
                    <input type='hidden' name='cargo' value='" . esc_attr($valores_post['cargo']) . "'>
                    <input type='hidden' name='linkedin' value='" . esc_attr($valores_post['linkedin']) . "'>
                    <input type='hidden' name='twitter' value='" . esc_attr($valores_post['twitter']) . "'>
                    <input type='hidden' name='webpage' value='" . esc_attr($valores_post['webpage']) . "'>
                    <input type='hidden' name='address_calle' value='" . esc_attr($valores_post['direccion']['calle']) . "'>
                    <input type='hidden' name='address_numero' value='" . esc_attr($valores_post['direccion']['numero']) . "'>
                    <input type='hidden' name='address_ciudad' value='" . esc_attr($valores_post['direccion']['ciudad']) . "'>
                    <input type='hidden' name='address_provincia' value='" . esc_attr($valores_post['direccion']['provincia']) . "'>
                    <input type='hidden' name='address_zip' value='" . esc_attr($valores_post['direccion']['zip']) . "'>
                    <input type='hidden' name='empresa' value='" . esc_attr($empresa) . "'>
                    <input type='hidden' name='mail_empresa' value='" . esc_attr($mail_empresa) . "'>
                    <input type='hidden' name='photo' value='" . wp_make_link_relative(esc_attr($photo)) . "'>
                    <input type='hidden' name='logo_empresa' value='" . wp_make_link_relative(esc_attr($logo_empresa)) . "'>";
            if ($opcion == 1) {
                $footer.= "<div data-role='navbar'><ul>";
                $footer.="<li><a href='#share' data-transition='slidefade'><i class='fa fa-share-alt fa-lg'></i><span>" . __('Share', 'team-vcard-generator') . "</span></a></li>";
                $footer.="<li><a href='#inicial' data-transition='slidefade'><i class='fa fa-home fa-lg'></i><span>" . __('Home', 'team-vcard-generator') . "</span></a></li>";
                $footer.="<li><button type='submit' name='noIOS' value='submit-value' class='ui-btn-hidden' aria-disabled='false'><i class='fa fa-user-plus fa-lg'></i><span>" . __('Download', 'team-vcard-generator') . "</span></button></li>";
                $footer.="</ul></div></form>";
                return $footer;
            } else if ($opcion == 2) {
                
            } else if ($opcion == 3) {
                
            }
        }
        ?>
        <div data-role="page" id="inicial" data-enhance="true"><div data-role='header' data-id='header' data-position='fixed'>
                <?php echo headers(1, __('Inicio', 'team-vcard-generator')); ?>
                <div data-role="main" class="ui-content">
                    <?php
                    $dia_semana = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m"), date("d"), date("Y")));
                    if ($dia_semana == 0) {
                        $dia_semana = 6;
                    } else {
                        $dia_semana--;
                    }

                    function sumatiempo($valor, $tiempo_diferencia) {
                        $tiempo = $valor;
                        list($hora, $minuto) = explode(':', $tiempo);
                        $minutos += $hora * 60;
                        $minutos += $minuto;
                        $minutos+=$tiempo_diferencia;

                        $hora = floor($minutos / 60);
                        $minutos-=$hora * 60;
                        return sprintf('%02d:%02d', $hora, $minutos);
                    }

                    function restatiempo($valor1, $valor2) {
                        $tiempo1 = $valor1;
                        $tiempo2 = $valor2;
                        list($hora1, $minuto1) = explode(':', $tiempo1);
                        list($hora2, $minuto2) = explode(':', $tiempo2);
                        $minutos1 = $hora1 * 60;
                        $minutos1+= $minuto1;
                        $minutos2 = $hora2 * 60;
                        $minutos2+= $minuto2;
                        $diferencia = $minutos1 - $minutos2;
                        $hora = floor($diferencia / 60);
                        $diferencia-=$hora * 60;

                        return sprintf('%02d:%02d', $hora, $diferencia);
                    }

                    $color_disponible_class = 'rojo';
                    $cuanto_falta = "";
                    if (count($valores_post['horario']) > 0) {
                        if (isset($valores_post['horario'][$dia_semana])&&($valores_post['horario'][$dia_semana][0]==$dia_semana)) {
                            foreach ($valores_post['horario'][$dia_semana][1] as $final => $hora_inicio) {
                                if (date('H:i') < $hora_inicio) {
                                    $diferencia = restatiempo($hora_inicio, date('H:i'));
                                    $usar_horas = true;
                                    $usar_hora = false;
                                    if ($diferencia > "02:00") {
                                        
                                    } else if ($diferencia > "01:00") {
                                        $usar_horas = false;
                                        $usar_hora = true;
                                    } else {
                                        $usar_horas = false;
                                    }
                                    list($hora, $minuto) = split(":", $diferencia);
                                    $hora = ltrim($hora, 0);
                                    $hora_actual = ltrim($hora, 0); //JQUERY
                                    $minuto = ltrim($minuto, 0);
                                    $minuto_actual = ltrim($minuto, 0); //JQUERY
                                    if ($valores_post["tipo"] == "vcard_individual") {
                                        if ($usar_horas) {
                                            $cuanto_falta = __('Disponible in', 'team-vcard-generator') . " " . $hora . " " . __('hours. ', 'team-vcard-generator');
                                        } else {
                                            if ($usar_hora) {
                                                $cuanto_falta = __('Disponible in', 'team-vcard-generator') . " " . $hora . " " . __('hour and', 'team-vcard-generator') . " " . $minuto . " " . __('minutes', 'team-vcard-generator');
                                            } else {
                                                $cuanto_falta = __('Disponible in', 'team-vcard-generator') . " " . $minuto . " " . __('minutes. ', 'team-vcard-generator');
                                                $color_disponible_class = "naranja";
                                            }
                                        }
                                    } else {
                                        if ($usar_horas) {
                                            $cuanto_falta = __('Open in', 'team-vcard-generator') . " " . $hora . " " . __('hours. ', 'team-vcard-generator');
                                        } else {
                                            if ($usar_hora) {
                                                $cuanto_falta = __('Open in', 'team-vcard-generator') . " " . $hora . " " . __('hour and', 'team-vcard-generator') . " " . $minuto . " " . __('minutes', 'team-vcard-generator');
                                            } else {
                                                $cuanto_falta = __('Open in', 'team-vcard-generator') . " " . $minuto . " " . __('minutes. ', 'team-vcard-generator');
                                                $color_disponible_class = "naranja";
                                            }
                                        }
                                    }
                                    break;
                                } else if (date('H:i') < $valores_post['horario'][$dia_semana][2][$final]) {
                                    $diferencia = restatiempo($valores_post['horario'][$dia_semana][2][$final], date('H:i'));
                                    list($hora, $minuto) = split(":", $diferencia);
                                    $hora = ltrim($hora, 0);
                                    $hora_actual = ltrim($hora, 0); //JQUERY
                                    $minuto = ltrim($minuto, 0);
                                    $minuto_actual = ltrim($minuto, 0); //JQUERY
                                    if ($diferencia > "00:30") {
                                        if ($valores_post["tipo"] == "vcard_individual") {
                                            $cuanto_falta = __('Disponible', 'team-vcard-generator');
                                            $color_disponible_class = "verde";
                                        } else {
                                            $cuanto_falta = __('Open', 'team-vcard-generator');
                                            $color_disponible_class = "verde";
                                        }
                                        break;
                                    } else {
                                        if ($valores_post["tipo"] == "vcard_individual") {
                                            $cuanto_falta = __('Disponible for ', 'team-vcard-generator') ." ". $minuto ." ". __(' minutes. ', 'team-vcard-generator');
                                            $color_disponible_class = "naranja";
                                        } else {
                                            $cuanto_falta = __('Open for ', 'team-vcard-generator') ." ". $minuto ." ". __(' minutes. ', 'team-vcard-generator');
                                            $color_disponible_class = "naranja";
                                        }
                                        break;
                                    }
                                }
                            }
                        } else {
                            if ($dia_semana == 6) {
                                $dia_semana = 0;
                            } else {
                                $dia_semana+=1;
                            }
                            foreach($valores_post['horario'] as $dia => $val){
                                   if (in_array($dia_semana, $val)) {
                                        $cuanto_falta = __('Tomorrow at ', 'team-vcard-generator') . " " . $valores_post['horario'][$dia][1][0];
                                    }
                            }
                            if($cuanto_falta==""){
                                if ($valores_post["tipo"] == "vcard_individual") {
                                    $cuanto_falta = __('No disponible', 'team-vcard-generator');
                                } else {
                                    $cuanto_falta = __('Closed', 'team-vcard-generator');
                                }
                            }
                        }
                    }
                    if ($cuanto_falta != '') {
                        echo "<div class='row' id='disponible'><span class='disponible_circulo " . $color_disponible_class . "'></span><span class='disponible_texto'>" . $cuanto_falta . "</span></div>";
                        $cuanto_falta = "<div class='row' id='disponible'><span class='disponible_circulo " . $color_disponible_class . "'></span><span class='disponible_texto'>" . $cuanto_falta . "</span></div>";
                    }
                    echo "<div class='row row_disponible' style='background:linear-gradient(272deg," . $options['color1'] . ", " . $options['color2'] . ");background-size: 400% 400%;-webkit-animation: fondocolores 12s ease infinite;-moz-animation: fondocolores 12s ease infinite;-o-animation: fondocolores 12s ease infinite;animation: fondocolores 12s ease infinite;'></div><img class='degradado_izquierda' src='" . plugin_dir_url(__FILE__) . "../assets/degradado.png'><img class='degradado_derecha' src='" . plugin_dir_url(__FILE__) . "../assets/degradado.png'>";
                    echo "<div class='responsive'>";
                    $usada_imagen_en_ficha = false;
                    if ($valores_post['photo'] != '') {
                        $usada_imagen_en_ficha = true;
                        echo "<div class='row' id='ficha'><div class='foto'><img src='" . $valores_post['photo'] . "'></div>";
                    } else if ($options['usar_imagen'] == true && $options['imagen'] != '') {
                        $usada_imagen_en_ficha = true;
                        echo "<div class='row' id='ficha'><div class='row'><div class='foto'><img src='" . $options['imagen'] . "'></div>";
                    } else {
                        echo "<div class='row' id='ficha'>";
                    }
                    $nombre = "<div class='nombre'>" . $valores_post['firstname'] . " ";
                    if (isset($valores_post['lastname'])) {
                        $nombre.="" . $valores_post['lastname'];
                    }
                    if (isset($valores_post['cargo'])) {
                        $nombre.="<span>" . $valores_post['cargo'] . "</span>";
                    }
                    if ($usada_imagen_en_ficha == false) {
                        echo "<div class='col-md-12 nombre engrande'>" . $nombre . "</div></div></div><ul data-role='listview' data-inset='true'>";
                    } else {
                        echo "<div class='col-md-12 nombre'>" . $nombre . "</div></div></div><ul data-role='listview' data-inset='true'>";
                    }

                    /* TEXTOS PROPIOS */
                    if (count($valores_post['textos']) > 0) {
                        echo "<li><a href='#about' data-transition='slidefade'>" . __('About me', 'team-vcard-generator') . "</a></li>";
                        $about = "<div data-role='page' id='about' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('About me', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('About me', 'team-vcard-generator') . "</a>";
                        foreach ($valores_post['textos'] as $texto) {
                            $contenido_about.="<div class='container textos'>";
                            if ($texto['imagen'] == '') {
                                $contenido_about.="<div class='row row_textos_titulo' style='margin-top:0px;background: linear-gradient(272deg, " . $options['color1'] . "," . $options['color2'] . ")'><div class='col-md-12'><h3>" . $texto['titulo'] . "</h3></div></div>";
                            } else {
                                $contenido_about.="<div class='row row_textos_imagen'><img src='" . $texto['imagen'] . "'></div>";
                                $contenido_about.= "<div class='row row_textos_titulo'><div class='col-md-12'><h3>" . $texto['titulo'] . "</h3></div></div>";
                            }
                            $contenido_about.="<div class='row row_textos_contenido'><div class='col-md-12'>" . nl2br($texto['texto']) . "</div></div></div>";
                        }
                        $about.=$contenido_about . "<button class='scroll-top' type='button' style='background: " . $options['color1'] . " url(" . plugin_dir_url(__FILE__) . "../public/img/top-arrow.svg) no-repeat center 50%!important;'></button></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    /* ENLACE PADRE */
                    $padre = wp_get_post_parent_id($id);
                    if ($padre > 0) {
                        echo "<li id='redireccion_padre'><a href='" . get_permalink($padre) . "' data-transition='slidefade'>" . __('About ', 'team-vcard-generator') . "" . get_the_title($padre) . "</a></li>";
                    }
                    /* OFERTAS */
                    /* --------------------------------------------------------------------------------------------------------------------------------------- */
                    /* -----------------------------------------------------------CONTACTOS------------------------------------------------------------------- */
                    echo "<li><a href='#contact' data-transition='slidefade'>" . __('Contact', 'team-vcard-generator') . "</a></li>";
                    $contact = "<div data-role='page' id='contact' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Contact', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Contact', 'team-vcard-generator') . "</a><ul data-role='listview' data-inset='true'>";
                    /* ----------- TELEFONOS ---------- */
                    if ($valores_post['telefono_profesional'] != '' || $valores_post['telefono_personal'] != '' || $valores_post['telefono_otro'] != '') {
                        $contact.="<li><a href='#phones' data-transition='slidefade'>" . __('Phones', 'team-vcard-generator') . "</a></li>";
                        $contenido_contact = "";
                        if ($valores_post['telefono_profesional'] != '') {
                            $contenido_contact.="<li><a href='tel:" . $valores_post['telefono_profesional'] . "' class='ui-btn ui-btn-icon-right ui-icon-phone'><span>" . __('Profesional', 'team-vcard-generator') . "</span><span>" . $valores_post['telefono_profesional'] . "</span></a></li>";
                        }
                        if ($valores_post['telefono_personal'] != '') {
                            $contenido_contact.="<li><a href='tel:" . $valores_post['telefono_personal'] . "' class='ui-btn ui-btn-icon-right ui-icon-phone'><span>" . __('Personal', 'team-vcard-generator') . "</span><span>" . $valores_post['telefono_personal'] . "</span></a></li>";
                        }
                        if ($valores_post['telefono_otro'] != '') {
                            $contenido_contact.="<li><a href='tel:" . $valores_post['telefono_otro'] . "' class='ui-btn ui-btn-icon-right ui-icon-phone'><span>" . __('Other', 'team-vcard-generator') . "</span><span>" . $valores_post['telefono_otro'] . "</span></a></li>";
                        }
                        $phones = "<div data-role='page' id='phones' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Contact', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Phone', 'team-vcard-generator') . "</a><ul data-role='listview' data-inset='true'>" . $contenido_contact . "</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    /* ----------- MAILS ---------- */
                    if ($valores_post['mail_profesional'] != '' || $valores_post['mail_personal'] != '' || $valores_post['mail_otro'] != '') {
                        $contact.="<li><a href='#mails' data-transition='slidefade'>" . __('Emails', 'team-vcard-generator') . "</a></li>";
                        $contenido_mails = "";
                        if ($valores_post['mail_profesional'] != '') {
                            $contenido_mails.="<li><a href='mailto:" . $valores_post['mail_profesional'] . "' class='ui-btn ui-btn-icon-right ui-icon-mail'><span>" . __('Profesional', 'team-vcard-generator') . "</span><span>" . $valores_post['mail_profesional'] . "</span></a></li>";
                        }
                        if ($valores_post['mail_personal'] != '') {
                            $contenido_mails.="<li><a href='mailto:" . $valores_post['mail_personal'] . "' class='ui-btn ui-btn-icon-right ui-icon-mail'><span>" . __('Peronal', 'team-vcard-generator') . "</span><span>" . $valores_post['mail_personal'] . "</span></a></li>";
                        }
                        if ($valores_post['mail_otro'] != '') {
                            $contenido_mails.="<li><a href='mailto:" . $valores_post['mail_otro'] . "' class='ui-btn ui-btn-icon-right ui-icon-mail'><span>" . __('Other', 'team-vcard-generator') . "</span><span>" . $valores_post['mail_otro'] . "</span></a></li>";
                        }
                        $mails = "<div data-role='page' id='mails' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Contact', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Email', 'team-vcard-generator') . "</a><ul data-role='listview' data-inset='true'>" . $contenido_mails . "</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    /* ----------- MAPA ---------- */
                    if ($valores_post['direccion']['calle'] != '' && $valores_post['direccion']['ciudad']) {
                        $contact.="<li><a href='#map'  data-transition='slidefade'>" . __('Where can find me', 'team-vcard-generator') . "</a></li>";
                        $direccion = $valores_post['direccion']['calle'];
                        if ($valores_post['direccion']['numero'] != '') {
                            $direccion.=" " . $valores_post['direccion']['numero'];
                        }
                        if ($valores_post['direccion']['numero'] != '') {
                            $direccion.=" " . $valores_post['direccion']['numero'];
                        }
                        $direccion.="," . $valores_post['direccion']['ciudad'];
                        if ($valores_post['direccion']['provincia'] != '') {
                            $direccion.=" " . $valores_post['direccion']['provincia'];
                        }
                        if ($valores_post['direccion']['zip'] != '') {
                            $direccion.="," . $valores_post['direccion']['zip'];
                        }
                        $direccion = preg_replace('/\s+/', '+', $direccion);
                        $contenido_map = "<iframe width='100%' height='450' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/place?key=AIzaSyBwsQctV_GYINbcItH-VW78oXo_u8wR-k4&q=" . $direccion . "' allowfullscreen></iframe>";
                        $map = "<div data-role='page' id='map' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Where can find me', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Where can find me', 'team-vcard-generator') . "</a><ul data-role='listview' data-inset='true'>" . $contenido_map . "</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    /* ----------- FORMULARIO ---------- */
                    $contact.="<li><a href='#form' data-transition='slidefade'>" . __('Contact form', 'team-vcard-generator') . "</a></li>";

                    $contenido_form = "<form action='' method='post' data-ajax='false' id='contacto'><div class='ui-field-contain'><label for='text-basic'>" . __('Name', 'team-vcard-generator') . ":</label><input type='text' name='nombre' id='nombre' data-mini='true'></div>";
                    $contenido_form.="<input type='hidden' name='comprueba' id='comprueba'><input type='hidden' name='to' id='to' value='" . $valores_post['mail_profesional'] . "'>";
                    $contenido_form.="<div class='ui-field-contain'><label for='text-basic'>" . __('Company', 'team-vcard-generator') . ":</label><input type='text' name='empresa' id='empresa' data-mini='true'></div>";
                    $contenido_form.="<div class='ui-field-contain'><label for='text-basic'>" . __('Phone', 'team-vcard-generator') . ":</label><input type='tel' name='numero' id='numero' data-mini='true'></div>";
                    $contenido_form.="<div class='ui-field-contain'><label for='text-basic'>" . __('Email', 'team-vcard-generator') . ":</label><input type='text' name='text-basic' id='nombre' data-mini='true'></div>";
                    $contenido_form.="<div class='ui-field-contain'><label for='text-basic'>" . __('Subject', 'team-vcard-generator') . ":</label><input type='text' name='asunto' id='asunto' data-mini='true'></div>";
                    $contenido_form.="<div class='ui-field-contain'><label for='text-basic'>" . __('Message', 'team-vcard-generator') . ":</label><textarea cols='40' rows='8' name='contenido' id='contenido' data-mini='true'></textarea></div>";
                    $contenido_form.="<button type='submit' name='submit' id='submit' class='ui-btn ui-shadow ui-corner-all'>" . __('Send', 'team-vcard-generator') . "</button></form>";
                    $form = "<div data-role='page' id='form' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Where can find me', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Contact form', 'team-vcard-generator') . "</a>" . $contenido_form . "</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";


                    /* ---------- SCHEDULE ----------- */
                    if ($valores_post['horario'] > 0) {
                        $contact.="<li><a href='#schedule' data-transition='slidefade'>" . __('When im available', 'team-vcard-generator') . "</a></li>";
                        $contenido_schedule = "<div class='table-responsive'><table class='table'><thead><tr><td>" . __('Days', 'team-vcard-generator') . "</td><td>" . __('Hours', 'team-vcard-generator') . "</td></tr></thead><tbody>";
                        $horas = "";
                        foreach ($valores_post['horario'] as $dia) {
                            $horas = "";
                            if ($dia[0] == 0) {
                                $nombre_dia = __('Monday', 'team-vcard-generator');
                            } else if ($dia[0] == 1) {
                                $nombre_dia = __('Tuesday', 'team-vcard-generator');
                            } else if ($dia[0] == 2) {
                                $nombre_dia = __('Wednesday', 'team-vcard-generator');
                            } else if ($dia[0] == 3) {
                                $nombre_dia = __('Thursday', 'team-vcard-generator');
                            } else if ($dia[0] == 4) {
                                $nombre_dia = __('Friday', 'team-vcard-generator');
                            } else if ($dia[0] == 5) {
                                $nombre_dia = __('Sataurday', 'team-vcard-generator');
                            } else if ($dia[0] == 6) {
                                $nombre_dia = __('Sunday', 'team-vcard-generator');
                            }
                            if ($dia[0] == $dia_semana) {
                                $horas.="<tr class='active'><td>" . $nombre_dia . "</td><td>";
                            } else {
                                $horas.="<tr><td>" . $nombre_dia . "</td><td>";
                            }
                            $cuenta_horarios = count($dia[1]);
                            foreach ($dia[1] as $numero => $hora) {
                                if (($numero + 1) == $cuenta_horarios) {
                                    $horas.="<span>" . $dia[1][$numero] . "-" . $dia[2][$numero] . "</span></td>";
                                } else {
                                    $horas.="<span>" . $dia[1][$numero] . "-" . $dia[2][$numero] . "</span> ";
                                }
                            }
                            $contenido_schedule.=$horas;
                        }

                        $contenido_schedule.="</tbody></table></div>";
                        $schedule = "<div data-role='page' id='schedule' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('When im available', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Schedule', 'team-vcard-generator') . "</a>" . $contenido_schedule . "</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }

                    $contact.="</ul></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";

                    /* --------------------------------------------------------- FINAL CONTACTOS ------------------------------------------------------------- */
                    /* --------------------------------------------------------------------------------------------------------------------------------------- */
                    /* OFERTAS */
                    if (count($valores_post['ofertas']) > 0) {
                        echo "<li><a href='#offers' data-transition='slidefade'>" . __('Offers', 'team-vcard-generator') . "</a></li>";
                        $ofertas = "<div data-role='page' id='offers' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Offers', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Offers', 'team-vcard-generator') . "</a>";
                        foreach ($valores_post['ofertas'] as $texto) {
                            $contenido_oferta.="<div class='container textos'>";
                            if ($texto['imagen'] == '') {
                                $contenido_oferta.= "<div class='row row_textos_titulo' style='margin-top:0px;background:linear-gradient(272deg, " . $options['color1'] . "," . $options['color2'] . ")'><div class='col-md-12'><h3>" . $texto['titulo'] . "</h3></div></div>";
                            } else {
                                $contenido_oferta.="<div class='row row_textos_imagen'><img src='" . $texto['imagen'] . "'></div>";
                                $contenido_oferta.= "<div class='row row_textos_titulo'><div class='col-md-12'><h3>" . $texto['titulo'] . "</h3></div></div>";
                            }
                            $contenido_oferta.="<div class='row row_textos_contenido'><div class='col-md-12'>" . nl2br($texto['texto']) . "</div></div></div>";
                        }
                        $ofertas.=$contenido_oferta . "<button class='scroll-top' type='button' style='background: " . $options['color1'] . " url(" . plugin_dir_url(__FILE__) . "../public/img/top-arrow.svg) no-repeat center 50%!important;'></button></div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    /* REDES SOCIALES */
                    //@todo navbar seleccion twitter y facebook?
                    if (count($valores_post['links']) > 0 || $valores_post['widget']['twitter'] != '' || $valores_post['widget']['facebook'] != '') {
                        echo "<li><a href='#social' data-transition='slidefade'>" . __('Social networks / Links', 'team-vcard-generator') . "</a></li>";
                    }
                    $contenido_social = "";
                    if (count($valores_post['links']) > 0) {
                        foreach ($valores_post['links'] as $texto) {
                            $contenido_social.="<li><a href='" . $texto['direccion'] . "'>" . $texto['sitio'] . "</a></li>";
                        }
                    }
                    $contenido_social.="</ul>";
                    if ((isset($valores_post['widget']['twitter']) && $valores_post['widget']['twitter'] != '') && ($valores_post['tipo'] = "vcard_individual" || $valores_post['widget']['facebook'] != '')) {
                        $contenido_social.="<p style='text-align:center;'>" . $valores_post['widget']['twitter'] . "</p>";
                    } else if ($valores_post['widget']['facebook'] != '') {
                        //$contenido_social.="<div class='fb-comments' data-href='".$valores_post['widget']['facebook']."' data-width='100%' data-numposts='5' data-colorscheme='light'></div>";
                        $contenido_social.="<div style='text-align:center;width:100%'><iframe src='https://www.facebook.com/plugins/page.php?href=" . $valores_post['widget']['facebook'] . "&tabs=timeline&width=320&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId' width='320' height='500' style='border:none;overflow:hidden' scrolling='no' frameborder='0' allowTransparency='true'></iframe></div>";
                    }
                    $social = "<div data-role='page' id='social' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Social networks / Links', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $cuanto_falta . "<a href='#' class='ui-btn' data-rel='back'><i class='fa fa-chevron-left'></i>" . __('Social networks / Links', 'team-vcard-generator') . "</a><ul data-role='listview' data-inset='true'>" . $contenido_social . "</div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";

                    /* LISTAS */

                    $padre_id = get_the_ID();
                    $individuo = false;
                    if ($valores_post['tipo'] == "vcard_individual") {
                        $padre_id = wp_get_post_parent_id($id);
                        $individuo = true;
                    }
                    $hijos_padre_sin_filtrar = get_posts('post_type=team_member&&post_status=publish&&post_parent=' . $padre_id);
                    $individuos = array();
                    foreach ($hijos_padre_sin_filtrar as $hijo) {
                        if ($hijo->post_excerpt == 'vcard_individual') {
                            array_push($individuos, $hijo->ID);
                        }
                    }
                    $sucursales = array();
                    $company_id = get_posts('post_type=team_member&&post_status=publish&&post_excerpt');
                            $cuenta_sucursales = get_posts('post_parent=' . $company_id . '&&post_type=team_member&&post_status=publish');
                            foreach ($cuenta_sucursales as $sucursal) {
                                if ($sucursal->post_excerpt == 'vcard_office') {
                                    array_push($sucursales, $sucursal->ID);
                                }
                            }
                    $team = "";
                    if (count($individuos) > 0) {
                        echo "<li><a href='#team' data-transition='slidefade'>" . __('Our team', 'team-vcard-generator') . "</a></li>";
                        $contenido_team = '<form><input id="filtro-team" data-type="search"/></form><table data-role="table" data-mode="" data-filter="true" data-input="#filtro-team" class="ui-responsive table-stroke"><tbody>';
                        $options = get_option('bscs_settings');
                        foreach ($individuos as $individuo_id) {
                            $valores_ind = json_decode(get_post_field('post_content', $individuo_id, 'raw'), true);
                            if ($valores_ind['photo'] != '') {
                                $foto_ind = $valores_ind['photo'];
                                $imagen_circular = "<img src='" . $foto_ind . "' style='width:50px;height:50px;-webkit-border-radius:150px;-moz-border-radius:150px;-ms-border-radius: 150px;-o-border-radius: 150px;border-radius: 150px;'>";
                            } else {
                                $foto_ind = plugin_dir_url(__FILE__) . 'user.svg';
                                $imagen_circular = '';
                            }
                            $contenido_team.='<tr href="' . get_permalink($individuo_id) . '"><td>' . $imagen_circular . '
                        </td><td><span style="font-weight:bold;">' . get_the_title($individuo_id) . '</span><br/><span style="font-size:10px;">' . $valores_ind['cargo'] . '</span></td></td></tr>';
                        }
                        $contenido_team .='</tbody></table>';
                        $team = "<div data-role='page' id='team' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Team', 'team-vcard-generator')) . "<div data-role='main' class='ui-content'><div class='responsive'>" . $contenido_team . "</div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div></div>";
                    }
                    $branches = "";
                    if (count($sucursales) > 0) {
                        if ($individuo == true && $padre_id!='') {
                            echo "<li><a href='#branches' data-transition='slidefade'>" . __('Other branches', 'team-vcard-generator') . "</a></li>";
                        } else if($padre_id!=''){
                            echo "<li><a href='#branches' data-transition='slidefade'>" . __('Our branches', 'team-vcard-generator') . "</a></li>";
                        }
                        $contenido_branches = '<form><input id="filtro-branches" data-type="search"/></form>
                        <table data-role="table" data-mode="" data-filter="true" data-input="#filtro-branches" class="ui-responsive table-stroke"><tbody>';
                        $options = get_option('bscs_settings');
                        foreach ($sucursales as $sucursal_id) {
                            $valores_suc = json_decode(get_post_field('post_content', $sucursal_id, 'raw'), true);
                            if ($valores_suc['photo'] != '') {
                                $foto_ind = $valores_suc['photo'];
                            } else {
                                $foto_ind = plugin_dir_url(__FILE__) . 'user.svg';
                            }
                            $contenido_branches.='<tr href="' . get_permalink($sucursal_id) . '"><td style="text-align:center;vertical-align: middle;"><img src="' . $foto_ind . '" style="width:50px;height:50px;-webkit-border-radius:150px;-moz-border-radius:150px;-ms-border-radius: 150px;-o-border-radius: 150px;border-radius: 150px;">
                        </td><td style="text-align:center;vertical-align: middle;"><span style="font-weight:bold;">' . get_the_title($sucursal_id) . '</span><br/><span style="font-size:10px;">' . $valores_suc['cargo'] . '</span></td></tr>';
                        }
                        $contenido_branches .='</tbody></table>';
                        $branches = "<div data-role='page' id='branches' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>" . headers(1, __('Branches', 'team-vcard-generator')) . "</h1><div data-role='main' class='ui-content'><div class='responsive'>" . $contenido_branches . "</div></div><div data-role='footer' data-id='footer' data-position='fixed'>" . footers(1) . "</div></div>";
                    }
                    ?> 
                    </ul>
                </div></div>
            <div data-role='footer' data-id="footer" data-position='fixed' id="donde"><?php echo footers(1); ?></div>
        </div>

        <!-- data-html="<span class='ui-bar ui-overlay-a ui-corner-all'><img src='http://preloaders.net/preloaders/728/Skype%20balls%20loader.gif'/></span>"; -->
        <?php
        if (isset($about)) {
            echo $about;
        }
        if (isset($cada_texto)) {
            echo $cada_texto;
        }
        if (isset($contact)) {
            echo $contact;
        }
        if (isset($phones)) {
            echo $phones;
        }
        if (isset($mails)) {
            echo $mails;
        }
        if (isset($map)) {
            echo $map;
        }
        if (isset($form)) {
            echo $form;
        }
        if (isset($schedule)) {
            echo $schedule;
        }
        if (isset($social)) {
            echo $social;
        }
        if (isset($team)) {
            echo $team;
        }
        if (isset($branches)) {
            echo $branches;
        }
        if (isset($ofertas)) {
            echo $ofertas;
        }
        ?>
        <div data-role='page' id='send' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>
                <?php echo headers(1, __('Sent!', 'team-vcard-generator')); ?>
                <div data-role='main' class='ui-content'><div class='responsive'><a href='#inicial' class='ui-btn'><i class='fa fa-chevron-left'></i><?php _e('Sent', 'team-vcard-generator'); ?></a>
                        <p><?php _e('Thank you', 'team-vcard-generator') ?></p>
                    </div></div><div data-role='footer' data-id="footer" data-position='fixed'><?php echo footers(1); ?></div></div>
            <div data-role='page' id='not-send' data-enhance='false'><div data-role='header' data-id='header' data-position='fixed'>
                    <?php echo headers(1, __('Mail cannot be send!', 'team-vcard-generator')); ?>
                    <div data-role='main' class='ui-content'><div class='responsive'><a href='#inicial' class='ui-btn'><i class='fa fa-chevron-left'></i><?php _e('Not send', 'team-vcard-generator'); ?>s</a>
                            <p><?php _e('There was a problem sending the mail', 'team-vcard-generator') ?></p>
                        </div></div><div data-role='footer' data-id="footer" data-position='fixed'><?php echo footers(1); ?></div></div>
                <div data-role="page" id="share" data-url="share" data-enhance="false"><div data-role='header' data-id='header' data-position='fixed'>
                        <?php echo headers(1, __('Share', 'team-vcard-generator')); ?>
                        <div data-role='main' class='ui-content'><div class='responsive'><a href='#inicial' class='ui-btn'><i class='fa fa-chevron-left'></i><?php _e('Share', 'team-vcard-generator'); ?></a>
                                <div class="container container_share">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ?>"><i class="fa fa-facebook-official fa-2x"></i><span>Facebook</span></a>  
                                        </div>
                                        <div class="col-xs-6">
                                            <a href="https://plus.google.com/share?content=<?php
                                            _e('Check this card: ', 'team-vcard-generator');
                                            echo " " . get_site_url() . $_SERVER["REQUEST_URI"]
                                            ?>"><i class="fa fa-google-plus fa-2x"></i><span>Google +</span></a>  
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <a href="https://twitter.com/intent/tweet?text=<?php
                                            _e('Check this card: ', 'team-vcard-generator');
                                            echo " " . get_site_url() . $_SERVER["REQUEST_URI"]
                                            ?>"><i class="fa fa-twitter fa-2x"></i><span>Twitter</span></a>  
                                        </div>
                                        <div class="col-xs-6">
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&title=<?php
                                            _e('Check this card: ', 'team-vcard-generator');
                                            echo " " . get_site_url() . $_SERVER["REQUEST_URI"]
                                            ?>"><i class="fa fa-linkedin-square fa-2x"></i><span>Linkedin</span></a>  
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <a href="whatsapp://send?text=<?php
                                            _e('Check this card: ', 'team-vcard-generator');
                                            echo " " . get_site_url() . $_SERVER["REQUEST_URI"]
                                            ?>" data-action="share/whatsapp/share"><i class="fa fa-whatsapp fa-2x"></i><span>Whatsapp</span></a>  
                                        </div>
                                        <div class="col-xs-6">
                                            <a href="sms:?&body=<?php
                                            _e('Check this card: ', 'team-vcard-generator');
                                            echo " " . get_site_url() . $_SERVER["REQUEST_URI"]
                                            ?>"><i class="fa fa-commenting fa-2x"></i><span>SMS</span></a>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div data-role='footer' data-id="footer" data-position='fixed' id="donde"><?php echo footers(1); ?></div>
                    </div>
                </div>
                </body>
                </html>
