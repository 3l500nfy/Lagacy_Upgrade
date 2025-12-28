<?php

require_once("../../lib/globals.php");

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
                <h2>Role </h2>
            </div>
            <?php require_once('../../partials/notification.php'); ?>            
            <a href ="create.php">Create</a>
            <br /><br />
            <table class ="table table-bordered table-condensed table-striped">
                <tr>
                    <th>S/N</th>
                    <th>Role</th>
                    <th>Description</th>
                    <th></th>
                </tr>
                <?php
                $query = "SELECT * FROM role";
                $stmt = $dbh->prepare($query);
                $stmt->execute();

                $count = 1;
                while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    $id = $row['id'];
                    $name = $row['name'];
                    $description = $row['description'];
                    echo "<td>$count</td><td>$name</td><td>$description</td><td><a href = 'edit.php?id=$id'>Edit</a> | <a href = 'delete.php?id=$id''>Delete</a></td>";
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
