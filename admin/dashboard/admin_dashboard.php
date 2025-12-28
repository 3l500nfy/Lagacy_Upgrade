<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
require_once('../../lib/security.php');

openConnection();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("Github-ExamPractice") ?>
        </title>
        <?php require_once("../../partials/cssimports.php") ?>
    </head>
    <body>
        <?php include_once("../../partials/navbar.php"); ?>

        <div id="container" class="container">
            <h2 class="page-header">User Administration Dashboard</h2>
            <ul class="nav nav-tabs smaller">
                <li class="active"><a href="">Home</a></li>
                <li><a href ="../role/index.php">Roles</a></li>
                <li><a href ="../permission/index.php">Permissions</a></li>
                <li><a href ="../user/index.php">Users</a></li>
                <li><a href ="../user/enabledisableuser_form.php">Enable/Disable User</a></li>
                <li><a href ="role_permission.php">Map Role/Permission</a></li>
                <li><a href ="users.php">Map User/Role & Permission</a></li>
            </ul>
        </div>
    </div>
    <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
    <script type="text/javascript" src="js/tabs.js"></script>
</body>
</html>
