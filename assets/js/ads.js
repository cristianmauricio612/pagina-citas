document.addEventListener("DOMContentLoaded", function () {

        if (!navigator.geolocation) {
            Swal.fire({
            title: "Error",
            text: "Tu navegador no soporta la geolocalización.",
            icon: "error"
            });
            return;
        }
        requestLocationPermission();

    
        const cards = document.querySelectorAll(".card");
        const progressBar = document.getElementById("progress");
        const prevButton = document.getElementById("prev");
        const nextButton = document.getElementById("next");
        let currentIndex = 0;

        const updateProgressBar = () => {
            const progress = ((currentIndex + 1) / cards.length) * 100;
            progressBar.style.width = `${progress}%`;
        };

        const validateCard = () => {
            const currentCard = cards[currentIndex];
            const inputs = currentCard.querySelectorAll("input:not([type='hidden']), select");
            return Array.from(inputs).every(input => {
                if (input.type === "number") {
                    // Para campos numéricos, asegúrate de que no estén vacíos o no tengan un valor inválido.
                    return input.value.trim() !== "" && !isNaN(input.value);
                }
                return input.value.trim() !== "";
            });
        };
        

        const updateButtons = () => {
            prevButton.style.display = currentIndex === 0 ? "none" : "inline-block";
            nextButton.style.display = currentIndex === cards.length - 1 ? "none" : "inline-block";
            nextButton.disabled = !validateCard();
        };

        const showCard = (index) => {
            const contenedores = document.querySelector('.content');
            if (contenedores) {
            contenedores.scrollTop = 0; // Establece el scroll en la parte superior
            }

            cards.forEach((card, i) => {
                card.classList.remove("active", "previous");
                if (i === index) {
                    card.classList.add("active");
                } else if (i < index) {
                    card.classList.add("previous");
                }
            });
            updateProgressBar();
            updateButtons();
        };

        prevButton.addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                showCard(currentIndex);
            }
        });

        nextButton.addEventListener("click", () => {
            if (currentIndex < cards.length - 1) {
                currentIndex++;
                showCard(currentIndex);
            }
        });

        document.querySelectorAll("input, select").forEach(input => {
            input.addEventListener("input", updateButtons);
        });

        document.querySelectorAll("input, select").forEach(input => {
            input.addEventListener("change", updateButtons);
        });

        showCard(currentIndex);


    const paisesUrl = "/php/backend/datalist/countries.json";

    const form = document.getElementById("createAdForm");
    const edadField = document.getElementById("edad");
    const paisSelect = document.getElementById("pais");
    const indicativoSelect = document.getElementById("indicativo");
    const idiomasContainer = document.getElementById("idiomasContainer");
    const disponibilidadContainer = document.getElementById("disponibilidadContainer");
    const serviciosContainer = document.getElementById("serviciosContainer");
    const profilePictureInput = document.getElementById("profilePicture");
    const picturesInput = document.getElementById("pictures");

    const loadPaises = async () => {
        try {
            const response = await fetch(paisesUrl);
            const paises = await response.json();
    
            // Ordenar los países alfabéticamente por su nombre
            paises.sort((a, b) => a.name.localeCompare(b.name));
    
            paises.forEach((pais) => {
                const option = document.createElement("option");
                option.value = pais.name;
                option.textContent = pais.name;
                option.setAttribute("data-flag", pais.code);
    
                paisSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error cargando los países:", error);
        }
    };
    
    

    const loadIndicativos = async () => {
        try {
            const response = await fetch(paisesUrl);
            const indicativos = await response.json();
            indicativos.forEach((indicativo) => {
                const option = document.createElement("option");
                option.value = indicativo.dial_code;
                option.textContent = `${indicativo.name} (${indicativo.dial_code})`;
                option.setAttribute("data-flag", indicativo.code);
                indicativoSelect.appendChild(option);
                $('#indicativo option[value="+34"]').prop('selected', true);
                changeindicativo();
            });
        } catch (error) {
            console.error("Error cargando los indicativos:", error);
        }
    };

    // Inicializar el mapa (Leaflet)
    const map = L.map("map").setView([0, 0], 2);
    L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);


     // Agregar marcador
     const pinkIcon = L.divIcon({
        html: '<img src="/assets/img/logos/location-dot.png" class="locationdot"/>',
        className: 'custom-icon',
        iconSize: [30, 42],
        iconAnchor: [15, 42]
    });

    let marker;
    map.on("click", (e) => {
    const { lat, lng } = e.latlng;
    document.getElementById("latitude").value = lat;
    document.getElementById("longitude").value = lng;

    if (marker) {
        map.removeLayer(marker);
    }
    marker = L.marker([lat, lng], { icon: pinkIcon }).addTo(map);
    });

    // Funciones para manejar la ubicación
    function requestLocationPermission() {
    navigator.geolocation.getCurrentPosition(
        (position) => {
        const { latitude, longitude } = position.coords;
        map.setView([latitude, longitude], 15); // Centrar el mapa en la ubicación del usuario
        },
        (error) => {
        if (error.code === error.PERMISSION_DENIED) {
            showLocationAlert();
        }
        }
    );
    }

    function showLocationAlert() {
    Swal.fire({
        title: "Se requiere acceso a la ubicación",
        text: "Para continuar con la creación del anuncio, debes permitir el acceso a tu ubicación.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Activar ubicación",
    }).then((result) => {
        if (result.isConfirmed) {
        requestLocationPermission();
        } else {
        setTimeout(showLocationAlert, 1000); // Reintentar tras cerrar el SweetAlert
        }
    });
    }


    const updateEdad = () => {
        const nacimiento = document.getElementById("nacimiento").value;
        const edadField = document.getElementById("edad");
    
        if (nacimiento) {
            const birthDate = new Date(nacimiento);
            const today = new Date();
    
            // Calcular los años, meses y días
            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            let days = today.getDate()-1 - birthDate.getDate();
    
            // Ajustar si el mes de nacimiento aún no ha llegado este año
            if (months < 0 || (months === 0 && days < 0)) {
                years--;
                months += 12; // Normalizar meses
            }
    
            // Ajustar si el día de nacimiento aún no ha llegado este mes
            if (days < 0) {
                const previousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 0);
                days += previousMonth.getDate(); // Añadir días del mes anterior
            }
    
            edadField.textContent = `${years} años`;
        } else {
            edadField.textContent = "Edad no calculada";
        }
    };

    function changeflagcountry() {
        $('#flagcountry').html('');
        $('#bandera').val('');
        var seleccion = $( "#pais option:selected" ).data("flag").toUpperCase();
        var bandera = 
        '<img src="https://flagsapi.com/' + seleccion + '/shiny/32.png" width="23px"/>';
        $('#flagcountry').html(bandera);
        $('#bandera').val(seleccion);
    }

    function changeindicativo() {
        $('#flagindicativo').html('');
        var seleccion = $( "#indicativo option:selected" ).data("flag").toUpperCase();
        var bandera = 
        '<img src="https://flagsapi.com/' + seleccion + '/shiny/32.png" width="23px"/>';
        $('#flagindicativo').html(bandera);
    }
    

    const addIdiomaField = () => {
        const idiomaGroup = document.createElement("div");
        idiomaGroup.classList.add("idioma-group");
        $("#verificacion").remove();
        nextButton.disabled = false;
        updateButtons;

        const idiomaSelect = document.createElement("select");
        idiomaSelect.classList.add("form-select", "idioma-select", "pointer");
        idiomaSelect.innerHTML = `
            <option value="" disabled selected>Seleccionar idioma</option>

            <option value="Español">Español</option>
            <option value="Inglés">Inglés</option>
            <option value="Árabe">Árabe</option>
            <option value="Alemán">Alemán</option>
            <option value="Catalán">Catalán</option>
            <option value="Chino Mandarín">Chino Mandarín</option>
            <option value="Euskera">Euskera</option>
            <option value="Francés">Francés</option>
            <option value="Gallego">Gallego</option>
            <option value="Hindi">Hindi</option>
            <option value="Holandés">Holandés</option>
            <option value="Italiano">Italiano</option>
            <option value="Japonés">Japonés</option>
            <option value="Noruego">Noruego</option>
            <option value="Portugués">Portugués</option>
            <option value="Ruso">Ruso</option>
            <option value="Sueco">Sueco</option>`;

        const nivelSelect = document.createElement("select");
        nivelSelect.classList.add("form-select", "nivel-select");
        nivelSelect.innerHTML = `
            <option value="" disabled selected>Nivel</option>
            <option value="1">Básico</option>
            <option value="2">Intermedio</option>
            <option value="3">Avanzado</option>
        `;

        const removeButton = document.createElement("button");
        removeButton.type = "button";
        removeButton.classList.add("btn", "btn-danger", "ms-2", "botonidiomaborrar");
        removeButton.innerHTML = "<i class='fa-solid fa-trash'></i> Borrar";
        removeButton.addEventListener("click", () => idiomaGroup.remove());

        idiomaGroup.appendChild(idiomaSelect);
        idiomaGroup.appendChild(nivelSelect);
        idiomaGroup.appendChild(removeButton);

        idiomasContainer.appendChild(idiomaGroup);
    };

    const collectIdiomas = () => {
        const idiomaGroups = document.querySelectorAll(".idioma-group");
        const idiomas = [];
        idiomaGroups.forEach((group) => {
            const idioma = group.querySelector(".idioma-select").value;
            const nivel = group.querySelector(".nivel-select").value;
            if (idioma && nivel) {
                idiomas.push({ idioma, nivel: parseInt(nivel) });
            }
        });
        return idiomas;
    };

    const collectDisponibilidad = () => {
        const dias = [];
        document.querySelectorAll(".disponibilidad-dias input:checked").forEach((input) => {
            dias.push(input.value);
        });

        const horarios = [];
        document.querySelectorAll(".disponibilidad-horarios input:checked").forEach((input) => {
            horarios.push(input.value);
        });

        return { dias, horarios };
    };

    const collectServicios = () => {
        const servicios = [];
        document.querySelectorAll(".servicio-item input:checked").forEach((input) => {
            servicios.push(input.value);
        });
        return servicios;
    };

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
    
        // Desactivar el botón #post
        const postButton = document.getElementById("post");
        postButton.disabled = true;
    
        // Mostrar alerta de "Creando anuncio"
        Swal.fire({
            title: "Creando anuncio...",
            text: "Por favor, espera mientras procesamos tu solicitud.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    
        // Recopilar datos
        const idiomas = collectIdiomas();
        const formData = new FormData(form);
        formData.append("idiomas", JSON.stringify(idiomas));
    
        try {
            const response = await fetch("../../php/backend/create_ad.php", {
                method: "POST",
                body: formData,
            });
            const result = await response.json();
    
            if (result.success) {
                // Obtener el id del anuncio y redirigir
                const anuncioId = result.anuncio_id; // El backend debe incluir 'anuncio_id' en su respuesta
                if (anuncioId) {
                    Swal.fire({
                        title: "Éxito",
                        text: "Anuncio creado correctamente.",
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false, // No mostrar botón para bloquear interacción
                    });
    
                    // Redirigir a "perfil/anuncios?id={id}"
                    setTimeout(() => {
                        window.location.href = `../perfil-user/anuncio?id=${anuncioId}`;
                    }, 1500); // Pequeño retraso para permitir al usuario ver la alerta de éxito
                } else {
                    throw new Error("No se recibió el ID del anuncio.");
                }
            } else {
                Swal.fire("Error", result.message, "error");
                postButton.disabled = false; // Rehabilitar el botón en caso de error
            }
        } catch (error) {
            console.error("Error enviando el formulario:", error);
            Swal.fire("Error", "No se pudo crear el anuncio.", "error");
            postButton.disabled = false; // Rehabilitar el botón en caso de error
        }
    });
    
    

    document.getElementById("selectAllDays").addEventListener("change", function () {
        const isChecked = this.checked;
        document.querySelectorAll(".day-checkbox").forEach((checkbox) => {
            checkbox.checked = isChecked;
        });
        updateDisponibilidad();
    });

    document.getElementById("selectAllServices").addEventListener("change", function () {
        const isChecked = this.checked;
        document.querySelectorAll(".service-checkbox").forEach((checkbox) => {
            checkbox.checked = isChecked;
        });
        updateServicios();
    });

    document.getElementById("locationtip").addEventListener("change", function () {
        $("#mapaselect").fadeIn();
        $("#mapvalid").remove();
        nextButton.disabled = false;
        updateButtons;

        const contenedores = document.querySelector('.content');
            if (contenedores) {
            contenedores.scrollTop = 0; // Establece el scroll en la parte superior
            }
    });

    document.getElementById("nombre").addEventListener("change", function () {
        const anunciantename = $("#nombre").val();
        if (anunciantename) {
        $(".nameport").html(' ' + anunciantename);
        } else {
            $(".nameport").html('');
        }
    });

    const imageBoxes = document.querySelectorAll('.image-box');
    let cropper;

    imageBoxes.forEach(box => {
        const input = box.querySelector('.hidden-input');

        // Abrir el input `file` al hacer clic en el cuadro
        box.addEventListener('click', () => input.click());

        // Escuchar cuando el usuario selecciona una imagen
        input.addEventListener('change', event => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Crear un modal para CropperJS
                    const cropperModal = document.createElement('div');
                    cropperModal.className = 'cropper-modal';
                    cropperModal.innerHTML = `
                        <div class="cropper-container">
                            <img id="cropper-image" src="${e.target.result}" alt="Imagen para recortar">
                            <button id="crop-button" class="btn btn-primary">Recortar</button>
                            <button id="cancel-button" class="btn btn-secondary">Cancelar</button>
                        </div>
                    `;
                    document.body.appendChild(cropperModal);

                    // Iniciar CropperJS
                    const cropperImage = document.getElementById('cropper-image');
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1, // Cambia según tus necesidades
                        viewMode: 2,
                    });

                    // Escuchar el botón de recortar
                    document.getElementById('crop-button').addEventListener('click', () => {
                        const canvas = cropper.getCroppedCanvas();
                        const croppedImage = canvas.toDataURL('image/webp', 0.8); // Generar imagen WebP con calidad 80%

                        // Actualizar el input `file` con la nueva imagen recortada
                        const blob = dataURItoBlob(croppedImage);
                        const newFile = new File([blob], 'cropped_image.webp', { type: 'image/webp' });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(newFile);
                        input.files = dataTransfer.files; // Asigna la nueva imagen al input `file`

                        // Reemplazar la vista previa de la imagen recortada
                        box.querySelectorAll('img').forEach(img => img.remove()); // Eliminar vista previa anterior
                        const imgElement = document.createElement('img');
                        imgElement.src = croppedImage;
                        box.appendChild(imgElement);
                        $("#post").show();

                        // Remover modal y cropper
                        cropper.destroy();
                        cropperModal.remove();
                    });

                    // Botón cancelar: Elimina el modal y no realiza cambios
                    document.getElementById('cancel-button').addEventListener('click', () => {
                        cropper.destroy();
                        cropperModal.remove();
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Función para convertir Base64 a Blob
    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const ab = new ArrayBuffer(byteString.length);
        const ia = new Uint8Array(ab);

        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }

        return new Blob([ab], { type: mimeString });
    }



    const nameInput = document.getElementById('nombre'); 
    const maxLength = 15; 

    nameInput.addEventListener('input', (event) => {
        let value = event.target.value;
        value = value.replace(/\s/g, '');
        if (value.length > maxLength) {
            value = value.substring(0, maxLength);
        }
        value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
        event.target.value = value;
    });




        const dayCheckboxes = $('.day-checkbox');
        const disponibilidadInput = $('#disponibilidadcheck');
    
        // Función para actualizar el valor del input con los días seleccionados
        function updateDisponibilidad() {
            const selectedDays = [];
            dayCheckboxes.each(function () {
                if ($(this).is(':checked')) {
                    selectedDays.push($(this).val());
                }
            });
            disponibilidadInput.val(JSON.stringify(selectedDays));
        }
    
        // Evento para los checkboxes de días
        dayCheckboxes.on('change', function () {
            updateDisponibilidad();
        });

        const horariosCheckboxes = $('.horario-checkbox');
        const horariosInput = $('#horarioscheck');
    
        // Función para actualizar el valor del input con los horarios seleccionados
        function updateHorarios() {
            const selectedHorarios = [];
            horariosCheckboxes.each(function () {
                if ($(this).is(':checked')) {
                    selectedHorarios.push($(this).val());
                }
            });
            horariosInput.val(JSON.stringify(selectedHorarios));
        }
    
        // Evento para los checkboxes de días
        horariosCheckboxes.on('change', function () {
            updateHorarios();
        });


        const servicesCheckboxes = $('.service-checkbox');
        const servicesInput = $('#servicioscheck');
    
        // Función para actualizar el valor del input con los servicios seleccionados
        function updateServicios() {
            const selectedServices = [];
            servicesCheckboxes.each(function () {
                if ($(this).is(':checked')) {
                    selectedServices.push($(this).val());
                }
            });
            servicesInput.val(JSON.stringify(selectedServices));
        }
    
        // Evento para los checkboxes de servicios
        servicesCheckboxes.on('change', function () {
            updateServicios();
        });
    
    

    loadPaises();
    loadIndicativos();

    document.getElementById("addIdioma").addEventListener("click", addIdiomaField);
    document.getElementById("nacimiento").addEventListener("change", updateEdad);
    document.getElementById("pais").addEventListener("change", changeflagcountry);
    document.getElementById("indicativo").addEventListener("change", changeindicativo);
});
