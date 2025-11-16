<?php
session_start();
include('conexion.php');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: Inicio Sesion.php");
    exit();
}

// Verificar si es administrador
$ci = intval($_SESSION['user_id']);
$sql_admin = "SELECT * FROM administrador WHERE CI = ?";
$stmt = mysqli_prepare($conn, $sql_admin);
mysqli_stmt_bind_param($stmt, "i", $ci);
mysqli_stmt_execute($stmt);
$result_admin = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result_admin) === 0){
    session_destroy();
    echo "<h3>Acceso denegado. Solo administradores pueden ingresar.</h3>";
    exit();
}

// --- Filtrado ---
$filtro = isset($_GET['filtro_validar']) ? $_GET['filtro_validar'] : '';

// --- Construcción de la consulta ---
$sql = "SELECT u.CI, u.Nombre_Completo, u.Telefono, u.Mail, a.Numero, a.Fecha, a.Hora, a.Tipo, a.Descripcion, a.Foto_de_rotura, a.Foto_de_arreglo, a.validar,
            CASE WHEN pv.CI_deUsuario IS NOT NULL THEN 'Plan Vereda' ELSE '' END AS plan_vereda_status, e.Nombre AS Estado
        FROM usuario u
        LEFT JOIN plan_vereda pv ON u.CI = pv.CI_deUsuario
        LEFT JOIN alerta a ON a.CI_deUsuario = u.CI
        LEFT JOIN estado e ON a.ID_Estado = e.ID_Estado
        WHERE 1"; // base para concatenar condiciones

// --- Aplicar filtro ---
if ($filtro === 'pv') {
    // Solo usuarios en Plan Vereda
    $sql .= " AND pv.CI_deUsuario IS NOT NULL";
} elseif ($filtro === '0') {
    // Usuario normal: no plan vereda y validar 0 o NULL
    $sql .= " AND pv.CI_deUsuario IS NULL AND (a.validar = 0 OR a.validar IS NULL)";
} elseif ($filtro === '1') {
    // Propietario (abona)
    $sql .= " AND a.validar = 1";
} elseif ($filtro === '2') {
    // Propietario (no abona)
    $sql .= " AND a.validar = 2";
}

// --- Ordenar resultados ---
$sql .= " ORDER BY a.Fecha DESC, a.Hora DESC";

// Ejecutar consulta
$result = mysqli_query($conn, $sql);


