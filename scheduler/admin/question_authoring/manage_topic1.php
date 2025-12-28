<?php

  
require_once("../../../lib/globals.php");
openConnection();



if(isset($_POST['addsubmit']))
{
    
    $subjectid =$_POST["subj"];
    $topic =$_POST["topic"];
    if($subjectid==''|| $topic==''){
        echo "No subject or topic selected";
        exit;
    }
    
    $query="SELECT * FROM tbltopic WHERE subjectid=? && topicname='?'";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($subjectid,$topic));
    $numrows = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($numrows>0)
    {
        echo"Entry already exists";
        exit;
    }
    $query="INSERT into tbltopic (subjectid, topicname) VALUES ( ?, '?' )";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($subjectid,$topic));
    $numrows = $stmt->rowCount();
    
    if($numrows> 0){
        echo "successful";    
    }
    else
     echo "error while querying the database";
    
    
    exit;
}
?>
<table class="table table-bordered mgt">
    <tr>
        <td>
            <div style="background-color: palegreen">
                <table class="table table-striped table-bordered">
                    <tr><td><a href="javascript:void(0);" id="addtopic">Add New Topic</a><br/></td></tr>
                    <tr><td> <a href="javascript:void(0);" id="edittopic">Edit Topic</a><br/></td></tr>
                    <tr><td><a href="javascript:void(0);" id="deletetopic">Delete Topic</a><br/></td></tr>
                </table>
            </div>

        </td>

        <td>

            <div>

                <form action="" method="">
                    <table class="table table-bordered">

                        
                        <tr><td><b>Add Topic:</b><input name="add_topic" id="add_topic" type="text"/><br></td></tr>
                        <tr><td><center><button id="btn_topic" type="submit">Submit</button></center></td></tr>

                    </table>
                </form>
            </div>
        </td>
    </tr>
</table>