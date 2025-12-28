<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();
$uid = $_SESSION['MEMBER_USERID'];

$tid=$_POST['testid'];
$as=array();

?>
<select style="margin-left: 10px;" name="tdt" id="tdt2"><option value="">Select a Date</option>
                        <?php 
                        $query="select date from tblexamsdate where testid=?";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute(array($tid));

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            $datstr = new DateTime($row['date'] . " 00:00:00");
                         echo"<option value='".$row['date']."'>".date_format($datstr, "D, jS M Y")."</option>";
                        }
                        ?>
                    </select>