<?php
// Verificación de autenticación
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: usuarios/login.php'); // Redirige al login si no está autenticado
    exit();
}

// Define el título de la página, dependiendo de si hay una categoría seleccionada
$pageTitle = isset($_GET['categoria']) ? "ACTIVIDADES - " . $_GET['categoria'] : "DEPORTE";

// Incluir el header
include 'MVC/Vista/includes/header.php';

// Instanciar el controlador de Planes y la BD
include 'config/db_connection.php';
include_once 'MVC/Controlador/PlanesController.php';

$controller = new PlanesController($conn);

// Obtener las actividades y la categoría desde el controlador
$data = $controller->mostrarActividades();
$categorias = $controller->mostrarCategorias();
?>

<!-- Contenido específico de la categoría seleccionada -->
<section class="top-section">
    <div class="section-title">
        <h2>Categoria<?php echo htmlspecialchars($data['categoria']); ?></h2>
    </div>
    <div class="date-time">
        <p id="current-date-time"></p> <!-- Elemento para mostrar la fecha y hora -->
    </div>
    
    <!-- Formulario de búsqueda -->
    <div class="search-bar">
        <form action="index.php" method="GET">
            <input type="text" name="search" placeholder="Busca una actividad" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <!-- Dropdown para seleccionar ciudad -->
    <div class="city-dropdown">
        <select onchange="window.location.href = 'index.php?ciudad=' + this.value + '&categoria=<?php echo isset($_GET['categoria']) ? $_GET['categoria'] : ''; ?>';">
            <?php
            // Obtener ciudades de la base de datos
            $query = "SELECT * FROM ciudad";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Marcar la ciudad seleccionada como "selected"
                    $selected = ($row['id_ciudad'] == ($_GET['ciudad'] ?? 1)) ? 'selected' : '';
                    echo '<option value="' . $row['id_ciudad'] . '" ' . $selected . '>' . $row['nombre'] . '</option>';
                }
            } else {
                echo '<option value="1">Madrid</option>';
            }
            ?>
        </select>
    </div>
</section>

<main>
    <div class="content">
        <!-- Actividades de la categoría seleccionada -->
        <section class="activities">
            <?php
            if (!empty($data['actividades'])) {
                foreach ($data['actividades'] as $row) {
                    // Filtrar actividades por la búsqueda si se ha introducido texto
                    if (isset($_GET['search'])) {
                        $searchQuery = strtolower($_GET['search']);
                        $titulo = strtolower($row['titulo']);
                        $descripcion = strtolower($row['descripcion']);
                        if (strpos($titulo, $searchQuery) === false && strpos($descripcion, $searchQuery) === false) {
                            continue; // Si no coincide con la búsqueda, no mostrar esta actividad
                        }
                    }

                    echo '
                    <div class="activity-card">
                        <div class="activity-image"></div> <!-- Placeholder para la imagen -->
                        <h3>' . htmlspecialchars($row['titulo']) . '</h3>
                        <p>' . htmlspecialchars($row['fecha']) . '</p>
                        <p>' . htmlspecialchars($row['precio']) . ' €</p>
                        <p>' . htmlspecialchars($row['ubicacion']) . '</p>
                    </div>';
                }
            } else {
                echo "<p>No hay actividades disponibles para esta búsqueda.</p>";
            }
            ?>
        </section>

        <!-- Filtro de categorías -->
        <aside class="category-filter">
            <h3>Categorías</h3>
            <ul>
                <?php
                if (!empty($categorias)) {
                    foreach ($categorias as $row) {
                        echo '
                        <li>
                            <a href="index.php?categoria=' . $row['id_categoria'] . '" class="category-link">
                                <span class="category-name">' . htmlspecialchars($row['nombre']) . '</span>
                                <span class="category-count">' . $row['num_actividades'] . '</span>
                            </a>
                        </li>';
                    }
                } else {
                    echo "<p>No hay categorías disponibles.</p>";
                }
                ?>
            </ul>
        </aside>
    </div>
</main>

<?php
include 'MVC/Vista/includes/footer.php'; // Incluir el footer
?>

</body>
</html>

<!-- Script para fecha y hora -->
<script>
function updateDateTime() {
    const now = new Date(); // Obtener la fecha y hora actuales
    const options = { 
        weekday: 'long', // Día de la semana (ej: "lunes")
        year: 'numeric', // Año (ej: "2024")
        month: 'long',   // Mes (ej: "marzo")
        day: 'numeric',  // Día del mes (ej: "22")
        hour: '2-digit', // Hora (ej: "03")
        minute: '2-digit', // Minutos (ej: "45")
        second: '2-digit'  // Segundos (ej: "30")
    };

    // Formatear la fecha y hora según el idioma y las opciones
    const formattedDateTime = now.toLocaleDateString('es-ES', options);

    // Actualizar el contenido del elemento con id "current-date-time"
    document.getElementById('current-date-time').textContent = formattedDateTime;
}

// Actualizar la fecha y hora cada segundo
setInterval(updateDateTime, 1000);

// Mostrar la fecha y hora al cargar la página
updateDateTime();
</script>