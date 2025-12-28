<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator"))) {
    echo"<option value=''>--Select--</option>";
    exit();
}

if (!isset($_POST['cid'])) {
echo"<option value=''>--Select--</option>";
}

    $cid = clean($_POST['cid']);
    echo get_venue_as_options(array('centerid' => $cid));

?>