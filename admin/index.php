<?php if (!isset($_SESSION))
   session_start();
require_once("../lib/globals.php");
require_once("../lib/security.php");
openConnection();
if(!isLoggedIn())
{header('Location:../login.php');
 //echo "Enesi: ".$_SESSION['MEMBER_USERID'];
exit();}
redirectTo("adminpage.php");
exit;
//if (has_roles(array("Super Admin"))) {
//    $adminDashboardPage = siteUrl("admin/dashboards/dashboard_1/admin_dashboard.php");
//    redirectTo($adminDashboardPage);
//} else
//if (has_roles(array("Test Administrator"))) {
//    $adminDashboardPage = siteUrl("admin/dashboards/dashboard_2/admin_dashboard.php");
//    redirectTo($adminDashboardPage);
//} else
//if (has_roles(array("Test Compositor"))) {
//    $adminDashboardPage = siteUrl("admin/dashboards/dashboard_3/admin_dashboard.php");
//    redirectTo($adminDashboardPage);
//} else {
//    $adminDashboardPage = siteUrl("admin/dashboard/admin_dashboard.php");
//    redirectTo($adminDashboardPage);
//}
?>
