<?php
 require_once("../../lib/globals.php");
    openConnection();
$candidatetypeid = $_REQUEST['candidatetypeid'];
?>
<html>
    <head>

    </head>
    <body>
            <p>Are you sure you want to delete this record ?</p>
            <a href ="delete_candidate_type.php?candidatetypeid=<?php echo $candidatetypeid; ?>">Yes</a>
            |
            <a href ="index.php">No</a>
        
    </body>
</html>
