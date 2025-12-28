<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT<?php echo $pgtitle; ?></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
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
                       <!-- <b style="padding:2px;text-decoration:underline;">Mode:</b> <span><?php /*echo $testinfo['displaymode']; */?> mins</span><br />-->

                        <span style="visibility:hidden;" id='remainingsecond'><b><?php echo $testinfo['remainingsecond']; ?> </b></span>
                        <input type="hidden" id='remainingsecond1' value= " <?php echo $testinfo['remainingsecond']; ?> " >    
                        <input type="hidden" id='displaymode' value= " <?php echo $testinfo['displaymode']; ?> " > 
                        <div id="endorsement"><a href="javascript:void(0);">Invigilator's Sign</a></div>
                    </div>
                    <div id="tsbj">
                        <div id="subjects">
                            <?php
                            if ($completion == true) {
                                echo"You have already taken the test.";
                            } else {
                                getsubject($candidateid, $testid);
                            }
                            global $dbh;
                            ///get the remaining number of question
                            $query2 = "SELECT count(distinct(questionid)) as remaining from tblpresentation where(candidateid='$candidateid' and testid='$testid' and
questionid not in (SELECT questionid from tblscore where(candidateid='$candidateid' and testid='$testid')))";
                            $stmt=$dbh->prepare($query2);
                            $stmt->execute();

                            if ($stmt->rowCOunt() > 0) {
                                $row=$stmt->fetch(PDO::FETCH_ASSOC);
                                $remaining = $row['remaining'];
                            }
                            ?>
                            <div class="remaining">Remaining Question(s) : <span id='remaining' ><?php echo $remaining; ?> </span></div>
                        </div>
                        
                        <div id="candid-img">
                            <div id="img-div" style="margin-top:-120px; background-color: #ffffff; border-style: solid; border-color:green;border-width: 1px;" >
                                <img src="
                                <?php
//				echo "https://putme.abu.edu.ng/candpixsjamb/".strtoupper($matric).".JPG";
				echo "http://portal.abu.edu.ng/studpix/".strtoupper($matric).".JPG";

/*				if (file_exists("https://portal.abu.edu.ng/studpix/".strtoupper($matric).".JPG")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtoupper($matric).".JPG";
                                } else if (file_exists("https://portal.abu.edu.ng/studpix/".strtoupper($matric).".PNG")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtoupper($matric).".PNG";
                                } else if (file_exists("https://portal.abu.edu.ng/studpix/".strtoupper($matric).".GIF")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtoupper($matric).".GIF";
                                } else if (file_exists("https://portal.abu.edu.ng/studpix/".strtolower($matric).".jpg")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtolower($matric).".jpg";
                                } else if (file_exists("https://portal.abu.edu.ng/studpix/".strtolower($matric).".png")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtolower($matric).".png";
                                } else if (file_exists("https://portal.abu.edu.ng/studpix/".strtolower($matric).".gif")) {
                                    echo "https://portal.abu.edu.ng/studpix/".strtolower($matric).".gif";
                                } else {
                                    echo siteUrl("assets/img/photo.png");
                                }

                                if (file_exists("../studpix/".strtoupper($matric).".JPG")) {
                                    echo siteUrl("studpix/".strtoupper($matric).".JPG");
                                } else {
                                    echo siteUrl("assets/img/photo.png");
                                }
*/

/*                                if (file_exists("../studpix/".strtoupper($matric).".JPG")) {
                                    echo siteUrl("studpix/".strtoupper($matric).".JPG");
                                } else if (file_exists("../studpix/".strtoupper($matric).".PNG")) {
                                    echo siteUrl("studpix/".strtoupper($matric).".PNG");
                                } else if (file_exists("../studpix/".strtoupper($matric).".GIF")) {
                                    echo siteUrl("studpix/".strtoupper($matric).".GIF");
                                } else if (file_exists("../studpix/".strtolower($matric).".jpg")) {
                                    echo siteUrl("studpix/".strtolower($matric).".jpg");
                                } else if (file_exists("../studpix/".strtolower($matric).".png")) {
                                    echo siteUrl("studpix/".strtolower($matric).".png");
                                } else if (file_exists("../studpix/".strtolower($matric).".gif")) {
                                    echo siteUrl("studpix/".strtolower($matric).".gif");
                                } else {
                                    echo siteUrl("assets/img/photo.png");
                                }

*/                                ?>
                                     "  style="width:110px; height: 130px;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content">
