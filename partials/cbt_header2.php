<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ABU CBT<?php echo $pgtitle; ?></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

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

                    </div>
                    <div id="tsbj">
                        <div id="subjects">
                            <?php
                            if ($completion == true) {
                                echo"You have already taken the test.";
                            } else {
                                getsubject($candidateid, $testid);
                            }
                            ?>
                        </div>
                        <div id="candid-img">
                            <div id="img-div" style="margin-top:-103px; background-color: #ffffff; border-style: solid; border-color:green;border-width: 1px;" >
                                <?php echo get_current_photo($matric); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content">
