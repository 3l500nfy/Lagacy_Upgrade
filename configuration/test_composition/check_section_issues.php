<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")))
{
   echo"Access denied";
   exit();
}


if (!isset($_POST['sectionid'])) {
    echo"Access denied";
    exit();
}
if (!isset($_POST['version'])) {
    echo"Access denied";
    exit();
}

$sectionid = clean($_POST['sectionid']);
$version = clean($_POST['version']);

$num_easy=get_num_diff_lvl($sectionid, "easy");
$num_moderate=get_num_diff_lvl($sectionid, "moderate");
$num_difficult=get_num_diff_lvl($sectionid, "difficult");
$num_easy_count=get_num_diff_lvl_count($sectionid, $version,  "easy");
$num_difficult_count=get_num_diff_lvl_count($sectionid, $version, "difficult");
$num_moderate_count=get_num_diff_lvl_count($sectionid, $version, "moderate");
if($num_easy>$num_easy_count || $num_difficult>$num_difficult_count || $num_moderate>$num_moderate_count)
{
    echo"<div class='alert-error'> <h2 class='coolh2'>You have issue(s) to address:</h2>";
}
else
    echo "<div> Total Questions Selected: ".($num_easy_count + $num_moderate_count + $num_difficult_count)." <i>($num_easy_count easy, $num_moderate_count moderate, $num_difficult_count difficult)</i>";
if($num_easy>$num_easy_count)
{
    echo "Required: $num_easy of easy questions, Currently selected: $num_easy_count <br />";
}
if($num_moderate>$num_moderate_count)
{
    echo "Required: $num_moderate of moderate questions, Currently selected: $num_moderate_count <br />";
}
if($num_difficult>$num_difficult_count)
{
    echo "Required: $num_difficult of difficult questions, Currently selected: $num_difficult_count <br />";
}

echo"</div>";
?>