<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
openConnection();
authorize();
$test_administrators = get_test_administrators_as_array();
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
            <h2 class="cooltitle2" style="border-bottom-style:solid;">Manage Test Administrators</h2><br />
            [<a class="anchor"href="../manage_admin/manage_admin.php">Manage Admin</a>] | [<a class="anchor" href="../pc_registrar/pc_registrar.php">Manage PC Registrars</a>]
            <form class="style-frm" id="test-admin-frm" >
                <br />
                <fieldset><legend>Add/Modify Test Administrator</legend>
                    <b>Staff P.No.: </b> <input type="text" name="pno" id="pno" /> <input type="submit" name="load" id="load" value="Generate" />
                    <div id="staff-detail">
                    </div>
                </fieldset><br />
                <fieldset id="test-administrator-list"><legend>Available test administrator(s)</legend>
                    <table class="style-tbl">
                        <tr><th>S/N</th><th>P.No.</th><th>FULL NAME</th><th>Department</th><th>ACTION</th></tr>
                        <?php
                        $c = 1;
                        foreach ($test_administrators as $test_administrator) {
                            $pno = get_staff_pno($test_administrator);
                            $biodata = get_staff_biodata($pno);
                            $fullname = ucfirst(strtolower($biodata['surname'])) . ", " . ucfirst(strtolower($biodata['firstname'])) . " " . ucfirst(strtolower($biodata['othernames']));
                            echo "<tr><td>" . $c++ . "</td><td>" . strtoupper($pno) . "</td><td>" . $fullname . "</td><td>" . ucwords(strtolower($biodata['departmentname'])) . "</td><td>[<a data-uid='$test_administrator' data-fname='$fullname' class='remove' href='javascript:void(0)'>Remove</a>]</td></tr>";
                        }
                        if ($c == 1)
                            echo"<tr><td colspan='6'>No test Administrator available.</td></tr>";
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
                    url:'get_administrator_frm.php',
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
                    url:'register_administrator.php',
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

            $(document).on('click','.remove_compositor',function(event){ 
               
                //alert($("#pno").val());
                var dis=$(this);
                var tid=dis.attr("data-tid");
                var uid=dis.attr("data-uid");
                var fname=dis.attr("data-fname");
                if(window.confirm("Are you sure you want to remove \""+fname+"\" from existing test compositors list."))
                {
                    $.ajax({
                        type:'POST',
                        url:'remove_compositor.php',
                        data:{tid:tid, uid:uid}
                    }).done(function(msg){ //alert(msg);
                        msg=($.trim(msg)-0);
                        if(msg==0)
                        {
                            alert("Operation was not successful!");
                        }
                        if(msg==1)//success
                        {
                            alert("User was successfully remove from list of compositors!");
                            window.location.reload(true);
                        }
                        if(msg==2)// access denied
                        {
                            alert("Access denied");
                        }
                        if(msg==3)//date passed
                        {
                            alert("Test date exceeded");
                        }
                        if(msg==4)// invalid input
                        {
                            alert("Invalid input!");
                        }
                        if(msg==5)//
                        {
                            alert("No subject selected!");
                        }
                        $("#submit-info").html("");
                    });
                }
                return false;
            });
            
            $(document).on('click','.onoff', function(event){
                var action=$(this).attr('data-action');
                var pno= $(this).attr('data-pno');
                var dis=$(this);
                // alert(pno);
                $.ajax({
                    url:'modify_test_admin.php',
                    type:'POST',
                    error:function(){alert("error");},
                    data:{action:action, pno:pno}
                }).done(function(msg){ //alert(msg);
                    msg=($.trim(msg)-0);
                    //alert(msg);
                    if(msg==1)
                    {
                        if(action=='disable')
                        {
                            dis.attr('data-action','enable');
                            dis.html('Enable');
                        }else{
                            dis.attr('data-action','disable');
                            dis.html('Disable');
                        }
                    }
                    else
                    {
                        alert("Operation was not successful!");
                    }
                });
                return false;     
            });
            
            $(document).on('click','.remove', function(event){
                var uid= $(this).attr('data-uid');
                var dis=$(this);
                // alert(pno);
                $.ajax({
                    url:'remove_test_admin.php',
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
                return false;     
            });

        </script>
    </body>
</html>