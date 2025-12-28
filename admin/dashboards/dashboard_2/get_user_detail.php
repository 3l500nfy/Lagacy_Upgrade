<?php

if (!isset($_SESSION))
    session_start();

require_once("../../../lib/globals.php");

require_once('../../../lib/security.php');

openConnection();
$uid = $_SESSION['MEMBER_USERID'];

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
    $fullname = strtoupper(trim($row2['surname'])).", ".ucfirst(strtolower($row2['firstname']))." ".ucfirst(strtolower($row2['othernames']));
    $email = $row['email'];
    $staffno = $row['staffno'];
    $enabled = $row['enabled'];
    $dept=get_department_name($row2['departmentid']);
 
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
        <td>Status:</td><td> <?php if ($enabled == 1) echo "Enabled"; else echo"Disabled"; ?></td>
    </tr>
    <tr>
        <td>Assign Test Compositor:</td><td></td>
    </tr>
    <tr>
        <td><select name="test" id="test"><option value=''>--select test--</option><?php
             $query2="select * from tbltestconfig inner join tblexamsdate on (tblexamsdate.testid= tbltestconfig.testid) where tblexamsdate.date>= CURDATE() && tbltestconfig.initiatedby = ? group by tbltestconfig.testid order by tblexamsdate.date desc";
             $stmt2 = $dbh->prepare($query2);
             $stmt2->execute(array($uid));

        while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
        {
            $session=$row2['session'];
            $sem=$row2['semester'];
            $tcode=$row2['testcodeid'];
            $testid=$row2['testid'];
            //echo $testid;
            $ttype=$row2['testtypeid'];

            $query3="select testtypename from tbltesttype where testtypeid=?";
            $stmt3 = $dbh->prepare($query3);
            $stmt3->execute(array($ttype));
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);


            $query4="select testname from tbltestcode where testcodeid=?";
            $stmt4 = $dbh->prepare($query4);
            $stmt4->execute(array($tcode));
            $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);

            if($tcode==1)//PUTME
            {
                echo"<option value='$testid'>$session-PUTME</option>";
            }
            elseif($tcode==2)
            {
                $tcat=$row['testcategory'];
                if($tcat=="Single Subject")
                {
                    
                    if($sem==1)
                        $semstr="First Term";
                    else {
                        $semstr="Second Term";
                    }
                    
                    echo"<option value='$testid'>$session-SBRS-".$row3['testtypename']."-$semstr</option>";
                }
                else
                {
                    
                    if($sem==0)
                        $semstr="Entrance Exam";
                    else 
                    if($sem==1)
                        $semstr="Mid Term";
                    else {
                        $semstr="Final Exam";
                    }
                    
                    echo"<option value='$testid'>$session-SBRS-".$row3['testtypename']."-$semstr</option>";
                }
            }
            else
            {
               if($sem==1)
               {
                   $semstr="First Semester";
               }
               else if($sem==2)
               {
                   $semstr="Second Semester";
               }
               else
               {
                   $semstr="Third Semester";
               }
                echo "<option value='$testid'>$session-".$row4['testname']."-".$row3['testtypename']."-$semstr</option>";
            }
        }
        ?></select></td><?php //echo $sql; ?><td></td>
    </tr>
    <tr>
        <td id="tsbj">
    
        </td><td></td>
    </tr>
    <tr>
        <td><input type="hidden" name="usrid" id="usrid" value="<?php echo $id; ?>" /></td><td><button id="addrole">Apply</button></td>
    </tr>
</table>
