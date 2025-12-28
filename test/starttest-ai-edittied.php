<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}

/* if(!isset($_COOKIE['registercomputer']))
  {
  header("Location:" . siteUrl("authorize_computer.php"));
  exit();
  }
  $passkey= trim($_COOKIE['registercomputer']);
 */
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
$stmt = $dbh->prepare($query1);
$stmt->execute();
$numrows = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($numrows >= 1) {
$totalquestions = $row['totalquestions'];
}
//$totalquestions = mysql_result($result1, 0, 'totalquestions');
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
    // echo $query2;
    $stmt = $dbh->prepare($query2);
    $stmt->execute();
    $numrows = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($numrows >= 1) {
        $totaltoanswer = $row['totaltoanswer'];
        }
    //$totaltoanswer = mysql_result($result2, 0, 'totaltoanswer');

//   echo"stud=$totalquestions test=$totaltoanswer";
    if ($totalquestions != $totaltoanswer) {

        // the total number of questions the student is to answer does not match the one specified.
        //if he has not started answering the questions, just recreate
        $query3 = "SELECT * FROM tblscore where (candidateid=$candidateid and testid=$testid)";
        $stmt = $dbh->prepare($query3);
        $stmt->execute();
        $numrows = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($numrows <= 0) {
            //the candidate has not started answering. delete and recreate the question
            $query4 = "delete from tblpresentation where(candidateid=$candidateid and testid=$testid)";
            $stmt = $dbh->prepare($query4);
            $stmt->execute();
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Based Test</title>
    <link href="<?php echo siteUrl("assets/css/startteststyle.css"); ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl("assets/css/jquery.calc.css"); ?>" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-color: #333;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Main Container Layout */
        #container {
            display: flex;
            width: 100%;
            min-height: calc(100vh - 60px);
            position: relative;
        }

        /* Left Navigation Panel */
        #side-widget {
            width: 250px;
            background: white;
            box-shadow: var(--shadow);
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 100px; /* Moved down from 60px to 100px */
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
            border-right: 1px solid #eee;
        }

        /* Question Content Area */
        .test-questions {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            background: white;
            min-height: calc(100vh - 60px);
        }

        /* Submit Button */
        #abandon {
            background: var(--accent-color);
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 0 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(231, 76, 60, 0.2);
        }

        #abandon:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }

        /* Timer Section */
        .test-time {
            background: white;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
        }

        #test-time-title {
            color: #999;
            font-size: 11px;
            font-weight: normal;
            margin-bottom: 3px;
            display: block;
        }

        #test-time {
            font-size: 32px;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Calculator Button */
        #calcbtn {
            text-align: center;
            margin: 0 10px 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        #calcbtn img {
            width: 40px;
            height: 40px;
            transition: var(--transition);
        }

        #calcbtn:hover img {
            transform: scale(1.1);
        }

        /* Navigation Panel */
        #qnav {
            flex: 1;
            margin: 0 10px 10px;
            border-radius: 5px;
            background: #f8f9fa;
            overflow-y: auto;
            border: 1px solid #eee;
        }

        #qnavtitle {
            background: var(--primary-color);
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
            font-size: 14px;
        }

        /* Question Navigation Buttons */
        .tbnnavigation {
            display: inline-block;
            width: 35px;
            height: 35px;
            margin: 4px;
            text-align: center;
            line-height: 35px;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            color: var(--text-color);
            border: 1px solid #ddd;
            transition: var(--transition);
        }

        .tbnnavigation:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .tbnnavigation.answered {
            background: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        /* Subject Buttons */
        .subjbtn {
            padding: 12px 15px;
            margin: 10px 20px;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            color: var(--text-color);
            border: 1px solid #ddd;
            transition: var(--transition);
            text-align: center;
        }

        .subjbtn:hover {
            background: #f0f0f0;
            border-color: #ccc;
        }

        .active-sbj {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-dark);
        }

        /* Question Styling */
        .question-container {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
        }

        .question-text {
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        /* Modern Checkbox Styling */
        .ansopt {
            margin-bottom: 15px;
        }

        .ao {
            display: flex;
            align-items: flex-start;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid #eee;
            margin-bottom: 10px;
        }

        .ao:hover {
            background: #f8f9fa;
        }

        .selected-opt {
            background: rgba(52, 152, 219, 0.1);
            border-color: var(--primary-color);
        }

        .optionlabel {
            display: flex;
            align-items: center;
            width: 100%;
        }

        /* Custom Checkbox */
        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 22px;
            height: 22px;
            margin-right: 15px;
            border: 2px solid #ddd;
            border-radius: 50%;
            transition: var(--transition);
        }

        .selected-opt .custom-checkbox {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }

        .selected-opt .custom-checkbox:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }

        .answeroption {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Calculator Styling */
        #calcdiv {
            display: none;
        }

        #calculator {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .digits {
            padding: 10px;
        }

        .rw {
            display: flex;
            margin-bottom: 5px;
        }

        .result {
            width: 100%;
            padding: 15px;
            text-align: right;
            font-size: 24px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .digit {
            flex: 1;
            text-align: center;
            padding: 15px 0;
            margin: 2px;
            background: #f8f9fa;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .digit:hover {
            background: #e9ecef;
        }

        .mathop {
            background: var(--primary-color);
            color: white;
        }

        .func {
            background: #e9ecef;
        }

        .close, .clr {
            background: #e9ecef;
        }

        #solve {
            background: var(--success-color);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            #side-widget {
                width: 240px;
            }
            
            .test-questions {
                margin-left: 240px;
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            #container {
                flex-direction: column;
            }
            
            #side-widget {
                position: static;
                width: 100%;
                order: 2;
                height: auto;
                max-height: 300px;
            }
            
            .test-questions {
                margin-left: 0;
                order: 1;
            }
            
            #abandon {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                width: auto;
                padding: 10px 20px;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div id="container">
        <!-- Left Navigation Panel -->
        <div id="side-widget">
            <div id="abandon">
                <i class="fas fa-paper-plane"></i> SUBMIT
            </div>
            
            <div class="test-time">
                <span id="test-time-title">COUNTDOWN (MIN:SEC)</span>
                <div id="test-time">00:00</div>
            </div>
            
            <?php if ($testinfo['allow_calc'] == true) { ?>
            <div id="calcbtn">
                <img src="<?php echo siteUrl("assets/img/cbtcalc.png"); ?>" title='Calculator'>
            </div>
            <?php } ?>
            
            <div id="qnav">
                <div id="qnavtitle">NAVIGATION</div>
                <!-- Navigation content will be loaded here -->
            </div>
        </div>
        
        <!-- Question Content Area -->
        <div id="sel" class="test-questions">
            <!-- Question content will be loaded here -->
        </div>
    </div>

    <?php if ($testinfo['allow_calc'] == true) { ?>
    <div id="calcdiv" style="display: none;">
        <div id="calculator">
            <div class="digits">
                <div class='rw'>
                    <div class='result'>0</div>
                </div>
                <div class='rw'>
                    <div class='digit func' data-op='sqr' title='Square'>x<sup>2</sup></div>
                    <div class='digit func' data-op='cube' title='Cube'>x<sup>3</sup></div>
                    <div class='digit func' data-op='sqrt' title='Square Root'>&radic;x</div>
                    <div class='digit func' data-op='cbrt' title='Cube Root'>&#8731;x</div>
                </div>
                <div class='rw'>
                    <div class='digit' id="seven">7</div>
                    <div class='digit' id="eight">8</div>
                    <div class='digit' id="nine">9</div>
                    <div class='digit mathop' title='Plus' id="plus">+</div>
                </div>
                <div class='rw'>
                    <div class='digit' id="four">4</div>
                    <div class='digit' id="five">5</div>
                    <div class='digit' id="six">6</div>
                    <div class='digit mathop' title='Minus' id="minus">-</div>
                </div>
                <div class='rw'>
                    <div class='digit' id="one">1</div>
                    <div class='digit' id="two">2</div>
                    <div class='digit' id="three">3</div>
                    <div class='digit mathop' data-op='mult' title='Multiply by' id="mult">&times;</div>
                </div>
                <div class='rw'>
                    <div class='digit' id="dot">.</div>
                    <div class='digit' id="zero">0</div>
                    <div class='digit' title='Negate'>+/-</div>
                    <div class='digit mathop' data-op='div' title='Divide by' id="division">&divide;</div>
                </div>
                <div class='rw'>
                    <div class='close' title='Close the calculator'>Close</div>
                    <div class='clr' title='Reset Calculator'>AC</div>
                    <div class='digit mathop' id='solve' title='Solve' id="solve">=</div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

<?php
require_once '../partials/cbt_footer.php';
?>
<script src="js/modern-dialog.js"></script>
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
            },
            dialogClass: 'modern-calculator-dialog'
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
                const timeExpiredDialog = new ModernDialog({
                    title: 'Computer Based Exams',
                    message: 'Your time is over! Please logout.',
                    type: 'warning',
                    buttons: [
                        {
                            text: 'Logout',
                            type: 'danger',
                            onClick: function() {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                            }
                        }
                    ],
                    onClose: function() {
<?php
if (strtoupper(substr($matric, 0, 4)) == "CAND")
    echo "window.location.href ='showscore.php';";
else
    echo "window.location.href = 'logout.php';";
?>
                    }
                });
                timeExpiredDialog.show();
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





//////
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
                const completionDialog = new ModernDialog({
                    title: 'Computer Based Exams',
                    message: 'You have successfully completed the exams! Would you like to review your work or submit?',
                    type: 'success',
                    buttons: [
                        {
                            text: 'Submit',
                            type: 'primary',
                            onClick: function() {
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
                                });
                            }
                        },
                        {
                            text: 'Review',
                            type: 'secondary',
                            onClick: function() {
                                // Do nothing, just close dialog and continue reviewing
                            }
                        }
                    ]
                });
                completionDialog.show();
                //////////////////////////////////

            }
            //alert(msg);
        });
        //return false;
        evt.stopPropagation();
    });

    $(document).on('click', "#abandon", function (evt) {
        showModernDialog.confirm(
            "Are you sure you want to submit your work?",
            function() {
                // Yes - Submit work
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
                });
            },
            function() {
                // No - Cancel
                // Do nothing, dialog will close automatically
            }
        );
    });
</script>
</body>
</html>
