<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('srv1764.hstgr.io', 'u350475089_rutvans_corp', 'Generacion-2022', 'u350475089_rutvans');
if ($mysqli->connect_error) {
    die('Error de conexión: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
echo "¡Conexión exitosa!";
?>