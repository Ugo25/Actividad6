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
        $mensaje = "Error de preparación de sentencia para eliminación: " . $conexion->error; 
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
    $mensaje = "Error de preparación de sentencia para consulta: " . $conexion->error; 
    $clase_mensaje = 'error';
}

$conexion->close(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPSIN - Visualización de Registros</title>
    <link rel="stylesheet" href="estilos/estilos_tabla.css">
</head>
<body>
    <div class="container">
        <h2>Registros de Prospectos</h2>

        <?php if ($mensaje): ?>
            <div class="message <?php echo $clase_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="nav-links" style="margin-bottom: 20px;">
            <a href="formulario.php">Volver al Formulario de Registro</a>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="search-form">
            <input type="text" name="search" placeholder="Buscar por ID exacto o Nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th class="hide-on-mobile">Correo</th>
                            <th>Carrera</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td>
                                    <?php 
                                    $foto = htmlspecialchars($row['foto_path']);
                                    $src = empty($foto) || !file_exists($foto) ? 'https://placehold.co/50x50/cccccc/333333?text=N/A' : $foto;
                                    ?>
                                    <img src="<?php echo $src; ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>" class="foto-miniatura">
                                </td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td class="hide-on-mobile"><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['carrera']); ?></td>
                                <td>
                                    <a href="javascript:void(0);" onclick="if(confirm('¿Está seguro de que desea eliminar a <?php echo htmlspecialchars($row['nombre']); ?>?')) { window.location.href = 'actividad6.php?delete_id=<?php echo $row['id']; ?>'; }" class="action-link">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No se encontraron registros. <?php if (!empty($busqueda)) echo "Intente con otra búsqueda."; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>