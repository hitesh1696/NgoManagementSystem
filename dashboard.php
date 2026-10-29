<?php
require_once __DIR__ . '/includes/functions.php';
requireLogin();
require_once __DIR__ . '/config/db.php';

$beneficiaryCount = (int) $conn->query('SELECT COUNT(*) AS total FROM beneficiaries')->fetch_assoc()['total'];
$volunteerCount = (int) $conn->query('SELECT COUNT(*) AS total FROM volunteers')->fetch_assoc()['total'];
$donationTotal = (float) $conn->query('SELECT COALESCE(SUM(amount), 0) AS total FROM donations')->fetch_assoc()['total'];
$activityCount = (int) $conn->query('SELECT COUNT(*) AS total FROM activities')->fetch_assoc()['total'];

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<p class="text-secondary mb-4">A quick overview of your organization's records.</p>
<div class="row g-3">
    <div class="col-sm-6 col-xl-3"><div class="card summary-card"><div class="card-body"><div class="summary-label">Registered beneficiaries</div><div class="summary-number"><?= number_format($beneficiaryCount) ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card summary-card"><div class="card-body"><div class="summary-label">Volunteers</div><div class="summary-number"><?= number_format($volunteerCount) ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card summary-card"><div class="card-body"><div class="summary-label">Donations received</div><div class="summary-number">₹<?= number_format($donationTotal, 2) ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card summary-card"><div class="card-body"><div class="summary-label">NGO activities</div><div class="summary-number"><?= number_format($activityCount) ?></div></div></div></div>
</div>
<div class="card mt-4"><div class="card-body">
    <h2 class="h5">Welcome to Helping Hands</h2>
    <p class="text-secondary mb-0">Use the navigation to manage beneficiaries, volunteers, donations and activities.</p>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
