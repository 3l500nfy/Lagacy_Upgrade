


<!DOCTYPE html>
<?php
session_start();
require_once("../../lib/globals.php");
openConnection();

$subjectid = $_REQUEST['subjectid'];
$subjcat = $_REQUEST['subjectcategory'];
$oldsubjcode = $_REQUEST['subjectcode'];

$query = "SELECT *  FROM  tblsubject WHERE subjectid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($subjectid));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $subjectid = $row['subjectid'];
    $subjectcode = $row['subjectcode'];
    $subjcat = $row['subjectcategory'];
    $subjectname = $row['subjectname'];
}
?>

<html lang="en">
    <head>
        <title><?php echo pageTitle("Computer Based Test") ?></title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
            #slipdiv
            {
                margin-left: auto;
                margin-right: auto;
                width:500px;
                border-style: solid;
                border-width: 1px;
                border-color: #cccccc;
                -moz-border-radius:3px;
                border-radius:3px;
                -webkit-border-radius:3px;
                padding:5px;
                -webkit-box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);
                -moz-box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);
                box-shadow:  0px 2px 4px rgba(0, 0, 0, 0.5);

            }
        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>
        <div id="container" class="container" style="padding-left: 20px">
            <div class="header">
                <h1>Edit Required Fields</h1>
                <br/>
                <br/>
                <br/>
                <a href="index.php">View Subject Manager</a>

                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                <br/>
                <br/>
                <br/>
            </div>
            <div id="slipdiv">
                <form action ="edit_subject_exec.php" method ="POST" class="style-frm">
                    <center>
                        <table>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>Enter Subject Code:</b> </td>
                                <td>
                                    <input type ="hidden" name ="subjectid" id ="subjectid" value ="<?php echo $subjectid; ?>" />
                                    <input type="text" name="subjcode" id="subjcode"  value="<?php echo $subjectcode; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Enter Subject Name:</b> </td>
                                <td>
                                    <input type ="hidden" name ="subjectcode" id ="subjectcode" value ="<?php echo $oldsubjcode; ?>" />
                                    <input type="text" name="subj" id="subj" value ="<?php echo $subjectname; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Select Subject Category: </b> </td>
                                <td>
                                    <select name="subjcat">
                                        <option value="1">Regular</option>
                                        <option value="2" <?php echo(($subjcat == 2)?("selected"):(""));?>>SBRS</option>
                                        <option value="3" <?php echo(($subjcat == 3)?("selected"):(""));?>>O'Level</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type ="submit" name = "submitted" class ="btn btn-primary" id ="continue_btn">Save Your Changes </button>
                                </td>
                            </tr>
                        </table>
                        <span id="msg" ><?php if (isset($msg) && $msg != "") echo $msg; ?></span>
                    </center>
                </form>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html>