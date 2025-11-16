<?php
session_start();
include 'conexion.php';

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ci = $_POST['ci'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $calle = $_POST['calle'] ?? '';
    $num_puerta = $_POST['num_puerta'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo = $_POST['tipo'] ?? 'Calle';
    $lat = $_POST['lat'] ?? null;
    $lon = $_POST['lon'] ?? null;
    $validar = isset($_POST['validar']) ? 1 : 0;


    // Validaciones básicas
    if (!preg_match('/^[0-9]{8}$/', $ci)) $errors[] = "CI inválido. Debe tener 8 dígitos.";
    if (!preg_match('/^[0-9]{8,9}$/', $telefono)) $errors[] = "Teléfono inválido. Debe tener 8 o 9 dígitos.";
    if (empty($calle)) $errors[] = "Debe indicar la calle.";
    if (empty($num_puerta)) $errors[] = "Debe indicar número de puerta.";
    if (empty($ciudad)) $errors[] = "Debe seleccionar una ciudad.";

    // Subir foto
    $foto_rotura = null;
    if (isset($_FILES['foto_rotura']) && $_FILES['foto_rotura']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0775, true);
        $ext = pathinfo($_FILES['foto_rotura']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'rotura_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $ruta_destino = $upload_dir . $nombre_archivo;
        if (move_uploaded_file($_FILES['foto_rotura']['tmp_name'], $ruta_destino)) {
            $foto_rotura = $ruta_destino;
        } else {
            $errors[] = "Error al subir la foto de rotura.";
        }
    }

    if (empty($errors)) {
        // Insertar usuario si no existe
        $stmt = $conn->prepare("INSERT IGNORE INTO usuario (CI, Nombre_Completo, Telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $ci, $nombre, $telefono);
        $stmt->execute();
        $stmt->close();

        // Insertar ubicación si no existe
        $stmt = $conn->prepare("SELECT Numero FROM ubicacion WHERE Nombre_Calle=? AND Numero_dePuerta=? AND Codigo_deCiudad=?");
        $stmt->bind_param("sii", $calle, $num_puerta, $ciudad);
        $stmt->execute();
        $stmt->store_result();
        $ubicacion_id = null;
        if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO ubicacion (Nombre_Calle, Numero_dePuerta, Codigo_deCiudad, Latitud, Longitud) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siidd", $calle, $num_puerta, $ciudad, $lat, $lon);
        $stmt->execute();
        $numero_ubicacion = $stmt->insert_id; // ID de la ubicación insertada
    } else {
        $stmt->bind_result($numero_ubicacion);
        $stmt->fetch();
    }
    $stmt->close();

        $id_estado = 1;
    $stmt = $conn->prepare("INSERT INTO alerta (Fecha, Hora, Foto_de_rotura, Tipo, Descripcion, Numero_deUbi, Codigo_deCiudad, CI_deUsuario, ID_Estado, Latitud, Longitud) 
                            VALUES (CURDATE(), CURTIME(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiidd", $foto_rotura, $tipo, $descripcion, $numero_ubicacion, $ciudad, $ci, $id_estado, $lat, $lon);
    if ($stmt->execute()) {
header("Location: index.php");
        exit;
    } else {
        $errors[] = "Error al registrar la alerta: " . $stmt->error;
    }
    $stmt->close();
        // Ejemplo de inserción:
        $stmt = $conn->prepare("INSERT INTO alerta (validar) VALUES (?)");
        $stmt->bind_param("i", $validar);
        $stmt->execute();
    }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Formulario de alerta</title>
<link rel="stylesheet" href="formularios.css">
<link rel="stylesheet" href="licencia.css">
<link rel="icon" href="assets/media/Isotipo.png" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="form-container">
<header>
  <a href="index.php" class="back-arrow"><i class="fa fa-arrow-left  fs-4"></i></a>
  <div class="logo-container">
    <a href="index.php"><img src="assets/media/MTC.png" alt="Logo MTC" class="logo-img"></a>
  </div>
</header>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger" style="text-align:center;">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<h2 class="titulo-from">Formulario de Alerta</h2>

<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">

<!-- FOTO -->
<div class="input-wrapper-f">
    <label for="foto_rotura" class="image-upload">
        <div class="contenedor-imagen-boton">
            <img id="preview" src="assets/media/fotogaleria.png" alt="Imagen por defecto">
            <div class="subiralerta">Subir imagen</div>
        </div>
    </label>
    <input type="file" id="foto_rotura" name="foto_rotura" accept="image/*" required>
</div>

<!-- TIPO -->
<div class="input-wrapper">
    <label class="titulo-from" for="tipo">Tipo de Alerta:</label>
    <select id="tipo" name="tipo" required>
        <option value="Calle">Calle</option>
        <option value="Vereda">Vereda</option>
    </select>
</div>

<!-- CI -->
<div class="input-wrapper">
    <label class="titulo-from" for="ci">CI:</label>
    <div class="input-wrapper position-relative">
        <input type="text" id="ci" name="ci" required placeholder="Ingrese su CI">
        <i class="fas fa-user position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
    </div>
</div>

<!-- NOMBRE -->
<div class="input-wrapper">
    <label class="titulo-from" for="nombre">Nombre Completo:</label>
    <div class="input-wrapper position-relative">
        <input type="text" id="nombre" name="nombre" required placeholder="Ingrese su nombre completo">
        <i class="fa-solid fa-address-book position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
    </div>
</div>

<!-- TELÉFONO -->
<div class="input-wrapper">
    <label class="titulo-from" for="telefono">Teléfono:</label>
    <div class="input-wrapper position-relative">
        <input type="text" id="telefono" name="telefono" placeholder="Ingrese su número de teléfono" required >
        <i class="fa fa-phone position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
    </div>
</div>

<!-- CIUDAD -->
<div class="input-wrapper">
    <label class="titulo-from" for="ciudad">Ciudad:</label>
    <select id="ciudad" name="ciudad" style="height: 20%;" required>
        <option value="">Seleccione una ciudad</option>
        <option value="1">Mercedes</option>
        <option value="2">Dolores</option>
        <option value="3">Cardona</option>
        <option value="4">Palmitas</option>
        <option value="5">José Enrique Rodó</option>
        <option value="6">Chacras de Dolores</option>
        <option value="7">Villa Soriano</option>
        <option value="8">Santa Catalina</option>
        <option value="9">Egaña</option>
        <option value="10">Agraciada</option>
        <option value="11">Risso</option>
        <option value="12">Sacachispas</option>
        <option value="13">Cañada Nieto</option>
        <option value="14">Palmar</option>
        <option value="15">Palo Solo</option>
        <option value="16">Castillos</option>
        <option value="17">Perseverano</option>
        <option value="18">La Loma</option>
        <option value="19">Lares</option>
        <option value="20">La Concordia</option>
        <option value="21">El Tala</option>
        <option value="22">Colonia Concordia</option>
        <option value="23">Cuchilla del Perdido</option>
    </select>
</div>

<!-- USAR GPS 
<button type="button" onclick="compartirUbicacion()" style="width:40%;">Usar mi ubicación</button>
<input type="hidden" id="lat" name="lat">
<input type="hidden" id="lon" name="lon"> -->

<!-- CALLE -->
<div class="input-wrapper">
    <label class="titulo-from" for="calle">Calle:</label>
    <div class="input-wrapper position-relative">
        <input type="text" id="calle" name="calle" autocomplete="off" placeholder="Ej: 18 de Julio" required>
        <ul id="sugerencias-calle" class="sugerencias-lista"></ul>
        <i class="fas fa-road position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
    </div>
</div>

<!-- NUMERO DE PUERTA -->
<div class="input-wrapper">
    <label class="titulo-from" for="num_puerta">Número de Puerta:</label>
    <div class="input-wrapper position-relative">
        <input type="number" id="num_puerta" name="num_puerta" autocomplete="off" onblur="verificarDireccion()" placeholder="Ej: 1234" required>
        <i class="fas fa-home position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
    </div>
</div>

<!-- DESCRIPCIÓN -->
<div class="input-wrapper-des">
    <label class="titulo-from" for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Describe la rotura"></textarea>
</div>
 <label >
        <input type="checkbox" name="notificar" value="1" style="padding: 2rem;" >
       <b> Abonar los materiales para la vereda</b>
    </label>
<button type="submit">Enviar Alerta</button>
</form>
  <footer>
    <p>El código fuente de esta aplicación está disponible bajo una 
      <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
      </a>
    </p>
  </footer>
</div>
   <style> 
       .input-wrapper-f {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: rgba(153, 228, 255, 0.9);
  border-radius: 20px;
  width: 250px;
  padding: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  margin: 0 auto;
}

.contenedor-imagen-boton {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.input-wrapper-f img {
  height: 6rem;
  object-fit: cover;
  border-radius: 20px;
  border: 10px solid #ffffff7b;
  margin-bottom: 10px;
}

.input-wrapper-f input[type="file"] {
  display: none;
}

.subiralerta {
  display: inline-block;
  background-color: rgba(94, 138, 242, 0.9);
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-align: center;
}

.subiralerta:hover {
  background-color: #162e56;
}
#descripcion{
    background-color: rgba(153, 228, 255, 0.9);
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    resize: vertical;
    font-size: 1rem;
    font-family: Arial, sans-serif;
    box-sizing: border-box;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
}
.sugerencias-lista {
    list-style: none;
    margin: 0;
    }
    </style>
<script>

    // Previsualización de la imagen antes de subir
const inputFoto = document.getElementById('foto_rotura');
const previewImg = document.getElementById('preview');

inputFoto.addEventListener('change', () => {
    const file = inputFoto.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.src = 'assets/media/fotogaleria.png'; // Imagen por defecto
    }
});
// Autocompletado calles filtrando por ciudad
const calleInput = document.getElementById('calle');
const ciudadSelect = document.getElementById('ciudad');
const sugerencias = document.getElementById('sugerencias-calle');

calleInput.addEventListener('input', async () => {
    const q = calleInput.value.trim();
    const ciudad = ciudadSelect.value;
    if(!q || !ciudad){ sugerencias.innerHTML=''; return; }

    try {
        const res = await fetch(`api/geo_suggest.php?tipo=calle&q=${encodeURIComponent(q)}&ciudad=${ciudad}`);
        const data = await res.json();
        sugerencias.innerHTML='';
        if(data.results && data.results.length > 0){
            data.results.forEach(item=>{
                const li = document.createElement('li');
                li.textContent = item.Nombre_Calle;
                li.addEventListener('click', ()=>{
                    calleInput.value = item.Nombre_Calle;
                    sugerencias.innerHTML='';
                });
                sugerencias.appendChild(li);
            });
        }
    } catch(e) {
        console.error('Error al obtener calles:', e);
    }
});

// Botón para usar GPS y rellenar calle + ciudad
function compartirUbicacion() {
    if (!navigator.geolocation) {
        alert("Tu navegador no soporta geolocalización.");
        return;
    }

    navigator.geolocation.getCurrentPosition(async function(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        // Guardamos coords en inputs ocultos
        document.getElementById('lat').value = lat;
        document.getElementById('lon').value = lon;

        try {
            // Reverse geocoding usando Nominatim
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&addressdetails=1`, {
                headers: {
                    "User-Agent": "MTCApp/1.0 (fabianacano20@gmail.com)",
                    "Accept-Language": "es"
                }
            });
            const data = await res.json();
            if(data && data.address){
                const addr = data.address;
                if(addr.road) calleInput.value = addr.road;
                if(addr.house_number) document.getElementById('num_puerta').value = addr.house_number;

                // Seleccionar ciudad en el select
                const ciudadNombre = addr.city || addr.town || addr.village;
                if(ciudadNombre){
                    for(let opt of ciudadSelect.options){
                        if(opt.text.toLowerCase() === ciudadNombre.toLowerCase()){
                            opt.selected = true;
                            break;
                        }
                    }
                }
            }
        } catch(e){
            console.error('Error reverse geocoding:', e);
            alert("No se pudo obtener la dirección desde tu ubicación.");
        }

    }, function(err) {
        if(err.code === 1) alert("Permiso denegado para acceder a la ubicación.");
        else if(err.code === 2) alert("Posición no disponible.");
        else if(err.code === 3) alert("Timeout obteniendo ubicación.");
        else alert("Error al obtener tu ubicación.");
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

// Verificar dirección (opcional)
function verificarDireccion(){
    const calle = calleInput.value.trim();
    const num = document.getElementById('num_puerta').value.trim();
    const ciudad = ciudadSelect.value;
    if(!calle || !num || !ciudad) return;
    // Aquí podrías hacer fetch a tu API para validar que la puerta existe si la agregás después
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>