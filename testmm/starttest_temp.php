<?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("testfunctions.php");
openConnection(true);
global $dbh;
//authorize();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}

//check if the candidate has taken and cmpleted the test
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];

$completion = checkcompletion($testid, $candidateid);

if ($completion == true) {
    $_SESSION['LOGIN_FAILED'] = "You have already taken the selected exams.";
    session_write_close();
    header("location: login.php");
    exit();
}


if (!isset($_SESSION['seequestion'])) {
    $_SESSION['seequestion'] = 1;
}
//get biodata
$biodata = array();
if (!isset($_SESSION['biodata'])) {
    $biodata = getbiodata($candidateid);
} else {
    $biodata = $_SESSION['biodata'];
}
$candidatename = $biodata['candidatename'];
$matric = $biodata['matric'];

//get the passport
$pict = "";
if (file_exists("../picts/$matric.jpg")) {
    $pict = "$matric";
} else {
    $pict = "nopicture";
}

//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];

$testinfo = array();
if (!isset($_SESSION['testinfo'])) {
    $testinfo = gettestinfo($testid);
} else {
//update remaining time in case of refresh
    $elapsed = timecontrol($testid, $candidateid, $waitingsecond = 30);
    $_SESSION['testinfo']['remainingsecond'] = $_SESSION['testinfo']['duration'] * 60 - $elapsed;


    $testinfo = $_SESSION['testinfo'];
//echo now();
    //  echo"elapsed".$elapsed;
    //      echo"remaining".$_SESSION['testinfo']['remainingsecond'];
}
$questionadministration = $testinfo['questionadministration'];
$optionadministration = $testinfo['optionadministration'];

//if it is the fisrt time he his login in, create his set of question based on the exams configuraion and store in presentation
//first check if the question exist already and the total number is equal to the specified number in the configuration and the
//questions are not yet answered.

$query1 = "SELECT count(distinct(questionid)) as totalquestions FROM tblpresentation where(candidateid=$candidateid and testid=$testid)";
$stmt=$dbh->prepare($query1);
$stmt->execute();
$row=$stmt->fetch(PDO::FETCH_ASSOC);
$totalquestions = $row['totalquestions'];
if ($totalquestions == 0) {
//create the questions. the question for the student is not yet created

    createcandidatequestions($testid, $candidateid, $questionadministration, $optionadministration);
} else {
    //already created check if the number matches the specified number in testconfifuration
    $query2 = "SELECT sum(num_toanswer) as totaltoanswer FROM tbltestsection
	INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid 	 
	where(testid='$testid' and tbltestsubject.subjectid in
	(select subjectid from tblcandidatestudent inner join tblscheduling on 
	tblcandidatestudent.scheduleid=tblscheduling.schedulingid where(testid='$testid' and candidateid='$candidateid')) )";
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute();
    $row1=$stmt1->fetch(PDO::FETCH_ASSOC);
    $totaltoanswer = $row1['totaltoanswer'];
    if ($totalquestions != $totaltoanswer) {

        // the total number of questions the student is to answer does not match the one specified.
        //if he has not started answering the questions, just recreate
        $query3 = "SELECT * FROM tblscore where (candidateid=$candidateid and testid=$testid)";
        $stmt2=$dbh->prepare($query3);
        $stmt2->execute();
        if ($stmt2->rowCount() <= 0) {
            //the candidate has not started answering. delete and recreate the question
            $query4 = "delete from tblpresentation where(candidateid=$candidateid and testid=$testid)";
            $stmt3=$dbh->prepare($query4);
            $stmt3->execute();
            //create question
            createcandidatequestions($testid, $candidateid, $questionadministration, $optionadministration);
        }
    }
}
$elapsed = timecontrol($testid, $candidateid, $waitingsecond = 30);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT</title>
<?php require_once("../partials/cssimports.php") ?>

        <link type="text/css" href="../assets/css/tconfig.css" rel="stylesheet"></link>

        <link rel="stylesheet" type="text/css" href="jquery.countdown/jquery.countdown.css"> 
        <script type="text/javascript" src="jquery.countdown/jquery.countdown.js"></script>
        <style type='text/css'>
            .tbnnavigation
            {
                -moz-border-radius:5px;
                -webkit-border-radius:5px;
                border-radius:5px;
                margin:3px;
                font-weight:bold;
                font-size:2em;
                padding:10px;

            }
            #subjectinfodiv
            {
                text-decoration:italics;
                font-style:arial;
                font-size:1em;

            }	

            .sectiondiv
            {
                padding:10px; 
                margin-top:20px;

            }	

            .questionanswerdiv{
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                border-radius:3px;
                border-width:1px;
                border-color:#e5f7e6;
                border-style:solid;
                background-color:#f5fbf5;
                margin-top:10px;
                padding:10px;
            }
            .questiondiv
            {
                padding-top:10px;
                padding-bottom:20px;
            }
            #answerdiv{
                -moz-border-radius:5px;
                -webkit-border-radius:5px;
                border-radius:5px;
                border-style:solid;
                border-color:#cccccc;
                border-width:1px;
                padding:5px;
                background-color:#f2f2f2;

            }

            .ansopt{
                list-style-type:upper-alpha;
            }

            .loader{
                position: fixed;
                top: 50%;
                left: 50%;
                margin-left: -50px; /* half width of the loader gif */
                margin-top: -50px; /* half height of the loader gif */
                text-align:center;
                z-index:9999;
                overflow: auto;
                width: 100px; /* width of the loader gif */
                height: 102px; /*hight of the loader gif +2px to fix IE8 issue */

            }

            .selected-opt{
                background-image:url(select.png); background-repeat:repeat; border-style:solid; border-width:1px; border-color:blue;
            }

            #qnav{
                position:fixed;
                top:50px;
                left:10px;
                padding:1px;
                border-width:1px;
                border-style: solid;
                border-color: #6aa64c;
                min-height: 100px;
                width: 100px;
                background-color: #7aba7b;
                z-index: 9999;
            }
            .qbut{

                display:inline-block;
                border-style:solid;
                border-color:lightblue;
                border-width: 1px;
                border-radius: 3px;
                -moz-border-radius:3px;
                -o-border-radius: 3px;
                -webkit-border-radius: 3px;
                -ms-border-radius:3px;
                background-color:oldlace;
                color:#000000;
                min-width: 10px;
                padding:2px;
                margin-left:1px;
                margin-bottom: 1px;
                font-size: 8pt;
                cursor:pointer;
            }
        </style>

    </head>

    <body>
