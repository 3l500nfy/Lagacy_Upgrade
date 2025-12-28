<?php

session_start();


require_once("../../lib/globals.php");

openConnection();

$id = $_REQUEST['id'];
$query = "SELECT * FROM permission WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($id));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $permissionname = $row['name'];
    $permissiondescription = $row['description'];
}
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
                <h2>Permission </h2>
            </div>
            <?php require_once('../../partials/notification.php'); ?> 
            <div class="span4 offset4">
                <div class="well">
                    <fieldset>
                        <form action="delete_exec.php" method="post">
                            <div class="control-group">
                                <div class="controls">
                                    <span class ="danger">Are you sure you want to delete this record. </span>
                                    <input type="hidden" name="id" id="id" value = "<?php echo $id; ?>" />                            
                                </div>
                            </div>
                            <div>
                                <button type ="submit" name="" id ="" class ="btn btn-success">Yes</button>
                                |
                                <a href ="index.php">
                                    <button type ="button" name="" id ="" class ="btn btn-danger">No</button>
                                </a>
                            </div>
                        </form>
                    </fieldset>
                </div> <!-- /.well -->
            </div>
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
    </body>
</html>
