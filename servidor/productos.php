<?php
    require 'model_layer/DBManager.php';

    header("Content-Type: application/json; charset=UTF-8");

    // Crear una instancia del DBManager
    $db = new DBManager();

    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
ini_set('display_errors', 1);
error_reporting(E_ALL);
    if ($accion === 'agregar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? '';
        $categoria = trim($_POST['categoria'] ?? '');
        $stock = $_POST['stock'] ?? '';

        // validaciones básicas
        if ($nombre === '' || $descripcion === '' || $precio === '' || $categoria === '' || $stock === '') {
            echo json_encode(["mensaje" => "❌ Faltan datos obligatorios."]);
            exit;
        }

        // Manejo de la imagen: comprobar error de upload
        $foto = null;
        if (isset($_FILES['foto'])) {
            if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['foto']['tmp_name'];
                $mime = mime_content_type($tmp) ?: 'image/jpeg';
                $data = file_get_contents($tmp);
                $base64 = base64_encode($data);
                // opcional: almacenar como data URI para desplegar directamente
                $foto = 'data:' . $mime . ';base64,' . $base64;
            } else {
                // Si quieres aceptar sin imagen, comenta la siguiente línea
                echo "❌ Error al subir la imagen. Código de error: " . $_FILES['foto']['error'];
                exit;
            }
        }

        // casteos
        $precio = floatval($precio);
        $stock = intval($stock);

        $ok = $db->agregarProducto($nombre, $descripcion, $precio, $categoria, $stock, $foto);

        if ($ok) {
            echo json_encode(["mensaje" => "✅ Producto agregado correctamente."]);
            exit;
        } else {
            echo json_encode(["mensaje" => "❌ Error al agregar el producto. Revisa logs del servidor."]);
            exit;
        }

////////////////////// Cargar ////////////////////////////////////
    } elseif ($accion === 'listar') {
        $productos = $db->obtenerProductos();
        echo json_encode($productos);
        exit;

////////////////////// Actualizar ////////////////////////////////////
    } elseif ($accion === 'actualizar') {
    $id = intval($_POST['id_producto'] ?? 0);
    error_log("ID recibido para actualizar: " . $id);

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $foto = $_POST['foto_actual'] ?? '';

    // Validación básica
    if ($id <= 0 || $nombre === '' || $descripcion === '' || $categoria === '' || $precio <= 0 || $stock < 0) {
        echo json_encode(["mensaje" => "❌ Datos inválidos o incompletos."]);
        exit;
    }

    // Verifica si el producto existe
    if (!$db->productoExiste($id)) {
        echo json_encode(["mensaje" => "❌ El producto con ID $id no existe."]);
        exit;
    }

    // Si se subió nueva imagen, reemplaza la actual
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['foto']['tmp_name'];
        $mime = mime_content_type($tmp) ?: 'image/jpeg';
        $data = file_get_contents($tmp);
        $base64 = base64_encode($data);
        $foto = 'data:' . $mime . ';base64,' . $base64;
    }

    $ok = $db->actualizarProducto($id, $nombre, $descripcion, $precio, $categoria, $stock, $foto);

    echo json_encode([
        "mensaje" => $ok ? "✅ Producto actualizado correctamente." : "❌ Error al actualizar el producto."
    ]);
    exit;

////////////////////// Eliminar ////////////////////////////////////
    } elseif ($accion === 'eliminar') {
        $id = intval($_POST['id_producto'] ?? 0);
        $ok = $db->eliminarProducto($id);
        echo json_encode([
            "mensaje" => $ok ? "🗑️ Producto eliminado correctamente." : "❌ Error al eliminar el producto."
        ]);
        exit;

    }else {
        echo json_encode(["mensaje" => "❌ Acción no reconocida."]);
        exit;
    }
?>
