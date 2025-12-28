<?php
if (!isset($_SESSION))
    session_start();

require_once(dirname(__FILE__) . "/lib/globals.php");
redirect(siteUrl("test/login.php"));

?>