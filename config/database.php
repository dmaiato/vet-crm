<?php
$host = getenv('DB_HOST'); // 'mysql' (docker service name)
$db = getenv('DB_NAME'); // 'vet_system'
$user = getenv('DB_USER'); // 'root'
$pass = getenv('DB_PASSWORD'); // 'root'

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>