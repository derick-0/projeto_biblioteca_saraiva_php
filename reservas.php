<?php
require 'db.php';
include 'templates/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if ($_POST['action']==='reserve') {
        $pdo->prepare('INSERT INTO reservas (livro_id,usuario_id,data_reserva,status) VALUES (?,?,?,?)')
            ->execute([$_POST['livro_id'],$_POST['usuario_id'],date('Y-m-d'),'ativa']);
    } elseif ($_POST['action']==='cancel') {
        $pdo->prepare('UPDATE reservas SET status=? WHERE id=?')->execute(['cancelada',$_POST['id']]);
    }
    header('Location: reservas.php'); exit;
}
$reservas = $pdo->query('SELECT r.*, l.titulo, u.nome FROM reservas r JOIN livros l ON r.livro_id=l.id JOIN usuarios u ON r.usuario_id=u.id ORDER BY r.data_reserva DESC')->fetchAll();
$livros = $pdo->query('SELECT id,titulo FROM livros')->fetchAll();
$usuarios = $pdo->query('SELECT id,nome FROM usuarios')->fetchAll();
?>
<h2>Reservas</h2>
<div class="card p-3 mb-3">
  <form method="post" class="row g-2">
    <input type="hidden" name="action" value="reserve">
    <div class="col-md-5"><select name="livro_id" class="form-select"><?php foreach($livros as $l): ?><option value="<?=$l['id']?>"><?=htmlspecialchars($l['titulo'])?></option><?php endforeach; ?></select></div>
    <div class="col-md-5"><select name="usuario_id" class="form-select"><?php foreach($usuarios as $u): ?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['nome'])?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Reservar</button></div>
  </form>
</div>
<table class="table">
  <thead><tr><th>Livro</th><th>Usuário</th><th>Data</th><th>Status</th><th>Ações</th></tr></thead>
  <tbody>
    <?php foreach($reservas as $r): ?>
      <tr>
        <td><?=htmlspecialchars($r['titulo'])?></td>
        <td><?=htmlspecialchars($r['nome'])?></td>
        <td><?=$r['data_reserva']?></td>
        <td><?=htmlspecialchars($r['status'])?></td>
        <td>
          <?php if($r['status']=='ativa'): ?>
            <form method="post" class="d-inline">
              <input type="hidden" name="action" value="cancel">
              <input type="hidden" name="id" value="<?=$r['id']?>">
              <button class="btn btn-sm btn-danger">Cancelar</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'templates/footer.php'; ?>
