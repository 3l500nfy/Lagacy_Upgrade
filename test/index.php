<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

require_once("testfunctions.php");

openConnection();
global $dbh;
//authorize();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}
//set a variable to show that the candidate has seen the question already
// in case he want to go back, redirect him here
if (isset($_SESSION['seequestion'])) {
    redirect(siteUrl("test/starttest.php"));
//header("location:starttest.php");
}
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];
//get the candidate biodata
$biodata = array();
if (!isset($_SESSION['biodata'])) {
    $biodata = getbiodata($candidateid);
} else {
    $biodata = $_SESSION['biodata'];
}
$candidatename = ucwords( trim($biodata['candidatename'],','));

$matric = $biodata['matric'];



$testinfo = array();
if (!isset($_SESSION['testinfo'])) {
    $testinfo = gettestinfo($testid);
} else {
    $testinfo = $_SESSION['testinfo'];
}
$name = $testinfo['name'];
$startingmode = $testinfo['startingmode'];
$duration = $testinfo['duration'];
$starttime = $testinfo['starttime'];
$dailystarttime = $testinfo['dailystarttime'];
unset($_SESSION['testinfo']); //unset in order to get the latest time as the student click on start		
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo pageTitle("Start Exams") ?></title>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style type="text/css">
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            width: 100%;
            text-align: center;
            padding: 20px 0;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        .container {
            display: flex;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        /* Welcome Section Styles */
        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 40px 30px;
            position: relative;
        }

        .welcome-section h1 {
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .welcome-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 25px;
            backdrop-filter: blur(5px);
        }

        .welcome-content h2 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .welcome-content ol {
            list-style-position: inside;
            margin-bottom: 30px;
        }

        .welcome-content li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .welcome-content li i {
            color: var(--light-color);
            margin-right: 10px;
            font-size: 18px;
        }

        .welcome-footer {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 40px;
            background: white;
        }

        .page-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .page-header h2 {
            font-size: 24px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .page-header small {
            font-size: 16px;
            color: #777;
            font-weight: normal;
        }

        .instructions-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .instructions-list {
            margin-bottom: 30px;
        }

        .instructions-list li {
            margin-bottom: 12px;
        }

        .btn-container {
            width: 300px;
            margin: 30px auto;
        }

        .btn-action {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-action:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .warning-text {
            color: red;
            font-size: 11px;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                width: 95%;
            }
            
            .welcome-section, .main-content {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .main-content, .welcome-section {
                padding: 25px 20px;
            }
            
            .welcome-section h1 {
                font-size: 24px;
            }
            
            .welcome-content h2 {
                font-size: 20px;
            }
            
            .page-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="<?php echo siteUrl("assets/img/hexadash.png"); ?>" alt="Hexadash" class="logo">
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h1>Welcome to the Computer Based Practice</h1>
            <div class="welcome-content">
                <h2>Examination Guidelines</h2>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Follow all instructions carefully</li>
                    <li><i class="fas fa-check-circle"></i> Complete all questions within the allotted time</li>
                    <li><i class="fas fa-check-circle"></i> Review your answers before submission</li>
                    <li><i class="fas fa-check-circle"></i> Maintain academic integrity at all times</li>
                    <li><i class="fas fa-check-circle"></i> Contact the proctor if you need assistance</li>
                </ul>
                <div class="welcome-footer">
                    <p>Good luck with your examination!</p>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <div class="page-header">
                <h2><?php echo $name; ?> <br>Welcome <small><?php echo" $candidatename"; ?></small></h2>
            </div>
            
            <?php
            if (checkcompletion($testid, $candidateid) == true) {
                echo '<span class="warning-text">*&nbsp;&nbsp;You have already taken the test.</span>';
                echo '<div class="btn-container"><button id="logoutbtn" class="btn-action">Logout</button></div>';
            } else {
            ?>
                <div class="instructions-header">
                    <h2>General instructions to candidates: <small>Read carefully</small></h2>
                </div>
                
                <ol class="instructions-list">
                    <li>Time starts when you click <i><b>Start Exams</b></i></li>
                    <li>Click subject codes to view questions</li>
                    <li>Use NAVIGATION panel to move between questions</li>
                    <li>Answers save automatically</li>
                    <li>Don't use browser back/forward/refresh buttons</li>
                    <li>Once submitted, you cannot return to the test</li>
                    <li>No phones or borrowing materials allowed</li>
                </ol>
                
                <div class="btn-container">
                    <button id="startbtn" class="btn-action">Start Exams</button>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

    <script type="text/javascript">
        $("#startbtn").bind('click', function(evt){
            window.location.href = "starttest.php";
        });

        $("#logoutbtn").bind('click', function(evt){
            window.location.href = "logout.php";
        });
    </script>
</body>
</html>
