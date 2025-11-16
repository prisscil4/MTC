<?php
include('verificacionuser.php');
include('conexion.php');

// coloca esto al inicio del archivo PHP (arriba, junto a includes)
define('RECAPTCHA_SITE_KEY', '6LcRFwgsAAAAABQdaRoFHUgWyZLXtNoZIhrQc1Ea');   // <-- pega aquí la Site Key (pública)
define('RECAPTCHA_SECRET',   '6LcRFwgsAAAAAM0Kejs1vMu1sZTyMz2mOpHlTRy0'); // <-- pega aquí la Secret Key (privada)


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === SANITIZAR Y TOMAR DATOS ===
    $nombre   = $conn->real_escape_string(trim($_POST['nombre'] ?? ''));
    $telefono = $conn->real_escape_string(trim($_POST['telefono'] ?? ''));
    $mail     = $conn->real_escape_string(trim($_POST['mail'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // === VALIDACIÓN LADO SERVIDOR ===

    // Nombre: mínimo 2 caracteres, máximo 100
    if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
        $errors[] = "Ingrese su nombre completo (2-100 caracteres).";
    }

    // Email: formato y dominio gmail
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    } elseif (!preg_match('/@gmail\.com$/i', $mail)) {
        $errors[] = "Solo se permiten correos de Gmail.";
    }

    // Teléfono: solo dígitos, 6-15 caracteres
    if (!preg_match('/^\d{6,15}$/', $telefono)) {
        $errors[] = "Ingrese un teléfono válido (solo dígitos, 6 a 15 caracteres).";
    }

    // Contraseña: reglas de seguridad (mín 8, mayúscula, minúscula, número, carácter especial)
    $pwd_errors = [];
    if (strlen($password) < 8) $pwd_errors[] = "mínimo 8 caracteres";
    if (!preg_match('/[A-Z]/', $password)) $pwd_errors[] = "una letra mayúscula";
    if (!preg_match('/[a-z]/', $password)) $pwd_errors[] = "una letra minúscula";
    if (!preg_match('/\d/', $password)) $pwd_errors[] = "un número";
    if (!preg_match('/[\W_]/', $password)) $pwd_errors[] = "un carácter especial";
    if ($password !== $confirm) $pwd_errors[] = "las contraseñas no coinciden";

    if (!empty($pwd_errors)) {
        $errors[] = "Contraseña inválida: " . implode(', ', $pwd_errors) . ".";
    }

    // reCAPTCHA: verificar respuesta no vacía
    if (empty($recaptcha_response)) {
        $errors[] = "Por favor verifica el captcha.";
    } else {
        // Verificar con Google (server-side)
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => RECAPTCHA_SECRET,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
        ];

        // usar cURL si está disponible
        $verify = null;
        if (function_exists('curl_version')) {
            $ch = curl_init($recaptcha_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $verify = curl_exec($ch);
            curl_close($ch);
        } else {
            // fallback a file_get_contents
            $options = ['http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
                'timeout' => 10
            ]];
            $context  = stream_context_create($options);
            $verify = @file_get_contents($recaptcha_url, false, $context);
        }

        if ($verify === null || $verify === false) {
            $errors[] = "No se pudo validar el captcha (error de comunicación). Intenta más tarde.";
        } else {
            $captcha_success = json_decode($verify, true);
            if (!($captcha_success['success'] ?? false)) {
                // Opcional: revisar 'score' o 'error-codes'
                $errors[] = "La verificación del captcha falló. Intenta nuevamente.";
            }
        }
    }

    // Si no hay errores, continuar a comprobar duplicados e insertar
    if (empty($errors)) {
        // Verificar correo duplicado
        $check = $conn->prepare("SELECT id FROM usuario WHERE Mail = ?");
        if (!$check) {
            $errors[] = "Error interno (consulta).";
        } else {
            $check->bind_param("s", $mail);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $errors[] = "Ya existe una cuenta con ese correo.";
            }
            $check->close();
        }
    }

    // Insertar si todo OK
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Ajusta el nombre de columna si tu BD tiene otro
        $stmt = $conn->prepare("INSERT INTO usuario (`Nombre_Completo`, `Telefono`, `Mail`, `Contraseña`) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $errors[] = "Error interno al preparar la inserción.";
        } else {
            $stmt->bind_param("ssss", $nombre, $telefono, $mail, $password_hash);
            if ($stmt->execute()) {
                // Registro correcto: redirigir al index sin mensajes
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Error al registrar: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro de Usuario</title>
  <link rel="icon" href="assets/media/Isotipo.png" />
  <link rel="stylesheet" href="licencia.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="formularios.css">
  <!-- reCAPTCHA v2 -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    .error-list { color: #b02a37; list-style: none; padding-left: 0; }
    .password-requirements {font-size: .9rem; color: #6c757d;}
  </style>
</head>
<body>
<div class="form-container">
  <header>
    <a href="index.php" class="back-arrow"><i class="material-icons">arrow_back</i></a>
    <div class="logo-container">
      <a href="index.php"><img src="assets/media/MTC.png" alt="Logo MTC" class="logo-img"></a>
    </div>
    <div class="text-center mt-3 mb-3">
      <h3><b>Registro de Usuario</b></h3>
      <p>¡Bienvenido a mejorar tu ciudad!</p>
    </div>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-warning">
      <ul class="error-list mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form id="registerForm" method="POST" action="" novalidate>
    <div class="input-wrapper mb-3">
      <label class="titulo-from">Nombre Completo</label>
      <div class="position-relative">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre completo" required>
        <i class="fa fa-user position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Correo Electrónico</label>
      <div class="position-relative">
        <input type="email" class="form-control" id="mail" name="mail" placeholder="Ingrese su correo electrónico (Gmail)" required>
        <i class="fa fa-envelope position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Teléfono</label>
      <div class="position-relative">
        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ingrese su número de teléfono" required inputmode="numeric" pattern="\d*">
        <i class="fa fa-phone position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Contraseña</label>
      <div class="position-relative">
        <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
        <i class="fa fa-eye position-absolute" id="togglePassword" style="right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
      </div>
      <div class="password-requirements mt-1">
        Requisitos: mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial.
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Confirmar Contraseña</label>
      <div class="position-relative">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
      </div>
    </div>

    <!-- ReCAPTCHA v2 checkbox -->
    <div class="mb-3">
      <div class="g-recaptcha" data-sitekey="RECAPTCHA_SITE_KEY"></div> <!-- reemplaza con tu site key -->
    </div>

    <button type="submit" id="submitBtn" class="btn">Registrarse</button>
  </form>

  <p class="text-center mt-3">
    ¿Ya tienes cuenta? <a href="Inicio Sesion.php">Inicia sesión</a><br>
    ¿Quieres unirte a Plan Vereda? <a href="incripcionvoluntarios.php">Unirse</a>
  </p>

  <footer>
    <p>El código fuente de esta aplicación está disponible bajo una 
      <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
      </a>
    </p>
  </footer>
</div>

<script>
/* Toggle visibilidad de contraseña */
const toggle = document.getElementById('togglePassword');
const passwordField = document.getElementById('password');
const confirmField = document.getElementById('confirm_password');
toggle.addEventListener('click', () => {
  const isHidden = passwordField.type === 'password';
  passwordField.type = confirmField.type = isHidden ? 'text' : 'password';
  toggle.classList.toggle('fa-eye');
  toggle.classList.toggle('fa-eye-slash');
});

/* Validación cliente antes de enviar */
document.getElementById('registerForm').addEventListener('submit', function(e) {
  // limpiar errores previos visuales
  const errors = [];

  const nombre = document.getElementById('nombre').value.trim();
  const mail = document.getElementById('mail').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const password = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;

  if (nombre.length < 2 || nombre.length > 100) errors.push("Nombre: 2-100 caracteres.");
  // email válido y gmail
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(mail)) errors.push("Formato de correo inválido.");
  else if (!/[@]gmail\.com$/i.test(mail)) errors.push("Solo se permiten correos de Gmail.");
  // teléfono
  if (!/^\d{6,15}$/.test(telefono)) errors.push("Teléfono: 6-15 dígitos.");
  // contraseña
  if (password.length < 8) errors.push("Contraseña: mínimo 8 caracteres.");
  if (!/[A-Z]/.test(password)) errors.push("Contraseña: debe tener una mayúscula.");
  if (!/[a-z]/.test(password)) errors.push("Contraseña: debe tener una minúscula.");
  if (!/\d/.test(password)) errors.push("Contraseña: debe tener un número.");
  if (!/[\W_]/.test(password)) errors.push("Contraseña: debe tener un carácter especial.");
  if (password !== confirm) errors.push("Las contraseñas no coinciden.");
  // reCAPTCHA (simple check: si no está marcado, g-recaptcha-response estará vacío)
  if (!document.querySelector('[name="g-recaptcha-response"]') || document.querySelector('[name="g-recaptcha-response"]').value.trim() === '') {
    errors.push("Verifica el captcha.");
  }

  if (errors.length > 0) {
    e.preventDefault();
    // Mostrar errores en un alert simple o inyectar en un contenedor, aquí mostramos alert:
    // (tu pediste sin ventanas emergentes al registrarse; mostrar alert solo para errores locales)
    const container = document.querySelector('.alert') || document.createElement('div');
    container.className = 'alert alert-warning';
    container.innerHTML = '<ul class="error-list"><li>' + errors.join('</li><li>') + '</li></ul>';
    // insertar antes del formulario
    const form = document.getElementById('registerForm');
    form.parentNode.insertBefore(container, form);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
</script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
