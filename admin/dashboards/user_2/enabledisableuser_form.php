<?php if(!isset($_SESSION)) session_start();
require_once("../../../lib/globals.php");
require_once("../../../lib/security.php");

openConnection();
authorize();
if (!has_roles(array("Test Administrator")))
    header("Location:" . siteUrl("403.php"));

$uid= $_SESSION['MEMBER_USERID'];
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
                <h2>Enable or Disable User</h2>
                <a href="../dashboard_2/admin_dashboard.php">&Lt;Back to Dashboard</a>
            </div>
            <?php
                $query = "SELECT user.id, user.username, user.password, user.displayname, user.staffno, user.email, user.enabled FROM user inner join userrole on (user.id= userrole.userid)  
                    inner join tbltestcompositor on (tbltestcompositor.userid = user.id) where (userrole.roleid=10) && tbltestcompositor.testid in 
                    (select testid from tbltestconfig where initiatedby = ?) group by user.id";
                $stmt = $dbh->prepare($query);
                $stmt->execute(array($uid));

                $count = 1;
            ?>
            <table class ="table table-bordered table-condensed table-striped smaller">
                <thead>
                    <tr>
                        <th>S/N</td>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Display</th>
                        <th>Email</th>
                        <th>Personnel No.</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $id = $row['id'];
                    $username = $row['username'];
                    $password = $row['password'];
                    $displayname = $row['displayname'];
                    $email = $row['email'];
                    $pnumber = $row['staffno'];
                    $enabled = $row['enabled'];

                    if ($enabled == 1) {
                        $enablestr = "  <select ".(($id==$uid)?('disabled'):(""))." name = 'enabledisableuserctl' id = 'enabledisableuserctl' class = 'enabledisableuserctl' style = 'width: 150px'>
                                                <option value = '" . $id . "|" . $enabled . "' selected = 'selected'>Enabled</option>
                                                <option value = '" . "$id" . "|" . "0" . "' >Disabled</option>
                                            </select>
                                         ";
                    } else if ($enabled == 0) {
                        $enablestr = "  <select ".(($id==$uid)?('disabled'):(""))." name = 'enabledisableuserctl' id = 'enabledisableuserctl' class = 'enabledisableuserctl' style = 'width: 150px'>
                                                <option value = '" . $id . "|" . "1" . "'>Enabled</option>
                                                <option value = '" . $id . "|" . $enabled . "' selected = 'selected'>Disabled</option>
                                            </select>
                                         ";
                    }
                    ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $username; ?></td>
                        <td>****</td>
                        <td><?php echo $displayname; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $pnumber; ?></td>
                        <td><?php echo $enablestr; ?></td>
                    <tr>
                        <?php $count++; ?>
                    <?php endwhile ?>
            </table> 
            <?php
            if ($stmt->rowCount() == 0)
                echo"No users found.";
            ?>

        </div>
        <?php include_once dirname(__FILE__) . "/../../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../../partials/jsimports.php" ?>;
        <script type ="text/javascript">
            //Enable Users
            $(document).on('change', "#enabledisableuserctl", function(){
                $.ajax({
                    type: "POST",
                    url: "enaabledisableuser_exec.php",
                    data: {
                        enabledisableuserctl: $(this).val()                            
                    }
                }).done(function( msg ) {//alert(msg);
                    if(msg == "success"){
                        window.location.reload();
                    }else{
                        alert("Operation Failed, Please try again");
                        window.location.reload();
                    }                                    
                });                
            });
        </script>
    </body>
</html>
