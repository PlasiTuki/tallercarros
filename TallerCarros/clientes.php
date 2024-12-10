<?php
include 'config\basedatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];

            $sql = "INSERT INTO clientes (nombre, apellido, telefono, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $apellido, $telefono, $email);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];

            $sql = "UPDATE clientes SET nombre = ?, apellido = ?, telefono = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $apellido, $telefono, $email, $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            $sql = "DELETE FROM clientes WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT * FROM clientes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
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
        <h1>Gestión de Clientes</h1>
        <form id="clientForm" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="clientId">
            <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" id="apellido" placeholder="Apellido" required>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono">
            <input type="email" name="email" id="email" placeholder="Email">
            <input type="submit" value="Agregar Cliente" id="submitBtn">
        </form>

        <h2>Lista de Clientes</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['apellido']; ?></td>
                <td><?php echo $row['telefono']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editClient(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</button>
                    <button class="btn btn-delete" onclick="deleteClient(<?php echo $row['id']; ?>)">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editClient(client) {
            document.getElementById('clientForm').action.value = 'edit';
            document.getElementById('clientId').value = client.id;
            document.getElementById('nombre').value = client.nombre;
            document.getElementById('apellido').value = client.apellido;
            document.getElementById('telefono').value = client.telefono;
            document.getElementById('email').value = client.email;
            document.getElementById('submitBtn').value = 'Actualizar Cliente';
        }

        function deleteClient(id) {
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

        document.getElementById('clientForm').addEventListener('submit', function(e) {
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