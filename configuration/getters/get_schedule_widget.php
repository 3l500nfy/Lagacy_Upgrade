<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator"))) {
    echo"<i>Access denied!</i>";
    exit();
}
$vid=((isset($_POST['vid']))?(clean($_POST['vid'])):(0));
$tid=((isset($_POST['tid']))?(clean($_POST['tid'])):(0));
$tdt=((isset($_POST['tdt']))?(clean($_POST['tdt'])):('0000-00-00'));

$venuecapacity=get_venue_capacity($vid);

echo"<br /><br />
    <div><table>
    <tr><td><b>No of batches for this schedule: </b> </td><td><input type='text' class='numeric-input' name='batchcount' id='batchcount' value='1' placeholder='Ignore if no limit' /><br /></td></tr>
    
    <tr><td><b>No of candidates per batch: </b> </td><td><input type='text' class='numeric-input' data-old='".$venuecapacity."' data-max='".$venuecapacity."' name='noperbatch' id='noperbatch' value='".$venuecapacity."' placeholder='Maximum of ".$venuecapacity."' /><br /></td></tr>
    
    <tr><td><b>Daily Start Time: </b> </td><td><input type='text' name='dailystarttime' placeholder='hh:mm' id='dailystarttime' value='00:00'/><br /></td></tr>
    
    <tr><td><b>Daily End Time: </b> </td><td><input type='text' placeholder='hh:mm' name='dailyendtime' id='dailyendtime'value='23:59'/><br /></td></tr>
    
    </table>
    <div id='clashdiv'></div>
    <div id='msg'></div>
    </div>";
?>