<?php
$conexion = new mysqli("localhost", "root", "", "upsin"); 


if ($conexion->connect_error) {
    die("ERROR: No se pudo conectar a MySQL. " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>