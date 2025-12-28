<!DOCTYPE html>
<?php
session_start();
require_once("../../lib/globals.php");
?>
<html lang="en">
    <head>
        <title>new candidate type</title>
        <?php javascriptTurnedOff(); ?>
        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php ?>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
         <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
    </head>
    <body>
       
        <div id="container" class="container" style="padding-left: 20px">
            <div class="page-header">
                <h1>Add New Candidate Type</h1>
                <br/>
                <br/>
            </div>
            
             <div>
                 <form action="create_new_candidate_type_exec.php" method="POST" class="style-frm">
                <table>
                    <tr>
                        <td>Candidate Type Name</td>
                        <td>
                            <input type="text" name="candidatetypename" id="candidatetypename"/>
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
            <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
    </body>
</html>
