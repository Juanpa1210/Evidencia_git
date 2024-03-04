<?php
require '../../includes/database.php';
$db = new Database();
$con = $db->conectar();

// Procesar eliminación
if(isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
    $eliminar_id = $_GET['eliminar'];
    $eliminar = $con->prepare("DELETE FROM tr_material WHERE mate_codg = ?");
    $eliminar->execute([$eliminar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar edición
if(isset($_POST['editar'])) {
    $editar_id = $_POST['mate_codg'];
    $editar_nom = $_POST['mate_nom'];
    $editar = $con->prepare("UPDATE tr_material SET mate_nom = ? WHERE mate_codg = ?");
    $editar->execute([$editar_nom, $editar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar agregar
if(isset($_POST['agregar'])) {
    $nuevo_codg = $_POST['nuevo_codg'];
    $nuevo_nom = $_POST['nuevo_nom'];
    $agregar = $con->prepare("INSERT INTO tr_material (mate_codg, mate_nom) VALUES (?, ?)");
    $agregar->execute([$nuevo_codg, $nuevo_nom]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Materiales</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div id="nav">
        <?php
            include '../../footer_nav/nav.html'
        ?>
    </div>

    <div class="container">
        <h2>Lista de Materiales.</h2><br>

        <!-- Formulario para agregar -->
        <h3>Agregar Nuevo Material</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="mb-3">
                <input type="number" class="form-control" name="nuevo_codg" placeholder="Código (No obligatorio)">
            </div>
            <div class="mb-3">
                <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="nuevo_nom" placeholder="Nombre" required>
            </div>
            <button type="submit" class="btn btn-primary" name="agregar">Agregar</button>
        </form>
        
        <br>
        <h3>Editar Roles</h3>
    <?php
        $insert = $con->prepare ('SELECT * FROM tr_material');
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resul as $row) { $i++; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $row["mate_codg"]; ?></td>
                        <td><?php echo $row["mate_nom"]; ?></td>
                        <td>
                            <!-- Botón para eliminar -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_eliminar_<?php echo $row["mate_codg"]; ?>">Eliminar</button>
                            <!-- Botón para editar -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_editar_<?php echo $row["mate_codg"]; ?>">Editar</button>
                        </td>
                    </tr>
                    <!-- Modal para eliminar -->
                    <div class="modal fade" id="modal_eliminar_<?php echo $row["mate_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <a href="?eliminar=<?php echo $row["mate_codg"]; ?>" class="btn btn-danger">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal para editar -->
                    <div class="modal fade" id="modal_editar_<?php echo $row["mate_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar Rol</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                      <input type="hidden" name="mate_codg" value="<?php echo $row['mate_codg']; ?>">
                                      <div class="mb-3">
                                        <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="mate_nom" value="<?php echo $row["mate_nom"]; ?>" placeholder="Descripción">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js"></script>


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