<?php
include_once "starttestnavbar.php";
?>

        <br /><br /><br /><br /><br />
        <div id="container"  class="container" style="visibility:hidden">


            <div id="loader" class="loader" style="display:none;">
                <img id="img-loader" src="loader.gif" alt="Loading..."/>
            </div>


            <br /><br />
            <form id="subselfrm" name="subselfrm" action="#" method="post">
                <div id="sel">
                    <table>
                        <tr>
                        </tr>
                    </table>
                </div>
                <br />
            </form>
        </div><!-- /container -->
        <input id="reloadValue" type="hidden" name="reloadValue" value="" />

        <div id="qnav" style="visibility:hidden">

            <p style="text-align:center; color:#ffffff; font-size:10px; font-weight:bold;">NAVIGATION PANEL</p>

        </div>
<?php include_once "../partials/footer.php" ?>;
<?php include_once "../partials/jsimports.php" ?>;
        <script language="JavaScript">
<!--


            var finishedtext = "Completed!";// text that appears when the countdown reaches 0

            var remainingsecond = ($("#remainingsecond1").val());
//alert(remainingsecond);
            remainingsecond = parseInt(remainingsecond);


            function cd() {
                remainingsecond = ($("#remainingsecond1").val());
                remainingsecond = parseInt(remainingsecond);
                remainingsecond = remainingsecond - 1;
                document.getElementById("remainingsecond").innerHTML = remainingsecond;
                document.getElementById("remainingsecond1").value = remainingsecond;

//alert(remainingsecond);
                min = Math.floor(remainingsecond / 60);
                sec = remainingsecond % 60;

                if (min < 10) {
                    min = "0" + min;
                }
                if (sec < 10) {
                    sec = "0" + sec;
                }

                if (remainingsecond <= 0) {
                    clearTimeout(timerID);
                    document.getElementById("cdtime").innerHTML = finishedtext;
                    ///////

                    $.ajax({
                        type: 'POST',
                        url: 'updatetime.php',
                        data: {endpoint: '1'}
                    }).done(function(msg) {
                        //alert(msg);
                        //////////////////////////////////////
                        $('<div></div>').appendTo('body')
                                .html('<div><h4>Your time is over! Logout please</h4></div>')
                                .dialog({
                            modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                            width: 'auto', resizable: true,
                            buttons: {
                                Logout: function() {
                                    window.location.href = "logout.php";
                                    $(this).dialog("close");
                                }
                            },
                            close: function(event, ui) {
                                window.location.href = "logout.php";
                                $(this).remove();
                            }
                        });
                    });
                }
                else {
                    document.getElementById("cdtime").innerHTML = min + " Min:" + sec + " Sec";
                }
                timerID = setTimeout("cd()", 1000);
            }

            window.onload = cd;
