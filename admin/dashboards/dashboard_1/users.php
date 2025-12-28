<?php
if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");
require_once('../../../lib/security.php');

openConnection();
authorize();
if (!has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

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
                <h2>Assign Test Administrator Role</h2>
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

            <a href="javascript:void(0);" id="showmodify">Modify existing user(s)' role...</a>
            <div id="lst" style="display:none;"><table class ="table table-bordered table-condensed table-striped">
                    <tr>
                        <th>S/N</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Display Name</th>
                        <th>Email</th>
                        <th>Personnel No.</th>
                        <th>Enable/Disable</th>
                        <th></th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM user inner join userrole on (user.id= userrole.userid) where (userrole.roleid=8 || userrole.roleid=9) group by user.id";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute();

                    $count = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = $row['id'];
                        $username = $row['username'];
                        $password = $row['password'];
                        $displayname = $row['displayname'];
                        $email = $row['email'];
                        $staffno = $row['staffno'];
                        $enabled = $row['enabled'];
                        echo "<tr>";
                        if ($enabled == 1) {
                            $enabled = "Enabled";
                        } else {
                            $enabled = "Disbled";
                        }
                        echo "<td>$count</td>
                            <td>$username</td>
                            <td>***</td>
                            <td>$displayname</td>
                            <td>$email</td>
                            <td>$staffno</td>
                            <td>$enabled</td>
                            <td>
                                <a href = 'assign_role_permission.php?staffno=$staffno'>Add/Remove Role/Permission</a>
                            </td>";
                        echo "</tr>";
                        $count++;
                    }
                    ?>
                </table>
                <?php
                if ($stmt->rowCount() == 0)
                    echo"No existing users found.";
                ?>
            </div>
        </div>
        <?php include_once dirname(__FILE__) . "/../../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../../partials/jsimports.php" ?>;

        <script type="text/javascript">
            $(document).on("click", "#showmodify", function(event){
                $("#lst").toggle();
                $("#pno").toggle();
                if($("#lst").css('display')!='none')
                {
                    $('#showmodify').text("Hide existing user(s)...");
                }
                else
                {
                    $('#showmodify').text("Modify existing user(s)' role...");
                }
            });
            
            $(document).on('click','#pno_btn',function(event){
                if($.trim($("#userpno").val())=="")
                return false;
            else
                {
                    $.ajax({
                        type:'POST',
                        async:true,
                        url:'get_user_detail.php',
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
                        url:'users_exec.php',
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
