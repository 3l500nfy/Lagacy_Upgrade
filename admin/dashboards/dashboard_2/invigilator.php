<?php
if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");
require_once('../../../lib/security.php');
if (!has_roles(array("Test Administrator")))
    header("Location:" . siteUrl("403.php"));


openConnection();

$uid = $_SESSION['MEMBER_USERID'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../../../partials/cssimports.php") ?>
        <style type="text/css">
            .userdetail tr td:first-child {
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <?php include_once("../../../partials/navbar.php"); ?>
        <div id="container" class="container">
            <div class="page-header">
                <h2>Assign Invigilator Role</h2>
                <a href="admin_dashboard.php">&Lt; Back to Dashboard</a>
            </div>
            <?php require_once('../../../partials/notification.php'); ?>
            <div id="pno">
                <form>
                    <input type="text" name="userpno" id="userpno" placeholder="Enter User's PNo." /> &nbsp;&nbsp; <button id="pno_btn">Load</button>
                    <div id="userdetail">

                    </div>
                </form>
            </div>

        </div>
        <?php include_once dirname(__FILE__) . "/../../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../../partials/jsimports.php" ?>;

        <script type="text/javascript">
            $(document).on("change","#test",function(event){
                var obj=$(this);
               // alert(obj.val());
                if(obj.val()=="")
                {
                    $('#tdt, #tvn').html("");
                    return;
                }
                $('#tdt').html("Loading test date...");
                $.ajax({
                    type:'POST',
                    url:'get_test_dates.php',
                    data:{testid:obj.val(), user:$("#usrid").val()}
                }).done(function(msg){ //alert(msg);
                    $('#tdt').html(msg);
                });
            });
    
            $(document).on("change","#tdt2",function(event){
                var obj=$(this);
               // alert(obj.val());
                if(obj.val()=="")
                {
                    $('#tvn').html("");
                    return;
                }
                $('#tvn').html("Loading test venue...");
                $.ajax({
                    type:'POST',
                    url:'get_test_venues.php',
                    data:{testid:$("#test").val(), tdt:obj.val(), user:$("#usrid").val()}
                }).done(function(msg){ //alert(msg);
                    $('#tvn').html(msg);
                });
            });
    

            $(document).on('click','#pno_btn',function(event){
                if($.trim($("#userpno").val())=="")
                    return false;
                else
                {
                    $.ajax({
                        type:'POST',
                        async:true,
                        url:'get_user_detail2.php',
                        data:{pno:$("#userpno").val()}
                    }).done(function(msg){
                        $("#userdetail").html(msg);
                    });
                    return false;
                }
            });
            
            $(document).on('click','#addrole', function(event){

                $.ajax({
                    type:'POST',
                    async:true,
                    url:'invigilator_exec.php',
                    data:$("#pno form").serialize()
                }).done(function(msg){
                    alert(msg);
                    $("#userdetail").html("");
                });
                return false;
                
            });
        </script>
    </body>
</html>
