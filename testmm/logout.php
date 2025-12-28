<?php

if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

session_destroy();
redirect(siteUrl("test/index.php"));
?>
