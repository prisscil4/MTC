<?php
// --- DEBUG (solo en desarrollo) ----------------------------------
// Muestra errores en pantalla y los guarda en php_error.log en la misma carpeta.
// QUÍTALO en producción.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log'); // archivo de log en la misma carpeta

// Capturar errores fatales al final del script y mostrarlos/loguearlos
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && ($err['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
        http_response_code(500);
        $msg = "FATAL: {$err['message']} in {$err['file']} on line {$err['line']}";
        // Mostrar en pantalla (solo desarrollo)
        echo "<pre style='color:red;background:#ffeeee;padding:10px;'>$msg</pre>";
        // Loguear en php_error.log
        error_log($msg);
    }
});
// Forzar que mysqli lance excepciones (opcional, útil para DB)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>


<?php
include("conexion.php");
include("verificacionuser.php");

$usuario = null;
$alertas = [];


// Obtener filtros de GET
$filtroTipo = $_GET['tipo'] ?? '';
$filtroEstado = $_GET['estado'] ?? '';
$filtroTexto = $_GET['texto'] ?? '';

$whereFiltros = [];
if ($filtroEstado !== '') {
    $estadoFilter = intval($filtroEstado);
    $whereFiltros[] = "a.ID_Estado = $estadoFilter";
}
if ($filtroTexto !== '') {
    $textoFilter = mysqli_real_escape_string($conn, $filtroTexto);
    $whereFiltros[] = "(a.Descripcion LIKE '%$textoFilter%' OR u.Nombre_Calle LIKE '%$textoFilter%')";
}
if ($filtroTipo !== '') {
    // Aquí asumimos que el "tipo" está en Nombre_Calle
    $tipoFilter = mysqli_real_escape_string($conn, $filtroTipo);
    $whereFiltros[] ="a.Tipo = '$tipoFilter'";
}

$whereSQL = "";
if (count($whereFiltros) > 0) {
    $whereSQL = "WHERE " . implode(" AND ", $whereFiltros);
}

// Cargar datos usuario solo si hay sesión
if (isset($_SESSION['user_id'])) {
    $ci = intval($_SESSION['user_id']);

    $sql = "SELECT u.Nombre_Completo, u.Mail, u.Telefono, u.FotoPerfil, pv.Barrio
            FROM usuario u
            INNER JOIN plan_vereda pv ON pv.CI_deUsuario = u.CI
            WHERE u.CI = $ci LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $usuario = mysqli_fetch_assoc($res);
    }
}
if (isset($_FILES['foto_rotura']) && $_FILES['foto_rotura']['error'] === 0) {
    $nombreArchivo = basename($_FILES['foto_rotura']['name']);
    $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
    $nuevoNombre = 'rotura_' . time() . '_' . rand(100,999) . '.' . $extension;
    $rutaDestino = '' . $nuevoNombre;

    }
// Consultar todas las alertas con filtros aplicados
$sqlAlertas = "SELECT a.Numero, a.Descripcion, a.ID_Estado, a.Tipo, e.Nombre AS EstadoNombre, a.Fecha, a.Hora, a.Foto_de_Rotura AS imagen, u.Nombre_Calle AS barrio
               FROM alerta a
               INNER JOIN estado e ON a.ID_Estado = e.ID_Estado
               INNER JOIN ubicacion u ON a.Codigo_deCiudad = u.Codigo_deCiudad AND a.Numero_deUbi = u.Numero
               $whereSQL
               ORDER BY a.Fecha DESC, a.Hora DESC";


$resAlertas = mysqli_query($conn, $sqlAlertas);
if ($resAlertas) {
    $alertas = $resAlertas->fetch_all(MYSQLI_ASSOC);
}
$sqlAlertas = "SELECT a.Numero, a.Descripcion, a.ID_Estado, a.Tipo, 
               e.Nombre AS EstadoNombre, a.Fecha, a.Hora, a.Foto_de_Rotura AS imagen, 
               u.Nombre_Calle AS barrio
               FROM alerta a
               INNER JOIN estado e ON a.ID_Estado = e.ID_Estado
               INNER JOIN ubicacion u ON a.Codigo_deCiudad = u.Codigo_deCiudad AND a.Numero_deUbi = u.Numero
               $whereSQL
               ORDER BY a.Fecha DESC, a.Hora DESC";

