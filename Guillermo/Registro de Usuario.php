<?php
session_start();
include('conexion.php'); // tu conexión a la base de datos

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cedula = trim($_POST['cedula']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $confirmar = $_POST['confirmar'];

    // Validaciones básicas
    if (empty($cedula) || empty($correo) || empty($telefono) || empty($contrasena) || empty($confirmar)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif ($contrasena !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el correo o la cédula ya existen
        $stmt = $conn->prepare("SELECT id FROM usuario WHERE mail = ? OR cedula = ?");
        $stmt->bind_param("ss", $correo, $cedula);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "El correo o la cédula ya están registrados.";
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuario (cedula, mail, telefono, contraseña) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $cedula, $correo, $telefono, $hash);

            if ($stmt->execute()) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_email'] = $correo;
                header("Location: index.php"); // redirige al inicio o perfil
                exit;
            } else {
                $mensaje = "Error al registrarse, intente nuevamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Registro de Usuario</title>
<style>
body {
  margin: 0;
  font-family: sans-serif;
  background: linear-gradient(to left, #38CCCA, #7D9BC4);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.contenedor {
  background: rgba(255, 255, 255, 0.1);
  padding: 30px;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 400px;
}

.cuadrado {
  width: 100%;
  margin: 10px 0;
  padding: 12px;
  border: 2px solid #fff;
  border-radius: 8px;
  font-size: 16px;
  background-color: #1b7dba;
  color: white;
}

.cuadrado::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.btn {
  margin-top: 15px;
  padding: 15px;
  width: 100%;
  border: none;
  border-radius: 8px;
  background: #1dd2b4;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

h1{
  font-size: 8mm;
}

h2 {
  color: rgb(0, 0, 0);
  margin-bottom: 5px;
  font-size: 16px;
  align-self: flex-start;
}

.header {
  display: flex;
  flex-direction: column;  
  align-items: center;    
  margin-bottom: 20px;     
}

.logo {
  width: 300px;  
  height: auto;
}

.titulo {
  color: rgb(0, 0, 0);
  font-size: 28px;
  margin: 0;  
}

.mensaje {
  color: red;
  margin-bottom: 10px;
  text-align: center;
}
</style>
</head>
<body>
<div class="contenedor">
  <div class="header">
    <img class="logo" src="assets/media/MTC.png" alt="Logo" />
    <h1 class="titulo">Registro de Usuario</h1>
  </div>

  <?php if ($mensaje) echo "<div class='mensaje'>$mensaje</div>"; ?>

  <form method="POST" action="">
    <h2>Cédula</h2>
    <input type="text" name="cedula" class="cuadrado" placeholder="Ingrese su cédula" required/>

    <h2>Correo electrónico</h2>
    <input type="text" name="correo" class="cuadrado" placeholder="Ingrese su correo electrónico" required/>

    <h2>Teléfono</h2>
    <input type="text" name="telefono" class="cuadrado" placeholder="Ingrese su número de teléfono" required/>

    <h2>Contraseña</h2>
    <input type="password" name="contrasena" class="cuadrado" placeholder="Ingrese su contraseña" required/>

    <h2>Confirmar Contraseña</h2>
    <input type="password" name="confirmar" class="cuadrado" placeholder="Confirme su Contraseña" required/>

    <button class="btn" type="submit">Registrarse</button>
  </form>
</div>
</body>
</html>
