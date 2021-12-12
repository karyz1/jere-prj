<?php
$get_id=$_GET['id'];

require('../connection.php');

session_start();
$id=$_SESSION['id'];

if (empty($id)) {
    header('location:../index.php');
}
$error=0;
$msg=0;

$pass="123";

$hased=password_hash($pass,PASSWORD_DEFAULT);

$query="UPDATE users SET password='$hased' WHERE id='$get_id' ";
$result=mysqli_query($con,$query);
if ($result) {
    header('location:viewuse.php');
    $msg="user password was reset succesfuly";
}else {
    $error="ooup your into truble password reset did not work";
}