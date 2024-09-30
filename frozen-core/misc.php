<?php
function is_connected()
{
	if($_SESSION['frozen-id'] == "" || $_SESSION['frozen-name'] == "" || $_SESSION['frozen-photo'] == "" || $_SESSION['frozen-email'] == "")
		return false;
	return true;
}

function return_time($timestamp)
{
	$mins = time() - $timestamp;
	if($mins == 0)
	{
		$mins = "adineauri";
	}elseif($mins < 20)
	{
		$mins = "acum $mins secunde";
	}elseif($mins < 60)
	{
		$mins = "acum $mins de secunde";
	}elseif($mins < 120)
	{
		$mins = "acum un minut";
	}elseif($mins < 1200)
	{
		$mins = floor($mins / 60);
		$mins = "acum $mins minute";	
	}elseif($mins < 3600){
		$mins = floor($mins / 60);
		$mins = "acum $mins de minute";
	}elseif($mins < 7200){
		$mins = "acum o ora";
	}elseif($mins < 72000){
		$mins = floor($mins / 3600);
		$mins = "acum $mins ore";
	}elseif($mins < 86400){
		$mins = floor($mins / 3600);
		$mins = "acum $mins de ore";
	}else{
		$mins = date("j.m.Y",$timestamp) . " la " . date("H:i:s",$timestamp);
	}
	return $mins;
}

function mfetch($query)
{
	global $mysql;
	$f = $mysql->query($query);
	return $f->fetch_array(MYSQLI_ASSOC);
}

function cdate($date)
{
	$date = explode(".",$date);
	if($date[0] == "01")
		return "Ianuarie " . $date[1];
	elseif($date[0] == "02")
		return "Februarie " . $date[1];
	elseif($date[0] == "03")
		return "Martie " . $date[1];
	elseif($date[0] == "04")
		return "Aprilie " . $date[1];
	elseif($date[0] == "05")
		return "Mai " . $date[1];
	elseif($date[0] == "06")
		return "Iunie " . $date[1];
	elseif($date[0] == "07")
		return "Iulie " . $date[1];
	elseif($date[0] == "08")
		return "August " . $date[1];
	elseif($date[0] == "09")
		return "Septembrie " . $date[1];
	elseif($date[0] == "10")
		return "Octombrie " . $date[1];
	elseif($date[0] == "11")
		return "Noiembrie " . $date[1];
	elseif($date[0] == "12")
		return "Decembrie " . $date[1];
}
