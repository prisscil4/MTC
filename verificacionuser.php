<?php

include('conexion.php');
define('REQUIERE_LOGIN', true); 
$usuario = null;
$rol = 'usuario'; // rol por defecto

if (isset($_SESSION['user_id'])) {
    $ci = intval($_SESSION['user_id']);

    // Obtener datos del usuario
    $sql = "SELECT CI, Nombre_Completo, Telefono, Mail, FotoPerfil 
            FROM usuario 
            WHERE CI = $ci LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $usuario = mysqli_fetch_assoc($res);

        // Determinar rol
        $sql_admin = "SELECT 1 FROM administrador WHERE CI = $ci LIMIT 1";
        $res_admin = mysqli_query($conn, $sql_admin);
        if ($res_admin && mysqli_num_rows($res_admin) > 0) {
            $rol = 'admin';
        } else {
            $sql_vol = "SELECT 1 FROM plan_vereda WHERE CI_deUsuario = $ci LIMIT 1";
            $res_vol = mysqli_query($conn, $sql_vol);
            if ($res_vol && mysqli_num_rows($res_vol) > 0) {
                $rol = 'voluntario';
            }
        }

        $_SESSION['rol'] = $rol;
    }
} else {
    
}
?>
