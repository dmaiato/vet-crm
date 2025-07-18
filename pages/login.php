<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  if (empty($email) || empty($password)) {
    $error = "Preencha todos os campos.";

  } else {
    $stmt = $pdo->prepare(
      "SELECT * FROM users WHERE email = ?"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      header("Location: dashboard.php");
      exit;

    } else {
      $error = "Email ou senha inválidos.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Email:</label>
    <input type="email" name="email" required>
    <br>

    <label>Senha:</label>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Entrar</button>
  </form>

  <p><a href="register.php">Não possui uma conta? Cadastre-se</a></p>
</body>

</html>