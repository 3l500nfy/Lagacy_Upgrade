<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();
authorize();
if (!has_roles(array("Test Administrator")))
    header("Location:" . siteUrl("403.php"));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link rel="stylesheet" href="<?php echo siteUrl('assets/css/select2.min.css') ?>" type="text/css"></link>

        <script type="text/javascript">
        </script>
        <style type="text/css">
            .anchor{color:#999999;}
            .anchor:hover{color:black;}
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Increase Candidate's Time</h2><br />
            [<a class="anchor" href="manage_restore.php">Manage Restore</a>] | [<a class="anchor" href="manage_time.php">Manage Time Increase</a>] |  [<a class="anchor" href="password_Retrieval.php">Candidate's Password Retrieval</a>]

            <br/><br/>
            <form  id="frm2" class="style-frm">
                <fieldset><legend>Enter Candidate's Details</legend>
                    <div id="cand_data">
                        <table>

                            <tr>
                                <td><b> Candidate Type</b></td>
                                <td> 
                                    <select name ="candidatetype_inc" id="candidatetype_inc" class="input-block-level">
                                        <option value="">--Select Candidate Type --</option>
                                        <?php
                                        $query = "SELECT * FROM tblcandidatetypes";
                                        $stmt=$dbh->prepare($query);
                                        $stmt->execute();

                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $candidatetypeid = $row['candidatetypeid'];
                                            $candidatetype = $row['candidatetype'];

                                            echo "<option value ='$candidatetypeid'>$candidatetype</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b> Exam Type</b></td>
                                <td> 
                                    <select name="testid_inc" id="testid_inc"  class="input-block-level"><option value="">Select Category</option>
                                        <?php
                                        //populate all exams to take place today
                                        $query1 = "SELECT tbltestconfig.testid,tbltestconfig.testname,testtypename,session,semester FROM tbltestconfig 
							inner join tbltestcode ON tbltestconfig.testcodeid=tbltestcode.testcodeid
							inner join tbltesttype ON tbltestconfig.testtypeid=tbltesttype.testtypeid
							INNER JOIN tblexamsdate on tblexamsdate.testid=	tbltestconfig.testid
							where(tblexamsdate.date=curdate( ))";
                                        $stmt1=$dbh->prepare($query1);
                                        $stmt1->execute();
//                                        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                                        $count=$stmt1->rowCount();
                                       // echo $count; exit;
					    while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
 //                                           for ($i = 0; $i < $count; $i++) {
                                                echo $testid = $row1['testid'];
                                                $testname = strtoupper($row1['testname']);
                                                $testtypename = $row1['testtypename'];
                                                $session = $row1['session'];
                                                $semester = $row1['semester'];
                                            echo"<option value='$testid'> $testname-$testtypename- $session </option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b> Username</b></td>
                                <td> <input type="text" name="username_inc" value=""/></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <button  name="inc_time" id="inc_time" class="btn btn-primary">View Candidate's Time Usage</button> 
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </fieldset>
            </form>
            <div id="cand_data2" style="display:none">

            </div>
        </div>

    </div>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
    <script type ="text/javascript" src ="<?php echo siteUrl("assets/js/select2.min.js"); ?>"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $("#testid_inc").select2();
            });
            //:::::::::::::::View candidate's time usage:::::::::::::
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on("click", "#inc_time", function(event) {
                $.ajax({
                    type: 'post',
                    url: 'view_candidate_time.php',
                    data: $("#frm2").serialize()

                }).done(function(msg) {
                    //alert($("#timespent").val());
                    $("#cand_data2").empty().html(msg).slideDown();
                    $("#cand_data").slideUp();
                    //alert($("#timespent").val());
                    $("#slider").slider({
                        min: 0,
                        max: ($("#duration").val() - 0),
                        value: ($("#timespent").val() - 0),
                        change: function(event, ui) {
                            if (ui.value > ($("#initialtimespent").val() - 0))
                            {
                                $("#slider").slider('value', ($("#initialtimespent").val() - 0));
                                $("#timespent").val(($("#initialtimespent").val() - 0));
                                $("#el").html("Elapse:" + ($("#initialtimespent").val() - 0) + " " + "mins");
                                $("#rem").html("Remaining:" + ($("#duration").val() - ($("#initialtimespent").val() - 0)) + "" + "mins");

                            }
                            else
                                $("#timespent").val(ui.value);

                        },
                        slide: function(event, ui) {
                            var v = ui.value;
                            // Update the hidden input value during slide
                            $("#timespent").val(v);
                            $("#el").html("Elapse:" + v + " " + "mins");
                            $("#rem").html("Remaining:" + ($("#duration").val() - v) + "" + "mins");
                        }

                    });

                    $("#contentframe", top.document).height(0).height($(document).height());


                });
                return false;
            });
            //::::::::::::;Move Back to Candidate's Details:::::::

            $(document).on("click", "#btn-bk-step3", function(event) {

                $("#cand_data").slideDown();
                $("#cand_data2").slideUp();

                $("#contentframe", top.document).height(0).height($(document).height());

                return false;
            });

            //:::::::::::::::::Save Time changes:::::::::::::::

            $(document).on("click", "#btn-nxt-step3", function(event) {
                // Ensure timespent value is current before submitting
                var currentSliderValue = $("#slider").slider("value");
                $("#timespent").val(currentSliderValue);
                
                $.ajax({
                    type: 'post',
                    url: 'save_time_adjustment.php',
                    data: $("#frm3").serialize()
                }).done(function(msg) {
                    msg = ($.trim(msg) - 0);
                    if (msg == 1) {
                        alert("Time was adjusted successfully!");
                        // Optionally reload the page or update the display
                    } else {
                        alert("Operation was not successful! Please check if the candidate has an active exam session.");
                    }
                }).fail(function(xhr, status, error) {
                    alert("An error occurred while saving. Please try again.");
                });
                return false;
            });

    </script>
</body>
</html>
