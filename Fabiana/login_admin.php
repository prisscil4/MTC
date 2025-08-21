<?php
session_start();
include 'F:\mtc\config\conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ci = $_POST["CI"];
    $pass = $_POST["Contraseña"];

    $sql = "SELECT * FROM administrador WHERE CI='$ci' AND Contraseña='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION["admin"] = true;
        $_SESSION["CI_admin"] = $ci;
        header("Location: panel_alertas.php");
        exit;
    } else {
        echo "❌ Datos incorrectos.";
    }
}
?>

<h2>Login Administrador</h2>
<form method="post">
  CI: <input type="number" name="CI" required><br>
  Contraseña: <input type="password" name="Contraseña" required><br>
  <input type="submit" value="Entrar">
</form>
