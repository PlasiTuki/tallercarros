<?php

include 'config\basedatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $cliente_id = $_POST['cliente_id'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $placa = $_POST['placa'];

            $sql = "CALL registrar_vehiculo(?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $cliente_id, $marca, $modelo, $anio, $placa);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $cliente_id = $_POST['cliente_id'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $placa = $_POST['placa'];

            $sql = "UPDATE vehiculos SET cliente_id = ?, marca = ?, modelo = ?, anio = ?, placa = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssi", $cliente_id, $marca, $modelo, $anio, $placa, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            $sql = "DELETE FROM vehiculos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT v.*, c.nombre, c.apellido FROM vehiculos v JOIN clientes c ON v.cliente_id = c.id");
$clientes = $conn->query("SELECT id, nombre, apellido FROM clientes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos</title>
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
        <h1>Gestión de Vehículos</h1>
        <form id="vehiculoForm" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="vehiculoId">
            <select name="cliente_id" id="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php while($cliente = $clientes->fetch_assoc()): ?>
                    <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre'] . ' ' . $cliente['apellido']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="marca" id="marca" placeholder="Marca" required>
            <input type="text" name="modelo" id="modelo" placeholder="Modelo" required>
            <input type="number" name="anio" id="anio" placeholder="Año" required>
            <input type="text" name="placa" id="placa" placeholder="Placa" required>
            <input type="submit" value="Agregar Vehículo" id="submitBtn">
        </form>

        <h2>Lista de Vehículos</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Placa</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                <td><?php echo $row['marca']; ?></td>
                <td><?php echo $row['modelo']; ?></td>
                <td><?php echo $row['anio']; ?></td>
                <td><?php echo $row['placa']; ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editVehiculo(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                    <button class="btn btn-delete" onclick="deleteVehiculo(<?php echo $row['id']; ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editVehiculo(vehiculo) {
            document.getElementById('vehiculoForm').action.value = 'edit';
            document.getElementById('vehiculoId').value = vehiculo.id;
            document.getElementById('cliente_id').value = vehiculo.cliente_id;
            document.getElementById('marca').value = vehiculo.marca;
            document.getElementById('modelo').value = vehiculo.modelo;
            document.getElementById('anio').value = vehiculo.anio;
            document.getElementById('placa').value = vehiculo.placa;
            document.getElementById('submitBtn').value = 'Actualizar Vehículo';
        }

        function deleteVehiculo(id) {
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

        document.getElementById('vehiculoForm').addEventListener('submit', function(e) {
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