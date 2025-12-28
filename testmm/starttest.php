<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

openConnection();
global $dbh;
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
    //
    unset($_SESSION['MEMBER_FULLNAME']);
    unset($_SESSION['MEMBER_USERID']);
    unset($_SESSION['candidateid']);
    unset($_SESSION['testid']);
    unset($_SESSION['seequestion']);
    unset($_SESSION['biodata']);
    unset($_SESSION['testinfo']);
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
     //echo"remaining".$_SESSION['testinfo']['remainingsecond'];
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
    //get active version of the test
    $version = activeversion($testid);
    if (validateversion($testid, $version) == true) {
        createcandidatequestions($testid, $candidateid, $questionadministration, $optionadministration, $version);
    } else {
        //question not corectly set in configuration
        $_SESSION['LOGIN_FAILED'] = "The questions are not ready. Please wait";
        unset($_SESSION['MEMBER_FULLNAME']);
        unset($_SESSION['MEMBER_USERID']);
        unset($_SESSION['candidateid']);
        unset($_SESSION['testid']);
        unset($_SESSION['seequestion']);
        unset($_SESSION['biodata']);
        unset($_SESSION['testinfo']);
        session_write_close();
        header("location: login.php");
        exit();
    }
} else {

    //already created check if the number matches the specified number in testconfifuration
    $query2 = "SELECT sum(num_toanswer) as totaltoanswer FROM tbltestsection
	INNER JOIN tbltestsubject on tbltestsection.testsubjectid=tbltestsubject.testsubjectid 	 
	where(testid='$testid' and tbltestsubject.subjectid in
	(select subjectid from tblcandidatestudent inner join tblscheduling on 
	tblcandidatestudent.scheduleid=tblscheduling.schedulingid where(testid='$testid' and candidateid='$candidateid')) )";
    $stmt1=$dbh->prepare($query2);
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
            $version = activeversion($testid);
            if (validateversion($testid, $version) == true) {
                createcandidatequestions($testid, $candidateid, $questionadministration, $optionadministration, $version);
            } else {
                //question not properly configured
                $_SESSION['LOGIN_FAILED'] = "The questions are not ready. Please wait";
                unset($_SESSION['MEMBER_FULLNAME']);
                unset($_SESSION['MEMBER_USERID']);
                unset($_SESSION['candidateid']);
                unset($_SESSION['testid']);
                unset($_SESSION['seequestion']);
                unset($_SESSION['biodata']);
                unset($_SESSION['testinfo']);
                session_write_close();
                header("location: login.php");
                exit();
            }
        }
    }
}

$elapsed = timecontrol($testid, $candidateid, $waitingsecond = 30);
$pgtitle = "::TEST";
require_once 'cbt_header2.php';
?>
<link href="<?php echo siteUrl("assets/css/startteststyle.css"); ?>" type="text/css" rel="stylesheet"></link>
<link href="<?php echo siteUrl("assets/css/jquery.calc.css"); ?>" type="text/css" rel="stylesheet"></link>

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
        <?php  if ($testinfo['allow_calc'] == true) { ?>
        <div id="calcbtn" style='text-align: center;'><img src="<?php echo siteUrl("assets/img/cbtcalc.png"); ?>" title='Calculator' style="width:50px; height: 50px;" /></div>
        <?php } ?>
        <div id="qnav">
            <div id="qnavtitle">NAVIGATION</div>

        </div>
    </div>
</div>
<?php  if ($testinfo['allow_calc'] == true) { ?>
<div id="calcdiv" style="display: none;">
    <div id="calculator">
        <div class="digits">
            <div class='rw'>
                <div class='result'>0</div>
            </div>
            <div class='rw'>
                <div class='digit func' data-op='sqr' title='Square'>x<sup>2</sup></div><div class='digit func' data-op='cube' title='Cube'>x<sup>3</sup></div><div class='digit func' data-op='sqrt' title='Square Root'>&radic;x</div><div class='digit func' data-op='cbrt' title='Cube Root'>&#8731;x</div>
            </div>
            <div class='rw'>
                <div class='digit' id="seven">7</div><div class='digit' id="eight">8</div><div class='digit' id="nine">9</div><div class='digit mathop' title='Plus' id="plus">+</div>
            </div>
            <div class='rw'>
                <div class='digit' id="four">4</div><div class='digit' id="five">5</div><div class='digit' id="six">6</div><div class='digit mathop' title='Minus' id="minus">-</div>
            </div>
            <div class='rw'>
                <div class='digit' id="one">1</div><div class='digit' id="two">2</div><div class='digit' id="three">3</div><div class='digit mathop' data-op='mult' title='Multiply by' id="mult">&times;</div>
            </div>
            <div class='rw'>
                <div class='digit' id="dot">.</div><div class='digit' id="zero">0</div><div class='digit' title='Negate'>+/-</div><div class='digit mathop' data-op='div' title='Divide by' id="division">&divide;</div>
            </div>
            <div class='rw'>
                <div class='close' title='Close the calculator'>Close</div><div class='clr' title='Reset Calculator'>AC</div><div class='digit mathop' id='solve' title='Solve' id="solve">=</div>
            </div>
        </div>
    </div>

</div>
<?php } ?>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    var finishedtext = "Completed!";// text that appears when the countdown reaches 0

    var remainingsecond = ($("#remainingsecond1").val());
    //alert(remainingsecond);
    remainingsecond = parseInt(remainingsecond);
    var onwarning = false;
    var onalert = false;
    var calcInstance= false;

