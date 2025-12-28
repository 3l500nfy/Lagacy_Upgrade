<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
//require_once('../../lib/candid_scheduling_func.php');
require_once("authoring_functions.php");
openConnection();
global $dbh;
authorize();
//get testid

$quests = (isset($_POST['qsel']) ? ($_POST['qsel']) : (array()));
if (count($quests) == 0) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>No question selected!</div>";
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Presentation Preview</title>
        <link href="<?php echo siteUrl("assets/css/presentationstyle.css"); ?>" rel="stylesheet" type="text/css" ></link>
        <style type="text/css">
            @media print{
                #print-ctr{
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <div>
            <?php
            $c = 1;
            foreach ($quests as $quest) {
                echo get_presentation_preview($quest, $c);
                $c++;
            }
            ?>
        </div>
        <div id="print-ctr"><a href="javascript:window.print();">Print</a></div>
    </body>
</html>
