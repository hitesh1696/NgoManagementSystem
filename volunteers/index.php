<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$pageUrl = '/NGOManagementSystem/volunteers/index.php';
$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $statement = $conn->prepare('SELECT * FROM volunteers WHERE id = ?');
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
            setFlash('danger', 'Invalid volunteer selected.');
        } else {
            $statement = $conn->prepare('DELETE FROM volunteers WHERE id = ?');
            $statement->bind_param('i', $id);
            $statement->execute();
            setFlash('success', 'Volunteer deleted.');
        }
        redirectTo($pageUrl);
    }
    if ($action === 'save') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $skills = trim((string) ($_POST['skills'] ?? ''));
        $joiningDate = (string) ($_POST['joining_date'] ?? '');
        $validDate = DateTime::createFromFormat('Y-m-d', $joiningDate);

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $skills === '' || !$validDate || $validDate->format('Y-m-d') !== $joiningDate) {
            setFlash('danger', 'Complete all fields with a valid email address and joining date.');
            redirectTo($id ? $pageUrl . '?edit=' . $id : $pageUrl);
        }
        try {
            if ($id) {
                $statement = $conn->prepare('UPDATE volunteers SET name = ?, email = ?, phone = ?, skills = ?, joining_date = ? WHERE id = ?');
                $statement->bind_param('sssssi', $name, $email, $phone, $skills, $joiningDate, $id);
                $statement->execute();
                setFlash('success', 'Volunteer updated.');
            } else {
                $statement = $conn->prepare('INSERT INTO volunteers (name, email, phone, skills, joining_date) VALUES (?, ?, ?, ?, ?)');
                $statement->bind_param('sssss', $name, $email, $phone, $skills, $joiningDate);
                $statement->execute();
                setFlash('success', 'Volunteer added.');
            }
        } catch (mysqli_sql_exception $exception) {
            setFlash('danger', 'Could not save volunteer: ' . $exception->getMessage());
        }
        redirectTo($pageUrl);
    }
}

$volunteers = $conn->query('SELECT * FROM volunteers ORDER BY joining_date DESC, id DESC');
$pageTitle = 'Volunteers';
require __DIR__ . '/../includes/header.php';
?>
<div class="card form-card"><div class="card-body">
    <h2 class="h5 mb-3"><?= $editing ? 'Edit volunteer' : 'Add volunteer' ?></h2>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div class="col-md-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" required value="<?= e($editing['name'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" required value="<?= e($editing['email'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="phone">Phone number</label><input class="form-control" id="phone" name="phone" required value="<?= e($editing['phone'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="skills">Skills</label><input class="form-control" id="skills" name="skills" required value="<?= e($editing['skills'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="joining_date">Joining date</label><input class="form-control" id="joining_date" name="joining_date" type="date" required value="<?= e($editing['joining_date'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-12"><button class="btn btn-primary"><?= $editing ? 'Update volunteer' : 'Add volunteer' ?></button><?php if ($editing): ?> <a class="btn btn-outline-secondary" href="<?= e($pageUrl) ?>">Cancel</a><?php endif; ?></div>
    </form>
</div></div>
<div class="table-responsive bg-white"><table class="table table-hover align-middle">
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Skills</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody><?php if ($volunteers->num_rows === 0): ?><tr><td colspan="6" class="text-center text-secondary py-4">No volunteers have been added.</td></tr><?php endif; ?>
    <?php while ($row = $volunteers->fetch_assoc()): ?><tr><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['phone']) ?></td><td><?= e($row['skills']) ?></td><td><?= e($row['joining_date']) ?></td><td class="text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="<?= e($pageUrl) ?>?edit=<?= (int) $row['id'] ?>">Edit</a>
        <form method="post" class="d-inline" onsubmit="return confirm('Delete this volunteer?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td></tr><?php endwhile; ?></tbody>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
