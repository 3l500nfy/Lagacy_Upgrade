<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator"))) {
    echo"<i>Assess Denied!</i>";
    exit();
}

if (!isset($_GET['testid'])) {
    echo"Test not set!";
    exit();
}
$testid = clean($_GET['testid']);


$test_subjects = get_test_subjects_as_array($testid);
if (count($test_subjects) > 0) {
    ?>

    <table class="style-tbl" style=""><tr><th>S/N</th><th>Subject Code</th><th>Subject name</th><th>Remove</th></tr>
        <?php
        $c = 1;
        foreach ($test_subjects as $test_subject) {
            $query = "select * from tblsubject where subjectid=?";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($test_subject));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $sbj_code = $row['subjectcode'];
            $sbj_name = $row['subjectname'];
            $sbj_id = $row['subjectid'];

            echo"<tr><td>" . $c++ . "</td><td>" . strtoupper(trim($sbj_code)) . "</td><td>" . intelligentStr(ucwords($sbj_name), 50) . "</td><td>[<a class='del-sbj' data-sbjid='" . $sbj_id . "' href='javascript:void(0);'>remove</a>]</td></tr>";
        }
        ?>
    </table>
    <?php
}
?>