<?php
$id = (int) $_GET['id'];
$access = (int) $_GET['access'];
if(($access == 1 || $access == 2) && $_SESSION['frozen-hash'] == $_GET['hash'])
{
	$sql = $mysql->query("select name from users where ID='$id'");
	if($sql->num_rows == 1)
	{
		$info = $sql->fetch_array(MYSQLI_ASSOC);
		$mysql->query("update users set access='$access' where ID='$id'");
		$sql = $mysql->query("select email from users where access = 2");
		while($f = $sql->fetch_array(MYSQLI_ASSOC))
		{
			if($access == 1)
				$access = "utilizator";
			else
				$access = "administrator";
			mail($f['email'], "[FRozen] " . $info['name'] . " a primit acces de $access de la " . $_SESSION['frozen-name'] . " e.o.m.","","From: frozen@bestbrasov.ro\r\nX-Mailer: php");
		}
	}

}

if($_GET['hash'] == $_SESSION['frozen-hash'] && $_GET['bar'] != "")
{
	$user = (int) $_GET['bar'];
	$info = $mysql->query("select * from bar_admin where IDuser='$user'");
	if($info->num_rows == 0)
	{
		$mysql->query("insert into bar_admin(IDuser) values ('$user')");
	}else{
		$mysql->query("delete from bar_admin where IDuser='$user'");
	}
}
?>  
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-cogs"></span></div>

            <div class="pagetitle">
                <h5>Utilizatorii platformei FRozen</h5>
                <h1>Administrare utilizatori</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

            <div class="widgetbox">
                <h4 class="widgettitle">Cauta utilizatori</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="get" action="">
                            <p>
                                <label>Email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="email">
                                </span>
                            </p>
                                                    
                            <p class="stdformbutton">
                                <input type="hidden" name="page" value="adminuser"><button class="btn btn-primary">Cautare</button>
                            </p>
                    </form>
                </div>
            </div>

<?php

	$email = htmlentities($_GET['email'],ENT_QUOTES);
	$query = $mysql->query("select * from users where email like '%$email%' order by name");
	$nr = $query->num_rows;

	if($nr > 0)
	{
?>
                <div class="peoplelist">

<?php
$i=0;
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	$i++;
	if($i % 3 == 1)
	{
		echo '<div class="row">';
	}
		$online = $f['lastactivity'] > time()-3600 ? '<span class="on"> Online</span>' : '<span class="off"> Offline</span>';
		$photo = $f['photo'] != "" ? $f['photo'] : "frozen-design/images/photos/nothumb.png";
		$access = $f['access'] == 1 ? "Utilizator" : "Administrator";

		$info = $mysql->query("select * from bar_admin where IDuser='".$f['ID']."'");
?>
                        <div class="col-md-4">
                            <div class="peoplewrapper">
                                <div class="thumb"><img src="<?php echo $photo; ?>" alt="" /></div>
                                <div class="peopleinfo">
                                    <h4><a href="dashboard.php?page=profile&id=<?php echo $f['ID'] ?>"><?php echo $f['name']; ?></a> <?php echo $online; ?></h4>
                                    <ul>
					<li><span>Acces:</span> <?php echo $access; ?></li>
                                        <li><span>Email:</span> <?php echo $f['email']; ?></li>
					<li><?php
					if($f['access'] == 2)
						echo '<a href="dashboard.php?page=adminuser&email='.$email.'&id='.$f['ID'].'&access=1&hash='.$_SESSION['frozen-hash'].'">Revoca administrator</a>';
					else
						echo '<a href="dashboard.php?page=adminuser&email='.$email.'&id='.$f['ID'].'&access=2&hash='.$_SESSION['frozen-hash'].'">Ofera administrator</a>';
					echo '&nbsp;|&nbsp;<a href="login.php?connectas=' . $f['ID'] . '">Conectare ca...</a>';
					if($info->num_rows > 0)
					{
					?>&nbsp;|&nbsp;<a href="?page=adminuser&bar=<?php echo $f['ID']; ?>&hash=<?php echo $_SESSION['frozen-hash']; ?>">Revoca acces la bar</a>
					<?php
					}else{
					?>
					&nbsp;|&nbsp;<a href="?page=adminuser&bar=<?php echo $f['ID']; ?>&hash=<?php echo $_SESSION['frozen-hash']; ?>">Ofera acces la bar</a>
					<?php } ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
<?php
	if($i % 3 == 0 || $i == $nr)
	{
		echo '</div>';
	}
} 
}
?>
