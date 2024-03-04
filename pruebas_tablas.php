<?php
require '../../includes/database.php';
$db = new Database();
$con = $db->conectar();

// Procesar eliminación
if(isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
    $eliminar_id = $_GET['eliminar'];
    $eliminar = $con->prepare("DELETE FROM tr_tip_mat WHERE tip_mate_codg = ?");
    $eliminar->execute([$eliminar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar edición
if(isset($_POST['editar'])) {
    $editar_id = $_POST['tip_mate_codg'];
    $editar_nom = $_POST['tip_mate_nom'];
    $editar_mate = $_POST['mate_codg'];
    $editar = $con->prepare("UPDATE tr_tip_mat SET tip_mate_nom = ?, mate_codg = ?,tip_mate_codg = ? WHERE tip_mate_codg = ?");
    $editar->execute([$editar_nom, $editar_mate, $editar_id, $editar_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Procesar agregar
if(isset($_POST['agregar'])) {
    $nuevo_codg = $_POST['nuevo_codg'];
    $nuevo_nom = $_POST['nuevo_nom'];
    $nuevo_mate = $_POST['mate_codg'];
    $agregar = $con->prepare("INSERT INTO tr_tip_mat (tip_mate_codg, tip_mate_nom, mate_codg) VALUES (?, ?, ?)");
    $agregar->execute([$nuevo_codg, $nuevo_nom, $nuevo_mate]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>prueba de tablas</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
</head>
<body>
<div id="nav">
    <?php
    include '../../footer_nav/nav.html'
    ?>
    </div>
    <div class="container">
        <h2>Lista de tipos de materiales.</h2><br>

        <!-- Formulario para agregar -->
        <h3>Agregar Nuevo tipo de material</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="mb-3">
                <input type="number" class="form-control" name="nuevo_codg" placeholder="Código (No obligatorio)">
            </div>
            <div class="mb-3">
                <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="nuevo_nom" placeholder="Nombre" required>
            </div>
            <div class="mb-3">
                <select class="form-control" name="mate_codg" id="mate_codg" type="submit" required>
                    <option value="" select="">**Seleccione Material**</option>
                    <?php
                    $statement = $con->prepare('SELECT * FROM tr_material');
                    $statement->execute();
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=" . $row['mate_codg'] . ">" . $row['mate_nom'] . "</option>";
                    }
                    ?>
                </select> 
            </div>
            <button type="submit" class="btn btn-primary" name="agregar">Agregar</button>
        </form>
        
        <br>
        <h3>Editar Roles</h3>
    <div class="p-5">
        <?php
            $insert = $con->prepare ('SELECT * FROM tr_tip_mat INNER JOIN tr_material ON tr_tip_mat.mate_codg = tr_material.mate_codg');
            $insert->execute();
            $resul = $insert->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Tipo</th>
                    <th>Material</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resul as $row) { ?>
                    <tr>
                        <td><?php echo $row["tip_mate_codg"]; ?></td>
                        <td><?php echo $row["tip_mate_nom"]; ?></td>
                        <td><?php echo $row["mate_nom"]; ?></td>
                        <td>
                            <!-- Botón para eliminar -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_eliminar_<?php echo $row["tip_mate_codg"]; ?>">Eliminar</button>
                            <!-- Botón para editar -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_editar_<?php echo $row["tip_mate_codg"]; ?>">Editar</button>
                        </td>
                    </tr>
                    <!-- Modal para eliminar -->
                    <div class="modal fade" id="modal_eliminar_<?php echo $row["tip_mate_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <a href="?eliminar=<?php echo $row["tip_mate_codg"]; ?>" class="btn btn-danger">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal para editar -->
                    <div class="modal fade" id="modal_editar_<?php echo $row["tip_mate_codg"]; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar Tipo de Material</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control" name="tip_mate_codg" value="<?php echo $row["tip_mate_codg"]; ?>" placeholder="codigo" required>
                                        </div>
                                        <div class="mb-3">
                                        <label for="tip_mate_nom" class="nav-link"><i class="fas fa-id-card"></i> &nbsp;<strong>Nombre del Tipo de Material</strong></label>
                                            <input type="text" onkeyup="mayus(this);"  pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().!#$%&’*+/=?^_`{|}~-].,\s ]{2,520}" class="form-control" name="tip_mate_nom" value="<?php echo $row["tip_mate_nom"]; ?>" placeholder="Nombre">
                                        </div>
                                        <div class="mb-3">
                                        <label for="tip_mate_codg" class="nav-link"><i class="fas fa-id-card"></i> &nbsp;<strong>Selecione el material</strong></label>
                                            <select class="form-control" name="mate_codg" id="mate_codg" required>
                                                <option value=""select=""><?php echo $row["mate_nom"]; ?></option>
                                                <?php
                                                $statement = $con->prepare('SELECT * FROM tr_material');
                                                $statement->execute();
                                                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value=" . $row['mate_codg'] . ">" . $row['mate_nom'] . "</option>";
                                                }
                                                ?>
                                            </select> 
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
            <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Age</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.js"></script>

    <script>
        new DataTable('#example');
    </script>

    

</body>

</html>