<?php
include_once 'config.php';

$mensaje = '';
$clase_mensaje = '';

$carreras_upsin = [
    'Ingeniería en Tecnologías de la Información',
    'Ingeniería en Biodemica',
    'Ingeniería en Mecatrónica',
    'Ingeniería en Animación',
    'Ingeniería en Energías',
    'Ingeniería en Nanotecnología',
    'Ingeniería en Bioingeniería',
    'Ingeniería en Transporte y Logística',
    'Licenciatura en Administración'
];

$nombre = '';
$correo = '';
$edad = '';
$genero = '';
$hobbies_arr = [];
$carrera = '';
$bio = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $edad = intval($_POST['edad'] ?? 0);
    $genero = $_POST['genero'] ?? '';
    $hobbies_arr = $_POST['hobbies'] ?? [];
    $hobbies = implode(',', $hobbies_arr);
    $carrera = $_POST['carrera'] ?? '';
    $bio = trim($_POST['bio'] ?? ''); 
    $foto_path = '';
    
    $errores = [];

    if (empty($nombre)) $errores[] = "El Nombre es obligatorio.";
    if (empty($correo)) $errores[] = "El Correo es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo no es válido.";
    if (empty($carrera) || !in_array($carrera, $carreras_upsin)) $errores[] = "Seleccione una carrera válida.";
    if ($edad < 18 || $edad > 100) $errores[] = "La edad debe estar entre 18 y 100.";

    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
        $archivo_temporal = $_FILES['fotografia']['tmp_name'];
        $nombre_archivo = $_FILES['fotografia']['name'];
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        $tamano_bytes = $_FILES['fotografia']['size'];
        $tamano_max = 2 * 1024 * 1024;
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $carpeta = "uploads/";

        if (!in_array($extension, $ext_permitidas)) {
            $errores[] = "Solo se permiten JPG, JPEG, PNG y GIF.";
        }
        if ($tamano_bytes > $tamano_max) {
            $errores[] = "La imagen no puede superar los 2MB.";
        }

        if (empty($errores)) {
            if (!is_dir($carpeta)) {
                if (!mkdir($carpeta, 0777, true)) { 
                    $errores[] = "Error interno: No se pudo crear la carpeta uploads.";
                }
            }
            
            if (empty($errores)) {
                $nombre_unico = uniqid("foto_") . "." . $extension;
                $ruta_destino = $carpeta . $nombre_unico;

                if (move_uploaded_file($archivo_temporal, $ruta_destino)) { 
                    $foto_path = $ruta_destino;
                } else {
                    $errores[] = "Error al guardar la imagen.";
                }
            }
        }
    }

    if (empty($errores)) {
        
        $sql = "INSERT INTO personas 
        (nombre, correo, edad, genero, hobbies, carrera, bio, foto_path) 
        VALUES (?,?,?,?,?,?,?,?)";

        if (isset($conexion) && $conexion->connect_error == null && $stmt = $conexion->prepare($sql)) {
            $stmt->bind_param(
                "ssisssss",
                $nombre,
                $correo,
                $edad,
                $genero,
                $hobbies,
                $carrera,
                $bio, 
                $foto_path
            );

            if ($stmt->execute()) {
                $mensaje = "Registro guardado exitosamente.";
                $clase_mensaje = "success";
                $nombre = $correo = $edad = $genero = $carrera = $bio = '';
                $hobbies_arr = [];
            } else {
                $mensaje = "Error al guardar: " . $stmt->error;
                $clase_mensaje = "error";
            }
            $stmt->close();
        } else {
            $mensaje = "Error de conexión: " . ($conexion->error ?? 'Desconocido');
            $clase_mensaje = "error";
        }
    } else {
        $mensaje = "<ul><li>" . implode("</li><li>", $errores) . "</li></ul>"; 
        $clase_mensaje = "error";
    }
}

if (isset($conexion) && !$conexion->connect_error) {
    $conexion->close();
}
?>