<?php

class DBManager {
    private $db;
	private $host;
	private $user;
	private $pass;
    private $port;
        public function __construct() {
            $this->db = "urbanstyle";
            $this->host = "localhost";
            $this->user = "root";
            $this->pass = null;
            $this->port = 3306;
        }
        private function open()
        {
            $link = mysqli_connect(
                $this->host, $this->user, $this->pass, $this->db, $this->port
            ) or die('Error al abrir conexion');

            return $link;
        }

        private function close($link)
        {
            mysqli_close($link);
        }


        // CRUD productos
        public function agregarProducto($nombre, $descripcion, $precio, $categoria, $stock, $foto) {
            $link = $this->open();
            if (!$link) return false;

            // Preparar la consulta con prepared statement
            $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, stock, foto) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $link->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed (agregarProducto): " . $link->error);
                $this->close($link);
                return false;
            }

            // Asegurar tipos: nombre (s), descripcion (s), precio (d), categoria (s), stock (i), foto (s)
            $stmt->bind_param("ssdsis", $nombre, $descripcion, $precio, $categoria, $stock, $foto);

            $ok = $stmt->execute();
            if (!$ok) {
                error_log("Execute failed (agregarProducto): " . $stmt->error);
            }

            $stmt->close();
            $this->close($link);
            return $ok;
        }

        public function obtenerProductos() {
            $link = $this->open();

            $sql = "SELECT * FROM productos ORDER BY id_producto DESC";
            $result = $link->query($sql);

            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }

            $this->close($link);
            return $productos;
        }
        
        public function actualizarProducto($id, $nombre, $descripcion, $precio, $categoria, $stock, $foto) {
            $link = $this->open();
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, categoria=?, stock=?, foto=? WHERE id_producto=?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("ssdsisi", $nombre, $descripcion, $precio, $categoria, $stock, $foto, $id);
            $ok = $stmt->execute();
            if (!$ok) {
            error_log("Error en UPDATE: " . $stmt->error);
}

            $stmt->close();
            $this->close($link);
            return $ok;
        }

        public function eliminarProducto($id) {
            $link = $this->open();
            $sql = "DELETE FROM productos WHERE id_producto=?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            $this->close($link);
            return $ok;
        }

        public function productoExiste($id) {
            $link = $this->open();
            $sql = "SELECT COUNT(*) FROM productos WHERE id_producto=?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            $this->close($link);
            return $count > 0;
        }

        
    }
?>