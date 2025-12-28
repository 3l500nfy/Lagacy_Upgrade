<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
authorize();
if (!isset($_POST['tid'])) {
    header("Location:".siteUrl("403.php"));
    exit();
}
if (!isset($_POST['sbjid'])) {
    header("Location:".siteUrl("403.php"));
    exit();
}

if (!isset($_POST['sectionid'])) {
    header("Location:".siteUrl("403.php"));
    exit();
}

$testid = clean($_POST['tid']);
$sbjid = clean($_POST['sbjid']);
$safemode= ((isset($_GET['safemode']))?("1"):("0"));
if (!is_test_administrator_of($testid) && !is_test_compositor_of($testid, null, $sbjid))
    header("Location:".siteUrl("403.php"));

if (date_exceeded($testid,0, "highest") && $safemode ==1) {
    echo"<div class='alert-error' style='margin-top:50px; width:200px; text-align:center; margin-left:auto; margin-right:auto;'>Test date exceeded!</div>";
    exit();
}

$sectionid = clean($_POST['sectionid']);
$diff_lvl = ((isset($_POST['diff-lvl'])) ? (clean($_POST['diff-lvl'])) : (""));
$author = ((isset($_POST['author'])) ? (clean($_POST['author'])) : (""));
$topic = ((isset($_POST['topic'])) ? (clean($_POST['topic'])) : (""));
$phrase = ((isset($_POST['phrase'])) ? (trim($_POST['phrase'])) : (""));
$version = clean($_POST['version']);
$page = clean($_GET['page']);
$pglimit = 50;

$test_config = get_test_config_param_as_array($testid);


$sql = "select * from tblquestionbank where subjectid=?";
if ($diff_lvl != "")
    $sql.=" && difficultylevel='$diff_lvl'";
if ($topic != "")
    $sql.=" && topicid=$topic";
if ($author != "") {
    if ($author == 'me')
        $sql.=" && author = " . $_SESSION['MEMBER_USERID'];
    else
        $sql.=" && author <> " . $_SESSION['MEMBER_USERID'];
}
$sql.=" && active =1";

//echo $sql2;
$stmt= $dbh->prepare($sql);
$stmt->execute(array($sbjid));

$numrecords = $stmt->rowCount();

if ($numrecords == 0) {
    echo "No record found.";
    exit();
}
if ($phrase != "") {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, 0)) {
        $qt = html_entity_decode($row['title'], ENT_QUOTES);
        $phrase_arr = explode(" ", $phrase);
        $qt2 = strip_tags($qt);
        for ($j = 0; $j < count($phrase_arr); $j++) {
            $v = trim(htmlspecialchars($phrase_arr[$j], ENT_QUOTES));

            if ($v != "" && (stripos($qt2, $v) || stripos($qt2, $v) === 0)) {

                continue;
            }
            if ($j == count($phrase_arr) - 1)
                $numrecords--;
        }
    }
}

if ($numrecords == 0) {
    echo "No record found.";
    exit();
}

//echo $numrecords;
//mysql_data_seek($query, 0);
$pgcount = ceil($numrecords / $pglimit);
$startpg = (($page - 1) * $pglimit);
$endpg = $startpg + $pglimit - 1;
$i = 1;


$i = 0;
$lower = $startpg + 1;
$upper = ((($lower + $pglimit - 1) > $numrecords) ? ($lower + ($numrecords - $lower)) : ($lower + $pglimit - 1));
echo "<span style='color:green;'><i>Showing " . ($lower) . " - " . ($upper) . " of $numrecords question(s)</i></span> <input type='submit' class='q_select_multi' id='q_select_all' value='Select all on this page' /> <input type='submit' class='q_select_multi' id='q_deselect_all' value='Deselect all on this page' />";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $qt = html_entity_decode($row['title'], ENT_QUOTES);
    if ($phrase != "") {
        $phrase_arr = explode(" ", $phrase);
        $qt2 = strip_tags($qt);
        for ($j = 0; $j < count($phrase_arr); $j++) {
            $v = trim(htmlspecialchars($phrase_arr[$j], ENT_QUOTES));
            if ($v != "" && (stripos($qt2, $v) || stripos($qt2, $v) === 0)) {
                break;
            }
        }
        if ($j == count($phrase_arr))
            continue;
    }
    if ($i < $startpg || $i > $endpg) {
        $i++;
        continue;
    }
    ?>
    <div class="ql" style="padding:10px;">
        <?php
        echo $qt;
        ?>&nbsp;&nbsp;&nbsp;[<a href="javascript:void(0);" id="m<?php echo $row['questionbankid']; ?>" class="fullquest" >Full question...</a>]<br /><br />

        <div class="qvinfo">
            <b>Difficulty Level: </b><?php
    if ($row['difficultylevel'] == 'simple')
        echo " Easy"; else if ($row['difficultylevel'] == 'difficult')
        echo " Moderate"; else
        echo " Difficult";
        ?> <br /><b>Versions on this test used:</b> <?php $vused = get_test_version_used_as_array($sectionid, $row['questionbankid'], $version);
        echo ((count($vused) > 0) ? (trim(implode(", ", $vused), " ,")) : ("none")); ?>
        </div>
        <div class="q">
            <div style="display:inline-block;">  <a href="javascript:void(0);" id="<?php echo $row['questionbankid']; ?>" class="moreinf">Show usage history...</a></div>
            <div style="text-align:right; display:inline-block; width:700px;"><span class="progress" style="display:none;">Processing...</span>
                <?php
                $sql3 = "select * from tbltestquestion where testsectionid=? && version= ? && questionbankid= " . $row['questionbankid'];
                $stmt3=$dbh->prepare($sql3);
                $stmt3->execute(array($sectionid,$version));

                $countquest = $stmt3->rowCount();
                if ($countquest > 0){
                    $ck = "checked='checked'";
                    if (date_exceeded($testid,0, "highest") && $safemode ==1) {
                        $ck .= " disabled";
                    }
                }
                else
                    $ck = "";
                ?>
                <label for="q<?php echo $i ?>"><?php
                if ($ck == '')
                    echo"Select"; else
                    echo"Deselect";
                ?></label> <input class="q_select" type="checkbox"  <?php echo $ck; ?> name="q<?php echo $i ?>" id="q<?php echo $i ?>" value="<?php echo $row['questionbankid']; ?>"/>
            </div>
            <div class="use_hist" id="uh<?php echo $row['questionbankid']; ?>">
                <?php
                $sql4 = "select session from tbltestquestion inner join tbltestsection on (tbltestquestion.testsectionid= tbltestsection.testsectionid)
                inner join tbltestsubject on (tbltestsection.testsubjectid=tbltestsubject.testsubjectid) inner join 
                tbltestconfig on (tbltestconfig.testid = tbltestsubject.testid) where tbltestsubject.testid<>? && tbltestquestion.questionbankid =" . $row['questionbankid'];
                $stmt4=$dbh->prepare($sql4);
                $stmt4->execute(array($testid));

                if ($stmt4->rwoCount() == 0)
                    echo "No usage history available yet.";
                else {
                    $u = 0;
                    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                        if ($u == 0)
                            echo $row4['session'];
                        else
                            echo " ," . $row4['session'];
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    $i++;
}
echo"<div id='pgkeydiv'>";
if ($pgcount < 11 && $pgcount != 1) {
    if ($page == 1) {
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
    }
}
else if ($pgcount != 1 && $pgcount > 10) {
    if ($page == 1) {
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
    }
}
echo"</div>";
?>