//calculator
    $(document).on('click', '#calcbtn', function (event) {
        if(calcInstance){
            $("#calcdiv").dialog('open');
            addKeyPressEventToCalculator();
            return false;
        }
        $("#calcdiv").dialog({
            modal: false,
            title: 'Calculator',
            zIndex: 10000,
            autoOpen: true,
            width: '250px',
            resizable: false,
            closeOnEscape: true,
            position: {at: "right-30% top-10%", my: "center", of: window},
            close: function(event, ui){                
                removeKeyPressEventFromCalculator();
            }
        });
        
        calcInstance = true;

        addKeyPressEventToCalculator();
    });

    $(document).on('click', '#calculator div.close', function (event) {
        $('#calcdiv').dialog("close");
    });
    function cd() {
        remainingsecond = ($("#remainingsecond1").val());
        remainingsecond = parseInt(remainingsecond);
        remainingsecond = remainingsecond - 1;
        document.getElementById("remainingsecond").innerHTML = remainingsecond;
        document.getElementById("remainingsecond1").value = remainingsecond;

        //alert(remainingsecond);
        hr = Math.floor(remainingsecond / (60 * 60))
        min = (Math.floor(remainingsecond / 60) - (hr * 60));
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
            }).done(function (msg) {
                //alert(msg);
                //////////////////////////////////////
                $('<div></div>').appendTo('body')
                        .html('<div><h4>Your time is over! Logout please</h4></div>')
                        .dialog({
                            modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                            width: 'auto', resizable: true,
                            buttons: {
                                Logout: function () {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                                    $(this).dialog("close");
                                }
                            },
                            close: function (event, ui) {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                                $(this).remove();
                            }
                        });
                /////////////////////////////////////
            });
        }
        else {
            if (hr == 0)
            {
                document.getElementById("test-time").innerHTML = min + ":" + sec;
                $("#test-time").css("fontSize", '38px');
                $("#test-time-title").text("COUNTDOWN (MIN:SEC)");
            }
            else
            {
                document.getElementById("test-time").innerHTML = hr + ":" + min + ":" + sec;
                $("#test-time").css("fontSize", '30px');
                $("#test-time-title").text("COUNTDOWN (HR:MIN:SEC)");
            }
            if (remainingsecond < (60 * 3) && onalert == false) {
                onalert = true;
                $(".test-time").css("backgroundColor", '#ffffff');
                window.setInterval(function () {
                    $(".test-time").animate({backgroundColor: '#ff0000'}, 300, 'swing', function () {
                        $(".test-time").animate({backgroundColor: '#FFFFFF'}, 300);
                    });
                    $("#test-time-title").animate({color: '#FFFFFF'}, 300, 'swing', function () {
                        $("#test-time-title").animate({color: '#999999'}, 300);
                    });
                    $("#test-time").animate({color: '#FFFFFF'}, 300, 'swing', function () {
                        $("#test-time").animate({color: '#ff0000'}, 300);
                    });
                }, 800);
            }
            else
            if (remainingsecond < (60 * 5) && onwarning == false) {
                onwarning = true;
                $(".test-time").animate({backgroundColor: '#ff0000'}, 300, 'swing', function () {
                    $(".test-time").animate({backgroundColor: '#FFFFFF'}, 300);
                });
                $("#test-time-title").animate({color: '#FFFFFF'}, 300, 'swing', function () {
                    $("#test-time-title").animate({color: '#999999'}, 300);
                });
                $("#test-time").animate({color: '#FFFFFF'}, 300, 'swing', function () {
                    $("#test-time").animate({color: '#ff0000'}, 300);
                });
            }
        }
        timerID = setTimeout("cd()", 1000);
    }

    window.onload = cd;
