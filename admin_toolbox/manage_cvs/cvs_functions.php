<?php

if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");

openConnection();

function get_venue_name($venueid) {
    global $dbh;
    $query = "select venuename, location from tblvenue where venueid=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($venueid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row['venuename']);
}

function get_centre_id($venueid) {
    global $dbh;
    $query = "select centreid from tblvenue where venueid=$venueid";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($venueid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['centreid'];
}

function get_center_as_options($vid = "") {
    global $dbh;
    $output = "";
    if ($vid != "")
        $cid = get_centre_id($vid);
    else
        $cid = "";
    $query = "select * from tblcentres";
    $stmt=$dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ct = $row['centrename'];
        $ctid = $row['centreid'];
        if ($ctid == $cid)
            $output .="<option value='$ctid' selected>$ct</option>";
        else
            $output .="<option value='$ctid'>$ct</option>";
    }
    return $output;
}
?>