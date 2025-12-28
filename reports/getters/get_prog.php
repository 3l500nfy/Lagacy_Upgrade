<?php 
require_once("../../lib/globals.php");
require_once("../../lib/security.php");

openConnection();
$did= $_POST['deptid'];

$progs = get_programme_as_array($did);

foreach ($progs as $prog){
    echo "<option value='".$prog['programmeid']."'>".$prog['programmename']."</option>";
}

?>