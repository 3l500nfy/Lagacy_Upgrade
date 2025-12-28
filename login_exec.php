<?php

if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");

require_once('lib/security.php');

openConnection();
global $dbh;
$errmsg_arr = array();  //Array to store validation errors
$errflag = false;       //Validation error flag

$username = clean($_POST['username']);
$password = clean($_POST['password']);

//Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Username is missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password is missing';
    $errflag = true;
}

//If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: login.php");
    exit();
}

// Fetch user by username only (no password check in SQL)
$query = "SELECT * FROM user WHERE username=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($username));

if ($stmt->rowCount() == 1) {

    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    $stored_password = $member['password'];
    $password_valid = false;
    $needs_migration = false;

    // First, try modern password hash verification
    if (password_verify($password, $stored_password)) {
        $password_valid = true;
    }
    // If modern hash fails, try SHA1 fallback (for legacy passwords)
    elseif (strlen($stored_password) == 40 && ctype_xdigit($stored_password)) {
        // SHA1 hash is 40 hex characters
        if (sha1($password) === $stored_password) {
            $password_valid = true;
            $needs_migration = true; // Mark for migration
        }
    }

    if (!$password_valid) {
        $_SESSION['LOGIN_FAILED'] = "Invalid Username/Password";
        session_write_close();
        redirect(siteUrl("login.php"));
        exit();
    }

    if ($member['enabled'] != '1') {    // For locked accounts.
        $_SESSION['LOGIN_FAILED'] = "Your account is locked. Please contact your administrator.";
        session_write_close();
        header("location: login.php");
        exit();
    }

    // Migrate SHA1 password to modern hash if needed
    if ($needs_migration) {
        $new_hash = password_hash($password, PASSWORD_BCRYPT);
        $update_query = "UPDATE user SET password = ? WHERE id = ?";
        $update_stmt = $dbh->prepare($update_query);
        $update_stmt->execute(array($new_hash, $member['id']));
    }

    session_regenerate_id();

    $userid = $member['id'];
    $_SESSION['MEMBER_FULLNAME'] = $member['displayname'];
    $_SESSION['MEMBER_USERID'] = $member['id'];
    $_SESSION['MEMBER_USERNAME'] = $member['username'];
    $_SESSION['MEMBER_EMAIL'] = $member['email'];
    $_SESSION['MEMBER_IDENTITY'] = $member['staffno'];
    $_SESSION['MEMBER_ROLES'] = fetch_roles_by_userid($userid);
    $_SESSION['MEMBER_PERMISSIONS'] = fetch_permissions_by_userid($userid);
   // $_SESSION['MEMBER_EMPLOYEEID'] = getEmployeeId($member['id']);

    session_write_close();
    redirect(siteUrl("admin.php"));
    exit();
} else {
    $_SESSION['LOGIN_FAILED'] = "Invalid Username/Password";
    session_write_close();
    redirect(siteUrl("login.php"));
    exit();
}
?>