<?php

include 'config\basedatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $vehiculo_id = $_POST['vehiculo_id'];
            $descripcion = $_POST['descripcion'];

            $sql = "CALL iniciar_reparacion(?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $vehiculo_id, $descripcion);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $vehiculo_id = $_POST['vehiculo_id'];
            $fecha_ingreso = $_POST['fecha_ingreso'];
            $fecha_salida = $_POST['fecha_salida'];
            $estado = $_POST['estado'];
            $descripcion = $_POST['descripcion'];

            $sql = "UPDATE reparaciones SET vehiculo_id = ?, fecha_ingreso = ?, fecha_salida = ?, estado = ?, descripcion = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssi", $vehiculo_id, $fecha_ingreso, $fecha_salida, $estado, $descripcion, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            $sql = "DELETE FROM reparaciones WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT r.*, v.marca, v.modelo, v.placa FROM reparaciones r JOIN vehiculos v ON r.vehiculo_id = v.id");
$vehiculos = $conn->query("SELECT id, marca, modelo, placa FROM vehiculos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reparaciones</title>
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
        <h1>Gestión de Reparaciones</h1>
        <form id="reparacionForm" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="reparacionId">
            <select name="vehiculo_id" id="vehiculo_id" required>
                <option value="">Seleccione un vehículo</option>
                <?php while($vehiculo = $vehiculos->fetch_assoc()): ?>
                    <option value="<?php echo $vehiculo['id']; ?>"><?php echo $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ' (' . $vehiculo['placa'] . ')'; ?></option>
                <?php endwhile; ?>
            </select>
            <textarea name="descripcion" id="descripcion" placeholder="Descripción de la reparación" required></textarea>
            <label for="fechas">Fecha ingreso</label>
            <input type="date" name="fecha_ingreso" id="fecha_ingreso" placeholder="Fecha de ingreso"><br><br>
            <label for="fechas">Fecha salida</label>
            <input type="date" name="fecha_salida" id="fecha_salida" placeholder="Fecha de salida"><br><br>
            <select name="estado" id="estado">
                <option value="En progreso">En progreso</option>
                <option value="Completado">Completado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
            <input type="submit" value="Iniciar Reparación" id="submitBtn">
        </form>

        <h2>Lista de Reparaciones</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Vehículo</th>
                <th>Fecha de Ingreso</th>
                <th>Fecha de Salida</th>
                <th>Estado</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['marca'] . ' ' . $row['modelo'] . ' (' . $row['placa'] . ')'; ?></td>
                <td><?php echo $row['fecha_ingreso']; ?></td>
                <td><?php echo $row['fecha_salida']; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td><?php echo $row['descripcion']; ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editReparacion(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                    <button class="btn btn-delete" onclick="deleteReparacion(<?php echo $row['id']; ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editReparacion(reparacion) {
            document.getElementById('reparacionForm').action.value = 'edit';
            document.getElementById('reparacionId').value = reparacion.id;
            document.getElementById('vehiculo_id').value = reparacion.vehiculo_id;
            document.getElementById('descripcion').value = reparacion.descripcion;
            document.getElementById('fecha_ingreso').value = reparacion.fecha_ingreso;
            document.getElementById('fecha_salida').value = reparacion.fecha_salida;
            document.getElementById('estado').value = reparacion.estado;
            document.getElementById('submitBtn').value = 'Actualizar Reparación';
        }

        function deleteReparacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        document.getElementById('reparacionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Quieres guardar estos cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>