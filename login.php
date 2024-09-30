<?php
include "frozen-core/config.php";

if($_SESSION['frozen-access'] == 2)
{
	connect_admin($_GET['connectas']);
	header("Location: dashboard.php");
}

if(!check_user($user->email,$user->picture,$user->name))
{
	header("Location: index.php?authorized=false");
	exit();
}else{
	header("Location: dashboard.php");
}
