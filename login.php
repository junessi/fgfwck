<?php

require_once 'includes/login.php';

session_start();
//echo session_id();

$username = $_POST['username'];
$password = $_POST['password'];

if(!empty($username) && !empty($password))
{
	$user = login($username, md5($password));
	if(!empty($user['uid']) && !empty($user['username']))
	{
		$_SESSION['uid'] = $user['uid'];
		$_SESSION['uname'] = $user['username'];
		header('Location: index.php');
		exit;
	}
	else
		echo 'Wrong Username or Password!<br>';
		echo '<a href="index.php">index</a>';
}
