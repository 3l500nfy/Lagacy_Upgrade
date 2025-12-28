<?php if(!isset($_SESSION)) session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
authorize();
if(!has_roles(array("Test Administrator")) && !has_roles(array("Super Admin")))
{
    header("Location:".siteUrl("403.php"));
    exit();
}
    $subj=((isset($_POST['sbj']))?($_POST['sbj']):(0));
    echo "<input type='hidden' name='sbj' id='sbj' value='$subj' />";
?>
<div id="ctrl">
    <a href="javascript:void(0);" id="addtopic"><div class="tab">Add New Topic</div></a>
    <a href="javascript:void(0);" id="edittopic"><div class="tab">Edit Topic</div></a>
    <a href="javascript:void(0);" id="deletetopic"><div class="tab"> Delete Topic</div></a>
</div>
<div id="ctr-cont">

</div>
<style type="text/css">
    .active{ background-color: #caf59f;}
    .tab:hover
    {
        background-color: #caf59f;
    }
    .tab{
        font-size: 10pt;
        display:inline-block; 
        margin:0px; 
        margin-top: 3px; 
        padding:3px;
        border-style:solid;
        border-width:1px;
        border-color:#cccccc;
        border-top-left-radius:3px;
        -o-border-top-left-radius:3px;
        -webkit-border-top-left-radius:3px;
        -ms-border-top-left-radius:3px;
        -moz-border-top-left-radius:3px;
        border-top-right-radius:3px;
        -o-border-top-right-radius:3px;
        -ms-border-top-right-radius:3px;
        -moz-border-top-right-radius:3px;
        -webkit-border-top-right-radius:3px;
        border-bottom-style:none;
        border-bottom-width: 0px;
        cursor:pointer;
    }
    #ctrl
    {
        padding:0px;
        margin:0px;
        border-style:none;
        border-width: 0px;
        text-align: center;
    }
    #ctr-cont
    {
        min-height: 200px;
        border-radius:3px;
        -o-border-radius:3px;
        -ms-border-radius:3px;
        -webkit-border-radius:3px;
        -moz-border-radius:3px;
        border-width:1px;
        border-style:solid;
    }
</style>