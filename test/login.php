<?php
if (!isset($_SESSION))
    session_start();
//@mysql_close();
require_once(dirname(__FILE__) . "/../lib/globals.php");
require_once(dirname(__FILE__) . "/../lib/security.php");
//authorize();
if (has_roles(array("Super Admin")) || has_roles(array("Admin")) || has_roles(array("Test Administrator")) || has_roles(array("Test Compositor")) || has_roles(array("Test Invigilator")) || has_roles(array("PC Registrar"))) {
    header("Location:" . siteUrl("admin.php"));
    exit();
}
openConnection();
global $dbh;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        <?php echo pageTitle("User Authentication") ?>
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css">
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
        }

        .container-fluid {
            width: 100%;
            height: 100vh;
            display: flex;
        }

        .row {
            display: flex;
            width: 100%;
        }

        .col-xl-7 {
            flex: 0 0 58.33333%;
            max-width: 58.33333%;
            background-size: cover;
            background-position: center;
        }

        .col-xl-5 {
            flex: 0 0 41.66667%;
            max-width: 41.66667%;
            padding: 0;
        }

        .login-card {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }

        .login-dark {
            background-color: white;
        }

        .logo {
            display: block;
            text-align: center;
            margin-bottom: 30px;
        }

        .login-main {
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .theme-form h4 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        .theme-form p {
            color: #777;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .col-form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .form-input {
            position: relative;
        }

        .show-hide {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .show-hide span {
            width: 20px;
            height: 20px;
            display: block;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }

        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .w-100 {
            width: 100%;
        }

        .text-end {
            text-align: right;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        /* Responsive styles */
        .instructions-container {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            padding: 40px;
            color: white;
        }

        .instructions-section {
            max-width: 600px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(5px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .instructions-section h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .instructions-content {
            padding: 20px;
        }

        .instructions-content h2 {
            font-size: 22px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }

        .instructions-content ul {
            list-style-type: none;
            padding: 0;
        }

        .instructions-content li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .instructions-content li i {
            color: #2ecc71;
            margin-right: 10px;
            font-size: 18px;
            margin-top: 2px;
        }

        .instructions-footer {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 15px;
        }

        @media (max-width: 1200px) {
            .instructions-section {
                padding: 20px;
            }
            
            .instructions-section h1 {
                font-size: 24px;
            }
            
            .instructions-content h2 {
                font-size: 20px;
            }
        }

        @media (max-width: 992px) {
            .col-xl-7 {
                display: none;
            }
            .col-xl-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .login-main {
                padding: 30px 20px;
            }
        }

        /* Additional styles for the login form */
        .control-group {
            margin-bottom: 20px;
        }

        .controls {
            position: relative;
        }

        .input-block-level {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        .input-block-level:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn-info {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-info:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
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

        .contentbox {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 15px;
            max-width: 500px;
            margin: 0 auto;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-error {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-7 instructions-container">
                <div class="instructions-section">
                    <h1>Welcome to the Computer Based Test</h1>
                    <div class="instructions-content">
                        <h2>Examination Instructions</h2>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Your time will start counting as soon as you click on <b>Start Exams</b></li>
                            <li><i class="fas fa-check-circle"></i> Use the navigation panel to quickly move between questions</li>
                            <li><i class="fas fa-check-circle"></i> Answers are automatically saved as they are selected</li>
                            <li><i class="fas fa-check-circle"></i> Avoid using browser navigation buttons as they may log you out</li>
                            <li><i class="fas fa-check-circle"></i> Once submitted, you cannot return to the test</li>
                        </ul>
                        <div class="instructions-footer">
                            <p>Good luck with your examination!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                           
                        </div>
                        <div class="login-main">
                            <div class="contentbox">
                                <div class="page-header">
                                    <h3>Please Login <small>to Start the Examination</small></h3>
                                </div>
                                <?php require_once('../partials/notification.php'); ?> 

                                <?php
                                //Display validation error
                                if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                                    echo '<div class="alert alert-error">';
                                    foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                                        echo '<i class="fas fa-exclamation-circle"></i> ', $msg, '<br />';
                                    }
                                    echo '</div>';
                                    unset($_SESSION['ERRMSG_ARR']);
                                }
                                ?>
                                <?php if (isset($_SESSION['LOGIN_FAILED'])): ?>
                                    <div class="alert alert-error">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <?php
                                        echo $_SESSION['LOGIN_FAILED'];
                                        unset($_SESSION['LOGIN_FAILED']);
                                        ?>
                                    </div>
                                <?php endif ?>

                                <form action="login_exec.php" method="post" class="theme-form" autocomplete='off'>
                                    <div class="form-group">
                                        <label class="col-form-label" for="testid">Exam Type</label>
                                        <select name="testid" id="testid" class="form-control">
                                            <?php
                                            //populate all exams to take place today
                                            $query = "SELECT tbltestconfig.testid,
                                                tbltestcode.testname,
                                                testtypename,
                                                session,
                                                semester 
                                                FROM tbltestconfig 
                                                inner join tbltestcode ON tbltestconfig.testcodeid=tbltestcode.testcodeid
                                                inner join tbltesttype ON tbltestconfig.testtypeid=tbltesttype.testtypeid
                                                INNER JOIN tblscheduling on tblscheduling.testid= tbltestconfig.testid
                                                where(tblscheduling.date=curdate()) 
                                                group by testid";

                                            $stmt=$dbh->prepare($query);
                                            $stmt->execute();
                                            if ($stmt->rowCount() > 1 || $stmt->rowCount() <= 0)
                                                echo "<option value=''>Select Test</option>";

                                            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                                                $testid = $row['testid'];
                                                $testname = strtoupper($row['testname']);
                                                $testtypename = $row['testtypename'];
                                                $session = $row['session'];
                                                $semester = $row['semester'];
                                                echo"<option value='$testid'>  $testname-$testtypename- $session </option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-form-label" for="username" id="usnlbl">UserName</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control" type="text" id="username" name="username" placeholder="Username" required autocomplete='off'>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-form-label" for="password" id="pwdlbl">Password</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control" type="password" name="password" id="password" placeholder="Password" required autocomplete='off'>
                                            <div class="show-hide"><span class="show"></span></div>
                                        </div>
                                        <select style="display:none;" disabled name="password" id="password2" class="form-control">
                                            <option value="">Select Your State</option>
                                            <?php
                                                $sqlstate = "select statename from tblstate";
                                                $stmt1=$dbh->prepare($sqlstate);
                                                $stmt1->execute();
                                                while($row= $stmt1->fetch(PDO::FETCH_ASSOC)){
                                                    echo "<option value='".trim($row['statename'])."'>".trim($row['statename'])."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-0">
                                        <div class="text-end mt-2">
                                            <button type="submit" class="btn btn-primary btn-block w-80">Sign in</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl('assets/js/select2.min.js'); ?>"></script>

    <script type="text/javascript">
        setTimeout(function () {
            location.reload();
        }, 360 * 1000);

        $(document).ready(function () {
            $('#username').focus();
            $("#testid").select2();
            
            // Show/hide password functionality
            $(".show-hide span").click(function () {
                if ($(this).hasClass("show")) {
                    $('input[name="password"]').attr("type", "text");
                    $(this).removeClass("show");
                } else {
                    $('input[name="password"]').attr("type", "password");
                    $(this).addClass("show");
                }
            });
        });

        $(document).on('change click', '#testid', function () {
            var tname = $('#testid option:selected').text();
            var match = tname.match(/Post-UTME/i);

            if (match) {
                $('#usnlbl').text('Jamb No.');
                $('#pwdlbl').text('State');
                $('#username').attr('placeholder', 'Jamb No.');
                $('#password').attr('placeholder', 'State');
                $('#password').hide();
                $('#password').prop('disabled',true);
                $('#password2').show();
                $('#password2').prop('disabled',false);
            } else {
                $('#usnlbl').text('UserName');
                $('#pwdlbl').text('Password');
                $('#username').attr('placeholder', 'UserName');
                $('#password').attr('placeholder', 'Password');
                $('#password2').hide();
                $('#password2').prop('disabled',true);
                $('#password').show();
                $('#password').prop('disabled',false);
            }
        });
        $('#testid option:selected').trigger('click');
    </script>
</body>
</html>
