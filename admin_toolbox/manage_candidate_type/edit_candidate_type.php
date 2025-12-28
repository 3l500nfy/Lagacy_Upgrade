<?php
 require_once("../../lib/globals.php");
    openConnection();

$candidatetypeid = $_REQUEST['candidatetypeid'];

$query = "SELECT *  FROM  tblcandidatetypes WHERE candidatetypeid=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($candidatetypeid));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $candidatetypeid = $row['candidatetypeid'];
    $candidatetype = $row['candidatetype'];
}
?>

<html lang="en">
    <head>
        <title>edit candidate type</title>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
         <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>
        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header">
                <h1>Edit Candidate Type</h1>
                <br/>
                <br/>
            </div>
             <div>
                 <form action="edit_candidate_type_exec.php" method="POST" class="style-frm">
                    <table>
                    <tr>
                        <td>Candidate Type Name</td>
                        <td>
                            <input type ="hidden" name ="candidatetypeid" id ="candidatetypeid" value ="<?php echo $candidatetypeid ; ?>" />
                            <input type="text" name="candidatetype" id="candidatetype" value="<?php echo  $candidatetype; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            <button type ="submit" name ="" class ="btn btn-primary">Save</button>
                        </td>
                    </tr> 
                    </table> 
                 </form>
             </div>
    </body>
</html>