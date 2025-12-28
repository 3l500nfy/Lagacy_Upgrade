<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
authorize();
openConnection();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>New Centre</title>
        <?php javascriptTurnedOff(); ?>
        <style type="text/css">
            .modaldialog{
                display: none;
            }
        </style>

        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>
        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header">
                <h1>Add New Center & Venue Configuration</h1>
                <br/>
                <br/>
                <br/>
            </div>

            <div>
                <form name="centrefrm" id="centrefrm" class="style-frm">
                    <table>
                        <tr>
                            <td>Enter Center Name:</td>
                            <td>
                                <input type="text" name="name" id="name"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><p style="color: green">Create venues for the newly created center through the link (Manage Center,Venue & System)</p></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <button type ="submit" name="submit" id="submit" class ="btn btn-primary">Save</button>
                            </td>
                        </tr>
                        </div>
                </form>
                <script type="text/javascript" src="../../assets/js/jquery-1.9.0.js"></script>
                <script type="text/javascript" src="../../assets/js/jquery-ui-1.10.0.custom.min.js"></script>

                <script type="text/javascript">
                    $(window.top.document).scrollTop(0);//.scrollTop();
                    $("#contentframe", top.document).height(0).height($(document).height());

                    $(document).on('click','#submit',function(event){
                        if($.trim($("#name").val())==""){
                    
                            $(this).attr('borderColor','red');
                            alert("Invalid form input.");
                            return false;
                        }
               
                        $.ajax({
                            type:'POST',
                            url:'centre_exec.php',
                            data:$("#centrefrm").serialize()
                        }).done(function(msg){ 
                            if($.trim(msg)==1)
                            {
                                alert("Operation was successful.");
                                document.location.reload();
                            }
                            else
                                if($.trim(msg)==2)
                            {
                                alert("Centre already exist.");
                            }
                            else
                                alert("Operation was not successful.");
                        });
                        return false;
                    });
                </script>
                </body>
</html>