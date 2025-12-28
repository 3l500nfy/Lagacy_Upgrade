<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT<?php echo $pgtitle; ?></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="css/modern-dialog.css" type="text/css" rel="stylesheet"></link>
	<style>
		.remaining{
			font-size:1.5em;font-weight:bold
				}
		#test-details{
			font-weight:bold
				}
	</style>

    </head>
    <body> 
        <div id="bannerparent">
            <div id="banner">
                <div id="banner-img" style="background-color: white;">
                    <img src="<?php echo siteUrl("assets/img/dariya_logo1.png") ?>" style="" />
                </div>
                <div id="navbar">
                    <div id="test-details">
                        <b style="padding: 2px;text-decoration:underline;">FULL NAME:</b><span> <?php echo $candidatename . " (" . trim(strtoupper($matric)) . ")"; ?></span><br />
                        <b style="padding:2px;text-decoration:underline;">TEST:</b> <span><?php echo $testinfo['name']; ?></span><br />
                        <b style="padding:2px;text-decoration:underline;">DURATION:</b> <span><?php echo $testinfo['duration']; ?> mins</span><br />

                        <span style="visibility:hidden;" id='remainingsecond'><b><?php echo $testinfo['remainingsecond']; ?> </b></span>
                        <input type="hidden" id='remainingsecond1' value= " <?php echo $testinfo['remainingsecond']; ?> " >    
                        <input type="hidden" id='displaymode' value= " <?php echo $testinfo['displaymode']; ?> " > 
                        <div id="endorsement"><a href="javascript:void(0);">Verification</a></div>
                    </div>
                    <div id="tsbj">
                        <div id="subjects">
                            <?php
                            if ($completion == true) {
                                echo"You have already taken the test.";
                            } else {
                                getsubject($candidateid, $testid);
                            }
                            ///get the remaining number of question
                            $query2 = "SELECT count(distinct(questionid)) as remaining from tblpresentation where(candidateid='$candidateid' and testid='$testid' and
questionid not in (SELECT questionid from tblscore where(candidateid='$candidateid' and testid='$testid')))";
                            $stmt = $dbh->prepare($query2);
                            $result = $stmt->execute(); 
                            $numrows = $stmt->rowCount();
                           // if ($numrows > 0) {
                            if ($numrows == 1) {
                                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                $remaining = $row['remaining'];
                                //$remaining = mysql_result($query2, 0, 'remaining');
                                //mysql_free_result($query2);
                            }
                            ?>
                            <div class="remaining">Remaining Question(s) : <span id='remaining' ><?php echo $remaining; ?> </span></div>
                        </div>
                        
                        <div id="candid-img">
                            <div id="img-div" style="margin-top:-120px; background-color: #ffffff; border-style: solid; border-color:green;border-width: 1px;" >
                                <img src="
                                <?php

				// echo "http://portal.abu.edu.ng/pixx/".strtoupper($matric).".JPG";
/*                                if (file_exists("../picts/".strtoupper($matric).".JPG")) {
                                    echo siteUrl("picts/".strtoupper($matric).".JPG");
                                } else {
                                    echo siteUrl("picts/".strtoupper($matric).".PNG");
                                }
*/
				echo siteUrl("assets/DP/photo.jpg");
                               ?>
                                     "  style="width:110px; height: 130px;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content">