// --- Eliminar alerta o usuario vía AJAX ---
if(isset($_POST['eliminar_alerta'])){
    $numero   = isset($_POST['numero']) ? intval($_POST['numero']) : 0;
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $ci       = isset($_POST['ci']) ? intval($_POST['ci']) : 0;

    if ($numero > 0) {
        // eliminar alerta
        $stmt = $conn->prepare("DELETE FROM alerta WHERE Numero = ?");
        $stmt->bind_param("i", $numero);
        $stmt->execute();
    } else {
        // eliminar usuario
        $condiciones = [];
        $parametros = [];
        $tipos = "";

        if ($ci > 0) {
            $condiciones[] = "CI = ?";
            $parametros[] = $ci;
            $tipos .= "i";
        }
        if (!empty($correo)) {
            $condiciones[] = "Mail = ?";
            $parametros[] = $correo;
            $tipos .= "s";
        }
        if (!empty($telefono)) {
            $condiciones[] = "Telefono = ?";
            $parametros[] = $telefono;
            $tipos .= "s";
        }

        if (count($condiciones) > 0) {
            $sql_delete = "DELETE FROM usuario WHERE " . implode(" OR ", $condiciones);
            $stmt = $conn->prepare($sql_delete);
            $stmt->bind_param($tipos, ...$parametros);
            $stmt->execute();
        }
    }

    echo json_encode(['success' => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Alertas y Usuarios</title>
  <link rel="icon" href="assets/media/Isotipo.png" />
  <link rel="stylesheet" href="licencia.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4"><h2 class="text-center text-capitalize fw-light mb-3">Panel de Usuarios</h2>
<div class="d-flex justify-content-start mb-3">
      <a href="logout.php" class="btn btn-success rounded-pill text-light me-2">Cerrar sesión</a>
      <a href="alertas_admin.php" class="btn btn-success rounded-pill text-light">Administrar Alertas</a>
  </div>
  
  <form method="GET" action="" class="text-center mb-4">
      <label class="fw-bold me-2">Filtrar por tipo de usuario:</label>
      <select name="filtro_validar" class="form-select d-inline-block w-auto">
          <option value="">Todos</option>
          <option value="pv" <?= $filtro === 'pv' ? 'selected' : '' ?>>Usuarios en Plan Vereda</option>
          <option value="0" <?= $filtro === '0' ? 'selected' : '' ?>>Usuario normal</option>
          <option value="1" <?= $filtro === '1' ? 'selected' : '' ?>>Propietario (abona materiales)</option>
          <option value="2" <?= $filtro === '2' ? 'selected' : '' ?>>Propietario (no abona materiales)</option>
      </select>
      <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-blue">
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Tipo</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Usuario</th>
          <th>Cédula</th>
          <th>Teléfono</th>
          <th>Email</th>
          <th>Fotos</th>
          <th>Tipo de Usuario</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr id="fila_<?= $row['Numero'] ?: $row['CI'] ?>">
          <td><?= $row['Numero'] ?: '-' ?></td>
          <td><?= $row['Fecha'] ?: '-' ?></td>
          <td><?= $row['Hora'] ?: '-' ?></td>
          <td><?= $row['Tipo'] ?: '-' ?></td>
          <td><?= $row['Descripcion'] ?: '-' ?></td>
          <td><?= $row['Estado'] ?: '-' ?></td>
          <td><?= $row['Nombre_Completo'] ?></td>
          <td><?= $row['CI'] ?></td>
          <td><?= $row['Telefono'] ?: '-' ?></td>
          <td><?= $row['Mail'] ?: '-' ?></td>
          <td>
            <?php if (!empty($row['Foto_de_rotura'])): ?>
                <img src="<?= htmlspecialchars($row['Foto_de_rotura']) ?>" width="150px" class="img-thumbnail">
            <?php else: ?>
                <em>Sin foto</em>
            <?php endif; ?>
          </td>
          <td>
            <?php
                if ($row['plan_vereda_status'] === 'Plan Vereda') {
                    echo '<span class="text-primary fw-bold">Usuario en Plan Vereda</span>';
                } else {
                    switch ($row['validar']) {
                        case 1:
                            echo '<span class="text-success fw-bold">Propietario (abona materiales)</span>';
                            break;
                        case 2:
                            echo '<span class="text-warning fw-bold">Propietario (no abona materiales)</span>';
                            break;
                        default:
                            echo '<span class="text-secondary">Usuario normal</span>';
                            break;
                    }
                }
            ?>
          </td>
          <td>
            <button class="btn btn-danger btn-sm"
                    onclick="abrirModal('<?= $row['Numero'] ?>','<?= $row['Mail'] ?>','<?= $row['Telefono'] ?>','<?= $row['CI'] ?>')">
                <i class="fa-solid fa-trash"></i> Eliminar
            </button>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>


<!-- Modal de eliminación -->
<div class="modal fade" id="modalEliminarAlerta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-white shadow-lg rounded-4">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <p>¿Estás seguro de que deseas eliminar este registro? Esta acción NO se puede deshacer.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button id="btnConfirmarEliminar" type="button" class="btn btn-danger rounded-pill px-4">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<style>
body {
    font-family: sans-serif;
    background: linear-gradient(to left, #38CCCA, #7D9BC4);
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
    margin: 0;
}
td {
    background-color: #f6faffff;
    text-align: center;
}
.btn a {
    display: inline-block;
    background-color: #38cca5ff;
    text-decoration: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    margin: 10px 5px 10px 10px;
    transition: background-color 0.3s;
}
.btn a:hover {
    background-color: #02c0ceff;
}
  /* TABLA — COLOR DE CABECERA */
  thead tr {
    background-color: #003366; /* azul marino o el color que elijas */
    color: white;
  }

  /* OPCIONAL — Color alterno de filas */
  tbody tr:nth-child(even) {
    background-color: #f5f8ff;
  }
  tbody tr:nth-child(odd) {
    background-color: #ffffff;
  }

  /* Para hacer la tabla más elegante */
  table {
    border-radius: 10px;
    overflow: hidden;
  }

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let alertaSeleccionada = {};

function abrirModal(numero, correo, telefono, ci) {
    alertaSeleccionada = { numero, correo, telefono, ci };
    const modal = new bootstrap.Modal(document.getElementById('modalEliminarAlerta'));
    modal.show();
}

document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
    const data = new FormData();
    data.append('eliminar_alerta', 1);
    data.append('numero', alertaSeleccionada.numero);
    data.append('correo', alertaSeleccionada.correo);
    data.append('telefono', alertaSeleccionada.telefono);
    data.append('ci', alertaSeleccionada.ci);

    fetch('', { method: 'POST', body: data })
        .then(res => res.json())
        .then(res => {
            if(res.success){
                const modalEl = document.getElementById('modalEliminarAlerta');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                const fila = document.getElementById('fila_' + (alertaSeleccionada.numero || alertaSeleccionada.ci));
                if(fila) fila.remove();
            }
        });
});
</script>

</body>
</html>
