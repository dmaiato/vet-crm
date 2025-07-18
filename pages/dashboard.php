<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$stmt = $pdo->prepare(
  "SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date, appointment_time"
);
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
  <h2>Menu do cliente: <?php echo htmlspecialchars($user_name); ?></h2>
  <p class="option-divider"><a href="new_appointment.php">Nova Consulta</a> | <a href="logout.php">Logout</a></p>

  <h3>Minhas Consultas</h3>

  <?php if (count($appointments) === 0): ?>
    <p>Você não possui consultas agendadas.</p>
  <?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
      <tr>
        <th>Data</th>
        <th>Hora</th>
        <th>Idade do Animal</th>
        <th>Motivo</th>
        <th>Menu</th>
      </tr>
      <?php foreach ($appointments as $appointment): ?>
        <tr>
          <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
          <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
          <td><?php echo htmlspecialchars($appointment['animal_age']); ?> anos</td>
          <td class="reason-container"><?php echo nl2br(htmlspecialchars($appointment['reason'])); ?></td>
          <td>
            <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>">Editar</a> |
            <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>"
              onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>

</html>