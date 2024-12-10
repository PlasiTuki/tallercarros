<?php

include 'config\basedatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio = $_POST['precio'];
            $stock = $_POST['stock'];

            $sql = "INSERT INTO repuestos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio = $_POST['precio'];
            $stock = $_POST['stock'];

            $sql = "UPDATE repuestos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            $sql = "DELETE FROM repuestos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT * FROM repuestos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Repuestos</title>
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
        <h1>Gestión de Repuestos</h1>
        <form id="repuestoForm" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="repuestoId">
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del repuesto" required>
            <textarea name="descripcion" id="descripcion" placeholder="Descripción"></textarea>
            <input type="number" name="precio" id="precio" placeholder="Precio" step="0.01" required>
            <input type="number" name="stock" id="stock" placeholder="Stock" required>
            <input type="submit" value="Agregar Repuesto" id="submitBtn">
        </form>

        <h2>Lista de Repuestos</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['descripcion']; ?></td>
                <td><?php echo $row['precio']; ?></td>
                <td><?php echo $row['stock']; ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editRepuesto(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                    <button class="btn btn-delete" onclick="deleteRepuesto(<?php echo $row['id']; ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editRepuesto(repuesto) {
            document.getElementById('repuestoForm').action.value = 'edit';
            document.getElementById('repuestoId').value = repuesto.id;
            document.getElementById('nombre').value = repuesto.nombre;
            document.getElementById('descripcion').value = repuesto.descripcion;
            document.getElementById('precio').value = repuesto.precio;
            document.getElementById('stock').value = repuesto.stock;
            document.getElementById('submitBtn').value = 'Actualizar Repuesto';
        }

        function deleteRepuesto(id) {
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

        document.getElementById('repuestoForm').addEventListener('submit', function(e) {
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