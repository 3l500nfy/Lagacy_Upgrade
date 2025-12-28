<?php
if (!isset($_SESSION))
    session_start();

require_once(dirname(__FILE__) . "/lib/globals.php");
require_once(dirname(__FILE__) . "/lib/security.php");
authorize();
if (!has_roles(array("Super Admin")) && !has_roles(array("Admin")) && !has_roles(array("Test Administrator")) && !has_roles(array("Test Compositor")) && !has_roles(array("Test Invigilator")) && !has_roles(array("PC Registrar")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Home";
$navindex = 1;

require_once 'partials/cbt_header.php';
?>
<link href="<?php echo siteUrl('assets/css/homestyle.css') ?>" type="text/css" rel="stylesheet"></link>
<br />
<div class="cooltitle">
    AHMADU BELLO UNIVERSITY COMPUTER BASED TEST SOFTWARE
</div>
<div id="container">
    <?php if (has_roles(array("Test Administrator")) || has_roles(array("Admin")) || has_roles(array("Super Admin")) || has_roles(array("Test Compositor"))) { ?>
        <div id="md1" class="module">
            Test Configuration
        </div>
        <div id="md5" class="module">
            Reports
        </div>
        <?php
    }
    if (has_roles(array("Test Administrator")) || has_roles(array("Test Compositor"))) {
        ?>
        <div id="md2" class="module">
            Admin Toolbox
        </div>
        <?php
    }
    if (has_roles(array("Super Admin"))) {
        ?>
        <div id="md3" class="module">
            Manage Users
        </div>
        <?php
    }
    if (has_roles(array("PC Registrar"))) {
        ?>
        <div id="md4" class="module">
            Computer Registration
        </div>
        <?php
    }
    ?>
</div>
<?php
require_once 'partials/cbt_footer.php';
?>
<script type="text/javascript">
    $(".module").click(function(){
        var id=$(this).attr("id");
        if(id=="md1")
        {
            window.location="<?php echo siteUrl("configuration/home.php?scheme=1"); ?>";
        }
        else
            if(id=="md2")
        {
            window.location="<?php echo siteUrl("admin_toolbox/index.php?scheme=1"); ?>";
        }
        else
            if(id=="md3")
        {
            window.location="<?php echo siteUrl("admin/adminpage.php"); ?>";
        }
        else
            if(id=="md4")
        {
            $("<div id='reg-dialog'>Loading...</div>").dialog({
                modal:true,
                title:"Register Computer",
                width:300,
                height:230,
                close:function(){
                    $(this).empty().remove();
                }
            });
                
            $.ajax({
                type:'POST',
                url:'registercomputer/reg_comp.php'
            }).done(function(msg){
                $("#reg-dialog").html(msg);
            });
        }
        else
            if(id=="md5")
        {
            window.location="<?php echo siteUrl("reports/reports.php?scheme=1"); ?>";
        }
    });

    $(document).on('change', "#centre", function(){
            
        $("#venue").html("<option value=''>loading...</option>").load("registercomputer/getvenue.php", {centreid: $(this).val()});
    });
  
 
    $(document).on('click',"#saves", function(evt){
        $.ajax({
            type:'POST',
            url:'registercomputer/registercomputer.php',
            data:{venueid:$("#venue").val(),type:0}
        }).done(function(msg){

            if(msg=="2"){

                $('<div></div>').appendTo('body')
                .html('<div><h4>This computer is already registered in another venue. Replace?</h4></div>')
                .dialog({
                    modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                    width: 'auto', resizable: true,
                    buttons: {
                        Yes: function () {
		  
                            $.ajax({
                                type:'POST',
                                url:'registercomputer/registercomputer.php',
                                data:{venueid:$("#venue").val(),type:2}
                            }).done(function(msg){
                                alert("Computer Successfully registered");
                            });

                            $(this).dialog("close");
                        },
                        No: function (event, ui) {
                            $(this).remove();
                        } 
                    },
                    close: function (event, ui) {
                        $(this).remove();
                    } 
                });
            }
            else if(msg==1){
                alert("Computer already registered in this venue");

            }
            else{
                alert("Computer Successfully registered");
            }

        }); 
    });

    $.ajax({
        type:'POST',
        url:'check_registration.php'
    }).done(function(msg){ //alert(msg);
           
        if($.trim(msg)==2)
        {
            return;
        }
        if($.trim(msg)==1)
            $("#regcom").click();
    });
</script>
</body>
</html>