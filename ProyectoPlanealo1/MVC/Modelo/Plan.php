<?php

class Plan {
    // Propiedad para almacenar la conexión con la BD
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para obtener las actividades de una categoría y ciudad específica
    public function obtenerActividadesPorCategoriaYCiudad($categoria_id, $ciudad_id) {
        $query = "SELECT * FROM planes WHERE id_categoria = ? AND id_ciudad = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $categoria_id, $ciudad_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $actividades = [];
        while ($row = $result->fetch_assoc()) {
            $actividades[] = $row;
        }
        return $actividades;
    }

    // Método para obtener los detalles de una categoría
    public function obtenerCategoria($categoria_id) {
        $query = "SELECT nombre FROM categorias WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoria_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // Método para obtener todas las categorías
    public function obtenerCategorias() {
        $query = "SELECT c.id_categoria, c.nombre, COUNT(p.id_plan) AS num_actividades
                  FROM categorias c
                  LEFT JOIN planes p ON c.id_categoria = p.id_categoria
                  GROUP BY c.id_categoria";
        $result = $this->conn->query($query);

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        return $categorias;
    }
}
?>
