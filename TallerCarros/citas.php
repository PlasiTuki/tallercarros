<?php

include 'config\basedatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $cliente_id = $_POST['cliente_id'];
            $vehiculo_id = $_POST['vehiculo_id'];
            $fecha = $_POST['fecha'];
            $motivo = $_POST['motivo'];

            $sql = "CALL programar_servicio(?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $cliente_id, $vehiculo_id, $fecha, $motivo);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $cliente_id = $_POST['cliente_id'];
            $vehiculo_id = $_POST['vehiculo_id'];
            $fecha = $_POST['fecha'];
            $motivo = $_POST['motivo'];
            $estado = $_POST['estado'];

            $sql = "UPDATE citas SET cliente_id = ?, vehiculo_id = ?, fecha = ?, motivo = ?, estado = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisssi", $cliente_id, $vehiculo_id, $fecha, $motivo, $estado, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            $sql = "DELETE FROM citas WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT c.*, cl.nombre, cl.apellido, v.marca, v.modelo, v.placa FROM citas c JOIN clientes cl ON c.cliente_id = cl.id JOIN vehiculos v ON c.vehiculo_id = v.id");
$clientes = $conn->query("SELECT id, nombre, apellido FROM clientes");
$vehiculos = $conn->query("SELECT id, marca, modelo, placa FROM vehiculos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas</title>
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
        <h1>Gestión de Citas</h1>
        <form id="citaForm" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="citaId">
            <select name="cliente_id" id="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php while($cliente = $clientes->fetch_assoc()): ?>
                    <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre'] . ' ' . $cliente['apellido']; ?></option>
                <?php endwhile; ?>
            </select>
            <select name="vehiculo_id" id="vehiculo_id" required>
                <option value="">Seleccione un vehículo</option>
                <?php while($vehiculo = $vehiculos->fetch_assoc()): ?>
                    <option value="<?php echo $vehiculo['id']; ?>"><?php echo $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ' (' . $vehiculo['placa'] . ')'; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="datetime-local" name="fecha" id="fecha" required>
            <textarea name="motivo" id="motivo" placeholder="Motivo de la cita" required></textarea>
            <select name="estado" id="estado">
                <option value="Programada">Programada</option>
                <option value="Completada">Completada</option>
                <option value="Cancelada">Cancelada</option>
            </select>
            <input type="submit" value="Programar Cita" id="submitBtn">
        </form>

        <h2>Lista de Citas</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                <td><?php echo $row['marca'] . ' ' . $row['modelo'] . ' (' . $row['placa'] . ')'; ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td><?php echo $row['motivo']; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editCita(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                    <button class="btn btn-delete" onclick="deleteCita(<?php echo $row['id']; ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editCita(cita) {
            document.getElementById('citaForm').action.value = 'edit';
            document.getElementById('citaId').value = cita.id;
            document.getElementById('cliente_id').value = cita.cliente_id;
            document.getElementById('vehiculo_id').value = cita.vehiculo_id;
            document.getElementById('fecha').value = cita.fecha.replace(' ', 'T');
            document.getElementById('motivo').value = cita.motivo;
            document.getElementById('estado').value = cita.estado;
            document.getElementById('submitBtn').value = 'Actualizar Cita';
        }

        function deleteCita(id) {
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

        document.getElementById('citaForm').addEventListener('submit', function(e) {
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