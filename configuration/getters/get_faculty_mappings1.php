<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!isset($_POST['schd'])) {
echo"Select a schedule!";
exit();
}
$output="<table class='style-tbl'><tr><th>S/N</th><th>Faculty Name</th><th>Mapping</th></tr>";
$sn = 1;
    $schd = clean($_POST['schd']);
    $mapped_fac=get_mapped_faculty_as_array($schd);
    $faculties = get_faculty_as_array();
    foreach($faculties as $faculty){
        $checked="";
        if(in_array($faculty['facultyid'], $mapped_fac)){
            $checked="checked";
        }
        $output.="<tr><td>$sn</td><td>".$faculty['facultyname']."</td><td><input type='checkbox' name='fac[]' $checked value='".$faculty['facultyid']."' /></td></tr>";
        $sn++;
    }
    
    $output.="</table> <br /><input style='margin-left:200px;' type='submit' name='map_fac' id='map_fac' value='Save Changes' />";
    
    echo $output;

?>