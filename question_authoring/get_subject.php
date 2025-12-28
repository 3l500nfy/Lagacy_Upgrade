<?php
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();
$sbjcat=$_POST['subjcat'];
get_subject_as_options($sbjcat);
?>