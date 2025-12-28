<?php
require_once("../../lib/globals.php");
openConnection();
global $dbh;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Manage Subject</title>

        <?php include_once "../../partials/jsimports.php" ?>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <style>
            #msg{
                text-decoration:blink;
                color:red;
                font-size: 20px;
                text-align: left;
            }
        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

    </head>
    <body>
        <span id="msg">
            <?php
            if (isset($message) && $message != "")
                echo $message;
            ?>
        </span>
        <div id="container" class="container" style="padding-left: 20px">
            <div class ="row">
                <div class ="span12"> 
                    <h1>Create, Edit & Delete Subject</h1>
                    <br /><br />
                </div>
                <hr/>
                <div class ="span12"> 
                    <a href="addsubject.php">Create New Subject</a>
                    <br/>
                    <br/>
                    <br/>
                </div>
                <div class ="span12">
                    <table class ="style-tbl" style="min-width:800px">
                        <tr>
                            <th>S/No.</th>
                            <th>Subject Code</th>
                            <th>Subject Category</th>
                            <th>Subject Name</th>
                            <th></th>
                        </tr>
                        <?php
                        $count = 1;
                        $query = "SELECT * FROM tblsubject";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $subjectid = $row['subjectid'];
                            $subjcode = $row['subjectcode'];
                            $subjcat = $row['subjectcategory'];
                            $subjectname = $row['subjectname'];
                            $deletable = false;
                            $query1 = "SELECT subjectid FROM tbltestsubject where subjectid=? limit 1";
                            $stmt1=$dbh->prepare($query1);
                            $stmt1->execute(array($subjectid));

                            if ($stmt1->rowCount() == 0) {
                                $deletable = true;
                            }
                            echo "<tr>
                        <td>$count</td>
                        <td>$subjcode</td>
                        <td>" . (($subjcat == 1) ? ("Regular") : (($subjcat == 2) ? ("SBRS") : ("O'Level"))) . " </td>
                        <td>$subjectname</td>
                        <td>
                            <a href = 'edit_subject.php?subjectid=$subjectid && subjectcategory=$subjcat && subjectcode=$subjcode'>Edit</a>
                            |
                            <a class='delsbj' data-del='$deletable' href = 'delete_subject.php?subjectid=$subjectid'>Delete</a>
                        </td>
                     </tr>
                    ";
                            $count++;
                        }
                        ?> 
                    </table>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
            $(document).on("click", ".delsbj", function(event) {
                if(($(this).attr("data-del"))!="")
                return window.confirm("Are you sure you want to delete this subject?");
            else
                alert("Cannot delete because it is been used in an existing test.");
            return false;
            });
        </script>
    </body>
</html>