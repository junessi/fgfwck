<?php

require_once 'includes/init.php';

session_start();

function change_password($old_pwd, $new_pwd)
{
	if(empty($old_pwd) || empty($new_pwd))
		return false;

	$mysql = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

	if($mysql->connect_errno)
	{
		error_log("Connection failed: ".$mysql->connect_error);
		return false;
	}

	$mysql->set_charset('utf8');

	//check if the current username and password correct
	$stmt = $mysql->prepare("select uid, username, password from fgfwck where username=? and password=?");
	if(!$stmt)
	{
		error_log("SQL statement prepare failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->bind_param('ss', $_SESSION['uname'], $old_pwd))
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
		if(empty($uid))
			return false;
	}
	
	//now change password in database
	$stmt->prepare("update fgfwck set password=? where uid=? and username=?");
	if(!$stmt)
	{
		error_log("SQL statement prepare failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->bind_param('sis', md5($new_pwd), $_SESSION['uid'], $_SESSION['uname']))
	{
		error_log("bind_param failed: ".$mysql->error);
		return false;
	}

	if(!$stmt->execute())
	{
		error_log("execute failed: ".$mysql->error);
		return false;
	}

	if($mysql->affected_rows == 1)
		return true;

	return false;
}

if(!empty($_POST['old_pwd']) && !empty($_POST['new_pwd']) && !empty($_SESSION['uid']))
{
	//echo $_POST['old_pwd'].'<br>';
	//echo $_POST['new_pwd'].'<br>';
	if(empty($_POST['new_pwd']))
		$errmsg = 'New Password is empty.';
	else if(preg_match_all('/^[0-9a-zA-Z_]*$/', $_POST['new_pwd']) == 0)
		$errmsg = 'There is at least one invalid character in New Password.';
	else if(strlen($_POST['new_pwd']) < 6 || strlen($_POST['new_pwd']) > 16)
		$errmsg = 'Length of Password must be between 6 and 16';
	else
	{
		if(change_password($_POST['old_pwd'], $_POST['new_pwd']))
			header("Location: index.php");
		else
			$errmsg = 'Username or Old Password is not correct.';
	}
}
?>

<html>
<head>
	<title>change password</title>
</head>
<body>
	<form action="changepwd.php" method="post">
	Old password:&nbsp;&nbsp;<input type="password" name="old_pwd">
	New Password:&nbsp;&nbsp;<input type="password" name="new_pwd">
	<input type="submit" value="confirm">
	</form>
	<?php
		if(!empty($errmsg))
			echo $errmsg.'<br>';
	?>
</body>
</html>


