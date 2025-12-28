<?php
if (!isset($_SESSION)) session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            <?php echo pageTitle("User Authentication") ?>
        </title>
        <?php require_once("../../../partials/cssimports.php") ?>
    </head>

    <body>
        <?php include_once("../../../partials/navbar.php"); ?>
        <div id="container" class="container">
            <div class="page-header">
                <h2>Role/Permission Assignment</h2>
            </div>
            <?php require_once('../../../partials/notification.php'); ?> 
            <div class ="row">
                <div class ="span6">
                    <div class="control-group">
                        <h3>Role Name</h3>
                        <div class="controls">
                            <select class ="inputselect" name ="role" id ="role">
                                <option value =''>--Select Role--</option>
                                <?php
                                $query = "SELECT * FROM role where (id=8 || id = 9)";
                                $stmt = $dbh->prepare($query);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $roleid = $row['id'];
                                    $rolename = $row['name'];
                                    echo "<option value ='$roleid'>$rolename</option>";
                                }
                                ?>
                            </select>
                            <br />
                            <button type ="button" name ="save_btn" id ="save_btn" class ="btn btn-primary">Save</button>
                            <br />
                            <div id ="progress_status_div">
                                
                            </div>
                        </div>
                    </div><!-- /.control-group -->                
                </div>
                <div class ="span6" id ="permissionlist_div">                    
                </div>
                <div style ="clear: both"></div>
            </div>
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
        <script type ="text/javascript">
            $(document).ready(function() {
                //Disables button on page load
                $("#save_btn").attr("disabled", "disabled"); 
                
                $("#role").live("change", function(){
                    $("#progress_status_div").empty().html("Please wait....");
                    if($(this).val() == ""){
                        $("#save_btn").attr("disabled", "disabled");                        
                    }
                    else{
                        $("#save_btn").removeAttr("disabled");
                        $.ajax({
                            type: "POST",
                            url: "viewpermission_list.php",
                            data: {
                                roleid: $(this).val()
                            }
                        }).done(function( msg ){
                            $("#permissionlist_div").html(msg);
                            $("#progress_status_div").html("");    
                        });
                    }    
                });
                
                $("#selallchkbox").live("click", function(){
                    if($(this).attr("checked") == "checked"){
                        $(".premissionchkbox").attr("checked", "checked");
                    }
                    else{
                        $(".premissionchkbox").removeAttr("checked");
                    }
                });
                
                $("#save_btn").live("click", function(){
                    var permission = "0";
                    $(".premissionchkbox").each(function(index) {
                        if($(this).attr("checked") == "checked"){
                            permission = permission+";"+$(this).val();
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "role_permission_exec.php",
                        data: {
                            permission: permission,
                            role: $("#role").val()
                        }
                    }).done(function( msg ){
                        alert(msg);                                                                      
                    });
                });                
            });
        </script>
    </body>
</html>

