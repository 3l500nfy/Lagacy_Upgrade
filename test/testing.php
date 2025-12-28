<?php
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

//require_once("../lib/test_config_func.php");
openConnection();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
if(testopened($testid=5)==true){
echo "true";}
else {echo "false";}
?>
