<?php
if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");
require_once('../../../lib/security.php');

authorize();
//authorize_roles(["admin", "head-of-dept"]); # For PHP 5.4 higher.
//authorize_roles(array("admin", "head-of-dept")); For PHP v5.3 lower
if (!has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

openConnection();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../../../partials/cssimports.php") ?>
    </head>
    <body>
        <?php include_once("../../../partials/navbar.php"); ?>

        <div id="container" class="container">
            <h2 class="page-header">User Administration Dashboard</h2>
            <ul class="nav nav-tabs smaller">
                <li class="active"><a href="">Home</a></li>
                <li><a href ="../user_1/index.php">Users</a></li>
                <li><a href ="../user_1/enabledisableuser_form.php">Enable/Disable User</a></li>
                <li><a href ="users.php">Assign Role/Permission</a></li>
            </ul>
        </div>

    <?php include_once dirname(__FILE__) . "/../../../partials/jsimports.php" ?>;
    <script type="text/javascript" src="js/tabs.js"></script>
</body>
</html>
