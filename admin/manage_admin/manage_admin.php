<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
$test_admins = get_test_admins_as_array();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
        <style type="text/css">
            .anchor{
                color:#999999;
            }
            .anchor:hover{
                color:black;
            }
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">
        <div id="framecontent">
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Manage Admin</h2><br />
            [<a class="anchor"href="../test_admin/test_admin.php">Manage Test Administrator</a>] | [<a class="anchor" href="../pc_registrar/pc_registrar.php">Manage PC Registrars</a>]
            <form class="style-frm" id="test-admin-frm" >
                <br />
                <fieldset><legend>Add Admin</legend>
                    <b>Staff P.No.: </b> <input type="text" name="pno" id="pno" /> <input type="submit" name="load" id="load" value="Generate" />
                    <div id="staff-detail">
                    </div>
                </fieldset><br />
                <fieldset id="test-admin-list"><legend>Available admin(s)</legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>P.No.</th><th>FULL NAME</th><th>Department</th><th>ACTION</th></tr>
                        <?php
                        $c = 1;
                        foreach ($test_admins as $test_admin) {
                            $pno = get_staff_pno($test_admin);
                            $biodata = get_staff_biodata($pno);
                            $fullname = ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames']));
                            echo "<tr><td>" . $c++ . "</td><td>" . strtoupper($pno) . "</td><td>" . $fullname . "</td><td>" . ucwords(strtolower($biodata['departmentname'])) . "</td><td>[<a data-uid='$test_admin' data-fname='$fullname' class='remove' href='javascript:void(0)'>Remove</a>]</td></tr>";
                        }
                        if ($c == 1)
                            echo"<tr><td colspan='6'>No Admin available.</td></tr>";
                        ?>
                    </table>
                </fieldset>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">
                    
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height($(document).height());
            
            $(document).on('click','#load',function(event){
                $("#staff-detail").html("Loading... ");
                //alert($("#pno").val());
                if($.trim($("#pno").val())=="")
                {
                    $("#staff-detail").html("No record found.");
                    return false;
                }
                    
                $.ajax({
                    type:'POST',
                    url:'get_admin_frm.php',
                    data:{pno:$("#pno").val()}
                }).done(function(msg){
                    $("#staff-detail").html(msg);
                    $("#contentframe", top.document).height(0).height($(document).height());
                });
                return false;
            });
            
            $(document).on('click','#submit',function(event){ 
                $("#submit-info").html("processing... ");
                //alert($("#pno").val());
                $.ajax({
                    type:'POST',
                    url:'register_admin.php',
                    data:$("#test-admin-frm").serialize()
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    if(msg==0)
                    {
                        alert("Operation was not successful!");
                    }
                    if(msg==1)//success
                    {
                        alert("User was successfully registered!");
                        window.location.reload(true);
                    }
                    if(msg==2)// access denied
                    {
                        alert("Access denied");
                    }
                    if(msg==4)// invalid input
                    {
                        alert("Invalid input!");
                    }
                    $("#submit-info").html("");
                });
                return false;
            });

            $(document).on('click','.remove',function(event){ 
               
                //alert($("#pno").val());
                var dis=$(this);
                var uid=dis.attr("data-uid");
                var fname=dis.attr("data-fname");
                if(window.confirm("Are you sure you want to remove \""+fname+"\" from existing admin list."))
                {
 $.ajax({
                    url:'remove_admin.php',
                    type:'POST',
                    error:function(){alert("error");},
                    data:{uid:uid}
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    //alert(msg);
                    if(msg==1)
                    {
                        window.location.reload(true);  
                    }
                    else
                    {
                        alert("Operation was not successful!");
                    }
                });
                }
                return false;
            });
            

        </script>
    </body>
</html>