<?php
session_start();
require '../php/backend/config.php';
include '../php/backend/obtener_perfil.php';

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if (!$isLoggedIn) {
    header("Location: /");
    exit();
}
?>
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
    <div class="perfil-container">
        <h2>Configuración de Perfil</h2>
        <form id="update-profile-form" method="POST">
            <!-- MANDAMOS DIRECTAMENTE EL ID DEL USUARIO -->
            <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
            <div class="foto-section">
                <img src="<?php echo $usuario['fotografia'] ? $usuario['fotografia'] : '/assets/img/fotos/foto-perfil.webp'; ?>"
                    id="foto" class="foto-perfil" alt="Foto de perfil">
                <input type="file" id="fotoInput" style="display: none" accept="image/*">
                <input type="hidden" id="fotografia" name="fotografia" value="<?php echo $usuario['fotografia'] ?>">
                <a id="subirFoto" class="btn">Subir foto de perfil</a>
            </div>

            <h3>Datos de Usuario</h3>
            <div class="row">
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>">
                </div>
                <div class="campo">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="nacimiento" value="<?php echo $usuario['nacimiento']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Sexo</label>
                    <select class="form-select" name="sexo" required>
                        <option value="hombre" <?php echo ($usuario['sexo'] == 'hombre') ? 'selected' : ''; ?>>Hombre
                        </option>
                        <option value="mujer" <?php echo ($usuario['sexo'] == 'mujer') ? 'selected' : ''; ?>>Mujer
                        </option>
                        <option value="trans" <?php echo ($usuario['sexo'] == 'trans') ? 'selected' : ''; ?>>Trans
                        </option>
                        <option value="otro" <?php echo ($usuario['sexo'] == 'otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>

                </div>
                <div class="campo">
                    <label>Pais</label>
                    <select class="form-select" name="pais" id="countrySelect" required>
                        <option value="" selected>Seleccionar país</option> <!-- Opción por defecto -->
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Bandera</label>
                    <select class="form-select" name="bandera" id="bandera" required>
                        <option value="" disabled selected>Seleccionar bandera</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Categoria</label>
                    <select class="form-select" name="categoria" required>
                        <option value="" <?php echo ($usuario['categoria'] == '') ? 'selected' : ''; ?>>Selecionar
                            categoria</option>
                        <option value="Escort" <?php echo ($usuario['categoria'] == 'Escort') ? 'selected' : ''; ?>>Escort
                        </option>
                        <option value="Gay" <?php echo ($usuario['categoria'] == 'Gay') ? 'selected' : ''; ?>>Gay</option>
                        <option value="Webcam" <?php echo ($usuario['categoria'] == 'Webcam') ? 'selected' : ''; ?>>Webcam
                        </option>
                        <!-- Más opciones aquí -->
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="campo">
                    <label>Indicativo</label>
                    <select class="form-select" name="indicativo" id="indicativo" required>
                        <option value="" disabled selected>Seleccionar indicativo</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?php echo $usuario['telefono']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-check mt-2">
                    <input type="checkbox" class="checkwsp" name="whatsapp" id="whatsapp" 
                        <?php echo ($usuario['whatsapp'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="whatsapp">Usa este número para Whatsapp</label>
                </div>
            </div>

            <div style="display:flex; justify-content:center; gap: 15px;">
                <button type="submit">Guardar Cambios</button>
                <a href="/" class="btn-regresar btn">Regresar</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('subirFoto').addEventListener('click', function () {
            document.getElementById('fotoInput').click();
        });

        document.getElementById('fotoInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('foto').src = e.target.result;

                    // Guardar la imagen en base64 en un campo oculto
                    document.getElementById("fotografia").value = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Guardamos el país del usuario en una variable de JavaScript desde PHP
        const userCountry = "<?php echo isset($usuario['pais']) ? $usuario['pais'] : ''; ?>";
        const userIndicativo = "<?php echo $usuario['indicativo']; ?>"; // Obtiene el indicativo del usuario desde PHP
        const userBandera = "<?php echo $usuario['bandera']; ?>";
        // Cargar países dinámicamente
        fetch('../php/backend/datalist/countries.json')
            .then(response => response.json())
            .then(countries => {
                let select = document.getElementById('countrySelect');
                let countryExists = false;

                countries.forEach(country => {
                    let option = document.createElement("option");
                    option.value = country.name;
                    option.textContent = country.name;

                    // Verificar si coincide con el país del usuario y marcarlo como seleccionado
                    if (userCountry && userCountry === country.name) {
                        option.selected = true;
                        countryExists = true;
                    }

                    select.appendChild(option);
                });

                // Si el país del usuario no existe en la lista, aseguramos que la opción "Seleccionar país" esté seleccionada
                if (!countryExists) {
                    select.value = "";
                }
            });

        fetch('../php/backend/datalist/countries.json')
            .then(response => response.json())
            .then(countries => {
                const select = document.getElementById('indicativo');

                countries.forEach(country => {
                    let option = document.createElement("option");
                    option.value = country.dial_code;
                    option.setAttribute("data-flag", country.flag);
                    option.setAttribute("data-dial", country.dial_code);
                    option.innerHTML = `${country.dial_code} (${country.name})`;

                    // Si el indicativo del usuario coincide, se selecciona la opción
                    if (userIndicativo === country.dial_code) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });

                // Si no hay un indicativo guardado, dejar la opción por defecto "Seleccionar indicativo"
                if (!userIndicativo) {
                    select.selectedIndex = 0;
                }
            })
            .catch(error => console.error('Error cargando los indicativos:', error));

        fetch('../php/backend/datalist/countries.json')
            .then(response => response.json())
            .then(countries => {
                const select = document.getElementById('bandera');

                countries.forEach(country => {
                    let option = document.createElement("option");
                    option.value = country.code;
                    option.setAttribute("data-code", country.code);
                    option.setAttribute("data-name", country.name);
                    option.innerHTML = `${country.code.toUpperCase()} (${country.name})`;

                    // Si la bandera del usuario coincide, se selecciona la opción
                    if (userBandera === country.code) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });

                // Si no hay un indicativo guardado, dejar la opción por defecto "Seleccionar indicativo"
                if (!userBandera) {
                    select.selectedIndex = 0;
                }
            })
            .catch(error => console.error('Error cargando las banderas:', error));

        document.getElementById("update-profile-form").addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('../php/backend/update_profile.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success == true) {
                        alert("Perfil actualizado correctamente.");
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error("Error al actualizar perfil:", error);
                    alert("Hubo un problema al procesar la solicitud.");
                });
        });


    </script>
</body>

</html>