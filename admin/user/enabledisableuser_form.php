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
                <h2>User</h2>
            </div>
            <?php
            $query = "SELECT * FROM user";
            $stmt = $dbh->prepare($query);
            $stmt->execute();

            $count = 1;
            ?>
            <table class ="table table-bordered table-condensed table-striped smaller">
                <thead>
                    <tr>
                        <th>S/N</td>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Display</th>
                        <th>Email</th>
                        <th>Personnel No.</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $id = $row['id'];
                    $username = $row['username'];
                    $password = $row['password'];
                    $displayname = $row['displayname'];
                    $email = $row['email'];
                    $pnumber = $row['staffno'];
                    $enabled = $row['enabled'];

                    if ($enabled == 1) {
                        $enablestr = "<select name = 'enabledisableuserctl' id = 'enabledisableuserctl' class = 'enabledisableuserctl' style = 'width: 150px'>
                                          <option value = '" . $id . "|" . $enabled . "' selected = 'selected'>Enabled</option>
                                          <option value = '" . "$id" . "|" . "0" . "' >Disabled</option>
                                      </select>
                                     ";
                    } else if ($enabled == 0) {
                        $enablestr = "<select name = 'enabledisableuserctl' id = 'enabledisableuserctl' class = 'enabledisableuserctl' style = 'width: 150px'>
                                          <option value = '" . $id . "|" . "1" . "'>Enabled</option>
                                          <option value = '" . $id . "|" . $enabled . "' selected = 'selected'>Disabled</option>
                                      </select>
                                     ";
                    }
                    ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><?php echo $username; ?></td>
                        <td><?php echo $password; ?></td>
                        <td><?php echo $displayname; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $pnumber; ?></td>
                        <td><?php echo $enablestr; ?></td>
                    <tr>
                        <?php $count++; ?>
                    <?php endwhile ?>
            </table> 
        </div>
        <?php include_once dirname(__FILE__) . "/../../partials/footer.php" ?>;
        <?php include_once dirname(__FILE__) . "/../../partials/jsimports.php" ?>;
        <script type ="text/javascript">
            //Enable Users
            $(document).on('change', "#enabledisableuserctl", function(){
                $.ajax({
                    type: "POST",
                    url: "enaabledisableuser_exec.php",
                    data: {
                        enabledisableuserctl: $(this).val()                            
                    }
                }).done(function( msg ) {
                    if(msg == "success"){
                        window.location.reload();
                    }else{
                        alert("Operation Failed, Please try again");
                        window.location.reload();
                    }                                    
                });                
            });
        </script>
    </body>
</html>
