<?php 
require_once("../../lib/globals.php");
require_once("../../lib/security.php");

openConnection();
$facid= $_POST['facid'];

$depts = get_department_as_array($facid);

foreach ($depts as $dept){
    echo "<option value='".$dept['departmentid']."'>".$dept['departmentname']."</option>";
}

?>