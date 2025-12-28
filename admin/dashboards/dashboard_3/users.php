<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();
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
                <h2>User</h2>
            </div>
            <?php require_once('../../partials/notification.php'); ?>
            <table class ="table table-bordered table-condensed table-striped">
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
                $query = "SELECT * FROM user";
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
                                <a href = 'assign_role_permission.php?staffno=$staffno'>Assign Role/Permission</a>
                            </td>";
                    echo "</tr>";
                    $count++;
                }
                ?>
            </table>
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
    </body>
</html>
