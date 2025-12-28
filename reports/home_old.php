<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!has_roles(array("Super Admin")) && !has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Reports";
$navindex = 5;

require_once '../partials/cbt_header.php';
?>
<link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
<link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>

<style type="text/css">
.scheme-select
{
    border-bottom-style:  dotted;
    border-bottom-color: green;
    border-bottom-width: 2px;
}
    #test-init-tbl tr td:first-child
    {
        font-weight: bold;
    }
</style>

<br />
<div class="cooltitle">
    TEST REPORTS
</div>
<div id="container">
    <div class="scheme-select">
        <h2>Regular</h2>
        <form class="scheme-frm style-frm">
            <table id="test-init-tbl">
                <tr><td colspan="2"><div id="infoDiv"></div></td></tr>
                <tr><td>Session:</td><td><select name="init-session"><?php
$dt = getdate();
$yr = $dt["year"];
for ($i = $yr - 3; $i < $yr + 3; $i++) {
    if ($i == $yr)
        echo "<option value='$i' selected>$i</option>";
    echo "<option value='$i'>$i</option>";
}
?></select></td></tr>
                <tr><td>Semester:</td><td><select name="init-semester"><option value="1">First</option><option value="2">Second</option><option value="3">Third</option></select></td></tr>
                <tr><td>Test Code:</td><td>
                        <select name="init-testcode" id="tcode"><option value="">--Select--</option><?php
                            $sql = "select * from tbltestcode where testname <>'PUTME' && testname <>'SBRS' && testname<>'SBRS-NEW'";
                            $stmt=$dbh->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['testcodeid'] . "'>" . $row['testname'] . "</option>";
                            }
?></select></td></tr>
                <tr><td>Test Type:</td><td><select name="init-testtype"><option value="">--Select--</option><?php
                            $sql2 = "select * from tbltesttype";
                            $stmt1=$dbh->prepare($sql2);
                            $stmt1->execute();
                            while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row2['testtypeid'] . "'>" . $row2['testtypename'] . "</option>";
                            }
?></select></td></tr>
                <tr><td></td><td><input type="submit" name="test-init-submit" id="test-init-submit" value="View Report" /></td></tr>
            </table>
        </form>
    </div>
    <div class="scheme-select">
        <h2>PUTME</h2>
        <form class="scheme-frm style-frm">
        <table id="test-init-tbl">
            <tr><td colspan="2"><div id="infoDiv"></div></td></tr>
            <tr><td>Session:</td><td><select name="init-session"><?php
                            $dt = getdate();
                            $yr = $dt["year"];
                            for ($i = $yr - 3; $i < $yr + 3; $i++) {
                                if ($i == $yr)
                                    echo "<option value='$i' selected>$i</option>";
                                echo "<option value='$i'>$i</option>";
                            }
?></select></td></tr>
            <tr><td>
                    <input type="hidden" name="init-semester" id="init-semester" value="0" />
                    <input type="hidden" name="init-testcode" id="init-testcode" value="1" />
                    <input type="hidden" name="init-testtype" id="init-testtype" value="1" />
                </td><td><input type="submit" name="test-init-submit" id="test-init-submit" value="View Report" /></td></tr>
        </table>
        </form>
    </div>
    <div class="scheme-select">
        <h2>SBRS-NEW</h2>
        <form class="scheme-frm style-frm">
        <table id="test-init-tbl">
            <tr><td colspan="2"><div id="infoDiv"></div></td></tr>
            <tr><td>Session:</td><td><select name="init-session"><?php
                        $dt = getdate();
                        $yr = $dt["year"];
                        for ($i = $yr - 3; $i < $yr + 3; $i++) {
                            if ($i == $yr)
                                echo "<option value='$i' selected>$i</option>";
                            echo "<option value='$i'>$i</option>";
                        }
?></select></td></tr>
            <tr><td>
                    <?php
                    $sql3 = "select testcodeid from tbltestcode where testname='SBRS-NEW' limit 1";
                    $stmt3=$dbh->prepare($sql3);
                    $stmt3->execute();
                    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                    $tcid = $row3['testcodeid'];
                    ?>
                    <input type="hidden" name="init-semester" id="init-semester" value="0" />
                    <input type="hidden" name="init-testcode" id="init-testcode" value="<?php echo $tcid; ?>" />
                    <input type="hidden" name="init-testtype" id="init-testtype" value="1" />

                </td><td><input type="submit" name="test-init-submit" id="test-init-submit" value="View Report" /></td></tr>
        </table>
        </form>
    </div>
    <div class="scheme-select">
        <h2>SBRS</h2>
        <form class="scheme-frm style-frm">
        <table id="test-init-tbl">
            <tr><td colspan="2"><div id="infoDiv"></div></td></tr>
            <tr><td>Session:</td><td><select name="init-session"><?php
                    $dt = getdate();
                    $yr = $dt["year"];
                    for ($i = $yr - 3; $i < $yr + 3; $i++) {
                        if ($i == $yr)
                            echo "<option value='$i' selected>$i</option>";
                        echo "<option value='$i'>$i</option>";
                    }
                    ?></select></td></tr>
            <tr><td>Semester:</td><td><select name="init-semester"><option value="1">First</option><option value="2">Second</option><option value="3">Third</option></select></td></tr>
            <tr><td>Test Type:</td><td><select name="init-testtype"><option value="">--Select--</option><?php
                        $sql2 = "select * from tbltesttype";
                        $stmt4=$dbh->prepare($sql2);
                        $stmt4->execute();
                        while ($row2 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $row2['testtypeid'] . "'>" . $row2['testtypename'] . "</option>";
                        }
                    ?></select></td></tr>
            <tr><td>
                    <input type="hidden" name="init-testcode" id="init-testcode" value="2"/>
                </td><td><input type="submit" name="test-init-submit" id="test-init-submit" value="View Report" /></td></tr>
        </table>
        </form>
    </div>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type ="text/javascript" src ="../assets/js/select2.min.js"></script>
<script type="text/javascript">

    $(document).on('submit','.scheme-frm',function(event){
        //alert("ghere");
        var dis=$(this);
        $.ajax({
           type:'POST',
           url:'get_testid.php',
        data:dis.serialize()
        }).done(function(msg){ //alert(msg);
            msg=($.trim(msg)-0);
            if(msg==0){
                alert("Test does not exist!");
            }
            else
            if(msg>0)
                {
                    window.location="<?php echo siteUrl("reports/reports.php?tid="); ?>"+msg;
                }
        });
        return false;
    });

    $(document).ready(function(){

        // Initialize select2
        $("#tcode").select2();
    });

</script>
</body>
</html>
