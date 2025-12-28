<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();
global $dbh;
if (!has_roles(array("Test Compositor", "Test Administrator")))
    header("Location:" . siteUrl("403.php"));

$qid = $_POST['qid'];

$query = "select * from tblquestionbank where questionbankid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($qid));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="ql" style="padding:10px;">
    <?php
    echo html_entity_decode($row['title'], ENT_QUOTES);
    ?>
    <div class="q">
        <?php
        $query1 = "select * from tblansweroptions where questionbankid=?";
        $stmt1=$dbh->prepare($query1);
        $stmt1->execute(array($qid));

        echo"<ol style='list-style-type:upper-alpha'>";
        while ($row3 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . html_entity_decode($row3['test'], ENT_QUOTES);
            echo"</li>";
        }
        echo"</ol>";
        ?>
    </div>
</div>