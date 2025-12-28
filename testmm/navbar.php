<?php require_once(realpath(dirname(__FILE__) . "/../lib/globals.php")) ?>
<?php require_once(realpath(dirname(__FILE__) . "/../lib/security.php")) ?>

<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" title="ABU Computer Based Testing" href="<?php echo siteUrl("index.php") ?>" style="position: relative;">
                Computer Based Test</a>
            <div class="nav-collapse" style="padding-left: 50px;">
               
                <ul class="nav pull-right">
                    <?php
                    if (is_authenticated()) {
                        echo "  <li class=''><a href='#'>" . $_SESSION['MEMBER_FULLNAME'] . "</a></li>
                                <li class=''><a id='abandon' href='#'><font size=4>|Abandon the Exams|</font></a></li>";
                        //    echo" <li class=''><a href='" . siteUrl('logout.php') . "'>Log Out</a></li> ";
                    } 
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>