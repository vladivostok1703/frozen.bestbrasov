<?php
/* Gamification script */

$id = $_SESSION['frozen-id'];

/* First call */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status>0");
$already = $mysql->query("select * from gamificationallocation where IDgamification='1' and IDuser='$id'");
if($check->num_rows > 0 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru primul telefon. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('1','".$id."')");
}

/* First accepted */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status=5 and activities.date_end<" . time());
$already = $mysql->query("select * from gamificationallocation where IDgamification='2' and IDuser='$id'");
if($check->num_rows > 0 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru prima entitate adusa la eveniment. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('2','".$id."')");
}

/* Coordinator */
$check = $mysql->query("select * from activities where coordinator='$id' and date_end<" . time());
$already = $mysql->query("select * from gamificationallocation where IDgamification='4' and IDuser='$id'");
if($check->num_rows > 0 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru coordonarea unei sesiuni de sunat. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('4','".$id."')");
}

/* 50 calls */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status>0");
$already = $mysql->query("select * from gamificationallocation where IDgamification='5' and IDuser='$id'");
if($check->num_rows >= 50 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 50 de entitati contactate. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('5','".$id."')");
}

/* 25 calls */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status>0");
$already = $mysql->query("select * from gamificationallocation where IDgamification='6' and IDuser='$id'");
if($check->num_rows >= 25 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 25 de entitati contactate. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('6','".$id."')");
}

/* 100 calls */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status>0");
$already = $mysql->query("select * from gamificationallocation where IDgamification='7' and IDuser='$id'");
if($check->num_rows >= 100 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 100 de entitati contactate. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('7','".$id."')");
}

/* 10 accepted */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status=5 and activities.date_end<" . time());
$already = $mysql->query("select * from gamificationallocation where IDgamification='8' and IDuser='$id'");
if($check->num_rows >= 10 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 10 entitati adusa la evenimente. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('8','".$id."')");
}

/* 50 accepted */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status=5 and activities.date_end<" . time());
$already = $mysql->query("select * from gamificationallocation where IDgamification='9' and IDuser='$id'");
if($check->num_rows >= 10 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 50 entitati adusa la evenimente. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('9','".$id."')");
}

/* 100 accepted */
$check = $mysql->query("select assigned.ID from activities, assigned where activities.ID=assigned.IDactivity and assigned.IDresponsible='$id' and assigned.status=5 and activities.date_end<" . time());
$already = $mysql->query("select * from gamificationallocation where IDgamification='10' and IDuser='$id'");
if($check->num_rows >= 10 && $already->num_rows == 0)
{
	mail($_SESSION['frozen-email'], "[FRozen] Ai primit o noua medalie","Salutari,\n\nAi primit o noua medalie pe FRozen pentru 100 entitati adusa la evenimente. Felicitari!\n\nO zi super!" ,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
	$mysql->query("insert into gamificationallocation(IDgamification,IDuser) values ('10','".$id."')");
}


