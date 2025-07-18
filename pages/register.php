<?php
require_once '../config/database.php';

$error = '';
$successMsg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $password = $_POST["password"];
  $password2 = $_POST["password2"];

  if (empty($name) || empty($email) || empty($password) || empty($password2)) {
    $error = "Preencha todos os campos.";

  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Email inválido.";

  } elseif ($password !== $password2) {
    $error = "As senhas não são iguais.";

  } elseif (strlen($password) < 6) {
    $error = "A senha deve ter pelo menos 6 caracteres.";

  } else {
    $stmt = $pdo->prepare(
      "SELECT id FROM users WHERE email = ?"
    );

    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
      $error = "Email já cadastrado.";

    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
      );

      if ($stmt->execute([$name, $email, $hash])) {
        $successMsg = "Usuário cadastrado com sucesso!";

      } else {
        $error = "Erro ao cadastrar usuário.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Cadastro</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
  <h2>Cadastro de Usuário</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
  <?php elseif ($successMsg): ?>
    <p class="success"><?php echo $successMsg; ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nome:</label>
    <input type="text" name="name" required>
    <br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br>

    <label>Senha:</label>
    <input type="password" name="password" required>
    <br>

    <label>Confirme a Senha:</label>
    <input type="password" name="password2" required>
    <br><br>

    <button type="submit">Cadastrar</button>
  </form>

  <p><a href="login.php">Já possui uma conta? Faça login</a></p>
</body>

</html>