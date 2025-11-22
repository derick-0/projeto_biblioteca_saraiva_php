<?php
session_start();
require 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $stmt = $pdo->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ?');
        $stmt->execute([$_POST['email']]);
        $u = $stmt->fetch();
        if ($u && password_verify($_POST['senha'], $u['senha_hash'])) {
            $_SESSION['user'] = ['id'=>$u['id'],'nome'=>$u['nome'],'email'=>$u['email']];
            header('Location: dashboard.php'); exit;
        } else $message = 'Credenciais inválidas.';
    } else if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome,email,senha_hash,role) VALUES (?,?,?,?)');
        $stmt->execute([$nome,$email,$senha_hash,'user']);
        $message = 'Registrado com sucesso. Faça login.';
    }
}
include 'templates/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <?php if($message): ?><div class="alert alert-info"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <div class="card mb-3 p-3">
      <h5>Entrar</h5>
      <form method="post">
        <input type="hidden" name="action" value="login">
        <div class="mb-2"><input required class="form-control" name="email" placeholder="Email"></div>
        <div class="mb-2"><input required class="form-control" type="password" name="senha" placeholder="Senha"></div>
        <button class="btn btn-primary">Entrar</button>
      </form>
    </div>
    <div class="card p-3">
      <h5>Registrar</h5>
      <form method="post">
        <input type="hidden" name="action" value="register">
        <div class="mb-2"><input required class="form-control" name="nome" placeholder="Nome"></div>
        <div class="mb-2"><input required class="form-control" name="email" placeholder="Email"></div>
        <div class="mb-2"><input required class="form-control" type="password" name="senha" placeholder="Senha"></div>
        <button class="btn btn-outline-primary">Registrar</button>
      </form>
    </div>
  </div>
</div>
<?php include 'templates/footer.php'; ?>
