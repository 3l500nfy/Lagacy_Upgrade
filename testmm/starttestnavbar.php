<?php require_once(realpath(dirname(__FILE__) . "/../lib/globals.php")) ?>
<?php require_once(realpath(dirname(__FILE__) . "/../lib/security.php")) ?>

<div id="navbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            
				 <p style="text-align:center; color:#ffffff; font-size:16px; font-weight:bold;">AHMADU BELLO UNIVERSITY COMPUTER BASED TEST SOFTWARE.</p> 
            <div class="nav-collapse" style="padding-left: 50px;">
               
                <ul class="nav pull-right">
                    <?php
                    if (is_authenticated()) {

				   } 
                    ?>
                </ul>
            </div>
			
        </div>
		
    </div>
			<table width="80%"align="center" border="1" style="background-color:#7aba7b;color:white;">
                        <tr>
                            <td><font size=5><?php echo $testinfo['name'];?> <br><font size=5> Duration:</font> <?php echo $testinfo['duration'];?> mns</font> </td>
                            <td><font size=5> Name: <?php echo $candidatename;?><br> Candidate Number:<?php echo" ". strtoupper($matric);?> </font> </td>
							<td>
							
</div>

<font size=5>Remaining Time</font>
							<br><span style="visibility:hidden;" id='remainingsecond'>
                                                            <b><?php echo $testinfo['remainingsecond'];?> </b></span>
                                                        <input type="hidden" id='remainingsecond1' value= " <?php echo $testinfo['remainingsecond'];?> " >    
                                                        <input type="hidden" id='displaymode' value= " <?php echo $testinfo['displaymode'];?> " > 
                                                        
							<br><font size=5> <span id='cdtime'style='color: #800000;font-size:larger' ></span></td>
							<td><img alt="Picture"  border="1" align="right" height="60"  width="60"src="<?php echo "../picts/$pict.jpg";?>" /></td>
						</tr>	
		</table>					
							

		<div  class="step_heading" style="padding:0px;width:80%; margin-right: auto; margin-left: auto; " > <?php 
		if($completion==true){
		echo"You have already taken the test.";
		}else{
		getsubject($candidateid,$testid);
		}
		
		///get the remaining number of question
$query2="SELECT count(distinct(questionid)) as remaining from tblpresentation where(candidateid='$candidateid' and testid='$testid' and
questionid not in (SELECT questionid from tblscore where(candidateid='$candidateid' and testid='$testid')))";
		$stmt=$dbh->prepare($query2);
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($stmt->rowCount() > 0){
$remaining=$row['remaining'];
//mysql_free_result($query2);
}
		?> 
		
		Remaining Question(s) : <span id='remaining' ><?php echo $remaining;?> </span> 
		
					<button id='abandon' class='cbtn tbnnavigation2 ' style='float:right; color:white; font-weight:bold;'>Make Submission</button>

                    
				</div>

</div>