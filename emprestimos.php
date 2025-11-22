<?php
require 'db.php';
include 'templates/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['action'] === 'loan') {

    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO emprestimos (livro_id,usuario_id,data_saida,data_prevista,status) VALUES (?,?,?,?,?)')
      ->execute([$_POST['livro_id'], $_POST['usuario_id'], date('Y-m-d'), $_POST['data_prevista'], 'ativo']);
    $pdo->prepare('UPDATE livros SET quantidade=quantidade-1 WHERE id=?')->execute([$_POST['livro_id']]);
    $pdo->commit();
  } elseif ($_POST['action'] === 'return') {
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE emprestimos SET data_devolucao=?,status=? WHERE id=?')->execute([date('Y-m-d'), 'devolvido', $_POST['id']]);

    $id = $_POST['id'];
    $livro_id = $pdo->query('SELECT livro_id FROM emprestimos WHERE id=' . (int)$id)->fetchColumn();
    $pdo->prepare('UPDATE livros SET quantidade=quantidade+1 WHERE id=?')->execute([$livro_id]);
    $pdo->commit();
  }
  header('Location: emprestimos.php');
  exit;
}
$emprestimos = $pdo->query('SELECT e.*, l.titulo, u.nome as usuario FROM emprestimos e JOIN livros l ON e.livro_id=l.id JOIN usuarios u ON e.usuario_id=u.id ORDER BY e.data_saida DESC')->fetchAll();
$livros = $pdo->query('SELECT id,titulo,quantidade FROM livros WHERE quantidade>0')->fetchAll();
$usuarios = $pdo->query('SELECT id,nome FROM usuarios')->fetchAll();
?>
<div class="d-flex justify-content-between mb-3">
  <h2>Empréstimos</h2>
  <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#novo">Novo Empréstimo</button>
</div>
<div class="collapse mb-3" id="novo">
  <div class="card p-3">
    <form method="post">
      <input type="hidden" name="action" value="loan">
      <div class="mb-2"><select name="livro_id" class="form-select"><?php foreach ($livros as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?> (<?= $l['quantidade'] ?>)</option><?php endforeach; ?></select></div>
      <div class="mb-2"><select name="usuario_id" class="form-select"><?php foreach ($usuarios as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nome']) ?></option><?php endforeach; ?></select></div>
      <div class="mb-2"><label>Data prevista retorno</label><input type="date" name="data_prevista" class="form-control"></div>
      <button class="btn btn-success">Registrar</button>
    </form>
  </div>
</div>
<table class="table">
  <thead>
    <tr>
      <th>Livro</th>
      <th>Usuário</th>
      <th>Saída</th>
      <th>Previsto</th>
      <th>Status</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($emprestimos as $e): ?>
      <tr>
        <td><?= htmlspecialchars($e['titulo']) ?></td>
        <td><?= htmlspecialchars($e['usuario']) ?></td>
        <td><?= $e['data_saida'] ?></td>
        <td><?= $e['data_prevista'] ?></td>
        <td><?= htmlspecialchars($e['status']) ?></td>
        <td>
          <?php if ($e['status'] == 'ativo'): ?>
            <form method="post" class="d-inline">
              <input type="hidden" name="action" value="return">
              <input type="hidden" name="id" value="<?= $e['id'] ?>">
              <button class="btn btn-sm btn-success">Registrar Devolução</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'templates/footer.php'; ?>