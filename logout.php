<?php

if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");
require_once('lib/security.php');

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
redirect(siteUrl("admin.php"));
?>