<?php
require 'db.php';
include 'templates/header.php';
$action = $_GET['action'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['action'] === 'create') {
    $stmt = $pdo->prepare('INSERT INTO livros (titulo,autor_id,categoria_id,isbn,ano,quantidade) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$_POST['titulo'], $_POST['autor_id'], $_POST['categoria_id'], $_POST['isbn'], $_POST['ano'], $_POST['quantidade']]);
    header('Location: livros.php');
    exit;
  } elseif ($_POST['action'] === 'update') {
    $stmt = $pdo->prepare('UPDATE livros SET titulo=?,autor_id=?,categoria_id=?,isbn=?,ano=?,quantidade=? WHERE id=?');
    $stmt->execute([$_POST['titulo'], $_POST['autor_id'], $_POST['categoria_id'], $_POST['isbn'], $_POST['ano'], $_POST['quantidade'], $_POST['id']]);
    header('Location: livros.php');
    exit;
  } elseif ($_POST['action'] === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM livros WHERE id=?');
    $stmt->execute([$_POST['id']]);
    header('Location: livros.php');
    exit;
  }
}
$livros = $pdo->query('SELECT l.*, a.nome as autor, c.nome as categoria FROM livros l LEFT JOIN autores a ON l.autor_id=a.id LEFT JOIN categorias c ON l.categoria_id=c.id ORDER BY l.titulo')->fetchAll();
$autores = $pdo->query('SELECT id,nome FROM autores')->fetchAll();
$categorias = $pdo->query('SELECT id,nome FROM categorias')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Livros</h2>
  <a class="btn btn-primary" data-bs-toggle="collapse" href="#formAdd">+ Novo</a>
</div>
<div class="collapse mb-3" id="formAdd">
  <div class="card p-3">
    <form method="post">
      <input type="hidden" name="action" value="create">
      <div class="mb-2"><input name="titulo" required class="form-control" placeholder="Título"></div>
      <div class="mb-2">
        <select name="autor_id" class="form-select" required>
          <option value="">Autor</option>
          <?php foreach ($autores as $a): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2">
        <select name="categoria_id" class="form-select" required>
          <option value="">Categoria</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2"><input name="isbn" class="form-control" placeholder="ISBN"></div>
      <div class="row">
        <div class="col"><input name="ano" class="form-control" placeholder="Ano"></div>
        <div class="col"><input name="quantidade" class="form-control" value="1" placeholder="Quantidade"></div>
      </div>
      <div class="mt-2"><button class="btn btn-success">Salvar</button></div>
    </form>
  </div>
</div>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Título</th>
      <th>Autor</th>
      <th>Categoria</th>
      <th>Qtd</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($livros as $l): ?>
      <tr>
        <td><?= htmlspecialchars($l['titulo']) ?></td>
        <td><?= htmlspecialchars($l['autor']) ?></td>
        <td><?= htmlspecialchars($l['categoria']) ?></td>
        <td><?= $l['quantidade'] ?></td>
        <td>
          <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit<?= $l['id'] ?>">Editar</button>
          <form method="post" class="d-inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $l['id'] ?>">
            <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir?')">Excluir</button>
          </form>
        </td>
      </tr>


      <div class="modal fade" id="edit<?= $l['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <div class="modal-header">
                <h5 class="modal-title">Editar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                <div class="mb-2"><input name="titulo" class="form-control" value="<?= htmlspecialchars($l['titulo']) ?>"></div>
                <div class="mb-2">
                  <select name="autor_id" class="form-select">
                    <?php foreach ($autores as $a): ?>
                      <option <?= $a['id'] == $l['autor_id'] ? 'selected' : '' ?> value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-2">
                  <select name="categoria_id" class="form-select">
                    <?php foreach ($categorias as $c): ?>
                      <option <?= $c['id'] == $l['categoria_id'] ? 'selected' : '' ?> value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-2"><input name="isbn" class="form-control" value="<?= htmlspecialchars($l['isbn']) ?>"></div>
                <div class="row">
                  <div class="col"><input name="ano" class="form-control" value="<?= htmlspecialchars($l['ano']) ?>"></div>
                  <div class="col"><input name="quantidade" class="form-control" value="<?= htmlspecialchars($l['quantidade']) ?>"></div>
                </div>
              </div>
              <div class="modal-footer"><button class="btn btn-primary">Salvar</button></div>
            </form>
          </div>
        </div>
      </div>

    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'templates/footer.php'; ?>