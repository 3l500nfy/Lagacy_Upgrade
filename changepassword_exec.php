<?php

if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");

openConnection();

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

$employeeid = clean($_SESSION['MEMBER_USERID']);
$oldpassword = clean($_POST['oldpassword']);
$newpassword = clean($_POST['newpassword']);
$reenterpassword = clean($_POST['reenterpassword']);

//Input Validations
if ($oldpassword == '') {
    $errmsg_arr[] = 'Old password is missing';
    $errflag = true;
}
if ($newpassword == '') {
    $errmsg_arr[] = 'New password is missing';
    $errflag = true;
}
if ($reenterpassword == '') {
    $errmsg_arr[] = 'Re-enter password is missing';
    $errflag = true;
}
if (strcmp($newpassword, $reenterpassword) != 0) {
    $errmsg_arr[] = 'Password do not match';
    $errflag = true;
}

if ($errflag == false) {
    // Fetch user by ID only (no password check in SQL)
    $query = "SELECT * FROM user WHERE id = ?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($employeeid));

    if ($stmt->rowCount() == 1) {
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_password = $member['password'];
        $password_valid = false;

        // First, try modern password hash verification
        if (password_verify($oldpassword, $stored_password)) {
            $password_valid = true;
        }
        // If modern hash fails, try SHA1 fallback (for legacy passwords)
        elseif (strlen($stored_password) == 40 && ctype_xdigit($stored_password)) {
            // SHA1 hash is 40 hex characters
            if (sha1($oldpassword) === $stored_password) {
                $password_valid = true;
            }
        }

        if (!$password_valid) {
            $errmsg_arr[] = 'Incorrect old password';
            $errflag = true;
        }
    } else {
        $errmsg_arr[] = 'User not found';
        $errflag = true;
    }
}

//If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: changepassword.php");
    exit();
}


// Use modern password hashing for new password
$new_password_hash = password_hash($newpassword, PASSWORD_BCRYPT);
$query1 = "UPDATE user set password = ? WHERE id = ?";
$stmt=$dbh->prepare($query1);
$stmt->execute(array($new_password_hash, $employeeid));

//Check whether the query was successful or not
if ($result) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
    header("location: changepassword_success.php");
    exit();
} else {
    $errmsg_arr[] = 'Server error!';
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: changepassword.php");
    exit();
}
?>