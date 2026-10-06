<?php
require_once __DIR__ . '/config/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO equipment(asset_code,equipment_name,laboratory,brand,model,status) VALUES(?,?,?,?,?,?)");
        $stmt->execute([
            trim($_POST['asset_code']), trim($_POST['equipment_name']),
            trim($_POST['laboratory']), trim($_POST['brand']),
            trim($_POST['model']), $_POST['status']
        ]);
    }

    if ($action === 'delete' && $_SESSION['user']['role'] === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM equipment WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
    }

    header('Location: equipment.php');
    exit;
}

$equipment = $pdo->query("SELECT * FROM equipment ORDER BY id DESC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="panel-head"><div><h1>Equipment Inventory</h1><p class="muted">Track campus computers and IT assets.</p></div></div>

<section class="panel">
<h2>Add Equipment</h2>
<form class="form-grid" method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="add">
<input name="asset_code" placeholder="Asset Code" required>
<input name="equipment_name" placeholder="Equipment Name" required>
<input name="laboratory" placeholder="Laboratory" required>
<input name="brand" placeholder="Brand">
<input name="model" placeholder="Model">
<select name="status"><option>Working</option><option>Under Repair</option><option>Retired</option></select>
<button class="btn primary" type="submit">Add Equipment</button>
</form>
</section>

<section class="panel">
<div class="table-wrap"><table>
<tr><th>Asset</th><th>Equipment</th><th>Laboratory</th><th>Brand/Model</th><th>Status</th><th></th></tr>
<?php foreach ($equipment as $row): ?>
<tr>
<td><?= htmlspecialchars($row['asset_code']) ?></td>
<td><?= htmlspecialchars($row['equipment_name']) ?></td>
<td><?= htmlspecialchars($row['laboratory']) ?></td>
<td><?= htmlspecialchars($row['brand'].' '.$row['model']) ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>
<td>
<?php if ($_SESSION['user']['role'] === 'admin'): ?>
<form method="post" onsubmit="return confirm('Delete this equipment?')">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
<button class="btn danger" type="submit">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table></div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
