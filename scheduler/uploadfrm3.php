<?php
session_start();
require_once("../lib/globals.php");
openConnection();

//require_once("../lib/globals.php");
$result=array('filename'=>"default", 'extension'=>"jpg", 'uploaderror'=>"");

function do_pic_upload($pic) {
    $uploaderr = "";
    $fname = "";
    $err = false;
    $x = "";

    $mimetype = $pic['type'];
    $tempname = $pic['tmp_name'];
    $fsize = $pic['size'];
    //echo $tempname;
    if (!is_uploaded_file($tempname)) {
        $uploaderr = "Possible breach in security";
        $err = true;
    }
    if ($err != true)
        if ($mimetype != "image/png" && $mimetype != "image/jpg" && $mimetype != "image/jpeg" && $mimetype != "image/gif") {
            $uploaderr = "File type not supported";
            $err = true;
        }
    if ($err != true)
        if ($fsize > (1024 * 1024)) {
            $uploaderr = "Max file size exceeded";
            $err = true;
        }
    if ($err != true) {
        $x = (($mimetype == "image/png") ? (".png") : (($mimetype == "image/jpg") ? (".jpg") : (($mimetype == "image/jpeg") ? (".jpeg") : (".gif"))));
        $newName = $_SESSION['matricnumber'];
        $pathAndName="../picts/" . $newName . $x;
        $_SESSION['pictureuploadpath'] = $pathAndName;
        
        if (!move_uploaded_file($tempname, "../picts/" . $newName . $x)) {
            $uploaderr = "unable to upload";
            $err = true;
        } else {

            $uploaderr = "";
            $fname = $newName;
        }
    }
    return array('filename' => $fname, 'uploaderror' => $uploaderr, 'extension' => $x);
}

if (isset($_FILES['pic'])) {
    $result = do_pic_upload($_FILES['pic']);
    
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Computer Based Test</title>

        <style>
            .modaldialog{
                display: none;
            }
        </style>
        <?php //require_once("../partials/cssimports.php")    ?>
    </head>
    <body>
        <form id="picfrm3" name="picfrm" action="" method="POST" enctype="multipart/form-data" style="overflow:hidden;">
            
            <input type="file" name="pic" id="pic" style="padding:0px; margin:0px; direction: rtl; background-color: white; border-style:solid; border-width:1px; border-color:#cccccc;"/>

        </form>

        <?php include_once dirname(__FILE__) . "/../partials/jsimports.php" ?>
        <script type ="text/javascript">
            /**$("#pic").live('change', function(event){ alert('en');
                $("#picfrm").submit();
            });**/
            $('#livepic', window.parent.document).show();
            $('#progpic', window.parent.document).hide();

            if(top.firstload){
                top.filename="<?php echo $result['filename'] . $result['extension']; ?>";
                top.uploaderr="<?php echo $result['uploaderror']; ?>";
                top.reloadpix();
            }
            top.firstload=true;
    
            $("#pic").change(function(event){
                $('#livepic', window.parent.document).hide();
                $('#progpic', window.parent.document).show();
        
                $("#picfrm3").submit();
            });
        </script>
    </body>
</html>