//update server time
    setInterval(function () {
        //alert("updated");
        $.ajax({
            type: 'POST',
            url: 'updatetime.php',
            data: {endtime: 2}
        }).done(function (msg) {
            //alert(msg);
            if (msg == "end") {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
            }
            else {
                var remain = parseInt(msg);
                var remainingsecond = ($("#remainingsecond1").val());
                remainingsecond = parseInt(remainingsecond);
                //if the time returned is not in the range of the time in the client,
                // update the client time with the server time
                if ((remain < remainingsecond) || (remain > remainingsecond + 10)) {
                    document.getElementById("remainingsecond").innerHTML = remain;
                    document.getElementById("remainingsecond1").value = remain;
                }
            }
        });

    }, 100 * 60 * 0.5);


    $(".subjbtn").bind('click', function (evt) {
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
            error: function (jqXHR) {
                //alert("Server not responding. Please retry!");
                // $("#sel").html(msg);
                $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');
                $("#qnav").html('<b>Error Loading the questions...</b>');
                $(window).scrollTop(0);

            },
            timeout: 80000,
            url: 'loadquestion.php',
            data: {subject: $(this).attr('id')}
        }).done(function (msg) {
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
            error: function (jqXHR) {
                //alert("Server not responding. Please retry!");
                // $("#sel").html(msg);
                $("#qnav").html('<b>Error Loading the questions...</b>');

            },
            timeout: 80000,
            url: 'loadnavpanel.php',
            data: {subject: sbj}
        }).done(function (msg) {
            $("#qnav").html(msg);
        });

    }

    $("div.subjbtn:first-child").trigger('click');

    $(document).on('click', ".tbnnavigation", function (evt) {
        $("#sel").html("<div style='padding-top:100px; text-align:center;'><img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' /><br />Loading...</div>");

        $.ajax({
            type: 'POST',
            error: function (jqXHR) {
                //alert("Server not responding. Please retry!");
                $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');

                $(window).scrollTop(0);

            },
            url: 'loadquestion.php',
            timeout: 80000,
            data: {direct: $(this).attr('id')}
        }).done(function (msg) {

            //alert(msg);
            $("#sel").html(msg);
            //disable previous and next if first and end

        });
        return false;
    });
    /////

    $(document).on('click', ".ao", function (evt) {
        //alert("clicked");

        var dis = $(".optionlabel > .answeroption", $(this));
        var code = dis.attr('name') + "code"; //alert(code);
        var navpanelelt = "navpanel" + dis.attr('name');
        var initialval = $("#" + code).val(); //alert(initialval)
        //var optpar=$(this).parent().parent().parent(".ansopt");
        $("#" + initialval).parents(".selected-opt").removeClass("selected-opt");
        //$("#" + initialval).parent().parent().removeClass("selected-opt");
        $(this).addClass("selected-opt");
        var curentval = dis.val();

        $.ajax({
            type: 'POST',
            error: function (jqXHR) {
                //$(this).removeAttr('checked');
                $("#" + curentval).removeAttr("checked");
                //restore initial 
                dis.parent().parent().removeClass("selected-opt");
                if (parseInt($.trim(initialval)) != 0) {
                    document.getElementById(initialval).checked = true;
                    $("#" + initialval).parent().parent().addClass("selected-opt");
                }
                //alert(jqXHR.status+" " +jqXHR.statusText+" error");
                alert("Your last answer wasn't saved because the server is not responding. Please retry!");

            },
            url: 'saveanswer.php',
            timeout: 40000,
            data: {question: dis.attr('name'), ans: dis.val()}
        }).done(function (msg) {
            // alert("answer saved");
            var sbjid = $(".active-sbj").attr('id');
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
                                Submit: function () {
                                    //////
                                    var comp = "1";
                                    $.ajax({
                                        type: 'POST',
                                        url: 'updatetime.php',
                                        data: {completion: comp}
                                    }).done(function (msg) {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                                    })
                                    /////

                                    $(this).dialog("close");
                                },
                                Review: function () {
                                    //document.getElementById("abandon").innerHTML ="<font size=4>|Log Out|</font>";

                                    $(this).remove();
                                }
                            },
                            close: function (event, ui) {
                                $(this).remove();
                            }
                        });
            }
            //alert(msg);
        });
        //return false;
        evt.stopPropagation();
    });
    $(document).on('click', ".answeroption____", function (evt) {

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
            error: function (jqXHR) {
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
        }).done(function (msg) {
            var sbjid = $(".active-sbj").attr('id');
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
                                Submit: function () {
                                    //////
                                    var comp = "1";
                                    $.ajax({
                                        type: 'POST',
                                        url: 'updatetime.php',
                                        data: {completion: comp}
                                    }).done(function (msg) {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                                    })
                                    /////

                                    $(this).dialog("close");
                                },
                                Review: function () {
                                    //document.getElementById("abandon").innerHTML ="<font size=4>|Log Out|</font>";

                                    $(this).remove();
                                }
                            },
                            close: function (event, ui) {
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

    $("#abandon").bind('click', function (evt) {
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
                        Submit: function () {
                            //////
                            var comp = "1";
                            $.ajax({
                                type: 'POST',
                                url: 'updatetime.php',
                                data: {completion: comp}
                            }).done(function (msg) {

<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                            })
                            /////

                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).remove();
                        }
                    },
                    close: function (event, ui) {
                        $(this).remove();
                    }
                });
    });

