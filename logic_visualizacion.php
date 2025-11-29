<?php
include_once 'config.php';

$mensaje = '';
$clase_mensaje = '';


if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql_select = "SELECT foto_path FROM personas WHERE id = ?";
    if ($stmt_select = $conexion->prepare($sql_select)) { 
        $stmt_select->bind_param("i", $delete_id);
        $stmt_select->execute();
        $resultado_select = $stmt_select->get_result();
        $fila = $resultado_select->fetch_assoc();
        $foto_a_borrar = $fila['foto_path'] ?? null;
        $stmt_select->close();
    }

    $sql_delete = "DELETE FROM personas WHERE id = ?";
    if ($stmt_delete = $conexion->prepare($sql_delete)) { 
        $stmt_delete->bind_param("i", $delete_id);
        if ($stmt_delete->execute()) {
            $mensaje = "Registro ID {$delete_id} eliminado exitosamente.";
            $clase_mensaje = 'success';
            
            if ($foto_a_borrar && file_exists($foto_a_borrar) && strpos($foto_a_borrar, 'placeholder') === false) {
                if (strpos($foto_a_borrar, '../') === false) {
                    unlink($foto_a_borrar);
                }
            }
        } else {
            $mensaje = "Error al eliminar el registro: " . $stmt_delete->error;
            $clase_mensaje = 'error';
        }
        $stmt_delete->close();
    } else {
        $mensaje = "Error de preparación: " . $conexion->error; 
        $clase_mensaje = 'error';
    }
}


$sql = "SELECT id, nombre, correo, carrera, foto_path FROM personas";
$parametros = [];
$tipos = '';
$where_clauses = [];

$busqueda = trim($_GET['search'] ?? '');

if (!empty($busqueda)) {
    if (is_numeric($busqueda)) {
        $where_clauses[] = "id = ?";
        $tipos .= 'i';
        $parametros[] = $busqueda;
    } else {
        $where_clauses[] = "nombre LIKE ?";
        $tipos .= 's';
        $parametros[] = "%" . $busqueda . "%";
    }
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY id DESC";
$result = null;

if ($stmt = $conexion->prepare($sql)) { 
    if (!empty($parametros)) {
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $mensaje = "Error de consulta: " . $conexion->error; 
    $clase_mensaje = 'error';
}

$conexion->close(); 
?>