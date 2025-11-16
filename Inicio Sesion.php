
<?php
session_start();
include('conexion.php');

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $conn->real_escape_string($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($mail) && !empty($password)) {
        // Buscar usuario normal
        $sql_user = "SELECT id_usuario, Nombre_Completo, Mail, Telefono, Contraseña, FotoPerfil 
                     FROM usuario 
                     WHERE Mail = '$mail' LIMIT 1";
        $res_user = $conn->query($sql_user);

        if ($res_user && $res_user->num_rows === 1) {
            $user = $res_user->fetch_assoc();
        } else {
            // Si no está en usuario, probar en Administrador
            $sql_admin = "SELECT CI, Nombre_Completo, Mail, Telefono, Contraseña
                          FROM administrador
                          WHERE Mail = '$mail' LIMIT 1";
            $res_admin = $conn->query($sql_admin);
            if ($res_admin && $res_admin->num_rows === 1) {
                $user = $res_admin->fetch_assoc();
            } else {
                $user = null;
            }
        }

        if ($user) {
            if ($password === $user['Contraseña']) {
                session_regenerate_id(true);
$_SESSION['id_usuario']   =$user['id_usuario'];
                $_SESSION['user_id']     = $user['CI'];
                $_SESSION['nombre']      = $user['Nombre_Completo'];
                $_SESSION['mail']        = $user['Mail'];
                $_SESSION['telefono']    = $user['Telefono'];
                $_SESSION['profile_pic'] = $user['FotoPerfil'] ?? 'assets/media/perfil.png';

                // Determinar rol
                $rol = 'usuario';
                $ci = $user['CI'];

                // Si viene de Administrador directamente
                if (isset($res_admin) && $res_admin && $res_admin->num_rows > 0) {
                    $rol = 'admin';
                } else {
                    // Verificar si está en Administrador
                    $check_admin = $conn->query("SELECT 1 FROM administrador WHERE CI = '$ci' LIMIT 1");
                    if ($check_admin && $check_admin->num_rows > 0) {
                        $rol = 'admin';
                    } else {
                        // Verificar si es voluntario
                        $check_vol = $conn->query("SELECT 1 FROM plan_vereda WHERE CI_deUsuario = '$ci' LIMIT 1");
                        if ($check_vol && $check_vol->num_rows > 0) {
                            $rol = 'voluntario';
                        }
                    }
                }

                $_SESSION['rol'] = $rol;

                header("Location: index.php");
                exit;
            } else {
                $mensaje = "❌ Contraseña incorrecta.";
            }
        } else {
            $mensaje = "❌ Usuario no encontrado.";
        }
    } else {
        $mensaje = "⚠️ Por favor, completa todos los campos.";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar Sesión</title>
  <link href="formularios.css" rel="stylesheet">
  <link href="licencia.css" rel="stylesheet" >
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

    <?php if (!empty($mensaje)): ?>
      <p style="color:red; text-align: center;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="titulo-from">Correo Electrónico</label>
        <div class="input-wrapper position-relative">
          <input type="email" class="form-control" name="mail" placeholder="Ingrese su correo electrónico" required>
          <i class="fa fa-envelope position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
        </div>
      </div>

      <div class="mb-3">
        <label class="titulo-from">Teléfono</label>
        <div class="input-wrapper position-relative">
          <input type="text" class="form-control" name="telefono" placeholder="Ingrese su número de teléfono" required inputmode="numeric" pattern="\d*">
          <i class="fa fa-phone position-absolute" style="right:12px; top:50%; transform:translateY(-50%);"></i>
        </div>
      </div>

      <div class="mb-3">
        <label class="titulo-from">Contraseña</label>
        <div class="input-wrapper position-relative">
          <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su contraseña" required>
          <i class="fa fa-eye position-absolute" id="togglePassword" style="right:12px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
        </div>
      </div>

      <button type="submit" class="btn">Iniciar Sesión</button>
    </form>

    <p class="titulo-from text-center mt-3">
      ¿No tienes cuenta? <a href="Registro de Usuario.php">Regístrate</a><br>
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
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  </script>
</body>
</html>
