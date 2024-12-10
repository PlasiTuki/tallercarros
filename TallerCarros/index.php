<?php

require_once 'config\basedatos.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Taller Mecánico</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css\styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php">Inicio</a>
            <a href="clientes.php">Clientes</a>
            <a href="vehiculos.php">Vehículos</a>
            <a href="servicios.php">Servicios</a>
            <a href="repuestos.php">Repuestos</a>
            <a href="reparaciones.php">Reparaciones</a>
            <a href="citas.php">Citas</a>
        </div>
    </nav>
    <div class="container">
        <h1>Sistema de Gestión de Taller Mecánico</h1>
        <p>Bienvenido al sistema de gestión. Utilice la navegación para acceder a las diferentes secciones.</p>
    </div>
<?php


$historial_query = "
    SELECT id, fecha, nombre, precio, descripcion
    FROM historial_servicios
";
$historial_result = $conn->query($historial_query);

if ($historial_result->num_rows > 0) {
    echo "<h2>Historial de Servicios</h2>";
    echo "<table border='1'><tr><th>Nombre del Servicio</th><th>Precio</th><th>Descripción</th><th>Fecha</th></tr>";
    while($row = $historial_result->fetch_assoc()) {
        echo "<tr><td>" . $row["nombre"]. "</td><td>" . $row["precio"]. "</td><td>" . $row["descripcion"]. "</td><td>" . $row["fecha"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 resultados en historial de servicios";
}


$alertas_query = "
    SELECT a.vehiculo_id, a.fecha_alerta AS fecha_salida, v.marca, v.modelo 
    FROM alertas_mantenimiento a
    JOIN vehiculos v ON a.vehiculo_id = v.id
";
$alertas_result = $conn->query($alertas_query);

if ($alertas_result->num_rows > 0) {
    echo "<h2>Mantenimientos pendientes</h2>";
    echo "<table border='1'><tr><th>Vehículo</th><th>Fecha de Salida</th></tr>";
    while($row = $alertas_result->fetch_assoc()) {
        echo "<tr><td>" . $row["marca"] . " " . $row["modelo"] . "</td><td>" . $row["fecha_salida"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 resultados en alertas de mantenimiento";
}
?>


</body>
</html>