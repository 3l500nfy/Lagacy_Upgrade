
<!DOCTYPE html>
<?php
session_start();
require_once("../../lib/globals.php");
openConnection();

$msg;

if (isset($_POST['submitted'])) {
    $subject = $_POST['subj'];
    $subjectcode = $_POST['subjcode'];
    $subjectcategory = $_POST['subjcat'];

    $subjectcode = strtoupper(strtolower(trim($subjectcode)));
    $subject = ucwords(strtolower(trim($subject)));

    if ($subject != "" && $subjectcode != "") {

        $query = "select * from tblsubject where(subjectcode=? && subjectcategory=?)";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($subjectcode,$subjectcategory));

        if ($query && $stmt->rowCount() == 0) {
            $query = "insert into tblsubject (subjectcode,subjectcategory,subjectname) values (?, ?,?)";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($subjectcode,$subjectcategory,$subject));

            if ($query && $stmt->rowCount() > 0) {
                $query="insert into tbltestcode (testname) values (?)";
                $stmt=$dbh->prepare($query);
                $stmt->execute(array($subjectcode));

                $query1="insert into tbltopics (subjectid,topicname) values ((select subjectid from tblsubject WHERE (subjectcode=? and subjectcategory=?)),'General')";
                $stmt1=$dbh->prepare($query1);
                $stmt1->execute(array($subjectcode,$subjectcategory));

                $msg = "Subject registered successfully!";
            }
            else
                $msg = "Subject not registered";
        }
        if ($query && $stmt->rowCount() > 0)
            $msg = "Subject already Exists";
    }
    else
        $msg = "No input specified";
}
?>

<html lang="en">
    <head>
        <title><?php echo pageTitle("Computer Based Test") ?></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
         <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
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

    </head>
    <body>
        <div id="container" class="container" style="padding-left: 20px">
            <div class="header">
                <h1>Fill the form below to register new subject</h1>
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
            <div id="slipdiv" style="height : 180px">
                <form action ="addsubject.php" method ="POST" class="style-frm">
                    <center>
                        <span id="msg" ><?php if (isset($msg) && $msg != "") echo $msg; ?></span>

                        <table>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>Enter Subject Code:</b> </td>
                                <td>
                                    <input type="text" name="subjcode" id="subjcode"/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Enter Subject Name:</b> </td>
                                <td>
                                    <input type="text" name="subj" id="subj"/>
                                </td>
                            </tr>

                            <tr>
                                <td><b>Select Subject Category: </b> </td>
                                <td>
                                    <select name="subjcat">
                                        <option>--select subject category--</option>
                                        <option value="1">Regular</option>
                                        <option value="2">SBRS</option>
                                        <option value="3">O'Level</option>
                                    </select>
                                    <br/>

                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button type ="submit" name = "submitted" class ="btn btn-primary" id ="continue_btn">Register</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html>