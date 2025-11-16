<?php
// DEBUG temporal — mostrar y escribir errores
// IMPORTANTE: desactivar display_errors en producción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

if (session_status() === PHP_SESSION_NONE) session_start();

// === INCLUDES: deben NO imprimir nada ===
include_once('conexion.php');      // Debe crear $conn (mysqli)
include_once('clavere.php');      // Debe definir $claveRecaptcha['publica'] y ['privada']
include_once('verificacionuser.php'); // Si este include hace salida o redirección, replantear su orden

// Forzar charset de conexión
if (isset($conn) && method_exists($conn, 'set_charset')) {
    $conn->set_charset('utf8mb4');
}

// Variables por defecto
$errors = [];
$nombre = $telefono = $mail = '';

// Umbral reCAPTCHA (v3) — ajustar según necesidad; poner null para no validar score
$RECAPTCHA_MIN_SCORE = 0.3;
$RECAPTCHA_EXPECTED_ACTION = 'register';

// PROCESAR POST antes de generar cualquier HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === TOMAR Y SANITIZAR DATOS ===
    $nombre   = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mail     = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validaciones lado servidor (similares a las del cliente)
    if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
        $errors[] = "Ingrese su nombre completo (2-100 caracteres).";
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    } elseif (!preg_match('/@gmail\.com$/i', $mail)) {
        $errors[] = "Solo se permiten correos de Gmail.";
    }

    if (!preg_match('/^\d{6,15}$/', $telefono)) {
        $errors[] = "Ingrese un teléfono válido (solo dígitos, 6 a 15 caracteres).";
    }

    // Reglas de contraseña
    $pwd_errors = [];
    if (mb_strlen($password) < 8) $pwd_errors[] = "mínimo 8 caracteres";
    if (!preg_match('/[A-Z]/', $password)) $pwd_errors[] = "una letra mayúscula";
    if (!preg_match('/[a-z]/', $password)) $pwd_errors[] = "una letra minúscula";
    if (!preg_match('/\d/', $password)) $pwd_errors[] = "un número";
    if (!preg_match('/[\W_]/', $password)) $pwd_errors[] = "un carácter especial";
    if ($password !== $confirm) $pwd_errors[] = "las contraseñas no coinciden";
    if (!empty($pwd_errors)) $errors[] = "Contraseña inválida: " . implode(', ', $pwd_errors) . ".";

    // === reCAPTCHA servidor-side (verificar token) ===
    $token = $_POST['g-recaptcha-response'] ?? '';
    if (empty($token)) {
        $errors[] = "No se pudo verificar reCAPTCHA (token vacío).";
    } else {
        $secret = $claveRecaptcha['privada'] ?? '';
        if (empty($secret)) {
            $errors[] = "Clave reCAPTCHA no configurada en el servidor.";
        } else {
            // Llamada POST a Google reCAPTCHA con timeouts
            $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'secret' => $secret,
                'response' => $token,
                //'remoteip' => $_SERVER['REMOTE_ADDR']
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            $resp = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_err = curl_error($ch);
            curl_close($ch);

            // Log temporal para debugging (quitar en producción)
            file_put_contents(__DIR__.'/recaptcha_debug.log', date('c')." | http: {$httpcode} | resp: ".($resp ?? 'NULL').PHP_EOL, FILE_APPEND);

            if ($resp === false || $httpcode !== 200) {
                $errors[] = "Error al conectar con reCAPTCHA: HTTP {$httpcode} {$curl_err}";
            } else {
                $data = json_decode($resp, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = "Respuesta reCAPTCHA inválida (no JSON).";
                } else {
                    if (!($data['success'] ?? false)) {
                        $codes = isset($data['error-codes']) ? implode(',', (array)$data['error-codes']) : 'sin-codes';
                        $errors[] = "reCAPTCHA inválido: " . $codes;
                    } else {
                        // (opcional) verificar action y score si usas v3
                        if (!empty($RECAPTCHA_EXPECTED_ACTION) && isset($data['action']) && $data['action'] !== $RECAPTCHA_EXPECTED_ACTION) {
                            $errors[] = "Acción reCAPTCHA inesperada.";
                        }
                        if ($RECAPTCHA_MIN_SCORE !== null && isset($data['score']) && $data['score'] < $RECAPTCHA_MIN_SCORE) {
                            $errors[] = "reCAPTCHA detectó tráfico sospechoso (score: {$data['score']}).";
                        }
                    }
                }
            }
        }
    }

    // === Si no hay errores, verificar duplicados e insertar usuario ===
    if (empty($errors)) {
        if (!isset($conn) || !$conn) {
            $errors[] = "Error interno: no hay conexión a la base de datos.";
        } else {
            // 1) Verificar correo duplicado
            $check = $conn->prepare("SELECT id_usuario FROM usuario WHERE Mail = ? LIMIT 1");
            if (!$check) {
                $errors[] = "Error interno al preparar verificación de email: " . $conn->error;
            } else {
                $check->bind_param("s", $mail);
                if (!$check->execute()) {
                    $errors[] = "Error al comprobar email: " . $check->error;
                } else {
                    $check->store_result();
                    if ($check->num_rows > 0) {
                        $errors[] = "Ya existe una cuenta con ese correo.";
                    }
                }
                $check->close();
            }
        }
    }

    // === Insertar si sigue sin errores ===
    if (empty($errors)) {
        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Valores por defecto
        $tipo = 'usuario';
        $banner = 'linear-gradient(135deg, #3498db, #2ecc71)';
        $fotoPerfil = 'assets/media/perfil.png';

        // Generar CI único con límite de intentos
        $ci = null;
        $attempt = 0;
        $max_attempts = 10;
        while ($attempt < $max_attempts) {
            $attempt++;
            $ci_candidate = rand(10000000, 99999999);
            $q = $conn->prepare("SELECT 1 FROM usuario WHERE CI = ? LIMIT 1");
            if (!$q) {
                // Si prepare falla, no vale la pena seguir probando CI aleatorios
                break;
            }
            $q->bind_param("i", $ci_candidate);
            $q->execute();
            $q->store_result();
            $exists = $q->num_rows > 0;
            $q->close();
            if (!$exists) { $ci = $ci_candidate; break; }
        }
        if ($ci === null) {
            $errors[] = "No se pudo generar un identificador único. Intenta nuevamente más tarde.";
        } else {
            // Usar transacción para insertar y evitar estados intermedios
            $conn->begin_transaction();
            $stmt = $conn->prepare("INSERT INTO usuario (CI, Nombre_Completo, Telefono, Mail, Contraseña, Banner, FotoPerfil, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                $errors[] = "Error interno al preparar la inserción: " . $conn->error;
                $conn->rollback();
            } else {
                if (!$stmt->bind_param("isssssss", $ci, $nombre, $telefono, $mail, $password_hash, $banner, $fotoPerfil, $tipo)) {
                    $errors[] = "Error al bindear parámetros: " . $stmt->error;
                    $stmt->close();
                    $conn->rollback();
                } else {
                    if (!$stmt->execute()) {
                        // Si hay error por duplicado improbable u otro
                        $errors[] = "Error al crear usuario: " . $stmt->error;
                        $stmt->close();
                        $conn->rollback();
                    } else {
                        $stmt->close();
                        $conn->commit();

                        // Obtener datos del usuario insertado (para sesión)
                        $q = $conn->prepare("SELECT CI, Nombre_Completo, FotoPerfil FROM usuario WHERE Mail = ? LIMIT 1");
                        if ($q) {
                            $q->bind_param("s", $mail);
                            if ($q->execute()) {
                                $res = $q->get_result();
                                if ($res && $row = $res->fetch_assoc()) {
                                    // Regenerar id de sesión para seguridad
                                    session_regenerate_id(true);
                                    $_SESSION['user_id']    = intval($row['CI']);
                                    $_SESSION['user_name']  = $row['Nombre_Completo'] ?: $nombre;
                                    $_SESSION['user_email'] = $mail;
                                    $_SESSION['user_photo'] = $row['FotoPerfil'] ?? $fotoPerfil;
                                } else {
                                    // No crítico: usuario insertado pero no recuperado (loguear)
                                    error_log("Usuario insertado pero no recuperado por mail: {$mail}");
                                }
                                if ($res) $res->free();
                            } else {
                                error_log("Error al obtener usuario por mail: " . $q->error);
                            }
                            $q->close();
                        } else {
                            error_log("Prepare error al obtener usuario por mail: " . $conn->error);
                        }
session_write_close();
                        // Redirigir (sin salida previa)
                        header("Location: index.php");
                        exit();
                    }
                }
            }
        }
    }
}

