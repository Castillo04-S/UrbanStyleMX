<?php
require 'model_layer/DBManager.php';
header("Content-Type: application/json; charset=UTF-8");
session_start();

$db = new DBManager();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

ini_set('display_errors', 1);
error_reporting(E_ALL);

////////////////////// LOGIN /////////////////////////
if ($accion === 'login') {
    $correo = trim($_POST['correo'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if ($correo === '' || $pass === '') {
        echo json_encode(["exito" => false, "mensaje" => "❌ Todos los campos son obligatorios."]);
        exit;
    }

    $usuario = $db->loginUsuario($correo, $pass);

    if ($usuario) {
        $_SESSION['id_cliente'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        echo json_encode([
            "exito" => true,
            "mensaje" => "✅ Bienvenido, {$usuario['nombre']}",
            "rol" => $usuario['rol'],
            "nombre" => $usuario['nombre']
        ]);
    } else {
        echo json_encode([
            "exito" => false,
            "mensaje" => "❌ Correo o contraseña incorrectos."
        ]);
    }
    exit;


////////////////////// AGREGAR /////////////////////////
} elseif ($accion === 'agregar') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? 'cliente');

    if ($nombre === '' || $correo === '' || $password === '') {
        echo json_encode(["mensaje" => "❌ Faltan datos obligatorios."]);
        exit;
    }

    $ok = $db->agregarUsuario($nombre, $correo, '', '', $password, $rol);
    echo json_encode([
        "mensaje" => $ok ? "✅ Usuario agregado correctamente." : "❌ Error al agregar usuario."
    ]);
    exit;


////////////////////// LISTAR /////////////////////////
} elseif ($accion === 'listar') {
    $usuarios = $db->obtenerUsuarios();
    echo json_encode($usuarios);
    exit;


////////////////////// ACTUALIZAR /////////////////////////
} elseif ($accion === 'actualizar') {
    $id = intval($_POST['id_usuario'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = trim($_POST['rol'] ?? 'cliente');

    if ($id <= 0 || $nombre === '' || $correo === '') {
        echo json_encode(["mensaje" => "❌ Datos inválidos o incompletos."]);
        exit;
    }

    $ok = $db->actualizarUsuario($id, $nombre, $correo, $rol);
    echo json_encode([
        "mensaje" => $ok ? "✅ Usuario actualizado correctamente." : "❌ Error al actualizar usuario."
    ]);
    exit;


////////////////////// ELIMINAR /////////////////////////
} elseif ($accion === 'eliminar') {
    $id = intval($_POST['id_usuario'] ?? 0);
    $ok = $db->eliminarUsuario($id);
    echo json_encode([
        "mensaje" => $ok ? "🗑️ Usuario eliminado correctamente." : "❌ Error al eliminar usuario."
    ]);
    exit;


////////////////////// ACCIÓN NO RECONOCIDA /////////////////////////
} else {
    echo json_encode(["mensaje" => "❌ Acción no reconocida."]);
    exit;
}
?>
