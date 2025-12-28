<?php if(!isset($_SESSION)) session_start ();
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();

$sbj=$_POST['subj'];
if(isset($_POST['addgen']))
get_topics_as_options($sbj, "", true);
else
get_topics_as_options($sbj, "", false);    
?>