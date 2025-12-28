<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
openConnection();
authorize();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Manage Centre, Venue & Host</title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

    </head>

    <body>

        <div id="container" class="container" style="padding-left: 20px">

            <div class ="row">
                <div class ="span12"> 
                    <h1>Manage Centre, Venue & Host</h1>
                    <br /><br />
                </div>
                <hr/>
                <div class ="span12"> 
                    <a href="centre.php">Create New Centre</a>
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    <a href="venue.php">Create New Venue</a>
                </div>
                <br/>
                <br/>
                <hr/>
                <div class ="span12">
                    <table class ="style-tbl" style="min-width: 900px">
                        <tr>
                            <th>S/No.</th>
                            <th>Centre Name</th>
                            <th>Venue Name</th>
                            <th>Location</th>
                            <th>Registered PCs</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        $query = "SELECT tblvenue.venueid,
                                        tblcentres.centrename as centrename,
                                        tblvenue.venueid,
                                        tblvenue.venuename as venuename,
                                        tblvenue.location,
                                        tblvenue.capacity
                                FROM tblvenue
                                INNER JOIN tblcentres
                                ON tblvenue.centreid=tblcentres.centreid
                                ";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();

                        $count = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $centrename = $row['centrename'];
                            $venuename = $row['venuename'];
                            $location = $row['location'];
                            $venueid = $row['venueid'];


                            $capacity = $row['capacity'];
                            echo "   
                                        <tr>
                                            <td>$count</td>
                                            <td>$centrename</td>
                                            <td>$venuename</td>
                                            <td>$location</td>
                                            <td>";
                            $query1 = "select count(*) as pcs from tblvenuecomputers where venueid=?";
                            $stmt1=$dbh->prepare($query1);
                            $stmt1->execute(array($venueid));
                            $pcs = $stmt1->fetch(PDO::FETCH_ASSOC);

                            echo $pcs['pcs'] . " [<a class='mng-pcs' href='javascript:void(0);' data-vid='$venueid'>manage</a>]";
                            echo"</td>
                                            <td>$capacity</td>
                                            <td>
                                                <a class='editvn' href ='javascript:void(0);' data-vid='$venueid' >Edit</a>
                                                |
                                                <a class='delvn' href ='javascript:void(0);' data-vid='$venueid' >Delete</a>
                                            </td>
                                        </tr>
                                    ";
                            $count++;
                        }
                        ?>
                    </table>
                </div>

            </div>
        </div>
        <script type="text/javascript" src="../../assets/js/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script type="text/javascript">
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());

            $(document).on('click','.mng-pcs',function(event){
                $("<div id='pcs-dialog'>Loading...</div>").dialog({title:'Select mac addresses to remove', modal:true, width:500, height:500, close:function(){
                        $(this).empty().remove();
                    }});
                $.ajax({
                    type:'POST',
                    url:'get_mng_pcs_frm.php',
                    data:{vid:$(this).attr("data-vid")}
                }).done(function(msg){
                    $("#pcs-dialog").html(msg);
                                
                    $("#contentframe", top.document).height(0).height($(document).height());

                });
            });
            
            $(document).on('click','.editvn',function(event){
                $("<div id='vn-dialog'>Loading...</div>").dialog({title:'Modify Venue', modal:true, width:400, height:300, close:function(){
                        $(this).empty().remove();
                    }});
                $.ajax({
                    type:'POST',
                    url:'get_venue_edit_frm.php',
                    data:{vid:$(this).attr("data-vid")}
                }).done(function(msg){
                    $("#vn-dialog").html(msg);
                                
                    $("#contentframe", top.document).height(0).height($(document).height());

                });
            });
            
            $(document).on('click','.delvn',function(event){
                if(window.confirm("Are you sure you want to delete this venue completely?")==1)
                {                       
                    $.ajax({
                        type:'POST',
                        url:'get_venue_del_frm.php',
                        data:{vid:$(this).attr("data-vid")}
                    }).done(function(msg){
                        if($.trim(msg)==1)
                        {
                            alert("Operation was successful");
                            document.location .reload();
                        }
                        else
                        {
                            alert("Operation was not successful");
                        }
                    });
                }

            });
            
            $(document).on('click','#editvn2',function(event){
            
                valid=true;
                $("#venuefrm input").each(function(){
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
                    url:'commit_venue_edit.php',
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
            
            $(document).on('click','#removemac',function(event){
                $.ajax({
                    type:'POST',
                    url:'removemacs.php',
                    data:$("#macs").serialize()
                }).done(function(msg){
                    alert(msg);
                    $("#pcs-dialog").empty().remove();
                });
                return false;
            });
            
            $(document).on("keydown","#edt-cap", function(event){
                keycode=event.keyCode;
                var validkeys=[8, 16, 17, 35, 36, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
                if($.inArray(keycode, validkeys)==-1)
                    return false;
            });
            
            $(document).on("copy paste", '#edt-cap', function (e) {
                e.preventDefault();
            }); 
        </script>
    </body>
</html>