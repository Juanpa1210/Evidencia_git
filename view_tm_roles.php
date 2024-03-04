<?php
require 'includes/database.php';
$db = new Database();
$con = $db->conectar();

// Procesar eliminación
if(isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
    $eliminar_id = $_GET['eliminar'];
    $eliminar = $con->prepare("DELETE FROM tr_roles WHERE rol_codg = ?");
    $eliminar->execute([$eliminar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar edición
if(isset($_POST['editar'])) {
    $editar_id = $_POST['rol_codg'];
    $editar_nom = $_POST['rol_nom'];
    $editar_desc = $_POST['rol_desc'];
    $editar = $con->prepare("UPDATE tr_roles SET rol_nom = ?, rol_desc = ? WHERE rol_codg = ?");
    $editar->execute([$editar_nom, $editar_desc, $editar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar agregar
if(isset($_POST['agregar'])) {
    $nuevo_codg = $_POST['nuevo_codg'];
    $nuevo_nom = $_POST['nuevo_nom'];
    $nuevo_desc = $_POST['nuevo_desc'];
    $agregar = $con->prepare("INSERT INTO tr_roles (rol_codg, rol_nom, rol_desc) VALUES (?, ?, ?)");
    $agregar->execute([$nuevo_codg, $nuevo_nom, $nuevo_desc]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roles</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div id="nav">
<?php
include '../../footer_nav/nav.html'
?>
</div>
    <div class="container">
        <h2>Lista de Roles.</h2><br>

        <!-- Formulario para agregar -->
        <h3>Agregar Nuevo Rol</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="mb-3">
                <input type="number" class="form-control" name="nuevo_codg" placeholder="Código (No obligatorio)">
            </div>
            <div class="mb-3">
                <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="nuevo_nom" placeholder="Nombre" required>
            </div>
            <div class="mb-3">
                <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="nuevo_desc" placeholder="Descripción">
            </div>
            <button type="submit" class="btn btn-primary" name="agregar">Agregar</button>
        </form>
        
        <br>
        <h3>Editar Roles</h3>
    <?php
        $insert = $con->prepare ('SELECT * FROM tr_roles');
        $insert->execute();
        $resul = $insert->fetchAll(PDO::FETCH_ASSOC);
        $i=0;
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resul as $row) { $i++; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $row["rol_codg"]; ?></td>
                        <td><?php echo $row["rol_nom"]; ?></td>
                        <td><?php echo $row["rol_desc"]; ?></td>
                        <td>
                            <!-- Botón para eliminar -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_eliminar_<?php echo $row["rol_codg"]; ?>">Eliminar</button>
                            <!-- Botón para editar -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_editar_<?php echo $row["rol_codg"]; ?>">Editar</button>
                        </td>
                    </tr>
                    <!-- Modal para eliminar -->
                    <div class="modal fade" id="modal_eliminar_<?php echo $row["rol_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Eliminar Rol</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar este rol?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <a href="?eliminar=<?php echo $row["rol_codg"]; ?>" class="btn btn-danger">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal para editar -->
                    <div class="modal fade" id="modal_editar_<?php echo $row["rol_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar Rol</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="rol_codg" value="<?php echo $row["rol_codg"]; ?>">
                                        <div class="mb-3">
                                            <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="rol_nom" value="<?php echo $row["rol_nom"]; ?>" placeholder="Nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="rol_desc" value="<?php echo $row["rol_desc"]; ?>" placeholder="Descripción">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="editar">Editar</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script>
        function mayus(e) {
        e.value = e.value.toUpperCase();
        }

        function minus(e) {
        e.value = e.value.toLowerCase();
        }
    </script>
</body>

</html>