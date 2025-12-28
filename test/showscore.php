<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("testfunctions.php");
require_once("../reports/test_report_function.php");
//require_once("../lib/test_config_func.php");
openConnection();
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/index.php"));
}

//check if the candidate has taken and cmpleted the test
//get candidate information and test he is writting
$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];


$testinfo = array();
if (!isset($_SESSION['testinfo'])) {
    $testinfo = gettestinfo($testid);
} else {
}
//$pgtitle = "::SCORE";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1200, initial-scale=1.0">
    <title><?php echo pageTitle("Test Results") ?></title>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .logo {
            height: 60px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .content {
            padding: 40px;
        }

        .score-section {
            background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .score-title {
            text-align: center;
            font-size: 2.2em;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .score-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .score-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .score-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.1em;
        }

        .score-table tr:last-child td {
            border-bottom: none;
        }

        .score-table tr:hover {
            background: #f8f9fa;
        }

        .total-row {
            background: #e8f4fd !important;
            font-weight: 700;
            font-size: 1.2em;
        }

        .note-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }

        .feedback-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .feedback-title {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            resize: vertical;
            transition: var(--transition);
        }

        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #667eea 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.3);
        }

        .percentage {
            font-size: 3em;
            font-weight: 700;
                text-align: center;
            color: #2ecc71;
            margin: 20px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tbnnavigation {
            border-radius: 8px;
            margin: 5px;
            font-weight: bold;
            font-size: 1.5em;
            padding: 15px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .tbnnavigation:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            }
            
        /* UTME ACE Banner and App Image Styles */
        .utme-ace-banner {
            background: linear-gradient(135deg,rgb(84, 67, 243) 0%,rgb(36, 147, 238) 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 8px 25px rgba(84, 67, 243, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .banner-content {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .banner-text {
            flex: 1;
            text-align: left;
        }
        
        .banner-title {
            color: white;
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }
        
        .banner-title i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        
        .app-description {
            color: white;
        }
        
        .app-tagline {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #f8f9fa;
        }
        
        .app-features {
            font-size: 1em;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #e9ecef;
        }
        
        .app-features i {
            color: #28a745;
            margin-right: 8px;
            font-size: 0.9em;
        }
        
        .download-section {
            margin-top: 20px;
        }
        
        .download-text {
            font-size: 1.1em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #f8f9fa;
        }
        
        .download-btn {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1em;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .download-btn i {
            margin-right: 8px;
        }
        
        .app-image-container {
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .app-image {
            max-width: 300px;
            max-height: 350px;
            width: auto;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 4px solid white;
            transition: transform 0.3s ease;
        }
        
        .app-image:hover {
            transform: scale(1.05);
        }
        
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .banner-content {
                flex-direction: column;
                text-align: center;
            }
            
            .banner-text {
                text-align: center;
            }
            
            .app-image {
                max-width: 250px;
                max-height: 300px;
            }
        }
        </style>
    </head>

    <body>
    <div class="container">
        <div class="header">
            <img src="<?php echo siteUrl("assets/img/dariya_logo1.png");?>" alt="Logo" class="logo">
            <h1><i class="fas fa-trophy"></i> Test Results</h1>
        </div>
        
        <div class="content">
            <div class="score-section">
                <div class="score-title">
                    <i class="fas fa-user-graduate"></i> 
                    <?php echo trim($_SESSION['biodata']['candidatename'],',');?>, Your Score Is:
                </div>
                
        <?php
        $aggregate2=0;
        $aggregate=0;
        $subjectscore=0;
        $overallscore=0;
        $rw="";
        $tsubj = get_subject_combination_as_array($testid);
        $rsubj = get_subject_registered_as_array($testid, $candidateid);
        foreach ($tsubj as $sbj) {
            if (in_array($sbj, $rsubj)) {
                $sbj_name=get_subject_code_name($sbj);
                $aggregate = get_candidate_subject_score($testid, $candidateid, $sbj);
                $aggregate2 += $aggregate;
               $subjectscore= get_subject_total_mark($sbj, $testid);
               $overallscore +=$subjectscore;
                        $rw .="<tr><td><i class='fas fa-book'></i> $sbj_name</td><td><strong>$aggregate / $subjectscore</strong></td></tr>";
                    }
                }
                $percentage = $overallscore > 0 ? round(($aggregate2 / $overallscore) * 100, 1) : 0;
                ?>
                
                <table class="score-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-subject"></i> Subject</th>
                            <th><i class="fas fa-chart-bar"></i> Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $rw; ?>
                        <tr class="total-row">
                            <td><i class="fas fa-trophy"></i> <strong>Total</strong></td>
                            <td><strong><?php echo $aggregate2; ?> / <?php echo $overallscore; ?></strong></td>
                        </tr>
                    </tbody>
    </table>
    
                <div class="percentage">
                    <?php echo $percentage; ?>%
                </div>
                
                <div class="note-section">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Note:</strong> <i>This is Just a Practice, Keep it up.</i>
    </div>
                
                <!-- UTME ACE Banner and App Image -->
                <div class="utme-ace-banner">
                    <div class="banner-content">
                        <div class="banner-text">
                            <h2 class="banner-title">
                                <i class="fas fa-mobile-alt"></i> UTME ACE
                            </h2>
                            <div class="app-description">
                                <p class="app-tagline">Your Ultimate UTME Success Companion</p>
                                <p class="app-features">
                                    <i class="fas fa-check-circle"></i> Access thousands of past UTME questions<br>
                                    <i class="fas fa-check-circle"></i> Practice with real exam simulations<br>
                                    <i class="fas fa-check-circle"></i> Track your progress with detailed analytics<br>
                                    <i class="fas fa-check-circle"></i> Study offline with downloaded content<br>
                                    <i class="fas fa-check-circle"></i> Get instant feedback and explanations<br>
                                    <i class="fas fa-check-circle"></i> Compete with other students nationwide
                                </p>
                                <div class="download-section">
                                    <p class="download-text">Ready to ace your UTME? Download the app now!</p>
                                    <a href="#" class="download-btn" onclick="downloadApp()">
                                        <i class="fas fa-download"></i> Click to Download App Here
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="app-image-container">
                            <img src="<?php echo siteUrl('assets/img/mob-app.png'); ?>" alt="UTME ACE Mobile App" class="app-image">
                        </div>
                    </div>
                </div>
</div>

            <div class="feedback-section">
                <div class="feedback-title">
                    <i class="fas fa-comment-dots"></i> Tell us About Your Experience..
                </div>
                
                <form method="POST" action="../online/feedback.php">
                      <?php echo" <input type='hidden' id='candidateid' value='$candidateid' name='candidateid'>";?>
                    <?php echo" <input type='hidden' id='testid' value='$testid' name='testid'>";?>
                    
                    <div class="form-group">
                        <label for="comments" class="form-label">
                            <i class="fas fa-info-circle"></i> Please kindly fill the form below to give us your feedback on the use of this system. Thank You!
                        </label>
                        <textarea name="comments" id="comments" class="form-textarea" placeholder="Enter your comments here..." rows="4" required></textarea>
                            </div>                

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Submit Feedback
                        </button>
                            </div>
                    </form>
                    </div>
            </div>
        </div>




<?php
  session_destroy();
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    function downloadApp() {
        // Redirect to the UTME ACE APK download link
        window.open('https://pqmaster.com/Downloads/UTME_ACE.apk', '_blank');
    }
    
    $("#viewsolution").bind('click', function(evt) {
       // alert("kk");
       
      ////////////////////////
      $.ajax({
                        type: 'POST',
                        url: '../online/showsolution.php',
                        data:{candidateid:$("#candidateid").val(),testid:$("#testid").val()}
                    }).done(function(msg) {
                        ////////////////////////////////
                        //alert(msg);
                         $('<div></div>').appendTo('body')
                .html('<div style="text-align: center; font-size: 3em; "> SOLUTION<p></div>\n\
                <b>Key:</b> <div class="selected-opt" style="width:30px; height:30px;"> </div> Candidate Selection <br /> <div style="width:30px; height:30px; background-color:#8dc96e;"> </div> Correct Option<div>' + msg + '</div>')
                .dialog({
            modal: true, title: 'Computer Based Exams Practice', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: true,
            buttons: {
                
                Done: function() {
                        $(this).remove();
                }
            },
            close: function(event, ui) {
                $(this).remove();
            }
                        
                        
                         
        });
         });
                     
                        //////////////////////
                        
                        
                      

      
      ////////////////////////
    });

</script>
</body>
</html>