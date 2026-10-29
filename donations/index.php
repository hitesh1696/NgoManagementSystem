<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$pageUrl = '/NGOManagementSystem/donations/index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            setFlash('danger', 'Invalid donation selected.');
        } else {
            $statement = $conn->prepare('DELETE FROM donations WHERE id = ?');
            $statement->bind_param('i', $id);
            $statement->execute();
            setFlash('success', 'Donation deleted.');
        }
        redirectTo($pageUrl);
    }
    if ($action === 'save') {
        $donorName = trim((string) ($_POST['donor_name'] ?? ''));
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $donationDate = (string) ($_POST['donation_date'] ?? '');
        $paymentMethod = (string) ($_POST['payment_method'] ?? '');
        $purpose = trim((string) ($_POST['purpose'] ?? ''));
        $validDate = DateTime::createFromFormat('Y-m-d', $donationDate);
        if ($donorName === '' || $amount === false || $amount <= 0 || !$validDate || $validDate->format('Y-m-d') !== $donationDate || !in_array($paymentMethod, ['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'], true) || $purpose === '') {
            setFlash('danger', 'Complete all donation fields with a positive amount and valid date.');
        } else {
            try {
                $statement = $conn->prepare('INSERT INTO donations (donor_name, amount, donation_date, payment_method, purpose) VALUES (?, ?, ?, ?, ?)');
                $statement->bind_param('sdsss', $donorName, $amount, $donationDate, $paymentMethod, $purpose);
                $statement->execute();
                setFlash('success', 'Donation recorded.');
            } catch (mysqli_sql_exception $exception) {
                setFlash('danger', 'Could not record donation: ' . $exception->getMessage());
            }
        }
        redirectTo($pageUrl);
    }
}

$donations = $conn->query('SELECT * FROM donations ORDER BY donation_date DESC, id DESC');
$totalDonations = (float) $conn->query('SELECT COALESCE(SUM(amount), 0) AS total FROM donations')->fetch_assoc()['total'];
$pageTitle = 'Donations';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><p class="text-secondary mb-0">Record contributions received by the organization.</p><strong>Total received: ₹<?= number_format($totalDonations, 2) ?></strong></div>
<div class="card form-card"><div class="card-body"><h2 class="h5 mb-3">Record donation</h2>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save">
        <div class="col-md-3"><label class="form-label" for="donor_name">Donor name</label><input class="form-control" id="donor_name" name="donor_name" required></div>
        <div class="col-md-2"><label class="form-label" for="amount">Amount (₹)</label><input class="form-control" id="amount" name="amount" type="number" min="0.01" step="0.01" required></div>
        <div class="col-md-2"><label class="form-label" for="donation_date">Donation date</label><input class="form-control" id="donation_date" name="donation_date" type="date" value="<?= e(date('Y-m-d')) ?>" required></div>
        <div class="col-md-2"><label class="form-label" for="payment_method">Payment method</label><select class="form-select" id="payment_method" name="payment_method" required><option value="">Choose</option><?php foreach (['Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other'] as $option): ?><option value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label" for="purpose">Purpose</label><input class="form-control" id="purpose" name="purpose" required></div>
        <div class="col-12"><button class="btn btn-primary">Save donation</button></div>
    </form>
</div></div>
<div class="table-responsive bg-white"><table class="table table-hover align-middle">
    <thead><tr><th>Donor</th><th>Amount</th><th>Date</th><th>Payment method</th><th>Purpose</th><th>Action</th></tr></thead>
    <tbody><?php if ($donations->num_rows === 0): ?><tr><td colspan="6" class="text-center text-secondary py-4">No donations have been recorded.</td></tr><?php endif; ?>
    <?php while ($row = $donations->fetch_assoc()): ?><tr><td><?= e($row['donor_name']) ?></td><td>₹<?= number_format((float) $row['amount'], 2) ?></td><td><?= e($row['donation_date']) ?></td><td><?= e($row['payment_method']) ?></td><td><?= e($row['purpose']) ?></td><td>
        <form method="post" onsubmit="return confirm('Delete this donation record?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td></tr><?php endwhile; ?></tbody>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
