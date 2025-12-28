<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("cvs_functions.php");
openConnection();
authorize();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>New venue</title>
        <?php javascriptTurnedOff(); ?>
        <style>
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
                <h1>Add New Venue</h1>
            </div>
            <div>
                <form method="POST" name="venuefrm" id="venuefrm" class="style-frm">
                    <table>                  
                        <tr>
                            <td>Centre: </td>
                            <td>
                                <select name ="centre" id ="centre">
                                    <option value="">--Select centre--</option>
                                    <?php
                                    echo get_center_as_options();
                                    ?>                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Venue Name: </td>
                            <td>
                                <input type ="text" name ="venue" id ="venue"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Location:</td>
                            <td>
                                <input type ="text" name ="location" id ="location"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Capacity:</td>
                            <td>
                                <input type ="text" name ="capacity" id ="capacity"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type ="submit" name ="submit" id="submit" class ="btn btn-primary">Save</button>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>

                            </td>
                        </tr> 
                    </table>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="../../assets/js/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script type="text/javascript">
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on('click','#submit',function(event){
            
                valid=true;
                $("#venuefrm input, #venuefrm select").each(function(){
                    //alert($(this).val())
                    if($.trim($(this).val())=="")
                    {
                        $(this).attr('borderColor','red');
                        valid=false;
                    }
                });
                if(valid==false)
                {
                    alert("Invalid form input.");
                    return false;
                }
            
                $.ajax({
                    type:'POST',
                    url:'venue_exec.php',
                    data:$("#venuefrm").serialize()
                }).done(function(msg){ 
                    if($.trim(msg)==1)
                    {
                        alert("Operation was successful.");
                        document.location.reload();
                    }
                    else
                        if($.trim(msg)==2)
                    {
                        alert("Venue already exist.");
                    }
                    else
                        alert("Operation was not successful.");
                });
                return false;
            });
            
            
            $(document).on("keydown","#capacity", function(event){
                keycode=event.keyCode;
                var validkeys=[8, 16, 17, 35, 36, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
                if($.inArray(keycode, validkeys)==-1)
                    return false;
            });
            
            $(document).on("copy paste", '#capacity', function (e) {
                e.preventDefault();
            }); 

        </script>
    </body>
</html>