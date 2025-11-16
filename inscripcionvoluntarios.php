<?php

include 'conexion.php';
include('verificacionuser.php');

$loggedIn = isset($_SESSION['user_id']);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ci = $_POST['ci'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $barrio = $_POST['direccion'] ?? null;
    $mayor18 = $_POST['mayor18'] ?? null;
    $dias = $_POST['dias'] ?? [];

    if ($ci && $nombre && $telefono && $email && $password && $barrio && $mayor18 === '1') {
        try {
            $conn->begin_transaction();

            $sql_usuario = "INSERT INTO usuario (CI, Nombre_Completo, Telefono, Mail, Contraseña) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_usuario);
            $stmt->bind_param("sssss", $ci, $nombre, $telefono, $email, $password);
            $stmt->execute();

            $sql_pv = "INSERT INTO plan_vereda (CI_deUsuario, Barrio) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_pv);
            $stmt->bind_param("ss", $ci, $barrio);
            $stmt->execute();

            if (!empty($dias)) {
                $sql_dispo = "INSERT INTO disponibilidad (CI_deUsuario, Dia) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_dispo);
                foreach ($dias as $dia) {
                    $stmt->bind_param("si", $ci, $dia);
                    $stmt->execute();
                }
            }

            $conn->commit();

            // Guardar sesión
            $_SESSION['user_id'] = $ci;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_email'] = $email;

            // Redirigir
            header("Location: index.php");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } elseif ($mayor18 === '0') {
        echo "Debes ser mayor de 18 años para registrarte.";
    } else {
        echo "Faltan campos obligatorios.";
    }

    $conn->close();
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Plan Vereda</title>
  <link rel="icon" href="assets/media/Isotipo.png" />
  <link rel="stylesheet" href="licencia.css"> 
  <link rel="stylesheet" href="formularios.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
 
</head>
<body >
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
  <form method="POST" action="inscripcionvoluntarios.php">
  <div class="form-container">
    
    <a href="index.php" class="back-arrow"><i class="fa fa-arrow-left"></i></a>
    <div class="logo-container mb-3 text-center">
      <img src="assets/media/MTC.png" alt="Logo MTC">
      <h3><b>Unirse al Plan Vereda</b></h3>
      <p>¡Gracias por tu interés en mejorar tu ciudad! Completa este formulario para sumarte al Plan Vereda.</p>
    </div>

      <div class="mb-3">
        <label class="titulo-from">Cédula</label>
        <div class="input-wrapper position-relative">
          <input name="ci" class="form-control" placeholder="Ingrese su cédula" required inputmode="numeric" pattern="\d*">
          <i class="fa fa-id-card position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%);"></i>
        </div>
      </div>

      <div class="mb-3">
        <label class="titulo-from">Nombre Completo</label>
        <div class="input-wrapper position-relative">
          <input type="text" name="nombre" class="form-control" placeholder="Ingrese su nombre completo" required>
          <i class="fa fa-user position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%);"></i>
        </div>
      </div>

      <?php if (!$loggedIn): ?>
        <div class="mb-3">
          <label class="titulo-from">Correo Electrónico</label>
          <div class="input-wrapper position-relative">
            <input type="email" name="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
            <i class="fa fa-envelope position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%);"></i>
          </div>
        </div>

        <div class="mb-3">
          <label class="titulo-from">Teléfono</label>
          <div class="input-wrapper position-relative">
            <input name="telefono" class="form-control" placeholder="Ingrese su número de teléfono" required inputmode="numeric" pattern="\d*">
            <i class="fa fa-phone position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%);"></i>
          </div>
        </div>

        <div class="mb-3">
          <label class="titulo-from">Contraseña</label>
          <div class="input-wrapper position-relative">
            <input type="password" name="password" id="contraseña" class="form-control" placeholder="Ingrese su contraseña" required>
            <i class="fa fa-eye position-absolute" id="togglePassword" style="top: 50%; right: 12px; transform: translateY(-50%); cursor: pointer;"></i>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="titulo-from">Dirección</label>
        <div class="input-wrapper position-relative">
          <input type="text" name="direccion" class="form-control" placeholder="Ingrese su dirección" required>
          <i class="fa fa-map-marker-alt position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%);"></i>
        </div>
      </div>

      <div class="mb-3">
        <label class="titulo-from">¿Eres mayor de 18 años?</label>
        <div class="input-wrapper">
          <select name="mayor18" class="form-select" required>
            <option value="">Seleccione una opción</option>
            <option value="1">Sí</option>
            <option value="0">No</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="titulo-from">Días disponibles</label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="1" id="lunes">
          <label class="form-check-label" for="lunes">Lunes</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="2" id="martes">
          <label class="form-check-label" for="martes">Martes</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="3" id="miercoles">
          <label class="form-check-label" for="miercoles">Miércoles</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="4" id="jueves">
          <label class="form-check-label" for="jueves">Jueves</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="5" id="viernes">
          <label class="form-check-label" for="viernes">Viernes</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="6" id="sabado">
          <label class="form-check-label" for="sabado">Sábado</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="dias[]" value="7" id="domingo">
          <label class="form-check-label" for="domingo">Domingo</label>
        </div>
      </div>

      <button type="submit" class="btn">Unirse</button>
    </form>
      <footer>
    <p>El código fuente de esta aplicación está disponible bajo una 
      <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
      </a>
    </p>
  </footer>
  </div>
</div>
  <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    if (togglePassword) {
      togglePassword.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
      });
    }
     const selectEdad = document.querySelector('select[name="mayor18"]');
  const submitBtn = document.querySelector('button[type="submit"]');

  if (selectEdad && submitBtn) {
    selectEdad.addEventListener('change', () => {
      submitBtn.disabled = (selectEdad.value === "0");
    });
  }
  </script>
</body>
</html>

