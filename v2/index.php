<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conectamos a la base de datos
$conn = conectar_bd();

// Si es una petición POST para cambiar el estado "visto", procesarla y devolver JSON sin generar salida HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_visto') {
  $id = intval($_POST['id']);
  $currentVisto = $_POST['visto'] === 'true'; // estado actual (string "true" o "false")
  $newVisto = !$currentVisto; // invertimos el estado

  // Actualizar la base de datos
  $updateSql = "UPDATE atracciones SET visto = ? WHERE id = ?";
  $stmt = $conn->prepare($updateSql);
  $vistoInt = $newVisto ? 1 : 0; // convertir a entero
  $stmt->bind_param("ii", $vistoInt, $id);
  $stmt->execute();
  $stmt->close();

  // Devolver la respuesta JSON y finalizar la ejecución
  echo json_encode(['success' => true, 'visto' => $newVisto]);
  exit;
}

// Para peticiones GET se sigue con el resto de la página
include $_SERVER['DOCUMENT_ROOT'] . '/v2/includes/head.php';

// Recuperamos la fecha pasada en la URL o usamos la fecha actual si no se especifica
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Consulta para obtener las atracciones del día
$query = "SELECT * FROM atracciones WHERE fecha = '$fecha'";
$result = $conn->query($query);
?>

<!doctype html>
<html class="h-100" lang="en">

<head>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body data-bs-spy="scroll" data-bs-target="#navScroll">

  <?php include 'includes/header.php'; ?>

  <main>
    <div class="w-100 overflow-hidden position-relative" id="top">
      <div class="container py-vh-5">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-12 col-lg-10">
            <h1 class="display-huge mb-3">Thailandia '25</h1>
            <h5 class="lead pt-2 py-vh-2"><?php echo htmlspecialchars($fecha); ?></h5>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-11 col-lg-10 col-xl-6">
          <div class="row border-bottom">
            <div class="container py-5">
              <div class="row">
                <?php if($result && $result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>
                    <article class="col-12 border-top px-0 py-4 d-flex justify-content-between align-items-center">
                      <h2 class="h4 lh-1 mb-0">
                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                          <?php echo $row['nombre']; ?>
                        </a>
                      </h2>
                      <span 
                        class="check-icon material-icons <?php echo $row['visto'] ? 'checked' : ''; ?>" 
                        data-id="<?php echo $row['id']; ?>"
                        onclick="toggleVisto(<?php echo $row['id']; ?>, <?php echo $row['visto'] ? 'true' : 'false'; ?>)">
                        check_circle
                      </span>
                    </article>
                  <?php endwhile; ?>
                <?php else: ?>
                  <p>No se encontraron atracciones para la fecha <?php echo $fecha; ?>.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <!-- Función JavaScript para actualizar el estado "visto" mediante fetch y recargar la página -->
  <script>
    function toggleVisto(id, currentVisto) {
      fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=toggle_visto&id=' + id + '&visto=' + currentVisto
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Recargamos la página para reflejar el cambio
          window.location.reload();
        } else {
          console.error('Error al actualizar el estado.');
        }
      })
      .catch(error => console.error('Error:', error));
    }
  </script>

</body>

</html>
