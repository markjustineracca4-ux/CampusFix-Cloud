<?php
require_once __DIR__ . '/config/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $code = 'INC-' . date('YmdHis') . '-' . random_int(100,999);
        $stmt = $pdo->prepare("INSERT INTO incidents(incident_code,equipment_id,reported_by,category,priority,description,assigned_to) VALUES(?,?,?,?,?,?,?)");
        $stmt->execute([
            $code, $_POST['equipment_id'] ?: null, $_SESSION['user']['id'],
            trim($_POST['category']), $_POST['priority'], trim($_POST['description']),
            $_POST['assigned_to'] ?: null
        ]);
    }

    if ($action === 'update') {
        $status = $_POST['status'];
        $resolved = $status === 'Resolved' ? date('Y-m-d H:i:s') : null;
        $stmt = $pdo->prepare("UPDATE incidents SET status=?, assigned_to=?, resolved_at=? WHERE id=?");
        $stmt->execute([$status, $_POST['assigned_to'] ?: null, $resolved, (int)$_POST['id']]);

        if (!empty($_POST['update_text'])) {
            $stmt = $pdo->prepare("INSERT INTO incident_updates(incident_id,user_id,update_text) VALUES(?,?,?)");
            $stmt->execute([(int)$_POST['id'], $_SESSION['user']['id'], trim($_POST['update_text'])]);
        }
    }

    header('Location: incidents.php?id=' . (int)($_POST['id'] ?? 0));
    exit;
}

$equipment = $pdo->query("SELECT id,asset_code,equipment_name FROM equipment ORDER BY asset_code")->fetchAll();
$technicians = $pdo->query("SELECT id,full_name FROM users WHERE role IN ('admin','technician') ORDER BY full_name")->fetchAll();

if (isset($_GET['action']) && $_GET['action'] === 'new') {
    include __DIR__ . '/includes/header.php';
?>
<h1>Create IT Incident</h1>
<section class="panel">
<form method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="create">
<label>Equipment</label>
<select name="equipment_id"><option value="">Not specified</option>
<?php foreach($equipment as $e): ?><option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['asset_code'].' — '.$e['equipment_name']) ?></option><?php endforeach; ?>
</select>
<label>Category</label><input name="category" placeholder="Hardware, Software, Network..." required>
<label>Priority</label><select name="priority"><option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option></select>
<label>Description</label><textarea name="description" rows="6" required placeholder="Describe the problem..."></textarea>
<label>Assign Technician</label><select name="assigned_to"><option value="">Unassigned</option>
<?php foreach($technicians as $t): ?><option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?>
</select>
<button class="btn primary" type="submit">Create Incident</button>
</form>
</section>
<?php include __DIR__ . '/includes/footer.php'; exit; }

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("
        SELECT i.*, e.asset_code, e.equipment_name, u.full_name reporter,
               a.full_name assignee
        FROM incidents i
        LEFT JOIN equipment e ON e.id=i.equipment_id
        LEFT JOIN users u ON u.id=i.reported_by
        LEFT JOIN users a ON a.id=i.assigned_to
        WHERE i.id=?
    ");
    $stmt->execute([(int)$_GET['id']]);
    $incident = $stmt->fetch();

    if (!$incident) { http_response_code(404); exit('Incident not found.'); }

    $stmt = $pdo->prepare("SELECT iu.*, u.full_name FROM incident_updates iu JOIN users u ON u.id=iu.user_id WHERE incident_id=? ORDER BY iu.created_at DESC");
    $stmt->execute([$incident['id']]);
    $updates = $stmt->fetchAll();

    include __DIR__ . '/includes/header.php';
?>
<div class="panel-head"><div><h1><?= htmlspecialchars($incident['incident_code']) ?></h1><p class="muted">Incident details and repair history</p></div></div>
<section class="detail-grid">
<div class="panel">
<h2>Incident Information</h2>
<p><b>Equipment:</b> <?= htmlspecialchars($incident['asset_code'] ?? 'N/A') ?></p>
<p><b>Category:</b> <?= htmlspecialchars($incident['category']) ?></p>
<p><b>Priority:</b> <?= htmlspecialchars($incident['priority']) ?></p>
<p><b>Status:</b> <?= htmlspecialchars($incident['status']) ?></p>
<p><b>Reported by:</b> <?= htmlspecialchars($incident['reporter'] ?? 'N/A') ?></p>
<p><b>Assigned to:</b> <?= htmlspecialchars($incident['assignee'] ?? 'Unassigned') ?></p>
<hr>
<p><?= nl2br(htmlspecialchars($incident['description'])) ?></p>
</div>
<div class="panel">
<h2>Update Incident</h2>
<form method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="<?= (int)$incident['id'] ?>">
<label>Status</label>
<select name="status"><option <?= $incident['status']==='Pending'?'selected':'' ?>>Pending</option><option <?= $incident['status']==='In Progress'?'selected':'' ?>>In Progress</option><option <?= $incident['status']==='Resolved'?'selected':'' ?>>Resolved</option></select>
<label>Assigned Technician</label>
<select name="assigned_to"><option value="">Unassigned</option>
<?php foreach($technicians as $t): ?><option value="<?= (int)$t['id'] ?>" <?= $incident['assigned_to']==$t['id']?'selected':'' ?>><?= htmlspecialchars($t['full_name']) ?></option><?php endforeach; ?>
</select>
<label>Repair/Progress Note</label>
<textarea name="update_text" rows="5" placeholder="What was checked or repaired?"></textarea>
<button class="btn primary" type="submit">Save Update</button>
</form>
</div>
</section>
<section class="panel">
<h2>Repair History</h2>
<?php foreach($updates as $u): ?>
<div class="timeline"><b><?= htmlspecialchars($u['full_name']) ?></b><small><?= htmlspecialchars($u['created_at']) ?></small><p><?= nl2br(htmlspecialchars($u['update_text'])) ?></p></div>
<?php endforeach; ?>
<?php if (!$updates): ?><p class="muted">No updates yet.</p><?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; exit; }

$incidents = $pdo->query("
SELECT i.*,e.asset_code,a.full_name assignee
FROM incidents i
LEFT JOIN equipment e ON e.id=i.equipment_id
LEFT JOIN users a ON a.id=i.assigned_to
ORDER BY i.created_at DESC
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="panel-head"><div><h1>IT Incidents</h1><p class="muted">Track faults, assignments, and resolutions.</p></div><a class="btn primary" href="incidents.php?action=new">+ New Incident</a></div>
<section class="panel">
<div class="table-wrap"><table>
<tr><th>Code</th><th>Equipment</th><th>Category</th><th>Priority</th><th>Status</th><th>Assigned</th></tr>
<?php foreach($incidents as $i): ?>
<tr>
<td><a href="incidents.php?id=<?= (int)$i['id'] ?>"><?= htmlspecialchars($i['incident_code']) ?></a></td>
<td><?= htmlspecialchars($i['asset_code'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($i['category']) ?></td>
<td><span class="badge <?= strtolower($i['priority']) ?>"><?= htmlspecialchars($i['priority']) ?></span></td>
<td><?= htmlspecialchars($i['status']) ?></td>
<td><?= htmlspecialchars($i['assignee'] ?? 'Unassigned') ?></td>
</tr>
<?php endforeach; ?>
</table></div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
