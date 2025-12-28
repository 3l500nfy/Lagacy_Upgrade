<?php
if (!isset($_SESSION))
    session_start();
?>
<?php require_once("../lib/globals.php");
require_once("../lib/security.php"); ?>

<?php
openConnection();
authorize();
unset($_SESSION['scheme']);
if(!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Test Invigilator")))
    header("Location:".siteUrl("403.php"));

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT</title>
        <?php require_once("../partials/cssimports.php") ?>
        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>
        <link type="text/css" href="../assets/css/tconfig1.css" rel="stylesheet"></link>
        <style type="text/css" >
            .cover{display:none;}
        </style>

    </head>

    <body>
        <?php include_once "../partials/navbar.php" ?>;

        <div id="container" class="container"> 
            <h1>Select a Scheme</h1><hr />
            <a href="../admin.php">&lt;&lt;Home</a><br /><br />
            <div id="cover" class="cover">
                <div class="sblinks" id="sblinks1">SBRS</div><div class="sblinks" id="sblinks2">PUTME</div><div class="sblinks" id="sblinks3">REGULAR</div>
            </div>
        </div><!-- /container -->

        <?php include_once "../partials/footer.php" ?>;
        <?php include_once "../partials/jsimports.php" ?>;
        <script type="text/javascript">
            $(".cover").fadeIn('slow');
            $("#sblinks1").click(function(event){ //alert('enesi');
                window.location="<?php echo siteUrl("configuration/home.php?scheme=1");?>";
            });
            $("#sblinks2").click(function(event){ //alert('enesi');
                window.location="<?php echo siteUrl("configuration/home.php?scheme=2");?>";
            });
            $("#sblinks3").click(function(event){ //alert('enesi');
                window.location="<?php echo siteUrl("configuration/home.php?scheme=3");?>";
            });
       </script>
    </body>
</html>