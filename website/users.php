<?php
require_once __DIR__ . '/config/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users(username,password,full_name,role) VALUES(?,?,?,?)");
        $stmt->execute([trim($_POST['username']),$hash,trim($_POST['full_name']),$_POST['role']]);
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if ($id !== (int)$_SESSION['user']['id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$id]);
        }
    }

    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT id,username,full_name,role,created_at FROM users ORDER BY id")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1>User Management</h1>
<section class="panel">
<h2>Add User</h2>
<form class="form-grid" method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="add">
<input name="username" placeholder="Username" required>
<input name="full_name" placeholder="Full name" required>
<input type="password" name="password" placeholder="Password" required minlength="8">
<select name="role"><option>staff</option><option>technician</option><option>admin</option></select>
<button class="btn primary" type="submit">Create User</button>
</form>
</section>
<section class="panel">
<div class="table-wrap"><table>
<tr><th>Username</th><th>Name</th><th>Role</th><th>Created</th><th></th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['full_name']) ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
<td><?= htmlspecialchars($u['created_at']) ?></td>
<td>
<?php if ((int)$u['id'] !== (int)$_SESSION['user']['id']): ?>
<form method="post" onsubmit="return confirm('Delete this user?')">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
<button class="btn danger">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table></div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
