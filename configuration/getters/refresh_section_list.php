<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
if (!isset($_GET['testid'])) {
    echo"Test not set!";
    exit();
}
$testid = clean($_GET['testid']);
$sbjid = clean($_GET['sbjid']);
$safemode=TRUE;

if(isset($_GET['safemode']) && $_GET['safemode']!= 1){
    $safemode= FALSE;
}
$subject_sections = get_test_sections_as_array($testid, $sbjid);
?>

<legend>Available section(s) in <?php echo strtoupper(get_subject_code($sbjid)); ?></legend>
<table class="style-tbl">
    <tr><th>S/N</th><th>SECTION TITLE</th><th>MARK PER QUEST.</th><th>NO. TO ANSWER</th><th>TOTAL MARK</th><th>ACTION</th></tr>
    <?php
    $c = 1;
    //echo count($subject_sections);
    foreach ($subject_sections as $subject_section) {
        $query = "select * from tbltestsection where testsectionid=?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($subject_section));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $section_title = $row['title'];
        $section_id = $row['testsectionid'];
        $markperquestion = $row['markperquestion'];
        $numtoanswer = $row['num_toanswer'];
        $totalmark = $markperquestion * $numtoanswer;
        echo"<tr><td>" . $c++ . "</td><td>$section_title</td><td>$markperquestion</td><td>$numtoanswer</td><td>$totalmark</td><td>[<a href='section_compose.php?tid=" . $testid . "&sbjid=" . $sbjid . "&sectionid=$subject_section"."&".(($safemode)?("safemode=1"):("safemode=0"))."'>compose</a>][<a href='section_modify.php?tid=" . $testid . "&sbjid=" . $sbjid . "&sectionid=" . $subject_section."&".(($safemode)?("safemode=1"):("safemode=0"))."'>modify</a>][<a class='remove-section' data-sectionsbj='" . $sbjid . "' data-sectionnm='" . $section_title . "' data-sectionid='" . $subject_section . "' href='javascript:void(0);'>remove</a>]</td></tr>";
    }
    if ($c == 1)
        echo"<tr><td colspan='6'>No section available.</td></tr>";
    ?>
</table>