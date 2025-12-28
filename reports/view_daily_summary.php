<?php
//echo 'am here1';exit;
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Admin")) && !has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Reports";
$navindex = 5;
require_once '../partials/cbt_header.php';

?>
<link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
<link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>

<div id="container">
    <div class="scheme-select">
        <h2>Regular</h2>
        <form class="scheme-frm style-frm" action="dailyreports.php" method="post">
            <table id="test-init-tbl">
                <tr>
                    <td colspan="2">
                        <div id="infoDiv"></div>
                    </td>
                </tr>

                <tr>
                    <td>Test Date:</td>
                    <td>
                        <select name="tdate" id="tdate">
                            <option value="">--Select--</option><?php
                            $sql = "select distinct date from tblscheduling order by date DESC limit 10";
                            $stmt = $dbh->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['date'] . "'>" . $row['date'] . "</option>";
                            }
                            ?></select></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="test-init-submit" id="test-init-submit" value="View Report"/></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php
require_once '../partials/cbt_footer.php';
?>
<script type ="text/javascript" src ="../assets/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // Initialize select2
        $("#tcode").select2();
    });


</script>


