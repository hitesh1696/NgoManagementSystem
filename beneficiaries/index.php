<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$pageUrl = '/NGOManagementSystem/beneficiaries/index.php';
$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $statement = $conn->prepare('SELECT * FROM beneficiaries WHERE id = ?');
    $statement->bind_param('i', $editId);
    $statement->execute();
    $editing = $statement->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            setFlash('danger', 'Invalid beneficiary selected.');
        } else {
            $statement = $conn->prepare('DELETE FROM beneficiaries WHERE id = ?');
            $statement->bind_param('i', $id);
            $statement->execute();
            setFlash('success', 'Beneficiary deleted.');
        }
        redirectTo($pageUrl);
    }

    if ($action === 'save') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim((string) ($_POST['name'] ?? ''));
        $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
        $gender = (string) ($_POST['gender'] ?? '');
        $contact = trim((string) ($_POST['contact_number'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $registrationDate = (string) ($_POST['registration_date'] ?? '');
        $validDate = DateTime::createFromFormat('Y-m-d', $registrationDate);

        if ($name === '' || !$age || $age < 1 || $age > 120 || !in_array($gender, ['Female', 'Male', 'Other'], true) || $contact === '' || $address === '' || !$validDate || $validDate->format('Y-m-d') !== $registrationDate) {
            setFlash('danger', 'Complete all fields with a valid age and registration date.');
            redirectTo($id ? $pageUrl . '?edit=' . $id : $pageUrl);
        }

        try {
            if ($id) {
                $statement = $conn->prepare('UPDATE beneficiaries SET name = ?, age = ?, gender = ?, contact_number = ?, address = ?, registration_date = ? WHERE id = ?');
                $statement->bind_param('sissssi', $name, $age, $gender, $contact, $address, $registrationDate, $id);
                $statement->execute();
                setFlash('success', 'Beneficiary updated.');
            } else {
                $statement = $conn->prepare('INSERT INTO beneficiaries (name, age, gender, contact_number, address, registration_date) VALUES (?, ?, ?, ?, ?, ?)');
                $statement->bind_param('sissss', $name, $age, $gender, $contact, $address, $registrationDate);
                $statement->execute();
                setFlash('success', 'Beneficiary added.');
            }
        } catch (mysqli_sql_exception $exception) {
            setFlash('danger', 'Could not save beneficiary: ' . $exception->getMessage());
        }
        redirectTo($pageUrl);
    }
}

$beneficiaries = $conn->query('SELECT * FROM beneficiaries ORDER BY registration_date DESC, id DESC');
$pageTitle = 'Beneficiaries';
require __DIR__ . '/../includes/header.php';
?>
<div class="card form-card"><div class="card-body">
    <h2 class="h5 mb-3"><?= $editing ? 'Edit beneficiary' : 'Add beneficiary' ?></h2>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div class="col-md-4"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" required value="<?= e($editing['name'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="age">Age</label><input class="form-control" id="age" name="age" type="number" min="1" max="120" required value="<?= e($editing['age'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="gender">Gender</label><select class="form-select" id="gender" name="gender" required><option value="">Choose</option><?php foreach (['Female', 'Male', 'Other'] as $option): ?><option value="<?= e($option) ?>" <?= ($editing['gender'] ?? '') === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label" for="contact_number">Contact number</label><input class="form-control" id="contact_number" name="contact_number" required value="<?= e($editing['contact_number'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label" for="address">Address</label><input class="form-control" id="address" name="address" required value="<?= e($editing['address'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label" for="registration_date">Registration date</label><input class="form-control" id="registration_date" name="registration_date" type="date" required value="<?= e($editing['registration_date'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-12"><button class="btn btn-primary"><?= $editing ? 'Update beneficiary' : 'Add beneficiary' ?></button><?php if ($editing): ?> <a class="btn btn-outline-secondary" href="<?= e($pageUrl) ?>">Cancel</a><?php endif; ?></div>
    </form>
</div></div>
<div class="table-responsive bg-white"><table class="table table-hover align-middle">
    <thead><tr><th>Name</th><th>Age</th><th>Gender</th><th>Contact</th><th>Address</th><th>Registered</th><th>Actions</th></tr></thead>
    <tbody><?php if ($beneficiaries->num_rows === 0): ?><tr><td colspan="7" class="text-center text-secondary py-4">No beneficiaries have been added.</td></tr><?php endif; ?>
    <?php while ($row = $beneficiaries->fetch_assoc()): ?><tr>
        <td><?= e($row['name']) ?></td><td><?= e($row['age']) ?></td><td><?= e($row['gender']) ?></td><td><?= e($row['contact_number']) ?></td><td><?= e($row['address']) ?></td><td><?= e($row['registration_date']) ?></td>
        <td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="<?= e($pageUrl) ?>?edit=<?= (int) $row['id'] ?>">Edit</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this beneficiary?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
    </tr><?php endwhile; ?></tbody>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
