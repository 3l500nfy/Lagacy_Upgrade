<?php if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");

openConnection();
global $dbh;
authorize();
$pgtitle = "::Admin ToolBox";
$navindex = 3;
require_once '../partials/cbt_header.php';
?> 

<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="admin_toolstyle.css" type="text/css" rel="stylesheet"></link>

    </head>
    <body>
        <div id="container" class="container">
            <h1>Admin Toolbox</h1>
            <hr/>
            <div id="container2">
                <table style="height: 100%;">
                    <tr>
                        <td id="left-nav">
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_candidate_type/index.php')?>" target="contentframe">Manage Candidate Type</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_cvs/index.php') ?>" target="contentframe">Manage Center, Venue & System</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_students/index.php') ?>" target="contentframe">Manage Student</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_students/upload_student_images.php') ?>" target="contentframe">Upload Passports</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_jamb/index.php') ?>" target="contentframe">Manage Jamb Candidates</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/manage_subject/index.php') ?>" target="contentframe">Manage Subjects</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/invigilators/index.php') ?>" target="contentframe">Invigilators Toolkit</a></div>
                            <div><a class="anchor" href="<?php echo siteUrl('admin_toolbox/avalable_students/index.php') ?>" target="contentframe">Available Students</a></div>
                        </td>
                        <td id="frametd">
                            <iframe src="blank.php" id="contentframe" name="contentframe" style="width:100%; display:block; min-height:500px; border-style: none; border-width: 0px;"></iframe>
                        </td>
                    </tr>
                </table>
            </div>
            <hr class="soften">
        </div> <!-- /.container -->
    </body>
</html>