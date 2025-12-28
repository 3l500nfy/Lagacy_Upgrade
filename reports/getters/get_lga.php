<?php 
require_once("../../lib/globals.php");
require_once("../../lib/security.php");

openConnection();
$st= $_POST['stateid'];

$lgas = get_lga_as_array($st);

foreach ($lgas as $lga){
    echo "<option value='".$lga['lgaid']."'>".$lga['lganame']."</option>";
}

?>