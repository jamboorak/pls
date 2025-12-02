<?php
// This file contains common session handling for the dev admin section
function startDevAdminSession() {
    session_name('dev_admin_session');
    session_start();
}

function isDevAdminLoggedIn() {
    return isset($_SESSION['dev_admin_logged_in']) && $_SESSION['dev_admin_logged_in'] === true;
}

function requireDevAdminLogin($redirect = 'login.php') {
    if (!isDevAdminLoggedIn()) {
        header("Location: $redirect");
        exit;
    }
}
?>
