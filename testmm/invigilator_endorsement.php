<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
require_once("testfunctions.php");

openConnection();
global $dbh;
if (!(isset($_SESSION) && isset($_SESSION['MEMBER_USERID']) && isset($_SESSION['testid']))) {
    redirect(siteUrl("test/login.php"));
}

$candidateid = $_SESSION['candidateid'];
$testid = $_SESSION['testid'];
?>
<!DOCTYPE html >
<html>
    <head>
        <title></title>
        <style type="text/css">
            .keyset table{
                margin-left: auto;
                margin-right: auto;
            }
            #input-display{
                width:450px;
                border-width: 3px;
                border-color:#333333;
                padding:5px;
                text-align: center;
                vertical-align: middle;
                height: 40px;
                border-style: solid;
                margin:10px;
                margin-left: 15px;
                font-size: 35px;

            }

            #authorize{
                background-color: #e3efdc;
                padding:5px;
                padding-top:20px;
                border-style: solid;
                border-width: 1px;
                border-color:  #3f6d26;
                border-radius:12px;
                -o-border-radius:12px;
                -ms-border-radius:8px;
                -webkit-border-radius:12px;
                -moz-border-radius:8px;
                text-align: center;
                width: 300px;
                min-height: 70px;
                display: inline-block;
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

            #cancel{
                background-color: #e3efdc;
                padding:5px;
                padding-top:20px;
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
                min-height: 70px;
                display: inline-block;
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

            .keyset{
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
                min-height: 70px;
                display: inline-block;
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

            .keyset:hover, #cancel:hover, #authorize:hover
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

            #keyset-container
            {
                width:500px; 
                margin-left: auto; 
                margin-right: auto; 
                padding:100px; 
                padding-top: 20px;
                padding-bottom: 20px;
                border-width: 1px; 
                border-style: solid; 
                border-color:#333333; 
                background-color:#d3efc4;
                border-radius:12px;
                -o-border-radius:12px;
                -ms-border-radius:8px;
                -webkit-border-radius:12px;
                -moz-border-radius:8px;

            }
        </style>
    </head>
    <body>

        <div id="keyset-container"style="">
            <h1>Invigilator's Endorsement</h1>
            <div id="input-display"></div>
            <input type="hidden" value="" name="endorse-str" id="endorse-str"/>
            <div id="ccc">
                <?php
                $alphanum = array();
                for ($i = 48; $i <= 57; $i++) {
                    $alphanum[] = $i;
                }

                for ($j = 65; $j <= 90; $j++) {
                    $alphanum[] = $j;
                }

                shuffle($alphanum);
                $q = 0;
                $keys = "";
                for ($k = 0; $k < 6; $k++) {
                    echo"<div class='keyset'><table>";
                    $c = 0;
                    $keys = "";
                    for (; ($q < count($alphanum) && $c < 6); $q = $q + 3) {
                        echo "<tr><td>" . chr($alphanum[$q]) . "</td><td>" . chr($alphanum[$q + 1]) . "</td><td>" . chr($alphanum[$q + 2]) . "</td></tr>";
                        $c = $c + 3;
                        $keys .=chr($alphanum[$q]) . chr($alphanum[$q + 1]) . chr($alphanum[$q + 2]);
                    }

                    echo"</table><input type='hidden' value='" . $keys . "' class='keys'/></div>";
                }
                ?>
                <div id="cancel">Cancel</div>
                <div id="authorize">Endorse</div>
            </div>
            <div style="text-align:center; padding-top: 20px;"><a id="return" href="javascript:void(0);">Return to test</a></div>
        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript">
            var alphanum=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9',];
            function sorter(a, b){ if((Math.random()*10)<5) return -1; else return 1;}
            
            function re_order_keys()
            {
                alphanum.sort(sorter);
                var result_str="";
                var q=0;
                var keys='';
                for(var i=0; i<6; i++)
                {
                    result_str +="<div class='keyset'><table>";
                    var c=0;
                    keys="";
                    for(; (q<alphanum.length && c<6); q= q+3)
                    {
                        result_str +="<tr><td>" +alphanum[q]+ "</td><td>"+ alphanum[q + 1]+ "</td><td>" + alphanum[q + 2] + "</td></tr>";
                        c = c + 3;
                        keys +=alphanum[q]+alphanum[q+1]+alphanum[q+2];
                    }
                    result_str +="</table><input type='hidden' value='"+keys+"' class='keys'/></div>";
                }
                result_str +='<div id="cancel">Cancel</div><div id="authorize">Endorse</div>';
                $("#ccc").html(result_str);
            }
            
            //alert(alphanum.toString());
            $(document).on('click','.keyset',function(event){
                //alert($(".keys", $(this)).val());
                $("#input-display").text($("#input-display").text()+"*");
                if($("#endorse-str").val()!="")
                    {
                        $("#endorse-str").val($("#endorse-str").val()+"-");
                    }
                $("#endorse-str").val($("#endorse-str").val()+$(".keys", $(this)).val());
                re_order_keys();
                //alert(eensi);
            });
            $(document).on('click','#cancel',function(event){
                $("#input-display").text("");
                $("#endorse-str").val("");
            });
            $(document).on('click','#return',function(event){
               window.location.href = "starttest.php";
            });
            $(document).on('click','#authorize',function(event){
                
                var keyz=$("#endorse-str").val();
                //alert(keyz);
                $.ajax({
                    url:'authorize.php',
                    error:function(){alert("Server Error!");},
                    type:'POST',
                    data:{keyz:keyz}
                }).done(function(msg){ //alert(msg);
                    msg= ($.trim(msg)-0);
                    if(msg==0)
                    {
                        alert("Invalid authorization key");
                            
                    }
                    else
                        if(msg==2)
                    {
                        alert("Server error.");
                    }
                    else
                        if(msg==3)
                    {
                        alert("Invalid input.");
                    }
                    else
                        if(msg==4)
                    {
                        alert("Candidate is not scheduled!");
                    }
                    else
                        if(msg==5)
                    {
                        alert("No Invigilator registered yet!");
                    }
                    else
                        if(msg==1)
                    {
                        alert("Endorsement was successful.");
                       window.location.href = "starttest.php";
                    }
                });
            });
            
        </script>
    </body>
</html>