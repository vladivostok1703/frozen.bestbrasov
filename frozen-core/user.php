<?php	
function check_user($email,$photo,$name)
{
	global $mysql;

	$email = htmlentities($email,ENT_QUOTES);
	$photo = htmlentities($photo,ENT_QUOTES);
	$name = htmlentities($name,ENT_QUOTES);
	
	$query = $mysql->query("select * from authorized where email='$email'");

	if($query->num_rows == 0)
	{
		return false;
	}else{
		register_user($email,$photo,$name);
		return true;
	}
}

function connect_admin($id)
{
	global $mysql;

	if($_SESSION['frozen-access'] == 2)
	{
		$id = (int) $id;
		$query = $mysql->query("select * from users where ID='$id'");

		$mysql->query("update users set activity=0 where ID='".$_SESION['frozen-id']."'");

		if($query->num_rows == 1)
		{
			
			$f = $query->fetch_array(MYSQLI_ASSOC);
			$_SESSION['frozen-id'] = $f['ID'];
			$_SESSION['frozen-access'] = $f['access'];
			$_SESSION['frozen-email'] = $f['email'];
			$_SESSION['frozen-photo'] = $f['photo'];
			$_SESSION['frozen-name'] = $f['name'];
			$_SESSION['frozen-hash'] = md5($f['name'] . $f['photo'] . $f['email'] . rand(1000,9999) . time());
		}else{
			die("Utilizatorul selectat nu exista.");
		}
	}else{
		die("Functionalitatea aceasta este disponibila doar administratorilor.");
	}
}

function register_user($email,$photo,$name)
{
	global $mysql;

	$query = $mysql->query("select ID,access from users where email='$email'");
	
	if($query->num_rows == 0)
	{
		$mysql->query("insert into users (name,email,photo,access) values ('$name','$email','$photo','1')");
		$_SESSION['frozen-id'] = $mysql->insert_id;
		$_SESSION['frozen-access'] = '1';
	}else{
		$mysql->query("update users set photo='$photo' where email='$email'");
		$f = $query->fetch_array(MYSQLI_ASSOC);
		$_SESSION['frozen-id'] = $f['ID'];
		$_SESSION['frozen-access'] = $f['access'];
	}
	$_SESSION['frozen-email'] = $email;
	$_SESSION['frozen-photo'] = $photo;
	$_SESSION['frozen-name'] = $name;
	$_SESSION['frozen-hash'] = md5($name . $photo . $email . rand(1000,9999) . time());
}

function check_exists($id)
{
	global $mysql;
	$id = (int) $id;

	$query = $mysql->query("select ID from users where ID='$id'");

	if($query->num_rows == 0)
		return false;

	return true;
}

function get_info($id)
{
	global $mysql;
	$id = (int) $id;

	$query = $mysql->query("select * from users where ID='$id'");
	$f = $query->fetch_array(MYSQLI_ASSOC);

	return $f;
}

function get_event_info($id)
{
	global $mysql;
	$id = (int) $id;

	$query = $mysql->query("select * from events where ID='$id'");
	$f = $query->fetch_array(MYSQLI_ASSOC);

	return $f;
}

function init_user()
{
	global $mysql;

	$mysql->query("update users set lastactivity='".time()."', lastactivityr='".time()."' where ID='".$_SESSION['frozen-id']."'");
	$_SESSION['frozen-activity']=time();
}

function is_bar_admin()
{
	global $mysql;

	if($mysql->query("select * from bar_admin where IDuser='".$_SESSION['frozen-id']."'")->num_rows == 1)
		return true;
	return false;
}
