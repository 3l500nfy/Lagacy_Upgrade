<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");
openConnection();
global $dbh;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Invigilator's toolkit</title>
    <style>
        .frm-request-cand-info, .frm-cand-profile, #cand_data {
            margin-left: auto;
            margin-right: auto;
            width: 500px;
            border-style: solid;
            border-width: 1px;
            border-color: #cccccc;
            -moz-border-radius: 3px;
            border-radius: 3px;
            -webkit-border-radius: 3px;
            padding: 5px;
            -webkit-box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.5);
            -moz-box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.5);
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.5);

        }

        .links {
            display: inline-block;
            padding: 5px;
        }
    </style>
    <link href="../../assets/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
    <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

    <?php include_once "../../partials/jsimports.php"; ?>

</head>
<body>
<div id="framecontent" class="container" style="padding-left: 20px">

    <h1 class="cooltitle2" style="border-bottom-style:solid;">Invigilator's Toolkit</h1>

    <form class="style-frm">
        <fieldset id="test-user-action-list">
            <legend>Select an operation:</legend>
            <ul>
                <li><a href="manage_restore.php">
                        <div class="links">Restore Logged Out Candidate</div>
                    </a></li>
                <li><a href="manage_time.php">
                        <div class="links">Increase Candidate's Time</div>
                    </a></li>
                <li><a href="password_Retrieval.php">
                        <div class="links">Candidate's Password Retrieval</div>
                    </a></li>
            </ul>
        </fieldset>
    </form>
</div>

<script type="text/javascript">

    $("#mytabs a").click(function (e) {//alert('aliyu');
        $(this).tab('show');
    });
    $(window.top.document).scrollTop(0);//.scrollTop();
    $("#contentframe", top.document).height(0).height($(document).height());
</script>
</body>
</html>