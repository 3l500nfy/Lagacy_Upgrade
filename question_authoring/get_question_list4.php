<?php

if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("authoring_functions.php");
openConnection();

$sbj = $_POST['sbj'];
$topicid = $_POST['topicid'];
$dlevel = $_POST['dlevel'];
$dtfro = $_POST['fro'];
$dtto = $_POST['to'];
$recperpage=$_POST['recperpage'];
$page=$_POST['page'];

if ($dlevel == "all") {
    if ($dtfro != "" && $dtto != "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " &&  author=" . $_SESSION['MEMBER_USERID'] . " && (DATE(questiontime) ='$dtfro' || DATE(questiontime) > '$dtfro') && (DATE(questiontime) ='$dtto' || DATE(questiontime) < '$dtto') order by questiontime desc";
    else
    if ($dtfro != "" && $dtto == "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " &&  author=" . $_SESSION['MEMBER_USERID'] . " && (DATE(questiontime) ='$dtfro' || DATE(questiontime) > '$dtfro') order by questiontime desc";
    else
    if ($dtfro == "" && $dtto != "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " &&  author=" . $_SESSION['MEMBER_USERID'] . " && (DATE(questiontime) ='$dtto' || DATE(questiontime) < '$dtto') order by questiontime desc";
    else
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " &&  author=" . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
} else {
    if ($dtfro != "" && $dtto != "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID'] . "  && (DATE(questiontime) ='$dtfro' || DATE(questiontime) > '$dtfro') && (DATE(questiontime) ='$dtto' || DATE(questiontime) < '$dtto') order by questiontime desc";
    else
    if ($dtfro != "" && $dtto == "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID'] . " && (DATE(questiontime) ='$dtfro' || DATE(questiontime) > '$dtfro') order by questiontime desc";
    else
    if ($dtfro == "" && $dtto != "")
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID'] . " && (DATE(questiontime) ='$dtto' || DATE(questiontime) < '$dtto') order by questiontime desc";
    else
        $query = "select * from tblquestionbank where subjectid='$sbj' " . (($topicid != "") ? ("&& topicid='$topicid'") : ("")) . " && difficultylevel = '$dlevel' && author=" . $_SESSION['MEMBER_USERID'] . " order by questiontime desc";
}

if(!isset($_POST['reccount'])){
    $stmt=$dbh->prepare($query);
    $stmt->execute();
    $reccount=$stmt->rowCount();
}
else{
    $reccount=$_POST['reccount'];
}

$pgcount = ceil($reccount / $recperpage);

$query.=" limit ".($page * $recperpage).", ". ($recperpage);
$stmt1=$dbh->prepare($query);
$stmt1->execute();

if ($stmt1->rowCount() == 0) {
    echo "No question found.";
    exit();
}

$page_1= $page +1;

//pagination
echo"<div id='pgkeydiv'>";
if ($pgcount < 11 && $pgcount != 1) {
    if ($page_1 == 1) {
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page_1 < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page_1 == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
    }
}
else if ($pgcount != 1 && $pgcount > 10) {
    if ($page_1 == 1) {
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page_1 < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page_1 == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
    }
}
echo"</div>";

if($reccount>0){
echo"Select all <input type='checkbox' value='selall' name='selall' id='selall' />";
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $q = stripslashes($row['title']);
    $qid = $row['questionbankid'];
    echo "<div class='qdiv'><label> <input style='padding:0px; margin:0px; border-width:0px;' type='checkbox' value='$qid' name='qsel[]' class='sel' /> <div style='display:inline-block'>" . html_entity_decode($q, ENT_QUOTES) . "</div> </label></div>";
}
echo"<input type='hidden' name='reccount' id='reccount' value='$reccount' /> <input type='submit' name='map' id='map' value='Preview Selected'/>";

//pagination
echo"<div id='pgkeydiv'>";
if ($pgcount < 11 && $pgcount != 1) {
    if ($page_1 == 1) {
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page_1 < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
        echo"<div class='pgkey'>Next</div>";
    }
    else if ($page_1 == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<div class='curr'>$j</div>";
            else
                echo"<div class='pgkey'>$j</div>";
    }
}
else if ($pgcount != 1 && $pgcount > 10) {
    if ($page_1 == 1) {
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page_1 < $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
        echo"<div class='pgkey'>Next</div>";
    }
    else
    if ($page_1 == $pgcount) {
        echo"<div class='pgkey'>Previous</div>";
        echo"<select name='pgsel' id='pgsel'>";
        for ($j = 1; $j <= $pgcount; $j++)
            if ($j == $page_1)
                echo"<option value='$j' selected='selected'>$j</option>";
            else
                echo"<option value='$j'>$j</option>";
        echo"</select>";
    }
}
echo"</div>";

?>