$resAlertas = mysqli_query($conn, $sqlAlertas);
$alertas = $resAlertas ? $resAlertas->fetch_all(MYSQLI_ASSOC) : [];
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alertas Plan Vereda</title>
<link rel="icon" href="assets/media/Isotipo.png"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: linear-gradient(to left, #7D9BC4, #38CCCA); font-family: 'Segoe UI', sans-serif; }
.header {
    background: #537eba6b;
    padding: 1.25rem;
    border-bottom-left-radius: 2.5rem;
    border-bottom-right-radius: 2.5rem;
    text-align: center;
}
.btn{
    border: none;
    color: #194e87ff;
    margin: 0 0.25rem;
}
.btn:hover{
    background: none;
    color: rgba(183, 219, 255, 1);
}
.input-group-text{
    background: none;
    border: none;
    color: #194e87ff;
}
.custom-alert-card {
    background-color: #8bf3f96c;
    border: none ;
    border-radius: 12px;
    margin: 20px auto;
    width: 85%;
    max-width: 187.5rem;
    box-shadow: 2px 4px 12px rgba(0,0,0,0.1);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.custom-alert-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    transition: 0.3s;
}
.alert-header {
    display: flex;
    justify-content: space-between;
    font-weight: 500;
    color: #2d4a68;
    border-bottom: 1px dotted #ccc;
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}
.alert-body {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.alert-text {
    flex: 2;
    font-size: 0.9rem;
    color: #333;
}
.alert-image {
    flex: 1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin: 1rem;
}
.alert-image img {
    width: auto;
    max-width: 100px;
    height: auto;
    max-height: 150px;

    border-radius: 10px;
    box-shadow: 2px 4px 12px rgba(0,0,0,0.1);
    background: #fff;
}
.estado-cuadro {
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 4px;
    margin-right: 5px;
    vertical-align: middle;
}
.estado-1 { background-color: #ffc400; }
.estado-2 { background-color: #ff8800; }
.estado-3 { background-color: #28a745; }
.estado-4 { background-color: #ff4d4d; }
.estado-5 { background-color: #00ff1aff; }
</style>
</head>
<body>

<!-- HEADER -->
<header class="header container-fluid">
  <div class="header-logo mb-3">
    <img src="assets/media/MTC.png" alt="Logo MTC" class="img-fluid" style="max-width:10rem;">
  </div>

<?php if ($usuario): ?>
    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
      <img src="<?php echo htmlspecialchars($fotoSrc); ?>" alt="Foto perfil" style="width:100px; height:100px; border-radius:50%;">
      <ul class="list-unstyled mb-0" style="text-align: left;">
        <li><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['Nombre_Completo']); ?></li>
        <li><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['Mail']); ?></li>
        <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['Telefono']); ?></li>
        <li><strong>Barrio:</strong> <?php echo htmlspecialchars($usuario['Barrio']); ?></li>
      </ul>
    </div>
<?php endif; ?>
  <!-- Barra de búsqueda y filtros -->
  <div class="d-flex justify-content-center my-3 gap-2">
    <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i></a>
    <div class="input-group">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" id="search-text" class="form-control rounded-pill" placeholder="Buscar alerta...">
      <button class="input-group-text btn btn-outline-secondary" id="btn-mic"><i class="fas fa-microphone"></i></button>
    </div>
    <div class="dropdown d-inline-block">
      <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i> Filtrar
      </button>
      <ul class="dropdown-menu p-3" aria-labelledby="filterDropdown" style="min-width:250px;">
        <li>
          <label class="form-label">Tipo:</label>
          <select class="form-select mb-2" id="filter-tipo">
            <option value="">Todos</option>
            <option value="Calle">Calle</option>
            <option value="Vereda">Vereda</option>
          </select>
        </li>
        <li>
          <label class="form-label">Estado:</label>
          <select class="form-select mb-2" id="filter-estado">
            <option value="">Todos</option>
            <option value="1">Pendiente</option>
            <option value="2">En revisión</option>
            <option value="3">Aceptada</option>
            <option value="4">Rechazada</option>
            <option value="5">Finalizada</option>
          </select>
        </li>
        <li class="mt-2">
          <button class="btn btn-primary w-100" id="apply-filter" style="background: #3a5e84ff; color: #f3f9ff;">Aplicar filtros</button>
        </li>
      </ul>
    </div>
    <button class="btn btn-outline-secondary" id="btn-refresh"><i class="fas fa-sync-alt"></i></button>
  </div>
    <a href="subiralerta.php" class="btn btn-success" style="background: #3a5e84ff; color: #f3f9ff;"><i class="fas fa-plus"></i> Subir alerta</a>

</header>

<div class="container mt-4">
   <?php
  $estadoColores = [1 => '#ffc400', 2 => '#ff8800', 3 => '#28a745', 4 => '#ff4d4d', 5 => '#00ff1aff'];
  foreach ($alertas as $alerta):
      $color = $estadoColores[$alerta['ID_Estado']] ?? '#999';
$img = !empty($alerta['imagen']) && file_exists($alerta['imagen'])
       ? $alerta['imagen']
       : 'assets/media/MTC.png';
  ?>
      <div class="custom-alert-card container-fluid">
        <div class="alert-header">
            <p><i class="fas fa-map-marker-alt"></i> Calle <?php echo htmlspecialchars($alerta['barrio']); ?></p>
            <p><i class="fas fa-map-marker-alt"></i> Número <?php echo htmlspecialchars($alerta['Numero']); ?></p>
        </div>
        <div class="alert-body">
            <div class="alert-text">
                <p><strong>Fecha y hora:</strong> <?php echo htmlspecialchars($alerta['Fecha'].' - '.$alerta['Hora']); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($alerta['Descripcion']); ?></p>
                <p><strong>Estado actual:</strong>
                    <span class="estado-cuadro" style="background-color: <?php echo $color; ?>;"></span>
                    <?php echo htmlspecialchars($alerta['EstadoNombre']); ?>
                </p>
               <p><strong>Tipo:</strong> 
                    <span class="tipo-alerta <?php echo strtolower($alerta['Tipo']); ?>">
                        <?php echo htmlspecialchars($alerta['Tipo']); ?>
                    </span>
              </p>

            </div>
            <div class="alert-image">
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Imagen de la alerta">
            </div>
        </div>
      </div>
  <?php endforeach; ?>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Reconocimiento de voz
let recognition;
const micBtn = document.getElementById("btn-mic");
const searchInput = document.getElementById("search-text");

if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();
    recognition.lang = "es-ES";
    recognition.continuous = false;
    recognition.interimResults = false;

    recognition.onresult = function(event) {
        const texto = event.results[0][0].transcript;
        searchInput.value = texto;
        aplicarFiltros();
    };
    recognition.onerror = function(event) {
        console.error("Error en reconocimiento:", event.error);
    };
} else {
    micBtn.disabled = true;
}

micBtn.addEventListener("click", () => {
    if (recognition) recognition.start();
});

function aplicarFiltros() {
    const tipo = document.getElementById('filter-tipo').value;
    const estado = document.getElementById('filter-estado').value;
    const texto = document.getElementById('search-text').value;

    const params = new URLSearchParams();
    if (tipo) params.append('tipo', tipo);
    if (estado) params.append('estado', estado);
    if (texto) params.append('texto', texto);

    window.location.href = window.location.pathname + '?' + params.toString();
}

document.getElementById('apply-filter').addEventListener('click', aplicarFiltros);
document.getElementById('btn-refresh').addEventListener('click', () => {
    window.location.href = window.location.pathname;
});
document.getElementById('search-text').addEventListener('keyup', function(e){
    if(e.key === 'Enter') aplicarFiltros();
});
</script>
</body>
</html>
