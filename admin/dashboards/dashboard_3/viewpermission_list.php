<?php
if (!isset($_SESSION))
    session_start();

require_once("../../lib/globals.php");

require_once('../../lib/security.php');

openConnection();

$roleid = $_POST['roleid'];

if ($roleid == '')
    exit;
?>
<h3>Permission List</h3>
<table class ="table">
    <tr>
        <td>
            <input type ="checkbox" name="selallchkbox" id ="selallchkbox" />
        </td>
        <td></td>
    </tr>
    <?php
    $query = "SELECT * FROM permission";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permissionid_outer = $row['id'];
        $permissionname_outer = $row['name'];

        $query1 = "SELECT * FROM rolepermission WHERE roleid = ? AND permissionid = ?";
        $stmt1 = $dbh->prepare($query1);
        $stmt1->execute(array($roleid,$permissionid_outer));

        if ($stmt1->rowCount() > 0) {
            echo "
                <tr>
                    <td >
                        <input type ='checkbox' class = 'premissionchkbox' name ='' id ='' value ='$permissionid_outer' checked = 'checked' />
                    </td>
                    <td>
                        $permissionname_outer
                    </td>
                </tr>
                ";
        } else {
            echo "
                <tr>
                    <td >
                        <input type ='checkbox' class = 'premissionchkbox' name ='' id ='' value ='$permissionid_outer' />
                    </td>
                    <td>
                        $permissionname_outer
                    </td>
                </tr>
                ";
        }
    }
    ?>                                                
</table>