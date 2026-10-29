<?php
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['admin_id'])) {
    redirectTo('/NGOManagementSystem/dashboard.php');
}

redirectTo('/NGOManagementSystem/login.php');
