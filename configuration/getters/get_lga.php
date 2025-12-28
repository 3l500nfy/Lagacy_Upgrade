<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_POST['stateid'])) {
echo"<option value=''> All </option>";
}

    $stateid = clean($_POST['stateid']);
    echo get_lga_as_option($stateid);

?>