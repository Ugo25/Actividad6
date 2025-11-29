<?php include 'logic_visualizacion.php'; ?>
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
            <a href="index.php">Volver al Formulario de Registro</a>
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
                                    <a href="javascript:void(0);" onclick="if(confirm('¿Está seguro de que desea eliminar a <?php echo htmlspecialchars($row['nombre']); ?>?')) { window.location.href = 'ver_registros.php?delete_id=<?php echo $row['id']; ?>'; }" class="action-link">
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