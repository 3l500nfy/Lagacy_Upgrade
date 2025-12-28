<table style="width:100%">
    <tr><td><h2>Enter a Question here ...</h2></td></tr>
    <tr>
        <td colspan="2">

            <textarea name="quest" placeholder="Type your Question here..." style="width:100%"><?php
if (isset($_POST['quest']))
    echo $_POST['quest'];
?></textarea><br /><br />
        </td>
    </tr>
</table>

<table id="opttb" >
    <tr>
        <td colspan="2">
            <h2> Enter Options here..</h2>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea  placeholder="Option 1" name="opt1" ></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr[]" value="1" checked style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea  placeholder="Option 2" name="opt2"></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr[]" value="2" style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea  placeholder="Option 3" name="opt3"></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr[]" value="3" style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <textarea  placeholder="Option 4" name="opt4"></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr[]" value="4" style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="5">
            <textarea  placeholder="Option 5" name="opt5"></textarea>&nbsp;<label style="float:right;"><input type="radio" name="corr[]" value="5" style="padding:0px; margin: 0px;" /> Set this option as the correct answer</label>
        <br /><br />
        </td>
    </tr>
</table>
<b>Difficulty Level:</b> &nbsp; <select name="dlvl"><option value="simple">Simple</option><option value="difficult">Difficult</option><option value="moredifficult" >More difficult</option></select><br />