<?php

require_once('includes/init.php');

//check if the user has logined
function check_identity()
{
	if(empty($_SESSION['uid']) || empty($_SESSION['uname']))
		return false;	//the user has not logined

	return true;	//yes, he/she has already login
}

function login($username, $md5pwd)
{
	if(empty($username) || empty($md5pwd))
		return false;

	$mysql = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

	if($mysql->connect_errno)
	{
		error_log("Connection failed: ".$mysql->connect_error);
		return false;
	}

	$mysql->set_charset('utf8');

	$stmt = $mysql->prepare("select uid, username, password from fgfwck where username=? and password=?");
	if(!$stmt)
	{
		error_log("SQL statement prepare failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->bind_param('ss', $username, $md5pwd))
	{
		error_log("bind_param failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->execute())
	{
		error_log("execute failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->bind_result($uid, $username, $password))
	{
		error_log("bind_result failed: ".$mysql->error);
		return false;
	}

	if($stmt->fetch())
	{
		return [
				'uid' => $uid,
				'username' => $username,
				];
	}

	return false;
}
