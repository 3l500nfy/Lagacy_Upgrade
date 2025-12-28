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
                <h2>List of users administered by you</h2>
                <a href="../dashboard_2/admin_dashboard.php">&Lt;Back to Dashboard</a>
            </div>
            <?php require_once('../../../partials/notification.php'); ?>
            <table class ="table table-bordered table-condensed table-striped">
                <tr>
                    <th>S/N</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Display Name</th>
                    <th>Email</th>
                    <th>Personnel No.</th>
                    
                    <th></th>
                </tr>
                <?php
                $query = "SELECT user.id, user.username, user.password, user.displayname, user.staffno, user.email, user.enabled FROM user inner join userrole on (user.id= userrole.userid)  
                    inner join tbltestcompositor on (tbltestcompositor.userid = user.id) where (userrole.roleid=10) && tbltestcompositor.testid in 
                    (select testid from tbltestconfig where initiatedby = ?) group by user.id";
                $stmt = $dbh->prepare($query);
                $stmt->execute(array($uid));

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
                        $enabled = "Disabled";
                    }
                    echo "<td>$count</td>
                            <td>$username</td>
                            <td>***</td>
                            <td>$displayname</td>
                            <td>$email</td>
                            <td>$staffno</td>
                            <td>$enabled</td>
";
                    echo "</tr>";
                    $count++;
                }
                ?>
            </table>
                <?php
                if ($stmt->rowCount() == 0)
                    echo"No users found.";
                ?>
        </div>
            <?php include_once dirname(__FILE__) . "/../../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../../partials/jsimports.php" ?>;
    </body>
</html>
