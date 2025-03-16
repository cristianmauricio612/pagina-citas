<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="configuracion.css">
</head>

<body>
    <?php include '../php/backend/obtener_perfil.php'; ?>
    <div class="perfil-container">
        <h2>Configuración de Perfil</h2>
        <form action="">
            <div class="foto-section">
                <img src="/assets/img/fotos/foto-perfil.webp" id="foto" class="foto-perfil" alt="Foto de perfil">
                <input type="file" id="fotoInput" style="display: none" accept="image/*">
                <a id="subirFoto" class="btn">Subir foto de perfil</a>
            </div>

            <h3>Datos de Usuario</h3>
            <div class="row">
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" value="<?php echo $usuario['nombre']; ?>">
                </div>
                <div class="campo">
                    <label>Fecha de nacimiento</label>
                    <input type="text" value="<?php echo $usuario['nacimiento']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Sexo</label>
                    <input type="text" value="<?php echo $usuario['sexo']; ?>">
                </div>
                <div class="campo">
                    <label>Pais</label>
                    <input type="text" value="<?php echo $usuario['pais']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Bandera</label>
                    <input type="text" value="<?php echo $usuario['bandera']; ?>" >
                </div>
                <div class="campo">
                    <label>Categoria</label>
                    <input type="text" value="<?php echo $usuario['categoria']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Indicativo</label>
                    <input type="text" value="<?php echo $usuario['indicativo']; ?>" >
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" value="<?php echo $usuario['telefono']; ?>">
                </div>
            </div>
            <div style="display:flex; justify-content:center; gap: 15px;">
                <button type="submit">Guardar Cambios</button>
                <a href="/" class="btn-regresar btn">Regresar</a>
            </div>
        </form>
    </div>

    <script>
        const fotografia = "";
        document.getElementById('subirFoto').addEventListener('click', function () {
            document.getElementById('fotoInput').click();
        });

        document.getElementById('fotoInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('foto').src = e.target.result;
                    fotografia = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>