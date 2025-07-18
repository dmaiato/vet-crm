<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$errorMsg = '';
$successMsg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $animal_age = intval($_POST['age']);
  $date = $_POST['date'];
  $time = $_POST['time'];
  $reason = trim($_POST['reason']);
  $user_id = $_SESSION['user_id'];

  if (empty($date) || empty($time) || empty($animal_age) || empty($reason)) {
    $errorMsg = "Todos os campos são obrigatórios.";

  } elseif (strlen($reason) < 10) {
    $errorMsg = "O motivo deve ter pelo menos 10 caracteres.";

  } else {
    $dateTime = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
    $now = new DateTime();

    if (!$dateTime) {
      $errorMsg = "Data ou hora inválida.";

    } elseif ($dateTime <= $now) {
      $errorMsg = "A data/hora deve ser futura.";

    } elseif (in_array($dateTime->format('N'), [6, 7])) {
      $errorMsg = "Não é permitido agendar em fins de semana.";

    } elseif ($time < "08:00" || $time > "18:00") {
      $errorMsg = "Horário deve ser entre 08:00 e 18:00.";

    } else {
      $stmt = $pdo->prepare(
        "INSERT INTO appointments (user_id, animal_age, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)"
      );
      if ($stmt->execute([$user_id, $animal_age, $date, $time, $reason])) {
        $successMsg = "Consulta agendada com sucesso!";
        // Limpar os campos após o sucesso
        $date = '';
        $time = '';
        $animal_age = '';
        $reason = '';
      } else {
        $errorMsg = "Erro ao agendar consulta.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Nova Consulta</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
  <h2>Nova Consulta</h2>
  <p><a href="dashboard.php">Retornar ao Dashboard</a></p>

  <?php if ($errorMsg): ?>
    <p class="error"><?php echo $errorMsg; ?></p>
  <?php elseif ($successMsg): ?>
    <p class="success"><?php echo $successMsg; ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Data da Consulta:</label>
    <input type="date" name="date" required>
    <br>

    <label>Hora da Consulta:</label>
    <input type="time" name="time" required>
    <br>

    <label>Idade do Animal (anos):</label>
    <input type="number" name="age" min="0" required>
    <br>

    <label>Motivo:</label>
    <textarea name="reason" rows="4" cols="40" required></textarea>
    <br><br>

    <button type="submit">Agendar</button>
  </form>
</body>

</html>