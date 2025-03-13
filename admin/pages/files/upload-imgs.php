<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/upload_imagen.php';

?>

<!doctype html>
<html class="no-js" lang="es">

<head>
    <style>
        .upload-container {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .upload-container:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .hidden-input {
            display: none;
        }

    </style>
</head>

<body>
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Subir Imagen</div>

                                

                                <!-- Contenedor de arrastrar y soltar -->
                                <div id="upload-container" class="upload-container">
                                    <p>Arrastra y suelta tu imagen aquí o <span style="color: blue; cursor: pointer;" onclick="document.getElementById('fileInput').click();">haz clic para seleccionar</span></p>
                                    <input type="file" id="fileInput" class="hidden-input" accept="image/png, image/jpeg, image/gif, image/webp">
                                </div>
                            </div>
                        </div>
                        
                    </div>     
                                
                </div>
                <!-- Notificación dinámica -->
                <br><div id="notification" class="notification"></div>   
            </div>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
    <script defer src="/admin/assets/js/upload_image.js"></script>

</body>
</html>