/////////////////////
    $(window).unload(function () {
        $.ajax({
            type: 'POST',
            url: 'updatetime.php',
            data: {endtime: 2}
        }).done(function (msg) {
            //alert(msg);
            if (msg == "end") {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
            }
        });
    });

    $("#loader").ajaxStart(function () {
        $(this).show();
    }).ajaxStop(function () {
        $(this).hide();
    });

    $(document).ready(function () {
        $("#loader").ajaxSend(function () {
            $(this).show();
        }).bind("ajaxStop", function () {
            $(this).hide();
        }).bind("ajaxError", function () {
            $(this).hide();
        });

    });


    $(document).load(function () {
//        alert("rr");
        $.ajax({
            type: 'POST',
            url: 'updatetime.php',
            data: {endtime: 2}
        }).done(function (msg) {
            //alert(msg);
            if (msg == "end") {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
            }
            else {
                var remain = parseInt(msg);
                //  alert(remain);
            }
        });

    });

    //

    $(document).on('click', '.qbut', function (event) {
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
            var tt = $("#banner").height();
            //alert(($("#"+divid).offset().top-0));

            $(window).scrollTop(($("#" + divid).offset().top - 0) - (tt - 0 + 20));

        }
        else {
            //single question display
            $("#sel").html("<div style='padding-top:100px; text-align:center;'><img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' /><br />Loading...</div>");
            $.ajax({
                type: 'POST',
                error: function (jqXHR) {
                    //alert("Server not responding. Please retry!");
                    $("#sel").html('<h4>Error Loading the question. The server is unreachable. Please Retry</h4>');
                    $(window).scrollTop(0);

                },
                url: 'loadquestion.php',
                timeout: 50000,
                data: {questionid: questionid, limit: limit, navigation: 1}
            }).done(function (msg) {

                //alert(msg);
                $("#sel").html(msg);

            });
            ////
        }
    });

    $(document).on('click', '#endorsement', function (event) {
        window.location = "invigilator_endorsement.php";
    });

    $("button").button();

    function removeKeyPressEventFromCalculator() {
        $(document).off('keypress');
    }
    
    function addKeyPressEventToCalculator() {
        $(document).on('keypress',document, kpe);
    }
    
    var kpe = function (event) {
            event.preventDefault();
            //alert(event.which);
            var el = "";
            switch (event.which) {
                case 42:
                    el = "mult";
                    break;
                case 43:
                    el = "plus";
                    break;
                case 45:
                    el = "minus";
                    break;
                case 46:
                    el = "dot";
                    break;
                case 47:
                    el = "division";
                    break;
                case 48:
                    el = "zero";
                    break;
                case 49:
                    el = "one";
                    break;
                case 50:
                    el = "two";
                    break;
                case 51:
                    el = "three";
                    break;
                case 52:
                    el = "four";
                    break;
                case 53:
                    el = "five";
                    break;
                case 54:
                    el = "six";
                    break;
                case 55:
                    el = "seven";
                    break;
                case 56:
                    el = "eight";
                    break;
                case 57:
                    el = "nine";
                    break;
                case 61:
                    el = "solve";
                    break;
            }

            if (el != "") {
                $('#' + el).trigger('click');
            }
        };
</script>

</body>
</html>