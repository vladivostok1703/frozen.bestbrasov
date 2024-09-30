<?php
include "frozen-core/config.php";

if($_GET['email'] != "")
{
	$email = htmlentities(urldecode($_GET['email']),ENT_QUOTES);
	if($mysql->query("select * from authorized where email='$email'")->num_rows == 0)
	{
		echo 0;
	}else{
		echo 1;
	}
}
