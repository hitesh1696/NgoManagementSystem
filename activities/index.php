<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$pageUrl = '/NGOManagementSystem/activities/index.php';
$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $statement = $conn->prepare('SELECT * FROM activities WHERE id = ?');
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
            setFlash('danger', 'Invalid activity selected.');
        } else {
            $statement = $conn->prepare('DELETE FROM activities WHERE id = ?');
            $statement->bind_param('i', $id);
            $statement->execute();
            setFlash('success', 'Activity deleted.');
        }
        redirectTo($pageUrl);
    }
    if ($action === 'save') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim((string) ($_POST['activity_name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = (string) ($_POST['event_date'] ?? '');
        $location = trim((string) ($_POST['location'] ?? ''));
        $status = (string) ($_POST['status'] ?? '');
        $validDate = DateTime::createFromFormat('Y-m-d', $eventDate);
        if ($name === '' || $description === '' || !$validDate || $validDate->format('Y-m-d') !== $eventDate || $location === '' || !in_array($status, ['Planned', 'Ongoing', 'Completed', 'Cancelled'], true)) {
            setFlash('danger', 'Complete all fields with a valid event date and status.');
            redirectTo($id ? $pageUrl . '?edit=' . $id : $pageUrl);
        }
        try {
            if ($id) {
                $statement = $conn->prepare('UPDATE activities SET activity_name = ?, description = ?, event_date = ?, location = ?, status = ? WHERE id = ?');
                $statement->bind_param('sssssi', $name, $description, $eventDate, $location, $status, $id);
                $statement->execute();
                setFlash('success', 'Activity updated.');
            } else {
                $statement = $conn->prepare('INSERT INTO activities (activity_name, description, event_date, location, status) VALUES (?, ?, ?, ?, ?)');
                $statement->bind_param('sssss', $name, $description, $eventDate, $location, $status);
                $statement->execute();
                setFlash('success', 'Activity added.');
            }
        } catch (mysqli_sql_exception $exception) {
            setFlash('danger', 'Could not save activity: ' . $exception->getMessage());
        }
        redirectTo($pageUrl);
    }
}

$activities = $conn->query('SELECT * FROM activities ORDER BY event_date DESC, id DESC');
$pageTitle = 'Activities';
require __DIR__ . '/../includes/header.php';
?>
<div class="card form-card"><div class="card-body"><h2 class="h5 mb-3"><?= $editing ? 'Edit activity' : 'Add activity' ?></h2>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">
        <div class="col-md-3"><label class="form-label" for="activity_name">Activity name</label><input class="form-control" id="activity_name" name="activity_name" required value="<?= e($editing['activity_name'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label" for="description">Description</label><input class="form-control" id="description" name="description" required value="<?= e($editing['description'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="form-label" for="event_date">Event date</label><input class="form-control" id="event_date" name="event_date" type="date" required value="<?= e($editing['event_date'] ?? date('Y-m-d')) ?>"></div>
        <div class="col-md-2"><label class="form-label" for="location">Location</label><input class="form-control" id="location" name="location" required value="<?= e($editing['location'] ?? '') ?>"></div>
        <div class="col-md-1"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status" required><?php foreach (['Planned', 'Ongoing', 'Completed', 'Cancelled'] as $option): ?><option value="<?= e($option) ?>" <?= ($editing['status'] ?? 'Planned') === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><button class="btn btn-primary"><?= $editing ? 'Update activity' : 'Add activity' ?></button><?php if ($editing): ?> <a class="btn btn-outline-secondary" href="<?= e($pageUrl) ?>">Cancel</a><?php endif; ?></div>
    </form>
</div></div>
<div class="table-responsive bg-white"><table class="table table-hover align-middle">
    <thead><tr><th>Activity</th><th>Description</th><th>Date</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody><?php if ($activities->num_rows === 0): ?><tr><td colspan="6" class="text-center text-secondary py-4">No activities have been added.</td></tr><?php endif; ?>
    <?php while ($row = $activities->fetch_assoc()): ?><tr><td><?= e($row['activity_name']) ?></td><td><?= e($row['description']) ?></td><td><?= e($row['event_date']) ?></td><td><?= e($row['location']) ?></td><td><?= e($row['status']) ?></td><td class="text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="<?= e($pageUrl) ?>?edit=<?= (int) $row['id'] ?>">Edit</a>
        <form method="post" class="d-inline" onsubmit="return confirm('Delete this activity?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td></tr><?php endwhile; ?></tbody>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
