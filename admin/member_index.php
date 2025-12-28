<?php
session_start();
require_once("redirect_notloggedinuser.php");
require_once("../lib/globals.php");

openConnection();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../partials/cssimports.php") ?>
    </head>

    <body>
        <?php include_once("../partials/navbar.php"); ?>
        <div id="container" class="container">
            <div class="page-header">
                <?php if (isset($_SESSION['MEMBER_FULLNAME'])): ?>
                    <h2>Welcome <small><?php echo $_SESSION['MEMBER_FULLNAME']; ?></small> </h2>
                <?php else: ?>
                    <h2>Welcome <small>Guest</small> </h2>
                <?php endif ?>
            </div>
            <br />
        </div>
        <?php include_once dirname(__FILE__) . "/../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>;
    </body>
</html>
