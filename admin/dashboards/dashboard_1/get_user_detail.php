<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();

$pno=(isset($_POST['pno'])?($_POST['pno']):(""));
$query = "SELECT * FROM user where staffno =?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($pno));

$query1="select * from tblemployee where UPPER(personnelno) = UPPER(?)";
$stmt1 = $dbh->prepare($query1);
$stmt1->execute(array($pno));

if($stmt->rowCount()==0)
{
    echo "User not registered.";
    exit();
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($stmt1->rowCount()==0)
{
    echo "User does not exist.";
    exit();
}
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $id = $row['id'];
    $username = $row['username'];
    $password = $row['password'];
    $fullname = strtoupper(trim($row1['surname'])).", ".ucfirst(strtolower($row1['firstname']))." ".ucfirst(strtolower($row1['othernames']));
    $email = $row['email'];
    $staffno = $row['staffno'];
    $enabled = $row['enabled'];
    $dept=get_department_name($row1['departmentid']);
 
    $roles= fetch_roles_by_userid($id);
    $roleids=  fetch_roleids_by_userid($id);
    
    ?>
<table class="userdetail">
    <tr>
        <td>Personnel No.:</td><td><?php echo $staffno; ?></td>
    </tr>
    <tr>
        <td>Full Name:</td><td><?php echo $fullname; ?></td>
    </tr>
    <tr>
        <td>Department:</td><td><?php echo $dept; ?></td>
    </tr>
    <tr>
        <td>Current Roles:</td><td><?php echo trim(implode(", ",$roles),",");?></td>
    </tr>
    <tr>
        <td>Status:</td><td><select name="status"><option value="0">Disabled</option><option value="1" <?php if ($enabled == 1) echo "selected"; ?>>Enabled</option></select></td>
    </tr>
    <tr>
        <td>Add Role:</td><td><select name="newrole"><option value=''>--select role--</option><?php
              if(!in_array("8", $roleids))
                      echo "<option value='8'>".  get_role_name(8)."</option>";
              if(!in_array("9", $roleids))
                      echo "<option value='9'>".  get_role_name(9)."</option>";
              if(!in_array("13", $roleids))
                      echo "<option value='13'>".  get_role_name(13)."</option>";
              
        ?></select></td>
    </tr>
    <tr>
        <td><input type="hidden" name="usrid" id="usrid" value="<?php echo $id; ?>" /></td><td><button id="addrole">Apply</button></td>
    </tr>
</table>

