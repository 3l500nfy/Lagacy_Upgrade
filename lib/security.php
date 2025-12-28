<?php
require_once 'globals.php';

function fetch_permissions_by_userid($userid) {
    openConnection();
global $dbh;
    $query = "SELECT * FROM userpermission INNER JOIN permission on userpermission.permissionid = permission.id WHERE (userid=?)";
	$result = $dbh->prepare($query);
  	$result->execute(array($userid));
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    $permission_arr[] = $row['name'];
		}
    return $permission_arr;
}

function get_role_name($roleid) {
    openConnection();
    global $dbh;
    $query = "SELECT name FROM role WHERE (id=?)";
	$result = $dbh->prepare($query);
	$result->execute(array($roleid));
   
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        return $row['name'];
        break;
    }

    return '';
}

function get_role_id($rolename) {
    openConnection();
    global $dbh;
    $query = "SELECT id FROM role WHERE (name=?)";
    $result = $dbh->prepare($query);
	$result->execute(array($rolename));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        return $row['id'];
        break;
    }

    return '';
}

function fetch_roles_by_userid($userid) {
    openConnection();
    global $dbh;
    $role_arr = array();
    $query = "SELECT * FROM userrole INNER JOIN role on userrole.roleid = role.id WHERE (userid=?)";
   	$result = $dbh->prepare($query);
  	$result->execute(array($userid));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $role_arr[] = $row['name'];
    }

    return $role_arr;
}

function fetch_roleids_by_userid($userid) {
    openConnection();
    global $dbh;
    $role_arr = array();
    $query = "SELECT role.id FROM userrole INNER JOIN role on userrole.roleid = role.id WHERE (userid=?)";
    $result = $dbh->prepare($query);
  	$result->execute(array($userid));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $role_arr[] = $row['id'];
    }

    return $role_arr;
}

function is_authenticated() {
    return (isset($_SESSION) && isset($_SESSION['MEMBER_USERID']));
}

function isLoggedIn() {
    return is_authenticated();
}

function authorize() {
    if (!isLoggedIn())
        redirect(siteUrl("login.php"));
}

function authorize_role(array $roles = NULL, $fallbackUrl = NULL) {
    authorize_roles($roles, $fallbackUrl);
}

function authorize_roles(array $roles = NULL, $fallbackUrl = NULL) {
    if ($fallbackUrl == NULL)
        $fallbackUrl = siteUrl("error/403.php");

    if (!is_authenticated())
        redirect(siteUrl("login.php"));

    if (!empty($roles)) {
        $assigned_roles = $_SESSION["MEMBER_ROLES"];
        foreach ($roles as $r) {
            if (!in_array($r, $assigned_roles))
                redirect($fallbackUrl);
        }
    }
}

function authorize_permission(array $permissions = NULL, $fallbackUrl = NULL) {
    authorize_permissions($permissions, $fallbackUrl);
}

function authorize_permissions(array $permissions = NULL, $fallbackUrl = NULL) {
    if ($fallbackUrl == NULL)
        $fallbackUrl = siteUrl("error/403.php");

    if (!is_authenticated())
        redirect(siteUrl("login.php"));

    if (!empty($permissions)) {
        $assigned_permissions = $_SESSION["MEMBER_PERMISSIONS"];
        foreach ($permissions as $r) {
            if (!in_array($r, $assigned_permissions))
                redirect($fallbackUrl);
        }
    }
}

function has_permissions(array $permissions = NULL) {
    if (!is_authenticated())
        return FALSE;

    if (empty($permissions))
        return TRUE;

    $assigned_permissions = $_SESSION["MEMBER_PERMISSIONS"];

    foreach ($permissions as $r) {
        if (!in_array($r, $assigned_permissions))
            return FALSE;
    }
}

function has_roles(array $roles = NULL) {
    if (!is_authenticated())
        return FALSE;

    if (empty($roles))
        return TRUE;

    $assigned_roles = $_SESSION["MEMBER_ROLES"];

    foreach ($roles as $r) {
        if (!in_array($r, $assigned_roles))
            return FALSE;
    }
    return TRUE;
}

function logout($redirectUrl) {
    session_start();
    session_destroy();

    if (isset($redirectUrl))
        redirect($redirectUrl);
}

function get_department_name($did) {
    global $dbh;
    $query = "select name from tbldepartment where (departmentid=?)";
	$result = $dbh->prepare($query);
	$result->execute(array($did));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        return $row['name'];
        break;
    }
    return "";
}


?>