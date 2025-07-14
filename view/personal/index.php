<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
require_once("../../models/Usuario.php");

$usuario = new Usuario();
$usuarios = $usuario->get_colaborador_oficina_sistemas(); // Puedes paginar o filtrar si quieres

$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "personal");

if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
?>

    <!doctype html>
    <html lang="es">

    <head>
        <title>Personal | Gestión TI</title>
        <?php require_once("../html/head.php") ?>
        <!-- Incluye los CSS de Minia aquí si no están en head.php -->
    </head>

    <body>
        <div id="layout-wrapper">

            <?php require_once("../html/header.php") ?>
            <?php require_once("../html/menu.php") ?>

            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Personal OSIN</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Personal</a></li>
                                            <li class="breadcrumb-item active">OSIN</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5 class="card-title">Lista de Usuarios <span class="text-muted fw-normal ms-2">(8)</span></h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link" href="usuarios_lista.php" data-bs-toggle="tooltip" title="List">
                                                <i class="bx bx-list-ul"></i>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#" data-bs-toggle="tooltip" title="Grid">
                                                <i class="bx bx-grid-alt"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    <a href="#" class="btn btn-light"><i class="bx bx-plus me-1"></i> Nuevo</a>
                                </div>
                            </div>
                        </div>

                        <!-- USER CARDS GRID -->
                        <div class="row">
                            <?php foreach ($usuarios as $u) { ?>
                                <div class="col-xl-3 col-sm-6">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <div class="dropdown text-end">
                                                <a class="text-muted dropdown-toggle font-size-16" href="#" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#">Editar</a>
                                                    <a class="dropdown-item" href="#">Eliminar</a>
                                                </div>
                                            </div>
                                            <div class="mx-auto mb-4">
                                                <?php if (!empty($u["usu_img"])) { ?>
                                                    <img src="<?= htmlspecialchars($u["usu_img"]) ?>" alt="" class="avatar-xl rounded-circle img-thumbnail">
                                                <?php } else { ?>
                                                    <div class="avatar-xl mx-auto mb-4">
                                                        <div class="avatar-title bg-soft-light text-light display-4 m-0 rounded-circle">
                                                            <i class="bx bxs-user-circle"></i>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <h5 class="font-size-16 mb-1">
                                                <a href="#" class="text-dark"><?= htmlspecialchars($u["usu_nomape"]) ?></a>
                                            </h5>
                                            <p class="text-muted mb-1">
                                                <?= htmlspecialchars($u["usu_cargo"]) ?>
                                            </p>
                                            <p class="text-muted mb-2 small">Anexo: 
                                                <?= htmlspecialchars($u["usu_anexo"]) ?>
                                            </p>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-light"><i class="uil uil-user me-1"></i> <?= htmlspecialchars($u["usu_fecnac"]) ?></button>
                                            <button type="button" class="btn btn-outline-light"><i class="uil uil-envelope-alt me-1"></i> Mensaje</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- end row -->

                    </div> <!-- container-fluid -->
                </div> <!-- page-content -->

                <?php require_once("../html/footer.php") ?>
            </div> <!-- .main-content -->

        </div> <!-- #layout-wrapper -->

        <?php require_once("../html/sidebar.php") ?>
        <div class="rightbar-overlay"></div>


        <?php require_once("../html/js.php") ?>
        <script src="encargatura.js" type="text/javascript"></script>

    </body>

    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>