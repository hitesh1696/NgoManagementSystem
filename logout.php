<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

verifyCsrfToken();
$_SESSION = [];
session_regenerate_id(true);
redirectTo('/NGOManagementSystem/login.php');
