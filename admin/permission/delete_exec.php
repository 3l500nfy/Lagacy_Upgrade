<?php


session_start();


require_once("../../lib/globals.php");

openConnection();

$id = clean($_POST['id']);

$query = "DELETE FROM permission WHERE id = ?";
$stmt = $dbh->prepare($query);
$stmt->execute(array($id));

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>

