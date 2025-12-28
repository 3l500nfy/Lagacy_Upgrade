<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_GET['tid']))
{
    header("Location:".siteUrl("403.php"));
    exit();
}
$testid = clean($_GET['tid']);
if (!is_test_administrator_of($testid))
    header("Location:".siteUrl("403.php"));

$test_config = get_test_config_param_as_array($testid);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Manage User Categories</h2><br />
            <form class="style-frm" id="test-user-frm" ><input type="hidden" name="tid" id="tid" value="<?php echo $test_config['testid']; ?>" />

                <fieldset id="test-user-action-list"><legend>Select an operation:</legend>
                    <ul>
                        <li><a href="manage_compositor.php?tid=<?php echo $testid; ?>">Manage Compositors</a></li>
                        <li><a href="manage_proctor.php?tid=<?php echo $testid; ?>">Manage Invigilators</a></li>
                        <li><a href="manage_previewer.php?tid=<?php echo $testid; ?>">Manage Previewer</a></li>
                    </ul>
                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());
            
        </script>
    </body>
</html>