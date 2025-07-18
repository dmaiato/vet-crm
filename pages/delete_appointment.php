<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
  die('ID da consulta não especificado.');
}

$id = (int) $_GET['id'];

$user_id = $_SESSION['user_id'];

try {
  $stmt = $pdo->prepare(
    'SELECT id FROM appointments WHERE id = ? AND user_id = ?'
  );
  $stmt->execute([$id, $user_id]);
  $appointment = $stmt->fetch();

  if (!$appointment) {
    die('Consulta não encontrada ou você não tem permissão para excluir.');
  }

  $stmt = $pdo->prepare(
    'DELETE FROM appointments WHERE id = ?'
  );
  $stmt->execute([$id]);

  header('Location: dashboard.php');
  exit;

} catch (PDOException $e) {
  die("Erro ao excluir a consulta: " . $e->getMessage());
}