// Si llegamos aquí, o no fue POST o hay errores: el HTML se renderizará después de este bloque
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario</title>
  <link rel="icon" href="assets/media/Isotipo.png" />
  <link rel="stylesheet" href="licencia.css">
  <link rel="stylesheet" href="formularios.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .error-list { color: #b02a37; list-style: none; padding-left: 0; }
    .password-requirements {font-size: .9rem; color: #6c757d;}
  </style>
</head>
<body>
<div class="form-container  col-12 col-sm-10 col-md-8 col-lg-6">
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
      <ul class="error-list mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form id="registerForm" method="POST" action="" novalidate>
    <div class="input-wrapper mb-3">
      <label class="titulo-from">Nombre Completo</label>
      <div class="position-relative">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre completo" required value="<?= htmlspecialchars($nombre) ?>">
        <i class="fa fa-user position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Correo Electrónico</label>
      <div class="position-relative">
        <input type="email" class="form-control" id="mail" name="mail" placeholder="Ingrese su correo electrónico (Gmail)" required value="<?= htmlspecialchars($mail) ?>">
        <i class="fa fa-envelope position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
      </div>
    </div>

    <div class="input-wrapper mb-3">
      <label class="titulo-from">Teléfono</label>
      <div class="position-relative">
        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ingrese su número de teléfono" required inputmode="numeric" pattern="\d*" value="<?= htmlspecialchars($telefono) ?>">
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

    <!-- hidden field para el token -->
    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="">

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

<!-- cargar reCAPTCHA con la site key correcta -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($claveRecaptcha['publica']) ?>"></script>

<script>
(function(){
  const form = document.getElementById('registerForm');
  const submitBtn = document.getElementById('submitBtn');

  if (!form) return;

  function showClientErrors(list) {
    const existing = document.querySelector('.alert.alert-warning.client');
    if (existing) existing.remove();
    if (list.length === 0) return;
    const container = document.createElement('div');
    container.className = 'alert alert-warning client';
    container.innerHTML = '<ul class="error-list mb-0"><li>' + list.join('</li><li>') + '</li></ul>';
    form.parentNode.insertBefore(container, form);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    // VALIDACIÓN CLIENTE (idéntica a la del servidor)
    const errors = [];
    const nombre = document.getElementById('nombre').value.trim();
    const mail = document.getElementById('mail').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    if (nombre.length < 2 || nombre.length > 100) errors.push("Nombre: 2-100 caracteres.");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(mail)) errors.push("Formato de correo inválido.");
    else if (!/[@]gmail\.com$/i.test(mail)) errors.push("Solo se permiten correos de Gmail.");
    if (!/^\d{6,15}$/.test(telefono)) errors.push("Teléfono: 6-15 dígitos.");
    if (password.length < 8) errors.push("Contraseña: mínimo 8 caracteres.");
    if (!/[A-Z]/.test(password)) errors.push("Contraseña: debe tener una mayúscula.");
    if (!/[a-z]/.test(password)) errors.push("Contraseña: debe tener una minúscula.");
    if (!/\d/.test(password)) errors.push("Contraseña: debe tener un número.");
    if (!/[\W_]/.test(password)) errors.push("Contraseña: debe tener un carácter especial.");
    if (password !== confirm) errors.push("Las contraseñas no coinciden.");

    if (errors.length > 0) {
      showClientErrors(errors);
      return;
    }

    submitBtn.disabled = true;

    if (typeof grecaptcha === 'undefined') {
      alert('No se pudo cargar reCAPTCHA. Recarga la página e inténtalo de nuevo.');
      submitBtn.disabled = false;
      return;
    }

    // pedir token justo antes de enviar
    grecaptcha.ready(function() {
      grecaptcha.execute('<?= htmlspecialchars($claveRecaptcha['publica']) ?>', {action: 'register'}).then(function(token) {
        console.log('Token generado (cliente):', token); // se ve en consola
        document.getElementById('g-recaptcha-response').value = token;
        form.submit();
      }).catch(function(err) {
        console.error('reCAPTCHA error:', err);
        alert('Error verificando reCAPTCHA. Intenta recargar la página.');
        submitBtn.disabled = false;
      });
    });

  });
})();
</script>
<script>
// ---- Mostrar/ocultar contraseña principal ----
const togglePassword = document.getElementById("togglePassword");
const passwordField = document.getElementById("password");

togglePassword.addEventListener("click", function () {
    // cambiar tipo
    const type = passwordField.type === "password" ? "text" : "password";
    passwordField.type = type;

    // cambiar icono
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
});

// ---- Mostrar/ocultar confirmar contraseña ----
const toggleConfirm = document.createElement("i");
toggleConfirm.className = "fa fa-eye";
toggleConfirm.style.cssText = "right:12px; top:50%; transform:translateY(-50%); cursor:pointer; position:absolute;";
document.querySelector("#confirm_password").parentNode.appendChild(toggleConfirm);

const confirmField = document.getElementById("confirm_password");

toggleConfirm.addEventListener("click", function () {
    const type = confirmField.type === "password" ? "text" : "password";
    confirmField.type = type;

    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
});
</script>

</body>
</html>
