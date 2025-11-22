<?php
session_start();
require 'db.php';
include 'templates/header.php';
$user = $_SESSION['user'] ?? null;
?>
<h2>Dashboard</h2>
<?php if(!$user): ?>
  <div class="alert alert-warning">Você não está logado. <a href="login.php">Entrar</a></div>
<?php else: ?>
  <div class="card p-3 mb-3">
    <h5>Olá, <?=htmlspecialchars($user['nome'])?></h5>
    <p>Email: <?=htmlspecialchars($user['email'])?></p>
    <a href="logout.php" class="btn btn-sm btn-danger">Sair</a>
  </div>
  <div class="row">
    <div class="col-md-4"><div class="card p-3"><h6>Livros</h6><?php $c=$pdo->query('SELECT COUNT(*) FROM livros')->fetchColumn(); echo $c; ?></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>Empréstimos</h6><?php $c=$pdo->query('SELECT COUNT(*) FROM emprestimos')->fetchColumn(); echo $c; ?></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>Reservas</h6><?php $c=$pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn(); echo $c; ?></div></div>
  </div>
<?php endif; ?>
<?php include 'templates/footer.php'; ?>