//update server time
            setInterval(function() {
//alert("updated");

                $.ajax({
                    type: 'POST',
                    url: 'updatetime.php',
                    data: {endtime: 2}
                }).done(function(msg) {
//alert(msg);
                    if (msg == "end") {
                        window.location.href = "logout.php";
                    }
                    else {
                        var remain = parseInt(msg);
                        var remainingsecond = ($("#remainingsecond1").val());
                        remainingsecond = parseInt(remainingsecond);
                        if (remain < remainingsecond) {
                            document.getElementById("remainingsecond").innerHTML = remain;
                            document.getElementById("remainingsecond1").value = remain;
                        }
                    }
                });

            }, 1000 * 60 * 0.5);
            $(".subjbtn").bind('click', function(evt) {
                $('#container').css("visibility", "visible");
                $('#qnav').css("visibility", "visible");

//$('#container').show();
                var subjectid = $(this).attr('id');
                $.ajax({
                    type: 'POST',
                    error: function(jqXHR) {
                        //alert("Server not responding. Please retry!");
                        // $("#sel").html(msg);
                        $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');

                    },
                    url: 'loadquestion.php',
                    data: {subject: $(this).attr('id')}
                }).done(function(msg) {
                    $("#sel").html(msg);

//panael
                    $.ajax({
                        type: 'POST',
                        error: function(jqXHR) {
                            //alert("Server not responding. Please retry!");
                            // $("#sel").html(msg);
                            $("#qnav").html('<b>Error Loading the questions...</b>');

                        },
                        url: 'loadnavpanel.php',
                        data: {subject: subjectid}
                    }).done(function(msg) {
                        $("#qnav").html(msg);
                    });

                });
                return false;
            });
            $(document).on('click', ".tbnnavigation", function(evt) {
                $.ajax({
                    type: 'POST',
                    error: function(jqXHR) {
                        //alert("Server not responding. Please retry!");
                        $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');

                    },
                    url: 'loadquestion.php',
                    timeout: 30000,
                    data: {direct: $(this).attr('id')}
                }).done(function(msg) {

//alert(msg);
                    $("#sel").html(msg);
//disable previous and next if first and end

                });
                return false;
            });

            $(document).on('click', ".answeroption", function(evt) {

                var dis = $(this);
                var code = $(this).attr('name') + "code";
                var navpanelelt = "navpanel" + $(this).attr('name');
                var initialval = document.getElementById(code).value;
//var optpar=$(this).parent().parent().parent(".ansopt");
                $("#" + initialval).parent().removeClass("selected-opt");
                $(this).parent().addClass("selected-opt");
                var curentval = $(this).val();

                $.ajax({
                    type: 'POST',
                    error: function(jqXHR) {
//$(this).removeAttr('checked');
                        document.getElementById(curentval).checked = false;
//restore initial 
                        dis.parent().removeClass("selected-opt");
                        if (parseInt($.trim(initialval)) != 0) {
                            document.getElementById(initialval).checked = true;
                            $("#" + initialval).parent().addClass("selected-opt");
                        }
//alert(jqXHR.status+" " +jqXHR.statusText+" error");
                        alert("Your last answer wasn't saved because the server is not responding. Please retry!");

                    },
                    url: 'saveanswer.php',
                    timeout: 40000,
                    data: {question: $(this).attr('name'), ans: $(this).val()}
                }).done(function(msg) {
                    document.getElementById("remaining").innerHTML = $.trim(msg);
//let the curent selection be the selected
                    document.getElementById(code).value = curentval;
//update the navigation panel to set the question as answered
                    $("#" + navpanelelt).css('background-color', 'greenyellow');
                    $("#" + navpanelelt).css('background-image', 'url(tickIcon.png)');


                    if (parseInt($.trim(msg)) == 0) {
//////////////////////////////
                        $('<div></div>').appendTo('body')
                                .html('<div><h4>You have sucessfuly completed the exams!</h4>  <h6> would you like to review your work or to submit?</h6></div>')
                                .dialog({
                            modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                            width: 'auto', resizable: true,
                            buttons: {
                                Submit: function() {
                                    //////
                                    var comp = "1";
                                    $.ajax({
                                        type: 'POST',
                                        url: 'updatetime.php',
                                        data: {completion: comp}
                                    }).done(function(msg) {
                                        window.location.href = "logout.php";
                                    })
                                    /////

                                    $(this).dialog("close");
                                },
                                Review: function() {
                                    //document.getElementById("abandon").innerHTML ="<font size=4>|Log Out|</font>";

                                    $(this).remove();
                                }
                            },
                            close: function(event, ui) {
                                $(this).remove();
                            }
                        });
//////////////////////////////////

                    }
//alert(msg);
                });
//return false;
            });
