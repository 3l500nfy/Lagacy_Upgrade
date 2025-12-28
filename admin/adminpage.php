<?php
if (!isset($_SESSION))
    session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
require_once("../lib/cbt_func.php");
//require_once("../lib/test_config_func.php");
openConnection();
authorize();
if (!has_roles(array("Super Admin")))
    header("Location:" . siteUrl("403.php"));

//page title
$pgtitle = "::Admin";
$navindex=0;

require_once '../partials/cbt_header.php';
?>
<link href="<?php echo siteUrl('assets/css/tconfigstyle.css') ?>" type="text/css" rel="stylesheet"></link>
<div id="container2">
    <table style="height: 100%;">
        <tr>
            <td id="left-nav">
                <div style="font-size: 14px;"><a class="anchor" href="../admin.php">&lt;&lt;Home</a></div>

                <div><a class="anchor" href="test_admin/test_admin.php" target="contentframe">Test Administrator</a></div>
                <div><a class="anchor" href="author/author.php" target="contentframe">Question Author</a></div>
                <div><a class="anchor" href="manage_admin/manage_admin.php" target="contentframe">Admin</a></div>
                <div><a class="anchor" href="pc_registrar/pc_registrar.php" target="contentframe">PC Registrar</a></div>
            </td>
            <td id="frametd">

                <iframe src="../blank.php" id="contentframe" name="contentframe" style="width:100%; display:block; min-height:500px; border-style: none; border-width: 0px;"></iframe>


            </td>
        </tr>
    </table>
</div>
<?php
require_once '../partials/cbt_footer.php';
?>
<script type="text/javascript">
    $(document).on('click','#left-nav .anchor',function(event){
        $(".active").removeClass("active");
        $(this).parent().addClass("active");
    });
</script>

</body>
</html>
