<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Handle AJAX upload before any output
if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/helpers.php';

    $allowedMimes = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp',
    ];

    $result = uploadImageFile('file', $allowedMimes);

    header('Content-Type: text/plain');
    if (isset($result['imagen_url'])) {
        echo "success";
    } else {
        echo $result['error'] ?? 'Error al subir la imagen.';
    }
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>

<body>
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Subir Imagen</div>

                                <style>
                                    .upload-container {
                                        border: 2px dashed #ccc;
                                        border-radius: 10px;
                                        padding: 40px 20px;
                                        text-align: center;
                                        cursor: pointer;
                                        transition: background 0.3s;
                                    }
                                    .upload-container:hover {
                                        background: rgba(0, 0, 0, 0.05);
                                    }
                                </style>

                                <div id="upload-container" class="upload-container">
                                    <p>Arrastra y suelta tu imagen aquí o
                                        <span style="color: blue; cursor: pointer;"
                                              onclick="document.getElementById('fileInput').click();">
                                            haz clic para seleccionar
                                        </span>
                                    </p>
                                    <input type="file" id="fileInput" style="display:none"
                                           accept="image/png, image/jpeg, image/gif, image/webp">
                                </div>

                                <div id="notification" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
    <script defer src="/admin/assets/js/upload_image.js"></script>

</body>
</html>
