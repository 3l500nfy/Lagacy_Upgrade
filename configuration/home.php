<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Super Admin")) && !has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Test Invigilator"))&& !has_roles(array("Test Previewer")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Test Config";
$navindex = 2;

require_once '../partials/cbt_header.php';
?>
<link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
<style type="text/css">
    .module:hover
{
    background-color:  #d3efc4;
    text-shadow: -4px 4px 3px #999999;
    -o-text-shadow: -4px 4px 3px #999999;
    -ms-text-shadow: -4px 4px 3px #999999;
    -webkit-text-shadow: -4px 4px 3px #999999;
    -moz-text-shadow: -4px 4px 3px #999999;
    box-shadow:0.4px 0.8px 8px -1px #000000;
    -o-box-shadow:0.4px 0.8px 8px -1px #000000;
    -ms-box-shadow:0.4px 0.8px 8px -1px #000000;
    -webkit-box-shadow:0.4px 0.8px 8px -1px #000000;
    -moz-box-shadow:0.4px 0.8px 8px -1px #000000;

}

.module, .module2
{
    background-color: #e3efdc;
    padding:5px;
    border-style: solid;
    border-width: 1px;
    border-color:  #3f6d26;
    border-radius:12px;
    -o-border-radius:12px;
    -ms-border-radius:8px;
    -webkit-border-radius:12px;
    -moz-border-radius:8px;
    text-align: center;
    width: 150px;
    height: 70px;
    display: inline-table;
    padding-top: 20px;
    cursor:pointer;
    color:#3f6d26;
    font-family: "bariollight","Helvetica neue",helvetica,sans-serif;
    font-size: 20px;
    line-height: 21px;
    box-sizing:border-box;
    -o-box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
    -ms-box-sizing:border-box;
    box-shadow:0.4px 0.8px 6px -1px #000000;
    -o-box-shadow:0.4px 0.8px 6px -1px #000000;
    -ms-box-shadow:0.4px 0.8px 6px -1px #000000;
    -webkit-box-shadow:0.4px 0.8px 6px -1px #000000;
    -moz-box-shadow:0.4px 0.8px 6px -1px #000000;
    margin-left: 15px;

}

#test-init-tbl tr td:first-child
{
    font-weight: bold;
}
</style>

<br />
<div class="cooltitle">
    TEST CONFIGURATION
</div>
<div id="container">

    <?php
    //show the initiate new button if user is a test Administrator
    if (has_roles(array("Test Administrator"))) {
        ?>
        <div id="md0" class="sub-mod-add" title="Initate a new test">
            <span >New</span>
        </div>
        <br />
        <br />
        <?php
    }
    ?>
    <br />
    <div class="cooltitle2">Existing... (last 500) <a href="javascript:void(0);" id="therest" class="anchor" style="color: #377216; font-size: 13px; float:right;">The rest...</a></div>
    <br />
    <div>
        <?php
        $testids1 = get_test_initiated_as_array(500);
        $testids2 = get_compositor_test_as_array(500);
        $testids3 = get_previewer_test_as_array(500);
        $testids = array_merge($testids1, $testids2, $testids3);
        $testids = array_unique($testids);
        if (false) {
            $tests = get_super_admin_test_as_array();
            $testids = array_merge($testids, $tests);
            $testids = array_unique($testids);
        }
        sort($testids);
        $testids = array_reverse($testids);
        $t = 1;
        foreach ($testids as $testid) {
            if ($t >500)
                break;
            $test_config = get_test_config_param_as_array($testid);
            $is_test_admin = is_test_administrator_of($testid);
            $is_test_compositor = is_test_compositor_of($testid);
            $unique = $test_config['session'] . " / " . strtoupper($test_config['testname']) . " / " . (($test_config['semester'] == 0) ? ("---") : (($test_config['semester'] == 1) ? ("First") : (($test_config['semester'] == 2) ? ("Second") : ("Third") ) ));
            ?>
            <div class="sub-mod">
                <h2 class="coolh2"><a href="<?php echo 'testpage.php?tid=' . $testid; ?>" title="<?php echo htmlspecialchars($unique, ENT_QUOTES); ?>"><?php echo htmlspecialchars($unique, ENT_QUOTES); ?></a><?php if ($test_config['status'] == 0)
            echo " <img style='width:20px; height:20px;' src='" . siteUrl("assets/img/notice_icon.png") . "' />"; ?></h2><hr />
                <ul>
                    <?php if ($is_test_admin) { ?><li><a href="javascript:void(0);" title="Make a test visible/hidden to candidates"><?php
                if ($test_config['status'] == 0)
                    echo "Make Available"; else
                    echo "Make Unavailable";
                ?></a></li>
                        <li><a href="<?php echo siteUrl("configuration/testpage.php?tid=".$testid."&framepg=basic");?>" title="The rest of the settings">Configurations</a></li>
                        <li><a href="<?php echo siteUrl("configuration/testpage.php?tid=".$testid."&framepg=upload");?>" title="Upload student's registration numbers so that they sit for the test">Upload Candidates</a></li>
                        <li><a href="<?php echo siteUrl("configuration/testpage.php?tid=".$testid."&framepg=venue");?>" title="Add or remove free venue">Add/Remove Venue</a></li><?php } ?>
                    <?php if ($is_test_compositor) { ?><li><a href="<?php echo siteUrl("configuration/testpage.php?tid=".$testid."&framepg=compose");?>" title="Select questions to be registered for the test">Test Composition</a></li><?php } ?>
            <?php if ($is_test_admin) { ?><li><a href="<?php echo siteUrl("configuration/testpage.php?tid=".$testid."&framepg=user");?>" title="Manage Compositors and Invigilators for this test">Manage Users</a></li> <?php } ?>
            <?php //if(!test_taken($testid)) { ?><li><a href="<?php echo siteUrl("configuration/test_initiation/delete_test.php?tid=".$testid); ?>" >Delete</a></li><?php // } ?>
                </ul>
            </div>
            <?php
            $t++;
        }
        ?>
    </div>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    $(document).on('click',"#md0",function(event){
        //window.location="test_initiation/test_initiation.php";
        $("<div id='scheme-dialog'><div class='module'>REGULAR</div><div class='module'>Post-UTME</div><div class='module'>SBRS-NEW</div><div class='module'>SBRS</div></div>").dialog({title:"Select a Scheme", modal:true, width:700, maxHeight:500, closeOnEscape:false, resizable:false, show:"fade", close:function(){$(this).dialog('close').remove();}});
       
    });
    
    $("#therest").click(function(event){
        //window.location="test_initiation/test_initiation.php";
        $("<div id='scheme-dialog'><div class='module2'>REGULAR</div><div class='module2'>Post-UTME</div><div class='module2'>SBRS-NEW</div><div class='module2'>SBRS</div></div>").dialog({title:"Select a Scheme", modal:true, width:700, maxHeight:500, closeOnEscape:false, resizable:false, show:"fade", close:function(){$(this).dialog('close').remove();}});
       
    });
    
    $(document).on('click','.module2',function(event){
        var scheme="";
        if($(this).text()=="REGULAR")
            {
                scheme='regular';
            }else
        if($(this).text()=="Post-UTME")
            {
               scheme='post-utme'; 
            }else
        if($(this).text()=="SBRS")
            {
                scheme='sbrs';
            }else
        if($(this).text()=="SBRS-NEW")
            {
                scheme='sbrs-new';
            }
            
        $("#scheme-dialog").dialog('close').remove();
        $("<div id='test-dialog'><i>Loading...</i></div>").dialog({title:"Test Initials For "+$(this).text(), modal:true, maxWidth:700, maxHeight:500, closeOnEscape:false, resizable:false, show:"fade", close:function(){$(this).dialog('close').remove();}});
        $.ajax({
            type:'POST',
            url:'test_initials/test_initials.php?scheme='+scheme
        }).done(function(msg){ //alert(msg);
            $("#test-dialog").html(msg);
        });
    });
    
    $(document).on('click','.module',function(event){
        var scheme="";
        if($(this).text()=="REGULAR")
            {
                scheme='regular';
            }else
        if($(this).text()=="Post-UTME")
            {
               scheme='post-utme'; 
            }else
        if($(this).text()=="SBRS")
            {
                scheme='sbrs';
            }else
        if($(this).text()=="SBRS-NEW")
            {
                scheme='sbrs-new';
            }
            
        $("#scheme-dialog").dialog('close').remove();
        $("<div id='initiate-dialog'><i>Loading...</i></div>").dialog({title:"Test Initiation For "+$(this).text(), modal:true, maxWidth:700, maxHeight:500, closeOnEscape:false, resizable:false, show:"fade", close:function(){$(this).dialog('close').remove();}});
        $.ajax({
            type:'POST',
            url:'test_initiation/test_initiation.php?scheme='+scheme
        }).done(function(msg){ //alert(msg);
            $("#initiate-dialog").html(msg);
        });
    });
    
    $(document).on('click','#test-init-submit',function(event){
     //alert("submitted");
     if($("#test-init-frm select").filter(function(){if($(this).val()!="") {$(this).css("borderColor", "green"); return false;} else { $(this).css("borderColor", "red"); return true; }}).size()>0)
         {
             alert("Error in form input");
             return false;
         }
     $("#infoDiv").addClass("alert-notice").html("Processing...");
     
     //chech uniqueness and submit
     $.ajax({
         type:'POST',
         error:function(){alert("ajax error");},
         url:'test_initiation/initiate_test.php',
         data:$("#test-init-frm").serialize()
     }).done(function(msg){ //alert(msg);
         msg=$.trim(msg)-0;
         if(msg==0)
             {
                 $("#infoDiv").removeClass("alert-notice").addClass("alert-error").html("Operation was not successful.");
             }else
             if(msg==2)
                 {
                     alert("Test already exist!");
                    $("#infoDiv").removeClass("alert-notice").addClass("alert-error").html("Test already exist!"); 
                 }
             else
                 if(msg==1)
                     {
                         alert("Test was initiated successfully!");
                         $("#initiate-dialog").dialog('close').remove();
                         window.location.reload(true);
                         //refresh_test_list();
                     }
     });
     return false;
    });
    
    $(document).on('click','#test-initial-submit',function(event){
     //alert("submitted");
     if($("#test-initial-frm select").filter(function(){if($(this).val()!="") {$(this).css("borderColor", "green"); return false;} else { $(this).css("borderColor", "red"); return true; }}).size()>0)
         {
             alert("Error in form input");
             return false;
         }
     $("#infoDiv").addClass("alert-notice").html("Processing...");
     
     //chech uniqueness and submit
     $.ajax({
         type:'POST',
         error:function(){alert("ajax error");},
         url:'test_initials/initiate_test.php',
         data:$("#test-initial-frm").serialize()
     }).done(function(msg){ //alert(msg);
         msg=$.trim(msg)-0;
         if(msg==0)
             {
                 $("#infoDiv").removeClass("alert-notice").addClass("alert-error").html("Operation was not successful.");
             }else
             if(msg==-1)
                 {
                     alert("Test does not exist!");
                    $("#infoDiv").removeClass("alert-notice").addClass("alert-error").html("Test does not exist!"); 
                 }
             else
                 if(msg>0)
                     {
                         //alert("Test was initiated successfully!");
                         $("#initial-dialog").dialog('close').remove();
                         window.location="<?php echo siteUrl("configuration/testpage.php?tid="); ?>"+msg;
                         //refresh_test_list();
                     }
     });
     return false;
    });
    
</script>
</body>
</html>
