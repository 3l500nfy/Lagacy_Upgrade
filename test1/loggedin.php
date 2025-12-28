<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");

if(isset($_SESSION['seequestion'])){
redirect(siteUrl("test/starttest.php"));
//header("location:starttest.php");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>CBT-already logged in</title>
        <?php require_once("../partials/cssimports.php") ?> 
    </head>

    <body>        
        <?php include_once "../partials/navbar.php" ?>  

        <div id="container" class="container" style="min-height: 400px; height: 80%;">
            <div style="margin: 200px 0;">
                <p class="lead" style="text-align: center;">Seems you are already logged in. Please log out before attempting to login again.</p>

                <p class="centeredtext">
                    <?php $logoutUrl = siteUrl("/test/logout.php"); ?>
                    <a href="<?php echo $logoutUrl ?>" class="btn btn-danger">Logout</a>
                <p>
                <hr class="soften">
            </div>
        </div>

        <?php include_once "../partials/jsimports.php" ?>;  
    </body>
</html>
