<?php
include("cabecera.php");
include("coneciondb.php");
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
}
?>
<?php
// Verifica si se ha pasado un ID de tarea en la URL
if (isset($_GET['id'])) {
    $tareas_id = $_GET['id'];

    // Buscar la tarea con el ID pasado
    $sql = "SELECT * FROM tareas WHERE tareas_id = :tareas_id AND usuarios_id = :usuarios_id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':tareas_id', $tareas_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuarios_id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();

    $task = $stmt->fetch();

    if (!$task) {
        die('Tarea no encontrada o no tienes permiso para editarla.');
    }
} else {
    die('ID de tarea no especificado.');
}

// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = $_POST['estado'];

    // Actualizar los datos de la tarea en la base de datos
    $sql = "UPDATE tareas 
            SET titulo = :titulo, descripcion = :descripcion, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado
            WHERE tareas_id = :tareas_id AND usuarios_id = :usuarios_id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':tareas_id', $tareas_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuarios_id', $_SESSION['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirigir después de la actualización
    } else {
        echo 'Error al actualizar la tarea.';
    }
}

include("./partials/cabecera.php");
?>

<section class="containerTask">
    <h3>Editar Tarea</h3>
    <div class="tareas">
        <form action="editar_tarea.php?id=<?php echo $tareas_id; ?>" method="POST" id="formTareas">
            <label for="titulo">Título</label>
            <input required type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($task['titulo']); ?>">

            <label for="descripcion">Descripcion</label>
            <input required type="text" name="descripcion" id="descripcion" value="<?php echo htmlspecialchars($task['descripcion']); ?>">

            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo $task['fecha_inicio']; ?>">

            <label for="fecha_fin">Fecha Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo $task['fecha_fin']; ?>">

            <select name="estado" id="estado">
                <option value="pendiente" <?php if ($task['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                <option value="en proceso" <?php if ($task['estado'] == 'en proceso') echo 'selected'; ?>>En proceso</option>
                <option value="finalizado" <?php if ($task['estado'] == 'finalizado') echo 'selected'; ?>>Finalizado</option>
            </select>

            <button>Actualizar</button>
        </form>
    </div>
</section>

<?php
include 'partials/footer.php';
?>