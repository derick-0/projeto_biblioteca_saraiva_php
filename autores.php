<?php
require 'db.php';
include 'templates/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if ($_POST['action']==='create') {
        $pdo->prepare('INSERT INTO autores (nome,biografia) VALUES (?,?)')->execute([$_POST['nome'],$_POST['biografia']]);
    } elseif ($_POST['action']==='update') {
        $pdo->prepare('UPDATE autores SET nome=?,biografia=? WHERE id=?')->execute([$_POST['nome'],$_POST['biografia'],$_POST['id']]);
    } elseif ($_POST['action']==='delete') {
        $pdo->prepare('DELETE FROM autores WHERE id=?')->execute([$_POST['id']]);
    }
    header('Location: autores.php'); exit;
}
$autores = $pdo->query('SELECT * FROM autores ORDER BY nome')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Autores</h2>
  <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#add">+ Novo</button>
</div>
<div class="collapse mb-3" id="add">
  <div class="card p-3">
    <form method="post">
      <input type="hidden" name="action" value="create">
      <input required class="form-control mb-2" name="nome" placeholder="Nome">
      <textarea class="form-control mb-2" name="biografia" placeholder="Biografia"></textarea>
      <button class="btn btn-success">Salvar</button>
    </form>
  </div>
</div>
<table class="table">
  <thead><tr><th>Nome</th><th>Bio</th><th>Ações</th></tr></thead>
  <tbody>
    <?php foreach($autores as $a): ?>
      <tr>
        <td><?=htmlspecialchars($a['nome'])?></td>
        <td><?=htmlspecialchars(substr($a['biografia'],0,120))?></td>
        <td>
          <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit<?=$a['id']?>">Editar</button>
          <form method="post" class="d-inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?=$a['id']?>">
            <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir?')">Excluir</button>
          </form>
        </td>
      </tr>

      <div class="modal fade" id="edit<?=$a['id']?>" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
          <form method="post">
            <div class="modal-header"><h5>Editar</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?=$a['id']?>">
              <input class="form-control mb-2" name="nome" value="<?=htmlspecialchars($a['nome'])?>">
              <textarea class="form-control" name="biografia"><?=htmlspecialchars($a['biografia'])?></textarea>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Salvar</button></div>
          </form>
        </div></div>
      </div>

    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'templates/footer.php'; ?>
