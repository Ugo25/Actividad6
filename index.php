<?php include 'logic_formulario.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro UPSIN</title>
<link rel="stylesheet" href="estilos/estilo_formulario.css">
</head>
<body>

<div class="container">
    <h2>Registro de Prospectos UPSIN</h2>

    <?php if ($mensaje): ?>
        <div class="message <?php echo $clase_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">

        <label>Nombre Completo *</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

        <label>Correo *</label>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>

        <label>Edad *</label>
        <input type="number" name="edad" min="18" max="100" value="<?php echo htmlspecialchars($edad); ?>" required>

        <label>Género</label>
        <select name="genero">
            <option value="Masculino" <?php if ($genero == 'Masculino') echo 'selected'; ?>>Masculino</option>
            <option value="Femenino" <?php if ($genero == 'Femenino') echo 'selected'; ?>>Femenino</option>
            <option value="Otro" <?php if ($genero == 'Otro') echo 'selected'; ?>>Otro</option>
        </select>

        <label>Hobbies</label>
        <?php $hobbies_seleccionados = $hobbies_arr; ?>
        <input type="checkbox" name="hobbies[]" value="Leer" <?php if (in_array('Leer', $hobbies_seleccionados)) echo 'checked'; ?>> Leer<br>
        <input type="checkbox" name="hobbies[]" value="Deportes" <?php if (in_array('Deportes', $hobbies_seleccionados)) echo 'checked'; ?>> Deportes<br>
        <input type="checkbox" name="hobbies[]" value="Música" <?php if (in_array('Música', $hobbies_seleccionados)) echo 'checked'; ?>> Música<br>
        <input type="checkbox" name="hobbies[]" value="Viajar" <?php if (in_array('Viajar', $hobbies_seleccionados)) echo 'checked'; ?>> Viajar<br>
        <input type="checkbox" name="hobbies[]" value="Cocinar" <?php if (in_array('Cocinar', $hobbies_seleccionados)) echo 'checked'; ?>> Cocinar<br><br>

        <label>Carrera *</label>
        <select name="carrera" required>
            <option value="">Seleccione una opción</option>
            <?php foreach ($carreras_upsin as $c): ?>
                <option value="<?php echo $c; ?>" <?php if ($carrera == $c) echo 'selected'; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>

        <label>Bio</label>
        <textarea name="bio"><?php echo htmlspecialchars($bio); ?></textarea>

        <label>Fotografía (2MB máx)</label>
        <input type="file" name="fotografia" accept="image/*">

        <button type="submit">Guardar</button>
    </form>

    <br>
    <div style="text-align: center;">
        <a href="ver_registros.php">Ver registros guardados</a>
    </div>
</div>

</body>
</html>