//////////////////////

            $("#abandon").bind('click', function(evt) {
                var remainingquestion = ($("#remaining").text());
                remainingquestion = parseInt(remainingquestion);

                if (remainingquestion > 0) {
                    var msg = "You haven't answered all the questions(" + remainingquestion + " question(s) remaining)!";
                } else {
                    var msg = "You are about to make submission without exhausting your time!";

                }

                $('<div></div>').appendTo('body')
                        .html('<div><h4>' + msg + '</h4> <h6> Once you submit, you cannot come back to your work again<br>  Do you really want to submit?</h6></div>')
                        .dialog({
                    modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                    width: 'auto', resizable: true,
                    buttons: {
                        Submit: function() {
                            //////
                            var comp = "1";
                            $.ajax({
                                type: 'POST',
                                url: 'updatetime.php',
                                data: {completion: comp}
                            }).done(function(msg) {
                                window.location.href = "logout.php";
                            })
                            /////

                            $(this).dialog("close");
                        },
                        Cancel: function() {
                            $(this).remove();
                        }
                    },
                    close: function(event, ui) {
                        $(this).remove();
                    }
                });
            });
/////////////////////
            $(window).unload(function() {
                $.ajax({
                    type: 'POST',
                    url: 'updatetime.php',
                    data: {endtime: 2}
                }).done(function(msg) {
//alert(msg);
                    if (msg == "end") {
                        window.location.href = "logout.php";
                    }
                });
            });

            $("#loader").ajaxStart(function() {
                $(this).show();
            }).ajaxStop(function() {
                $(this).hide();
            });


            $(document).ready(function() {
                $("#loader").ajaxSend(function() {
                    $(this).show();
                }).bind("ajaxStop", function() {
                    $(this).hide();
                }).bind("ajaxError", function() {
                    $(this).hide();
                });

            });


            $(document).load(function() {
                alert("rr");
                $.ajax({
                    type: 'POST',
                    url: 'updatetime.php',
                    data: {endtime: 2}
                }).done(function(msg) {
//alert(msg);
                    if (msg == "end") {
                        window.location.href = "logout.php";
                    }
                    else {
                        var remain = parseInt(msg);
//  alert(remain);
                    }
                });

            });

//

            $(document).on('click', '.qbut', function(event) {
//get the question div id
//var divid=3;
                var questionid = $(this).attr('id').substring(8);
                var limit = $(this).attr('name').substring(8);
//alert(limit);
                var examstype = $("#displaymode").val();
                var divid = "questiondiv" + questionid;

                if ($.trim(examstype) == "All") {
// all question
                    var divid = "questiondiv" + questionid;
// $(document).scrollTop($("#"+divid).offset().top);
                    $(window).scrollTop($("#" + divid).offset().top);
                }
                else {
//single question display
                    $.ajax({
                        type: 'POST',
                        error: function(jqXHR) {
                            //alert("Server not responding. Please retry!");
                            $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');

                        },
                        url: 'loadquestion.php',
                        timeout: 30000,
                        data: {questionid: questionid, limit: limit, navigation: 1}
                    }).done(function(msg) {

//alert(msg);
                        $("#sel").html(msg);

                    });
////
                }
            });
            $("button").button();
        </script>
    </body>
</html>