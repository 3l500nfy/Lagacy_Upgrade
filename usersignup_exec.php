<?php

if (!isset($_SESSION))
    session_start();

require_once("lib/globals.php");


openConnection();

$errmsg_arr = array();

$errflag = false;

$username = clean($_POST['username']);
$password = clean($_POST['password']);
$displayname = clean($_POST['displayname']);
$email = clean($_POST['email']);
$pnumber = clean($_POST['pnumber']);
$question = clean($_POST['question']);
$answer = clean($_POST['answer']);


//Not a valid personnel number
if ($pnumber != '') {
    $query = "SELECT * FROM tblemployee WHERE personnelno =?";
    $stmt=$dbh->prepare($query);
    $exec=$stmt->execute(array($pnumber));

    if ($exec) {
        if ($stmt->rowCount() <= 0) {
            $errmsg_arr[] = 'Invalid Personnel Number';
            $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
            session_write_close();
            header("location: usersignup.php");
            exit();
        }
      //  @mysql_free_result($result);
    } else {
        die();
    }
}

//Check for duplicate login ID
if ($username != '') {
    $qry = "SELECT * FROM user WHERE username =?";
    $stmt=$dbh->prepare($query);
    $exec=$stmt->execute(array($username));

    if ($exec) {
        if ($stmt->rowCount() > 0) {
            $errmsg_arr[] = 'Username already in use';
            $errflag = true;
        }
        //@mysql_free_result($result);
    } else {
        die("Query failed");
    }
}

//Already existing personnel number
if ($pnumber != '') {
    $qry = "SELECT * FROM user WHERE staffno =?";
    $stmt=$dbh->prepare($query);
    $exec=$stmt->execute(array($pnumber));

    if ($exec) {
        if ($stmt->rowCount() > 0) {
            $errmsg_arr[] = 'Personnel number already in use';
            $errflag = true;
        }
       // @mysql_free_result($result);
    } else {
        die("Query failed");
    }
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: usersignup.php");
    exit();
}

// Hash password using modern secure hashing before insertion
$password_hash = password_hash($password, PASSWORD_BCRYPT);

//Create INSERT query (password already hashed, no SHA1 in SQL)
$qry = "INSERT INTO user VALUES('',?,?,?,?,?,'1',?,?)";
$stmt=$dbh->prepare($qry);
$exec1=$stmt->execute(array($username,$password_hash,$email,$staffno,$question,$answer));

//Check whether the query was successful or not
if ($exec1) {
    header("location: usersignup_success.php");
} else {
    die("Query failed");
}
?>