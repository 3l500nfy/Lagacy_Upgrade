<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$pnumber = clean($_REQUEST['staffno']);


$query = "      SELECT tblemployee.employeeid,
                tblemployee.firstname,
                tblemployee.surname,
                tblemployee.othernames,
                tblemployee.personnelno,
                tblemployee.departmentid,
                tbldepartment.name AS departmentname
                from tblemployee
                INNER JOIN tbldepartment
                ON tblemployee.departmentid = tbldepartment.departmentid
                WHERE tblemployee.personnelno = ?
                ";
$stmt=$dbh->prepare($query);
$stmt->execute(array($pnumber));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $employeeid = $row['employeeid'];
    $firstname = $row['firstname'];
    $surname = $row['surname'];
    $othernames = $row['othernames'];
    $fullname = strtoupper($surname) . ", " . $firstname . " " . $othernames;
    $departmentname = $row['departmentname'];
    $personnelno = $row['personnelno'];
    break;
}
?>
<?php
$query1 = "SELECT * FROM user WHERE staffno  = ?";
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($pnumber));

while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $userid = $row1['id'];
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../../partials/cssimports.php") ?>
    </head>

    <body>
        <?php include_once("../../partials/navbar.php"); ?>
        <div id="container" class="container">
            <div class="page-header">
                <h2>Role/Permission Assignment</h2>
            </div>
            <?php require_once('../../partials/notification.php'); ?> 
            <div class ="row">
                <div class ="span4">
                    <ul class="thumbnails">
                        <li class="span3">
                            <a href="#" class="thumbnail">
                                <img src ="photo.png" />
                            </a>
                        </li>
                    </ul>                    
                </div>
                <div class ="span8">
                    <table class ="table">
                        <tr>
                            <td>Personnel Number:</td>
                            <td><?php echo $personnelno; ?></td>
                        </tr>
                        <tr>
                            <td>Full Name:</td>
                            <td><?php echo $fullname; ?></td>
                        </tr>
                        <tr>
                            <td>Department/Unit:</td>
                            <td><?php echo $departmentname; ?></td>
                        </tr>
                    </table>
                </div>
                <div style ="clear: both"></div>
                <div class ="span6">
                    <h3>ROLE &nbsp;&nbsp<small><a href ="#" id ="addrole">[Add]</a></small></h3>                    
                    <table class ="table table-condensed table-striped">
                        <?php
                        $query2 = "SELECT role.id, role.name FROM  userrole JOIN role ON userrole.roleid = role.id WHERE userrole.userid = ?";
                        $stmt2=$dbh->prepare($query2);
                        $stmt2->execute(array($userid));

                        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                            $roleid = $row2['id'];
                            $rolename = $row2['name'];
                            echo "
                                    <tr>
                                        <td>$rolename</td>
                                        <td><a href ='#' class = 'userrolelink' rel1 = '$roleid' rel2 = '$userid'>[Del]</a></td>
                                    </tr>
                                ";
                        }
                        ?>
                    </table>
                </div>
                <div class ="span6">
                    <h3>PERMISSION &nbsp;&nbsp;<small><a href ="#" id ="addpermission">[Add]</a></small></h3>
                    <table class ="table table-condensed table-striped">
                        <?php
                        $query3 = "SELECT permission.id, permission.name FROM  userpermission JOIN permission ON userpermission.permissionid = permission.id WHERE userpermission.userid = ?";
                        $stmt3=$dbh->prepare($query3);
                        $stmt3->execute(array($userid));

                        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                            $permissionid = $row3['id'];
                            $permissionname = $row3['name'];
                            echo "
                                    <tr>
                                        <td>$permissionname</td>
                                        <td><a href ='#' class = 'userpermissionlink' rel1 = '$permissionid' rel2 = '$userid'>[Del]</a></td>
                                    </tr>
                                ";
                        }
                        ?>
                    </table>
                </div>
                <div class ="span12" id ="addrole_dialog" title ="Add Role">
                    <form>
                        <fieldset>
                            <legend>Add Role</legend>
                            <div class="control-group">
                                <label for="rolename">Role Name</label>
                                <div class="controls">
                                    <input type="hidden" name ="r_userid" id ="r_userid" value ="<?php echo $userid; ?>" />
                                    <select class ="inputselect" name ="role" id ="role">
                                        <option value =''>--Select Role--</option>
                                        <?php
                                        $query4 = "SELECT * FROM role";
                                        $stmt4=$dbh->prepare($query4);
                                        $stmt4->execute();

                                        while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                            $roleid = $row4['id'];
                                            $rolename = $row4['name'];
                                            echo "<option value ='$roleid'>$rolename</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div><!-- /.control-group -->
                            <div class="control-group">
                                <button type ="button" name ="role_save_btn" id ="role_save_btn" class ="btn btn-primary">Save</button>
                            </div><!-- /.control-group -->
                        </fieldset>
                    </form>
                </div>


                <div class ="span12" id ="addpermission_dialog" title ="Add Permission">
                    <form>
                        <fieldset>
                            <legend>Add Permission</legend>
                            <div class="control-group">
                                <label for="persissionname">Permission Name</label>
                                <div class="controls">                                    
                                    <input type="hidden" name ="p_userid" id ="p_userid" value ="<?php echo $userid; ?>" />
                                    <select class ="inputselect" name ="permission" id ="permission">
                                        <option value =''>--Select Permission--</option>
                                        <?php
                                        $query5 = "SELECT * FROM permission";
                                        $stmt5=$dbh->prepare($query5);
                                        $stmt5->execute();

                                        while ($row5 = $stmt5->fetch(PDO::FETCH_ASSOC)) {
                                            $permissionid = $row5['id'];
                                            $permissionname = $row5['name'];
                                            echo "<option value ='$permissionid'>$permissionname</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div><!-- /.control-group -->
                            <div class="control-group">
                                <button type ="button" name ="permission_save_btn" id ="permission_save_btn" class ="btn btn-primary">Save</button>
                            </div><!-- /.control-group -->
                        </fieldset>
                    </form>
                </div>




            </div>
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
        <script>
            $( "#addrole_dialog" ).dialog({ autoOpen: false, modal: true });
            $( "#addpermission_dialog" ).dialog({ autoOpen: false, modal: true });

            $(document).on("click","#addrole", function(){
                $( "#addrole_dialog" ).dialog("open");
                return false;
            });

            $(document).on("click", "#addpermission", function(){
                $( "#addpermission_dialog" ).dialog("open");
                return false;
            });

            $(document).on("click", ".userrolelink", function(){                        
                $.ajax({
                    type: "POST",
                    url: "delete_userrole_exec.php",
                    data: {
                        roleid : $(this).attr("rel1"),
                        userid : $(this).attr("rel2")
                    }
                }).done(function( msg ){
                    if(msg == "1"){
                        window.location.reload(true);
                    }
                    else{
                        alert("Error processing request...");
                    }
                                                                                               
                });
                        
            });

            $(document).on("click", ".userpermissionlink", function(){                        
                $.ajax({
                    type: "POST",
                    url: "delete_userpermission_exec.php",
                    data: {
                        permissionid : $(this).attr("rel1"),
                        userid : $(this).attr("rel2")
                    }
                }).done(function( msg ){
                    if(msg == "1"){
                        window.location.reload(true);
                    }
                    else{
                        alert("Error processing request...");
                    }                                                                     
                });
            });


            $(document).on("click", "#role_save_btn", function(){
                $.ajax({
                    type: "POST",
                    url: "add_userrole_exec.php",
                    data: {
                        roleid: $("#role").val(),
                        userid: $("#r_userid").val()
                    }
                }).done(function( msg ){
                    $("#addrole_dialog").dialog("close");
                    window.location.reload(true);                                                                    
                });
            });
                    
            $(document).on("click", "#permission_save_btn", function(){
                $.ajax({
                    type: "POST",
                    url: "add_userpermission_exec.php",
                    data: {
                        permissionid: $("#permission").val(),
                        userid: $("#p_userid").val()
                    }
                }).done(function( msg ){
                    $("#addpermission_dialog").dialog("close");
                    window.location.reload(true);                                                                    
                });
            });

        </script>
    </body>
</html>

