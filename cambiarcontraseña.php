<?php
include('verificacionuser.php');
include('conexion.php'); // debe proveer $conn (mysqli)

$mensaje = "";
$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword     = $_POST['new-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // Validaciones
    if ($newPassword !== $confirmPassword) {
        $mensaje = "Las nuevas contraseñas no coinciden.";
    } elseif (empty($newPassword)) {
        $mensaje = "Completa todos los campos.";
    } elseif (!$userId) {
        $mensaje = "No se detectó sesión de usuario. Inicia sesión e intenta de nuevo.";
    } else {
        // Validación mínima de longitud
        if (strlen($newPassword) < 6) {
            $mensaje = "La nueva contraseña debe tener al menos 6 caracteres.";
        } else {
            // Hashear y actualizar directamente (sin pedir la contraseña actual)
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sqlUp = "UPDATE usuario SET `contraseña` = ? WHERE CI = ?";
            if ($stmt2 = $conn->prepare($sqlUp)) {
                $stmt2->bind_param("si", $hashedPassword, $userId);
                if ($stmt2->execute()) {
                    $mensaje = "✅ Contraseña actualizada correctamente.";
                } else {
                    $mensaje = "Ocurrió un error al actualizar la contraseña: " . $stmt2->error;
                }
                $stmt2->close();
            } else {
                $mensaje = "Ocurrió un error interno (preparando consulta).";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cambiar Contraseña</title>
<link rel="icon" href="assets/media/Isotipo.png" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="formularios.css">
</head>
<body>

<div class="form-container">
    <a href="index.php" class="back-arrow"><i class="fas fa-arrow-left"></i></a>

    <div class="logo-container">
        <img src="assets/media/MTC.png" alt="Logo">
    </div>

    <h2>Cambiar Contraseña</h2>

    <?php if($mensaje): ?>
      <p style="color: <?= strpos($mensaje,'✅')!==false ? 'green' : 'red' ?>; text-align:center;"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="titulo-from">Nueva contraseña</div>
        <div class="input-wrapper">
            <input type="password" name="new-password" placeholder="Nueva contraseña" required>
            <span class="toggle-pass"><i class="fa-solid fa-eye"></i></span>
        </div>

        <div class="titulo-from">Confirmar contraseña</div>
        <div class="input-wrapper">
            <input type="password" name="confirm-password" placeholder="Repite la nueva contraseña" required>
            <span class="toggle-pass"><i class="fa-solid fa-eye"></i></span>
        </div>

        <button type="submit">Actualizar Contraseña</button>
    </form>
</div>

<script>
    // Mostrar / ocultar contraseñas
    document.querySelectorAll('.toggle-pass').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = btn.previousElementSibling;
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });
</script>

</body>
</html>
