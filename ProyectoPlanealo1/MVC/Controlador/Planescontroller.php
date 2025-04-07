<?php
class PlanesController {
    // Propiedad para almacenar la conexión con la BD
    private $conn; 

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function mostrarActividades()
    {
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : ''; 
        // Obtener la categoría desde la URL
        $ciudad = isset($_GET['ciudad']) ? $_GET['ciudad'] : 1; 
        // Obtener la ciudad desde la URL, con valor por defecto
        $search = isset($_GET['search']) ? $_GET['search'] : ''; 
        // Obtener el término de búsqueda desde la URL
        
        // Consulta para obtener planes de la ciudad seleccionada
        $sql = "SELECT * FROM planes WHERE id_ciudad = $ciudad";
    
        if ($categoria) {
            $sql .= " AND id_categoria = $categoria";
        }

        if ($search) {
            $sql .= " AND (titulo LIKE '%$search%' OR descripcion LIKE '%$search%')";
        }
        
        // Ejecuta la consulta
        $result = $this->conn->query($sql);
        
        // Array para almacenar los resultados
        $actividades = [];
        while ($row = $result->fetch_assoc()) {
            $actividades[] = $row;
        }
    
        // Obtener la categoría actual
        $categoriaNombre = '';
        if ($categoria) {
            $categoriaResult = $this->conn->query("SELECT nombre FROM categorias WHERE id_categoria = $categoria");
            $categoriaData = $categoriaResult->fetch_assoc();
            $categoriaNombre = $categoriaData['nombre'];
        }
    
        return [
            'actividades' => $actividades,
            'categoria' => $categoriaNombre
        ];
    }
    
    // Método para obtener las categorías
    public function mostrarCategorias() {
        $query = "SELECT c.id_categoria, c.nombre, COUNT(p.id_plan) AS num_actividades
                  FROM categorias c
                  LEFT JOIN planes p ON c.id_categoria = p.id_categoria
                  GROUP BY c.id_categoria";
        $result = $this->conn->query($query);
        $categorias = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }
        return $categorias;
    }
}
?>
