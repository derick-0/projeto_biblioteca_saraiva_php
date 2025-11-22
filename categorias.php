<?php
require 'db.php';
include 'templates/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if ($_POST['action']==='create') $pdo->prepare('INSERT INTO categorias (nome) VALUES (?)')->execute([$_POST['nome']]);
    if ($_POST['action']==='delete') $pdo->prepare('DELETE FROM categorias WHERE id=?')->execute([$_POST['id']]);
    header('Location: categorias.php'); exit;
}
$cats = $pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
?>
<h2>Categorias</h2>
<form method="post" class="mb-3 d-flex">
  <input type="hidden" name="action" value="create">
  <input class="form-control me-2" name="nome" placeholder="Nova categoria">
  <button class="btn btn-primary">Adicionar</button>
</form>
<ul class="list-group">
  <?php foreach($cats as $c): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <?=htmlspecialchars($c['nome'])?>
      <form method="post" class="mb-0">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?=$c['id']?>">
        <button class="btn btn-sm btn-danger">Excluir</button>
      </form>
    </li>
  <?php endforeach; ?>
</ul>
<?php include 'templates/footer.php'; ?>
