<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator")))
    header("Location:" . siteUrl("403.php"));
if (!isset($_GET['scheme']))
    $scheme = 'regular';
else
    $scheme = clean($_GET['scheme']);
?>
<html lang="en">
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>
</head>

<form id="test-initial-frm" name="test-initial-frm" class="style-frm">
    <?php
    if ($scheme == 'regular') {
        ?>
        <table id="test-initial-tbl">
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
                    <select name="init-testcode" id="subj"><option value="">--Select--</option><?php
                    $sql = "select * from tbltestcode where testcodeid <>'1' && testcodeid <>'2' && testcodeid<>'12'";
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
            <tr><td></td><td><input type="submit" name="test-initial-submit" id="test-initial-submit" value="Open Test" /></td></tr>
        </table>

        <?php
    } else
    if ($scheme == 'post-utme') {
        ?>    <table id="test-initial-tbl">
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
                </td><td><input type="submit" name="test-initial-submit" id="test-initial-submit" value="Open Test" /></td></tr>
        </table>

        <?php
    }
    else
    if ($scheme == 'sbrs') {
        ?>    
       
            <table id="test-initial-tbl">
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
                            $stmt2=$dbh->prepare($sql2);
                            $stmt2->execute();

                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $row2['testtypeid'] . "'>" . $row2['testtypename'] . "</option>";
                        }
        ?></select></td></tr>
                <tr><td>
                        <input type="hidden" name="init-testcode" id="init-testcode" value="2"/>
                    </td><td><input type="submit" name="test-initial-submit" id="test-initial-submit" value="Open Test" /></td></tr>
            </table>

            <?php
        } else
        if ($scheme == 'sbrs-new') {
            ?>
            <table id="test-initial-tbl">
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
                        $sql3="select testcodeid from tbltestcode where testname='SBRS-NEW' limit 1";
                        $stmt3=$dbh->prepare($sql3);
                        $stmt3->execute();

                        $row3= $stmt3->fetch(PDO::FETCH_ASSOC);
                        $tcid=$row3['testcodeid'];
                        ?>
                                            <input type="hidden" name="init-semester" id="init-semester" value="0" />
                    <input type="hidden" name="init-testcode" id="init-testcode" value="<?php echo $tcid; ?>" />
                    <input type="hidden" name="init-testtype" id="init-testtype" value="1" />

                    </td><td><input type="submit" name="test-initial-submit" id="test-initial-submit" value="Open Test" /></td></tr>
            </table>

        <?php } ?>
</form>
<script type ="text/javascript" src ="../../assets/js/jquery-1.7.2.min.js"></script>
<script type ="text/javascript" src ="../../assets/js/select2.min.js"></script>
<script type="text/javascript">

    $("#subj").select2();

</script>