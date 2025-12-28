<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();

if (!isset($_POST['tid']) || !isset($_POST['versions']) || clean($_POST['versions']) == "" || (!isset($_POST['active-v']) && !isset($_POST['activev']))) {
    echo 5;
    exit();
}

$tid = clean($_POST['tid']);

if (!is_test_administrator_of($tid)) {
    echo 2;
    exit();
}

$ver = clean($_POST['versions']);
$active_ver = (isset($_POST['active-v']) ? (clean($_POST['active-v'])) : (clean($_POST['activev'])));

$test_config = get_test_config_param_as_array($tid);

if (date_exceeded($tid, 0, "highest") && isset($_POST['safemode'])) {
    echo 4;
    exit();
}

//compare previous and new test versions
$old_test_versions = $test_config['versions'];
$new_test_versions = $ver;
if ($old_test_versions > $new_test_versions) {
    if (!isset($_GET['displace'])) {
        echo 3;
        exit();
    } else {
        if (date_exceeded($tid, 0)&& isset($_POST['safemode'])) {
            echo 6;
            exit();
        }

        $tsections = get_test_sections_as_array($tid);
        $tsections[] = 0;
        $tsections_str = trim(implode(",", $tsections), " ,");
        try {
            $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, array(
                        PDO::ATTR_PERSISTENT => true
                    ));
        } catch (PDOException $e) {

            echo 0;
            exit();
        }

        try {
            $dbh->beginTransaction();
            $sql = "update tbltestconfig set versions='$ver', activeversion = '$active_ver' where testid='$tid'";
            $query = $dbh->exec($sql);
            if ($query != 1 && $query !== 0) {
                $dbh->rollBack();
                echo 0;
                exit();
            }

            $sql2 = "delete from tbltestquestion where version > '$ver' && testsectionid in ($tsections_str)";
            $query2 = $dbh->exec($sql2);
            if ($query2 != 1 && $query2 !== 0) {
                $dbh->rollBack();
                echo 0;
                exit();
            }

            $dbh->commit();
            echo 1;
            exit();
        } catch (PDOException $e) {
            $dbh->rollBack();
            echo 0;
            exit();
        }
    }
}

$query = "update tbltestconfig set versions=?, activeversion = ? where testid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($ver,$active_ver,$tid));

if ($stmt->rowCount() > 0) {
    echo 1;
    exit();
} else {
    echo 0;
    exit();
}
?>