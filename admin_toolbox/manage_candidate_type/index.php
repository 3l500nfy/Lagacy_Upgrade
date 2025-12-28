<?php
require_once("../../lib/globals.php");
openConnection();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Candidate Type Index page</title>

        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>

    </head>

    <body>
        <div id="container" class="container" style="padding-left: 20px">

            <div class ="row">
                <div class ="span12"> 
                    <h1>Create, Edit & Delete Candidate Type</h1>
                    <br /><br />
                </div>
                <div class ="span12"> 
                    <a href="create_new_candidate_type.php">Create New Candidate Type</a>
                </div>
                <div class ="span12">
                    <table style="min-width: 800px" class ="style-tbl" >
                        <tr>
                            <th>S/No.</th>
                            <th>Candidate Type Name</th>
                            <th></th>
                        </tr>
                        <?php
                        $count = 1;
                        $query = "SELECT * FROM tblcandidatetypes";
                        $stmt=$dbh->prepare($query);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $candidatetypeid = $row['candidatetypeid'];
                            $candidatetype = $row['candidatetype'];

                            echo "<tr>
                        <td>$count</td>
                       
                        <td>$candidatetype</td>
                        
                        <td>
                            <a href = 'edit_candidate_type.php?candidatetypeid=$candidatetypeid'>Edit</a>
                            |
                            <a href = 'delete.php?candidatetypeid=$candidatetypeid'>Delete</a>
                        </td>
                     </tr>
                    ";
                            $count++;
                        }
                        ?> 
                    </table>
                </div>

            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type="text/javascript">
            $(window.top.document).scrollTop(0);//.scrollTop();
            $("#contentframe", top.document).height(0).height($(document).height());
    
        </script>
    </body>
</html>