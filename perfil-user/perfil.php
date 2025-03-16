<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="perfil.css">
</head>

<body>
    <?php include '../php/backend/obtener_perfil.php'; ?>
    <div class="perfil-container">
        <h2>Perfil de Usuario</h2>
        <div class="foto-section">
            <img src="/assets/img/fotos/foto-perfil.webp" class="foto-perfil" alt="Foto de perfil">
        </div>

        <h3>Datos de Usuario</h3>
        <div class="row">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" value="<?php echo $usuario['nombre']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Fecha de nacimiento</label>
                <input type="text" value="<?php echo $usuario['nacimiento']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Sexo</label>
                <input type="text" value="<?php echo $usuario['sexo']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Pais</label>
                <input type="text" value="<?php echo $usuario['pais']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Bandera</label>
                <input type="text" value="<?php echo $usuario['bandera']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Categoria</label>
                <input type="text" value="<?php echo $usuario['categoria']; ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="campo">
                <label>Indicativo</label>
                <input type="text" value="<?php echo $usuario['indicativo']; ?>" disabled>
            </div>
            <div class="campo">
                <label>Telefono</label>
                <input type="text" value="<?php echo $usuario['telefono']; ?>" disabled>
            </div>
        </div>
        <div style="display: flex; justify-content: right;">
            <a href="/" class="btn-regresar btn">Regresar</a>
        </div>
    </div>
</body>

</html>