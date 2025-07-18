<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$errorMsg = '';
$successMsg = '';

if (!isset($_GET['id'])) {
  header("Location: dashboard.php");
  exit;
}

$appointment_id = intval($_GET['id']);

$stmt = $pdo->prepare(
  "SELECT * FROM appointments WHERE id = ? AND user_id = ?"
);

$stmt->execute([$appointment_id, $user_id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
  die("Consulta não encontrada ou acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $date = $_POST['date'];
  $time = $_POST['time'];
  $animal_age = intval($_POST['age']);
  $reason = trim($_POST['reason']);

  if (empty($date) || empty($time) || empty($animal_age) || empty($reason)) {
    $errorMsg = "Todos os campos são obrigatórios.";

  } elseif (strlen($reason) < 10) {
    $errorMsg = "Motivo deve ter pelo menos 10 caracteres.";

  } else {
    $dataHora = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
    $now = new DateTime();

    if (!$dataHora) {
      $errorMsg = "Data ou hora inválida.";

    } elseif ($dataHora <= $now) {
      $errorMsg = "A data/hora deve ser futura.";

    } elseif (in_array($dataHora->format('N'), [6, 7])) {
      $errorMsg = "Não é permitido agendar em fins de semana.";

    } elseif ($time < "08:00" || $time > "18:00") {
      $errorMsg = "Horário deve ser entre 08:00 e 18:00.";

    } else {
      $stmt = $pdo->prepare(
        "UPDATE appointments SET animal_age = ?, appointment_date = ?, appointment_time = ?, reason = ? WHERE id = ? AND user_id = ?"
      );

      if ($stmt->execute([$animal_age, $date, $time, $reason, $appointment_id, $user_id])) {
        $successMsg = "Consulta atualizada com sucesso!";
        $appointment['animal_age'] = $animal_age;
        $appointment['appointment_date'] = $date;
        $appointment['appointment_time'] = $time;
        $appointment['reason'] = $reason;
      } else {
        $errorMsg = "Erro ao atualizar consulta.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Editar Consulta</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
  <h2>Editar Consulta</h2>
  <p><a href="dashboard.php">Retornar ao Dashboard</a></p>

  <?php if ($errorMsg): ?>
    <p class="error"><?php echo $errorMsg; ?></p>
  <?php elseif ($successMsg): ?>
    <p class="success"><?php echo $successMsg; ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Data da Consulta:</label>
    <input type="date" name="date" value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" required>
    <br>

    <label>Hora da Consulta:</label>
    <input type="time" name="time" value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>" required>
    <br>

    <label>Idade do Animal (anos):</label>
    <input type="number" name="age" value="<?php echo htmlspecialchars($appointment['animal_age']); ?>" min="0"
      required>
    <br>

    <label>Motivo:</label>
    <textarea name="reason" rows="4" cols="40"
      required><?php echo htmlspecialchars($appointment['reason']); ?></textarea>
    <br><br>

    <button type="submit">Salvar Alterações</button>
  </form>
</body>

</html>