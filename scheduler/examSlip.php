<?php









		
	
	

	





//require_once("globals.php");
//require_once("lib/database.php");
//require_once('logout.php');
//require_once('checklogin.php');
//require_once('checkpayment.php');



?>

<!DOCTYPE html>
<html>

    <head>
        <title>Ahmadu Bello University, Zaria PUTME Application:Exams Slip</title>
        <script src="js/jquery/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery/jquery.unobtrusive-ajax.min.js" type="text/javascript"></script>
        <script src="js/site/Site.js" type="text/javascript"></script>
        <script src="js/jquery.leanModal.min.js" type="text/javascript"></script>
        <!-- CSS Imports -->
        <link href="css/jquery/jquery_ui_datepicker.css" rel="stylesheet" type="text/css">
        <link href="css/site/css3buttons.css" rel="stylesheet" type="text/css">
        <link href="css/centeredmenu.css" rel="stylesheet" type="text/css">
        <link href="css/site/print.css" rel="stylesheet" type="text/css">
        <script src="SpryAssets/SpryTabbedPanels.js" type="text/javascript"></script>

        <link href="SpryAssets/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            $(function() {
                window.print();
            });
          
        </script>
        <!--
        <style type="text/css">

.style1 {	font-size: x-large;
	font-weight: bold;
}
.style2 {	font-size: 18px;
	font-weight: bold;
}

        </style>
        -->
</head>

    <body>

        <div id="page">
            <div id="main">
<?php include ('header.php'); ?>
<?php //echo getCenteredMenu("examSlip.php");?>
                <div style="width: 800px; margin: 30px auto;"><h2 style="text-align: center;"><?php //echo $purpose; ?></h2>
                  <table width="60%" border="1" align="center" cellpadding="1" cellspacing="0" bordercolor="#000000">
                    <tr bordercolor="#F0F0F0">
                      <td bordercolor="#FFFFFF"><div align="center"><span class="style1"><u>POST UTME ACKNOWLEDGEMENT/EXAMINATION SLIP</u></span></div></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td>&nbsp;</td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td><fieldset>
                        <legend class="style2">Personal Details</legend>
                        <table width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td width="32%">JAMB Reg. Number: </td>
                            <td width="68%"><?php echo $row_rsStaff['regNum']; ?></td>
                            <td width="68%" rowspan="7"><?php echo "<img src=".$pix." height='178' width='142' border='1'>";?></td>
                          </tr>
                          <tr>
                            <td>Name: </td>
                            <td><?php echo $row_rsStaff['surname']; ?></td>
                          </tr>
                          <tr>
                            <td>Gender: </td>
                            <td><?php echo $row_rsStaff['gender']; ?></td>
                          </tr>

                          <tr>
                            <td>Email:</td>
                            <td><?php echo $row_rsStaff['email']; ?></td>
                          </tr>
                          <tr>
                            <td>GSM:</td>
                            <td><?php echo $row_rsStaff['gsm']; ?></td>
                          </tr>
                          <tr>
                            <td>Faculty applied</td>
                            <td><?php echo $row_rsStaff['FACULT1']; ?></td>
                          </tr>
                          <tr>
                            <td>Course of Study: </td>
                            <td><?php echo $row_rsStaff['degree']; ?></td>
                          </tr>
                          <tr>
                            <td>JAMB Total Score: </td>
                            <td><?php echo $row_rsStaff['JAMBScore']; ?></td>
                          </tr>
                        </table>
                      </fieldset></td>
                    </tr>
                    <tr bordercolor="#ffffff">
                     <!-- <td><fieldset>
                        <legend class="style2"> Payment Details</legend>
                        <table width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td width="23%">Transaction ID:</td>
                            <td width="77%"><?php //echo $row_rsStaff['transcode']; ?></td>
                          </tr>
                          <tr>
                            <td>Amount:</td>
                            <td><?php //echo $amount; ?>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>Payment Status:</td>
                            <td><?php //echo $status; ?></td>
                          </tr>
                          <tr>
                            <td>Date Paid</td>
                            <td><?php //echo $dpaid; ?></td>
                          </tr>
                        </table>
                      </fieldset></td> -->
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td><fieldset>
                        <legend class="style2">Post UME Examination Subjects</legend>
                        <table width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td width="25%">1.</td>
                            <td width="75%">English Language</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td><?php echo $row_rsStaff['SUB2']; ?></td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td><?php echo $row_rsStaff['SUB3']; ?></td>
                          </tr>
                          <tr>
                            <td valign="top">4.</td>
                            <td><?php echo $row_rsStaff['SUB4']; ?></td>
                          </tr>
                        </table>
                      </fieldset></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td><fieldset>
                        <legend class="style2">Post UME Test Schedule</legend>
                        <table width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            
                            <td width="25%">Venue:</td>
                            <td><?php echo $candvenue; ?></td>
                          </tr>
                          <tr>
                            
                            <td width="25%">Batch:</td>
                            <td><?php echo $candbatch; ?></td>
                          </tr>
                          <tr>
                            
                            <td width="25%">Date:</td>
                            <td><?php echo $candday; ?></td>
                          </tr>
                          <tr>
                            
                            <td width="25%">Time:</td>
                            <td><?php echo $candtime; ?></td>
                          </tr>
                        </table>
                      </fieldset></td>
                    </tr>
                    <tr>
                    <tr>
                      <td bordercolor="#FFFFFF"><div align="justify"><strong>Please bring 2 copies of this slip with you on the day of your examination. You will not be allowed to write the Post UME Examination if you fail to  present this slip and will also forfeit your chance of being considered for admission.</strong></div></td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td>&nbsp;</td>
                    </tr>
                    <tr bordercolor="#FFFFFF">
                      <td><div align="center">
                          <input name="print" type="button" class="btn" value="Print Slip" onClick="javascript:window.print(); location.href='menu.php'; " />
                        &nbsp;&nbsp;
                          <input name="close" type="button" class="btn" value="Close" onClick="javascript:location.href = 'index.php';" />
                      </div></td>
                    </tr>
                  </table>
                  <p style="text-align: center;">&nbsp;</p>
           <div class="footer" style="width: 100%; clear: both;">	
        <span style="position: relative; float: right; height: 20px; border-bottom: 2px #3A5D00 solid; padding-left: 5px;"> &copy;2011-Ahmadu Bello University </span></div>
              </div>
            </div>
        </div>
    </body>

</html>
<?php
mysql_free_result($schedule);
?>
