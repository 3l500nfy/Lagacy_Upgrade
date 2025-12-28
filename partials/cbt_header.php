<!DOCTYPE html>
<html lang="en">
    <head>
        <title>GITHUB TetfundTest<?php echo $pgtitle; ?></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css')?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css')?>" type="text/css" rel="stylesheet"></link>
        
    </head>
    <body> 
        <div id="bannerparent">
        <div id="banner">
            <div id="banner-img" style="background-color: white;">
                <img src="<?php echo siteUrl("assets/img/dariya_logo1.png")?>" style="" />
            </div>
            <div id="navbar">
                <div class="nav <?php if($navindex==1) echo "active-nav"; ?>">
                    <a href="<?php echo siteUrl("admin.php"); ?>"><img class="nav-img" src="<?php echo siteUrl("assets/img/cbt_home.png");?>" title="Home"/> Home</a>
                </div>
                <div class="nav <?php if($navindex==2) echo "active-nav"; ?>">
                    <a href="<?php echo siteUrl("configuration/home.php"); ?>">Test Configuration</a>
                </div>
                <div class="nav <?php if($navindex==3) echo "active-nav"; ?>">
                    <a href="<?php echo siteUrl("admin_toolbox/index.php"); ?>">Admin Toolbox</a>
                </div>
                <div class="nav <?php if($navindex==4) echo "active-nav"; ?>">
                    <a href="<?php echo siteUrl("question_authoring/index.php"); ?>">Question Authoring</a>
                </div>
                <div class="nav <?php if($navindex==5) echo "active-nav"; ?>">
                    <a href="<?php echo siteUrl("reports/reports.php"); ?>">Reports</a>
                </div>
                <div id="userdetail">
                   Welcome <?php echo $_SESSION['MEMBER_FULLNAME']; ?> &nbsp;&nbsp;&nbsp;<a href="<?php echo siteUrl("logout.php"); ?>">Logout</a> | <a href="<?php echo siteUrl("changepassword.php"); ?>">Change Password</a>
                </div>
            </div>
        </div>
        </div>
        <div id="content">
