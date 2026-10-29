<aside class="sidebar">
    <a class="brand" href="/NGOManagementSystem/dashboard.php">
        <span class="brand-mark">NG</span>
        <span>Helping Hands<small>Administration</small></span>
    </a>
    <nav class="nav flex-column" aria-label="Main navigation">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="/NGOManagementSystem/dashboard.php">Dashboard</a>
        <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/beneficiaries/') ? 'active' : '' ?>" href="/NGOManagementSystem/beneficiaries/index.php">Beneficiaries</a>
        <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/volunteers/') ? 'active' : '' ?>" href="/NGOManagementSystem/volunteers/index.php">Volunteers</a>
        <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/donations/') ? 'active' : '' ?>" href="/NGOManagementSystem/donations/index.php">Donations</a>
        <a class="nav-link <?= str_contains($_SERVER['PHP_SELF'], '/activities/') ? 'active' : '' ?>" href="/NGOManagementSystem/activities/index.php">Activities</a>
        <form action="/NGOManagementSystem/logout.php" method="post" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <button type="submit" class="nav-link logout-link w-100 text-start">Logout</button>
        </form>
    </nav>
</aside>
