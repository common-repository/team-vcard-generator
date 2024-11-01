<?php
if (isset($_POST['comprueba'])) {
    if ($_POST['comprueba'] == '') {
        $to = $_POST['to'];
        $subject = $_POST['asunto'];
        $contenido=$_POST['contenido'];
        $message = "<html><head></head><body><p>".$contenido."</p></body></html>";
        if (mail($to, $subject, $message)) {
            echo "true";
        } else {
            echo "false";
        }
    } else {
        echo "ROBOT!";
    }
}
?>
