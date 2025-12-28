<?php 
$optcount=$_POST['optcount'];
$nxtcount=$optcount+1;
?>
<tr>
    <td colspan="2">
        <textarea name="opt<?php echo $nxtcount; ?>"></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr<?php echo $nxtcount; ?>" value="1" style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
    </td>
</tr>