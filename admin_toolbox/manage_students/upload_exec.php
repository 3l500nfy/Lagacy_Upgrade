
<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();

ini_set("memory_limit", "256M") + ini_set('max_execution_time', 300);

extract($_POST);
$error = array();
$extension = array("jpg","png");
$path="../../picts";
foreach ($_FILES["files"]["tmp_name"] as $key => $tmp_name) {
    $file_name = $_FILES["files"]["name"][$key];
    $file_tmp = $_FILES["files"]["tmp_name"][$key];
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);

    if (in_array($ext, $extension)) {
        if (!file_exists($path.'/'.$file_name)) {
            move_uploaded_file($file_tmp = $_FILES["files"]["tmp_name"][$key], $path."/".$file_name);
        } else {
            @unlink($path.'/'.$file_name);
            $filename = basename($file_name, $ext);
            $newFileName = $filename . $ext;
            move_uploaded_file($file_tmp = $_FILES["files"]["tmp_name"][$key], $path."/".$newFileName);
//            echo '<span id="noerror">Image uploaded successfully!.</span><br/><br/>';
        }
//        echo '<span id="noerror">Image uploaded successfully!.</span><br/><br/>';

    } else {
//        array_push($error, "$file_name, ");
        echo "<span style='color:red;'>Error: this not a supported format or something went wrong</span>";
    }

}
echo '<span style="color:green;">Images uploaded successfully!.</span><br/><br/>';

?>

