<?php
if (!isset($_SESSION))
    session_start();

require_once("../lib/globals.php");
require_once('../lib/security.php');

openConnection();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
<?php echo pageTitle("Online Tutorial") ?>
        </title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>

        <style type="text/css">

            .tbnnavigation
            {
                -moz-border-radius:5px;
                -webkit-border-radius:5px;
                border-radius:5px;
                margin:3px;
                font-weight:bold;
                font-size:2em;
                padding:10px;

            }

            .auto-style1 {
                text-align: center;
            }
            .auto-style2 {
                text-align: left;
            }
        </style>
    </head>

    <body>
        <div style="text-align:center;"><img src="<?php echo siteUrl("assets/img/dariya_logo1.png"); ?>" /></div>
        
        <div class="span5 style-div" style=" background-color:green; color:whitesmoke; margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;">
                   
                        <font size="6">Welcome to Student C.B.T. Tutorial</font>
                       <h3> Fill the form below to register before taking a sample test.</h3>
                       <b><i> Note that the questions you will answer are for tutorial purpose only and must not be seen as real
                               Test questions: 
                           </i></b>
                </div>
            <div id="maindiv" class="span5 style-div" style="margin-left: auto; width:700px; margin-top: 50px; padding-left: 30px; margin-right: auto;">

                <form class="style-frm" method="POST">
                        
						<div class="control-group">
                            <label for="username" style="font-weight: bold;">Full Name</label>
                            <div class="controls">
                                <input class="input-block-level" type="text" id="username" name="username" placeholder="Full Name" required/>
                            </div>
                        </div><!-- /.control-group -->
                        <div class="control-group">
                            <label for="password" style="font-weight: bold;">State of Origin </label> 
                            <div class="controls">
                                <select name="state" id="state" class="input-block-level" required>
                                       <option value="">--Select State --</option>

                                        <?php
                                        $query = "SELECT * FROM tblstate";
                                        $stmt = $dbh->prepare($query);
                                        $stmt->execute();

                                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                $stateid = $row['stateid'];
                                                $statename = $row['statename'];
                                                
                                                echo "<option value ='$statename'>$statename</option>";
                                            }
                                       
                                ?>
                                       </select>
                            </div>                

                        </div><!-- /.control-group -->
                        
                        <div class="control-group">
                            <label for="examstype" style="font-weight: bold;">Exam Type</label>
                            <div class="controls">
							<select name="testtype" id="testtype" >
							<option value='1'>  Online Test Tutorial </option>
                                                        </select>
                            </div>
                        </div><!-- /.control-group -->			
                        
                        
                        <div class="control-group">
                            <label for="examstype" style="font-weight: bold;">Are you an  STUDENT?</label>
                            <div class="controls">
                                <input type="radio" name="abu" id="abu" class="abuapp" value="yes"> Yes
                                 <input type="radio" name="abu" id="abu1" class="abuapp"  value="no"> No
							
                            </div>
                        </div><!-- /.control-group -->		
                        
                         <div class="control-group" id="abudegree">
                            <label for="programme" style="font-weight: bold;">Programme applying for: </label> 
                            <div class="controls">
                                <select name="programme" id="programme" >
                                       <option value="">--Select programme --</option>

                                        <?php
                                        $query1 = "SELECT * FROM tblprogramme where programmetypeid=2 
                                                and hprogtypecode is null order by name";
                                        $stmt = $dbh->prepare($query1);
                                        $stmt->execute();

                                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                 	 
                                                $programmeid = $row['programmeid'];
                                                $name = $row['name'];
                                                
                                                echo "<option value ='$programmeid'>$name</option>";
                                            }
                                ?>
                                       </select>
                            </div>                

                        </div><!-- /.control-group -->
			
                        <p>
                            <div id="requirement" style=" background-color: darkgreen; color:whitesmoke;  "></div>
                            
                        <div style=" background-color:greenyellow "class="control-group" id="jambsubject">
                             <label for="subject" style="font-weight: bold;">Select your  subjects:(Exactly four subjects)</label>
                            <div class="controls">
                      
                        <?php
                        $query2= "Select * from tblsubject where subjectcategory=3 order by subjectcode";
                        $stmt = $dbh->prepare($query2);
                        $stmt->execute();
                        $row1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if(count($row1) > 0){
                                    for ($i=0; $i < count($row1); $i++){
                                        $code = $row1[$i]['subjectcode'];
                                        $subjectid = $row1[$i]['subjectid'];
                                        echo "   <input class='input-block-level subjectcode' type='checkbox' name='$subjectid' id='$i'>$code";
                                    }
                                    echo"<span style='display:none' id='numsubject'>". count($row1) ."</span>";
                                }
                        ?>
                        
                             </div>
                        </div> <!-- /.control-group -->

                        <div id="disclaimerbutton" style="font-weight: bold; font-size: 14px;" >
                        <div class="control-group" >
                            <div class="controls">
                               <input class="input-block-level" type="checkbox" id="usedterms" name="usedterms" required/> Disclaimer:<br>
                            </div><i>I understand that this software is used for tutorial only and that results from the tutorial cannot be considered as Original test results.
                                     </i> </div> <!-- /.control-group -->

                        <div class="control-group">
                            <div class="controls">
                                 <input type="button" id="register" style="font-weight: bold; font-size: 20px; height: 50px; width: 200px " value="Save Data"> <span id="loader"></span> 
                            </div>
                        </div> <!-- /.control-group -->
                     </div>
                        
                    </form>

                <div class="contentbox" style="padding-top: 10px;  ">
                    <div class="page-header" style="border-bottom-color: #cccccc; border-bottom-style: solid; border-bottom-width: 1px;">
                        <h2 style="font-family: 'Segoe UI',Helvetica,Arial,sans-serif; color:rgb(51, 51, 51); text-rendering: optimizelegibility; font-size: 18px; font-weight: 700; line-height:40px; "> 
                           </h2>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>

        <script type="text/javascript">

 $(".abuapp").bind('click', function(evt){
    if( $(this).val()=='yes'){
        $("#abudegree").show();
        $(".subjectcode").removeAttr("checked");
        
         $("#disclaimerbutton").hide();
    
     $(jambsubject).hide();
       
    }else{
         $("#abudegree").hide();
 $(".subjectcode").removeAttr("checked");
 
         $("#disclaimerbutton").hide();
    
 $(jambsubject).show();
        
    }
       $("html, body").animate({ scrollTop: $(document).height() }, 1000);
     
 });


 $(".subjectcode").bind('click', function(evt){
  
  var count=0;
  
    var total=parseInt($.trim($("#numsubject").text()));
    for(i=0;i<total;i++){
        if ( $("#"+i).prop("checked")){
            count=count + 1;
        }
        
    }
    if(count==4){
        $("#disclaimerbutton").show();
           $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        
    }
    else if(count>4){
    if( $(this).prop("checked")){
        alert("Select only four subjects");
       $(this).removeAttr("checked");
       
    }
    }else{ //count less than 4
        
         $("#disclaimerbutton").hide();

    }
    
 });

   $("#programme").bind('change', function(evt){ 
      // alert($(this).text());
      $('#requirement').html("<div style='padding-top:30px; text-align:center;'><img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' /><br />Loading...</div>");
        $(jambsubject).hide();
        $.ajax({
                type: 'POST',
                url: 'loadjambcombination.php',
                data: {programmeid: $(this).val()}
            }).done(function(msg) {
                $("#requirement").html(msg);
               // alert(msg);
               $(".subjectcode").removeAttr("checked");
                $(jambsubject).show();
 
              
            });
             $("html, body").animate({ scrollTop: $(document).height() }, 1000);
       
       
    });

    $("#register").bind('click', function(evt){ 
    var subject="";
    var total=parseInt($("#numsubject").text());
    
    for(i=0;i<total;i++){
        if ( $("#"+i).prop("checked")){
            subject=subject + "-" + $("#"+i).attr('name');
        }
        
    }
   //alert(subject);
   
   var fulname=$("#username").val();
   var state=$("#state").val();
  var  error="";
 
   if( $.trim($("#username").val()).length ==0){
       error=" * Specify your full name \n";
   }
   
   if( $("#state").val().length <=2){
       error= error + " * Specify your state \n";
   }
   
   if(!($("#usedterms").prop("checked"))){
        error= error + " * Select Disclaimer";
       
   }

   if(error){
       alert(error);
       
   }
      else{
           $('#loader').html("<img src='<?php echo siteUrl('assets/img/preloader.gif'); ?>' />");
         
        $.ajax({
                type: 'POST',
                error: function(jqXHR) {
                
                 //////////////////////////////////////
                $('<div></div>').appendTo('body')
                .html('<div><h4>Your registration is taking longer time than usual.<br> Please try later</div>')
                .dialog({
                    modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                    width: 'auto', resizable: true,
                    buttons: {
                        OK: function() {
                                   $('#loader').html("");
       
                            $(this).dialog("close");
                            
                        }
                    },
                    close: function(event, ui) {
                $('#loader').html("");
                      $(this).remove();
                         
                           
                    }
                });
                /////////////////////////////////////
            },
            timeout:80000,
                url: 'register.php',
                data: {state:$("#state").val(),username:$("#username").val(),testtype:$("#testtype").val(), subject:subject}
            }).done(function(msg) {
                $('#loader').html("");
       
                if($.trim(msg)!="false"){
                //alert(msg);
                //////////////////////////////////////
                $('<div></div>').appendTo('body')
                .html('<div><h4>You have sussessfully registed</h4>'+msg+'</div>')
                .dialog({
                    modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                    width: 'auto', resizable: true,
                    buttons: {
                        Continue: function() {
                            
                            //window.location.href = "logout.php";
                            $(this).dialog("close");
                            $("#maindiv").html('<div>'+msg+'<h4><a href=\'../test\'>Click here to continue</h4></div>');
                            
                        }
                    },
                    close: function(event, ui) {
                       // window.location.href = "logout.php";
                        $(this).remove();
                         $("#maindiv").html('<div>'+msg+'<h4><a href=\'../test\'>Click here to continue</h4></div>');
                           
                    }
                });
                /////////////////////////////////////
                
            }
                else{
                 //////////////////////////////////////
                $('<div></div>').appendTo('body')
                .html('<div><h4>Your registration failled. Please try later</div>')
                .dialog({
                    modal: true, title: 'Computer Based Exams', zIndex: 10000, autoOpen: true,
                    width: 'auto', resizable: true,
                    buttons: {
                        OK: function() {
                            
                            //window.location.href = "logout.php";
                            $(this).dialog("close");
                            
                        }
                    },
                    close: function(event, ui) {
                       // window.location.href = "logout.php";
                        $(this).remove();
                         
                           
                    }
                });
                /////////////////////////////////////
                }
            });
   }//no error
       
    });

      $(document).ready(function (){
               // $('#username').focus();
               $("#abudegree").hide();
             $("#disclaimerbutton").hide();
             $(jambsubject).hide();
        //       alert("am ready");
            }); 
            
        </script>
    </body>
</html>