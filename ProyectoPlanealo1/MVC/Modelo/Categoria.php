<?php

class Categoria {
    // Propiedad para almacenar la conexión con la BD
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Método para obtener todas las categorías
    public function obtenerTodas() {
        $query = "SELECT * FROM categorias";
        $result = $this->conn->query($query);
        return $result;
    }

    // Método para obtener una categoría por su ID
    public function obtenerPorId($id_categoria) {
        $query = "SELECT nombre FROM categorias WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
