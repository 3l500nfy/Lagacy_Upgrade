<?php

if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
openConnection();

$candidatetype = $_POST['candidatetype'];
$examtype = $_POST['testid'];
$username = $_POST['username'];

?>