<?php
if (!isset($_SESSION))
    session_start();
require_once("../../lib/globals.php");
require_once("../../lib/security.php");
require_once("../../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
global $dbh;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <link href="<?php echo siteUrl('assets/css/jquery-ui.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/globalstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <link href="<?php echo siteUrl('assets/css/reportstyle.css') ?>" type="text/css" rel="stylesheet"></link>
        <script type="text/javascript">
        </script>
        <style>
            #slider-info{
                font-style:italic;
            }

            #fac-dept-prog-container{
                border-style: solid;
                border-color:green;
                border-width:1px;
                padding:2px;
                max-width: 800px;
                background-color: #e2f6cd;
            }

            #fac-list
            {
                border-style: solid;
                border-color:green;
                border-width:1px;
                padding:2px;
            }
            .facitemdiv
            {
                padding:3px;
                border-style:solid;
                border-width: 1px;
                border-color: transparent;

            }

            .facitemdiv a
            {
                text-decoration: none;
            }

            .facitemdiv:hover
            {
                border-color: #66cc00;

            }
            .facitemdiv a:hover
            {
                font-weight: bold;

            }

            .deptitems-component, .progitems-component
            {
                display:none;
                margin-top:1px;
                border-style: solid;
                border-color:green;
                border-width:1px;
                padding:2px;
            }
        </style>
    </head>
    <body style="background-image: url('../img/bglogo2.jpg');">

        <div id="fac-dept-prog-container">
            <?php
            $sql = "select * from tblfaculty";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            echo"<div id='fac-list'><tr><td colspan='3'><label><input type='checkbox' name='all' id='all' checked value='*'/> All</label></td></tr>";
            $c = 0;
            echo"<table style='width:100%'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $facname = $row['name'];
                $facid = $row['facultyid'];
                if ($c % 3 == 0)
                    echo"<tr>";

                echo"<td style='width:30%'><div class='facitemdiv'><label><input type='checkbox' checked class='facitem' data-facid='$facid' style='display:none' />$facname</label></div><td>";
                if ($c % 3 == 2)
                    echo"</tr>";
                $c++;
            }
            echo"</table>";
            echo "</div>";

            echo"<div id='dept-list'>";
            $depts = array();
            //mysql_data_seek($query, 0);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $facid2 = $row['facultyid'];
                $sql2 = "select * from tbldepartment where facultyid='$facid2'";
                $stmt1 = $dbh->prepare($sql2);
                $stmt1->execute();
                echo"<div class='deptitems-component' data-facid='$facid2'>";
                if ($stmt1->rowCount() == 0) {
                    echo"No department found";
                    echo"</div>";
                    continue;
                }

                $c = 0;
                echo"<table style='width:100%'><tr><td colspan='3'><label><input type='checkbox' class='alldepartment' checked name='alldept" . $facid2 . "' id='alldept" . $facid2 . "' value='$facid2'/> All</label></td></tr>";
                while ($row2 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    $deptid = $row2['departmentid'];
                    $depts[] = $deptid;
                    $deptname = $row2['name'];
                    if ($c % 3 == 0)
                        echo"<tr>";
                    echo"<td style='width:30%'><div class='deptitemdiv'><label><input type='checkbox' style='display:none;' class='deptitem' checked data-facid='$facid2' data-deptid='$deptid'/>" . intelligentStr($deptname) . "</label><div></td>";
                    if ($c % 3 == 2)
                        echo"</tr>";
                    $c++;
                }

                echo"</table></div>";
            }
            echo"</div>";

            echo"<div id='prog-list'>";

            foreach ($depts as $deptid2) {
                $sql3 = "select * from tblprogramme where departmentid='$deptid2'";
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->execute();

                echo"<div class='progitems-component' data-deptid='$deptid2'>";
                if ($stmt3->rowCount() == 0) {
                    echo"No programme found";
                    echo"</div>";
                    continue;
                }
                $facid3 = get_faculty_id($deptid2);
                echo"<table style='width:100%'><tr><td colspan='3'><label><input type='checkbox' checked class='allprogramme' name='allprog" . $deptid2 . "' data-facid='$facid3' id='allprog" . $deptid2 . "' value='$deptid2'/> All</label></td></tr>";
                $c = 0;
                while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    $progid = $row3['programmeid'];
                    $progname = $row3['name'];
                    if ($c % 3 == 0)
                        echo"<tr>";

                    echo"<td style='width:30%'><div class='progitemdiv'><label> <input type='checkbox' checked class='progitem' data-facid='$facid3' data-deptid='$deptid2' data-progid='$progid'/> " . intelligentStr($progname) . "</label></div></td>";
                    if ($c % 3 == 2)
                        echo"</tr>";
                    $c++;
                }
                if ($c % 3 > 0)
                    for ($q = $c % 3; $q <= 2; $q++) {
                        echo"<td style='width:30%'></td>";
                        if ($q == 2)
                            echo"</tr>";
                    }

                echo"</table></div>";
            }
            echo"</div>";
            ?>

        </div>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-1.9.0.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/jquery-ui-1.10.0.custom.min.js"); ?>"></script>
        <script type="text/javascript" src="<?php echo siteUrl("assets/js/cbt_js.js"); ?>"></script>
        <script type="text/javascript">
            $(document).on('click','.facitem',function(event){
                var facid=$(this).attr("data-facid");
                $(".deptitems-component").hide();
                $(".deptitems-component[data-facid="+facid+"]").show();
                $(".progitems-component").hide();
            });
            
            $(document).on('click','.progitem',function(event){
                var dept=$(this).attr("data-deptid");
                var fac=$(this).attr("data-facid");
                if($(".progitem[data-deptid="+dept+"]").filter(function(){
                    if($(this).prop("checked"))
                        {
                            return false;
                        }
                        return true;
                }).size()==0)
                {
                    $(".allprogramme[value="+dept+"]").trigger('click');                    
                }
            else{
                    $(".deptitem[data-deptid="+dept+"], .alldepartment[value="+fac+"], .allprogramme[value="+dept+"], #all").prop('checked',false);
            }
                    
            });
            
            $(document).on('click','.deptitem',function(event){
                var deptid=$(this).attr("data-deptid");
                $(".progitems-component").hide();
                $(".progitems-component[data-deptid="+deptid+"]").show();
                if( $(this).prop('checked'))
                {
                    //alert($(this).prop('checked'));
                    $(this).prop('checked',false);
                }
                else
                {
                    $(this).prop('checked',true);
                }

                
            });
            
            $(document).on('click','.allprogramme',function(event){
                var dept=$(this).val();
                var fac=$(this).attr("data-facid");
                if($(this).prop('checked')==true)
                {
                    $(".progitem[data-deptid="+dept+"]").prop('checked',true);
                    $(".deptitem[data-deptid="+dept+"]").prop('checked',true);
                  if($(".deptitem[data-facid="+fac+"]").filter(function(){
                    if($(this).prop("checked"))
                        {
                            return false;
                        }
                        return true;
                }).size()==0)
                {
                    if($(".alldepartment[value="+fac+"]").prop('checked')!=true)
                    $(".alldepartment[value="+fac+"]").trigger('click');                    
                }
                }
                else
                {
                    $(".progitem[data-deptid="+dept+"]").prop('checked',false);
                    $(".deptitem[data-deptid="+dept+"]").prop('checked',false);
                    $(".alldepartment[value="+fac+"], #all").prop("checked", false);
                }
            });
        
            $(document).on('click','.alldepartment',function(event){
                var fac=$(this).val();
                if($(this).prop('checked')==true)
                {
                    $(".allprogramme[data-facid="+fac+"]").prop('checked',false).trigger('click');
                    $(".deptitem[data-facid="+fac+"]").prop('checked',true);

                }
                else
                {
                    $(".allprogramme[data-facid="+fac+"]").prop('checked',true).trigger('click');
                    $(".deptitem[data-facid="+fac+"], #all").prop('checked',false);
                }
            });
            
            $(document).on('click','#all',function(event){
                if($(this).prop("checked"))
                    $(".alldepartment").prop('checked',false).trigger('click');
                else
                    $(".alldepartment").prop('checked',true).trigger('click');
            });
        </script>
    </body>
</html>