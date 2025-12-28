

<!DOCTYPE html>

<?php
session_start();
require_once("../../lib/globals.php");
openConnection();
?>
<html lang="en">
    <head>
        <title>new venue</title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php require_once("../../partials/cssimports.php") ?>
    </head>
    <body>

        <div id="container" class="container">
            <div class="page-header">
                <h2>Add Host</h2>
            </div>

            <div>
                <form action="host_exec.php " method="POST">
                    <table>

                        <tr>
                            <td></td>
                            <td>
                                <input type="hidden" name="hostid" id="hostid"/>
                                Upper Bound IP Address
                                <input type="text" name="ipuv" id="ipuv"/>
                                Lower Bound IP Address
                                <input type="text" name="iplv" id="iplv"/>

                            </td>
                        </tr>
                    </table>
            </div>
            <div class="page-footer">
                <table>
                    <tr>
                        <td></td>
                        <td>
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

                            <button type ="submit" name ="" class ="btn btn-primary">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            <a href="index.php">Back to Center & Venue Manager</a>
                        </td>
                    </tr> 
                </table>
            </div>
        </form>
</body>
</html>
