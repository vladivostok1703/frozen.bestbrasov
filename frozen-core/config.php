<?php
/* General configuration */
session_start();
date_default_timezone_set("Europe/Bucharest");

include "user.php";
include "misc.php";
include 'libraries/Google/autoload.php';

/* Database configuration */
$server = "localhost";
$username = "bestbras_frozen";
$password = "frozen123";
$database = "bestbras_frozen";
$mysql = mysqli_connect($server,$username,$password,$database) or die("<b>Error:</b> Connection to database failed.");

$frversion = "1.2";

/* Google initialization */
$client_id = '816673065431-cbe1bv3230jpfgb759a7sm6eu8u38lo4.apps.googleusercontent.com'; 
$client_secret = 'ebZhf5pXIXzGaLpVllGLx1pp';
$redirect_uri = 'https://frozen.bestbrasov.ro/login.php';

if($_SESSION['frozen-email']=="")
{
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope("email");
	$client->addScope("profile");

	$service = new Google_Service_Oauth2($client);

	if ($_GET['code']!="") 
	{
	  $client->authenticate($_GET['code']);
	  $_SESSION['access_token'] = $client->getAccessToken();
	  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	  exit;
	}

	$authUrl = $client->createAuthUrl();

	if (isset($_SESSION['access_token'])) 
	{
		$client->setAccessToken($_SESSION['access_token']);
		$user = $service->userinfo->get();
	}
}

/* Other configuration */
if($_SESSION['frozen-activity'] > 0 && $_SESSION['frozen-activity'] < time() - 3600)
	session_destroy();

$mysql->query("update users set lastactivity=0 where lastactivity<" . time() - 3600);

include "gamification.php";

function get_full_url()
{
	return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
