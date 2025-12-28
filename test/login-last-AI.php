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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo pageTitle("User Authentication") ?></title>
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

        /* Instructions Section Styles */
        .instructions-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 40px 30px;
            position: relative;
        }

        .instructions-section h1 {
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .instructions-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 25px;
            backdrop-filter: blur(5px);
        }

        .instructions-content h2 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .instructions-content ul {
            list-style: none;
            margin-bottom: 30px;
        }

        .instructions-content li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .instructions-content li i {
            color: var(--light-color);
            margin-right: 10px;
            font-size: 18px;
        }

        .instructions-footer {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }

        /* Login Container Styles */
        .login-container {
            flex: 1;
            padding: 40px;
            background: white;
        }

        .login-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .login-header h2 {
            font-size: 24px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .login-header small {
            font-size: 16px;
            color: #777;
            font-weight: normal;
        }

        .login-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
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

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        .input-with-icon input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-login:active {
            transform: translateY(0);
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

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-bottom: 15px;
        }

        /* Select2 Overrides */
        .select2-container--default .select2-selection--single {
            height: 45px;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                width: 95%;
            }
            
            .instructions-section, .login-container {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .login-container, .instructions-section {
                padding: 25px 20px;
            }
            
            .instructions-section h1 {
                font-size: 24px;
            }
            
            .instructions-content h2 {
                font-size: 20px;
            }
            
            .login-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" alt="Dariya Logo" class="logo">
    </div>
    
    <div class="container">
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
        
        <div class="login-container">
            <div class="login-header">
                <h2>Please Login <small>to Start the Examination</small></h2>
            </div>
            
            <?php require_once('../partials/notification.php'); ?> 

            <?php
            //Display validation error
            if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                echo '<div class="error-message">';
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

            <form action="login_exec.php" method="post" class="login-form" autocomplete='off'>
                <div class="form-group">
                    <label for="testid">Exam Type</label>
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
                    <label for="username" id="usnlbl">UserName</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Username" required autocomplete='off'>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" id="pwdlbl">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required autocomplete='off'>
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
                
                <div class="form-group">
                    <button type="submit" class="btn-login">Sign in</button>
                </div>
            </form>
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
