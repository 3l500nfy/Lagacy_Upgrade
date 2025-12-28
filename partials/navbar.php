<?php require_once(realpath(dirname(__FILE__) . "/../lib/globals.php")) ?>
<?php require_once(realpath(dirname(__FILE__) . "/../lib/security.php")) ?>

<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" title="Github(.)com/computerbased/tetfund" href="<?php echo siteUrl("index.php") ?>" style="position: relative;">
                Home 
            </a>
            <div class="nav-collapse" style="padding-left: 50px;">
                <ul class="nav"> 
                    <li class="">
                        <a href="<?php echo siteUrl('configuration/index.php') ?>">Configurations</a>
                    </li>
                    <li class="">
                        <a href="<?php echo siteUrl('admin_toolbox/index.php') ?>">Admin Toolbox</a>
                    </li>
                    <li class="">
                        <a href="<?php echo siteUrl('scheduler/index.php') ?>">Scheduling</a>
                    </li>
                    <li class="">
                        <a href="<?php echo siteUrl('reports/index.php') ?>">Reports</a>
                    </li>
                </ul>
                <ul class="nav pull-right">
                    <?php
                    if (is_authenticated()) {
                        echo "  <li class=''><a href='#'>" . $_SESSION['MEMBER_FULLNAME'] . "</a></li>
                                <li class=''><a href='" . siteUrl('changepassword.php') . "'>Change Password</a></li>
                                <li class=''><a href='" . siteUrl('logout.php') . "'>Log Out</a></li>  
                            ";
                    } else {
                        echo "  <li class=''><a href='" . siteUrl('login.php') . "'>Login</a></li>
                                <li class=''><a href = '" . siteUrl('usersignup.php') . "'>Sign up</a></li>
                            ";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>