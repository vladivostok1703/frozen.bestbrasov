<?php
include "frozen-core/config.php";
$mysql->query("update users set lastactivity=0 where ID='".$_SESSION['frozen-id']."'");
session_destroy();
header("Location: index.php");
die();
