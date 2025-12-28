<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

//require_once("../lib/test_config_func.php");
openConnection();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}

//check if the candidate has taken and cmpleted the test
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];

/* if(examsperiod($testid,$candidateid)==false){
  echo"$testid,$candidateid ff";exit();
  $_SESSION['LOGIN_FAILED'] = "Illegal exams time.";
  session_write_close();
  header("location: logout.php");
  exit();


  } */

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
    $pict = "$matric.jpg";
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
//echo $query1;
$result1 = mysql_query($query1);
$totalquestions = mysql_result($result1, 0, 'totalquestions');
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
    // echo $query2;
    $result2 = mysql_query($query2);
    $totaltoanswer = mysql_result($result2, 0, 'totaltoanswer');
//	 echo"stud=$totalquestions test=$totaltoanswer";
    if ($totalquestions != $totaltoanswer) {

        // the total number of questions the student is to answer does not match the one specified.
        //if he has not started answering the questions, just recreate
        $query3 = "SELECT * FROM tblscore where (candidateid=$candidateid and testid=$testid)";
        $result3 = mysql_query($query3);
        if (mysql_num_rows($result3) <= 0) {
            //the candidate has not started answering. delete and recreate the question
            $query4 = "delete from tblpresentation where(candidateid=$candidateid and testid=$testid)";
            mysql_query($query4);
            //create question
            createcandidatequestions($testid, $candidateid, $questionadministration, $optionadministration);
        }
    }
}
$elapsed = timecontrol($testid, $candidateid, $waitingsecond = 30);
$pgtitle = "::TEST";
require_once 'cbt_header2.php';
?>
<link href="<?php echo siteUrl("assets/css/startteststyle.css"); ?>" type="text/css" rel="stylesheet"></link>
<div id="container" style="">
    <div  id="sel" class="test-questions">

    </div>
    <div id="side-widget">
        <div id="abandon">
            SUBMIT
        </div>
        <div  class="test-time">
            <span  id="test-time-title" style="color:#999999; font-size: 10px; font-weight: normal;">COUNTDOWN (MIN:SEC)</span>
            <div id="test-time">
                00:00
            </div>
        </div>
        <div id="qnav">
            <div id="qnavtitle">NAVIGATIONS</div>

        </div>
    </div>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    var finishedtext = "Completed!";// text that appears when the countdown reaches 0

    var remainingsecond = ($("#remainingsecond1").val());
    //alert(remainingsecond);
    remainingsecond = parseInt(remainingsecond);
    var onwarning=false;
    var onalert=false;

    function cd() {
        remainingsecond = ($("#remainingsecond1").val());
        remainingsecond = parseInt(remainingsecond);
        remainingsecond = remainingsecond - 1;
        document.getElementById("remainingsecond").innerHTML = remainingsecond;
        document.getElementById("remainingsecond1").value = remainingsecond;

        //alert(remainingsecond);
        hr=Math.floor(remainingsecond/(60*60))
        min = (Math.floor(remainingsecond / 60)-(hr*60));
        sec = remainingsecond % 60;

        if (min < 10) {
            min = "0" + min;
        }
        if (hr < 10) {
            hr = "0" + hr;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }

        if (remainingsecond <= 0) {
            clearTimeout(timerID);
            document.getElementById("test-time").innerHTML = "00:00";
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
                /////////////////////////////////////



            });


    }
else {
    if(hr==0)
    {
        document.getElementById("test-time").innerHTML = min + ":" + sec;
        $("#test-time").css("fontSize",'38px');
        $("#test-time-title").text("COUNTDOWN (MIN:SEC)");
    }
    else
    {
        document.getElementById("test-time").innerHTML = hr+":"+min + ":" + sec;
        $("#test-time").css("fontSize",'30px');
        $("#test-time-title").text("COUNTDOWN (HR:MIN:SEC)");
    }
    if(remainingsecond<(60*3) && onalert==false){
        onalert=true;
        $(".test-time").css("backgroundColor",'#ffffff');
        window.setInterval(function(){
            $(".test-time").animate({backgroundColor:'#ff0000'}, 300,'swing', function(){$(".test-time").animate({backgroundColor:'#FFFFFF'}, 300);});
            $("#test-time-title").animate({color:'#FFFFFF'}, 300,'swing', function(){$("#test-time-title").animate({color:'#999999'}, 300);});
            $("#test-time").animate({color:'#FFFFFF'}, 300,'swing', function(){$("#test-time").animate({color:'#ff0000'}, 300);});
        }, 800);
    }
    else
        if(remainingsecond<(60*5) && onwarning==false){
            onwarning=true;
            $(".test-time").animate({backgroundColor:'#ff0000'}, 300,'swing', function(){$(".test-time").animate({backgroundColor:'#FFFFFF'}, 300);});
            $("#test-time-title").animate({color:'#FFFFFF'}, 300,'swing', function(){$("#test-time-title").animate({color:'#999999'}, 300);});
            $("#test-time").animate({color:'#FFFFFF'}, 300,'swing', function(){$("#test-time").animate({color:'#ff0000'}, 300);});

        }
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
    alert(msg);
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
$(".active-sbj").removeClass("active-sbj");
$(this).addClass("active-sbj");
// $('#container').css("visibility", "visible");
$('#qnav').html("<div style='padding-top:30px; text-align:center;'><img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' /><br />Loading...</div>");

//$('#container').show();
$("#sel").html("<div style='padding-top:100px; text-align:center;'><img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' /><br />Loading...</div>");
//alert($("#sel").html());
var subjectid = $(this).attr('id');
//alert(subjectid);
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
    loadnavpanel(subjectid);
});
        
return false;
});

function loadnavpanel(sbj)
{
$.ajax({
    type: 'POST',
    error: function(jqXHR) {
        //alert("Server not responding. Please retry!");
        // $("#sel").html(msg);
        $("#qnav").html('<b>Error Loading the questions...</b>');

    },
    url: 'loadnavpanel.php',
    data: {subject: sbj}
}).done(function(msg) {
    $("#qnav").html(msg);
});

}

//////
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
/////


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
    var sbjid=$(".active-sbj").attr('id');
    loadnavpanel(sbjid);
     document.getElementById("remaining").innerHTML = $.trim(msg);
    //let the curent selection be the selected
    //alert(code);
    document.getElementById(code).value = curentval;
    //update the navigation panel to set the question as answered
    //$("#" + navpanelelt).css('background-color', 'greenyellow');
    //$("#" + navpanelelt).css('background-image', 'url(tickIcon.png)');


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
    var tt=$("#banner").height();
    //alert(($("#"+divid).offset().top-0));

    $(window).scrollTop(($("#"+divid).offset().top-0)-(tt-0+20));
   
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
