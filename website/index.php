<?php
require_once __DIR__ . '/config/auth.php';
require_login();

$equipmentCount = (int)$pdo->query("SELECT COUNT(*) FROM equipment")->fetchColumn();
$incidentCount = (int)$pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$resolvedCount = (int)$pdo->query("SELECT COUNT(*) FROM incidents WHERE status='Resolved'")->fetchColumn();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM incidents WHERE status='Pending'")->fetchColumn();
$repairCount = (int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE status='Under Repair'")->fetchColumn();

$recent = $pdo->query("
    SELECT i.*, e.asset_code, u.full_name AS reporter
    FROM incidents i
    LEFT JOIN equipment e ON e.id=i.equipment_id
    LEFT JOIN users u ON u.id=i.reported_by
    ORDER BY i.created_at DESC LIMIT 5
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>IT Operations Dashboard</h1>
<p class="muted">Monitor campus equipment and resolve technical incidents.</p>

<div class="stats">
    <div class="stat"><span>Equipment</span><strong><?= $equipmentCount ?></strong></div>
    <div class="stat"><span>Total Incidents</span><strong><?= $incidentCount ?></strong></div>
    <div class="stat"><span>Pending</span><strong><?= $pendingCount ?></strong></div>
    <div class="stat"><span>Resolved</span><strong><?= $resolvedCount ?></strong></div>
    <div class="stat"><span>Under Repair</span><strong><?= $repairCount ?></strong></div>
</div>

<section class="panel">
    <div class="panel-head"><h2>Recent Incidents</h2><a class="btn primary" href="incidents.php?action=new">+ New Incident</a></div>
    <div class="table-wrap">
    <table>
        <tr><th>Code</th><th>Equipment</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th></tr>
        <?php foreach ($recent as $row): ?>
        <tr>
            <td><a href="incidents.php?id=<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['incident_code']) ?></a></td>
            <td><?= htmlspecialchars($row['asset_code'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><span class="badge <?= strtolower($row['priority']) ?>"><?= htmlspecialchars($row['priority']) ?></span></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="6">No incidents yet.</td></tr><?php endif; ?>
    </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
