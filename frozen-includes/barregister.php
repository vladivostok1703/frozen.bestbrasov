<div class="pageheader">
<?php
if($mysql->query("select * from activities where ID='".(int) $_GET['event']."'")->num_rows > 0)
{
?>
              <form class="searchbar">
                <a href="dashboard.php?page=barregister&event=<?php echo (int) $_GET['event']; ?>&close=y&hash=<?php echo $_SESSION['frozen-hash'] ?>" class="btn btn-primary" style="color:white;">Inchidere registru</a>
              </form>
<?php
}
?>
<div class="pageicon"><span class="iconfa-glass"></span></div>

            <div class="pagetitle">
                <h5>Inregistreaza vanzari la bar</h5>
                <h1>Registru nou</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
if($_GET['event']=="")
{
	if(date("G") > 0 && date("G") < 5)
		$datef = strtotime("today 5 AM");
	else
		$datef = strtotime("tomorrow 5 AM");
	$session = $mysql->query("select * from bar_session where dateend=$datef and IDuser=" . $_SESSION['frozen-id']);
	if($session->num_rows > 0)
	{
		$f = $session->fetch_array(MYSQLI_ASSOC);
		?> <h4><em>Vei fi redirectionat imediat la registrul activ...</em></h4><meta http-equiv="refresh" content="0;URL='dashboard.php?page=barregister&event=<?php echo $f['IDevent']; ?>'" /><?php
	}else{
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Selecteaza evenimentul</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="get" action="">
                            <p>
                                <label>Nume eveniment</label>
                                <span class="field">
                                    <select name="event" class="chzn-select" tabindex="2" style="width:100%;">
                                  <option value=""></option>
<?php
$query = $mysql->query("select * from events where dateend > ".time()." order by dateend ASC");
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$f['ID'].'"';
	if($_GET['event'] == $f['ID'])
		echo " selected";
	echo '>'.$f['name'].'</option>';
}
?>
                                  
                                </select>
                                </span>
                            </p>
                                                    
                            <p class="stdformbutton">
                               <input type="hidden" name="page" value="barregister"><button class="btn btn-primary">Selecteaza</button>
                            </p>
                    </form>
                </div>
            </div>
<?php
	}
}
$event = (int) $_GET['event'];
if($mysql->query("select * from events where dateend > ".time()." and ID=$event order by dateend ASC")->num_rows == 1)
{
	if(date("G") > 0 && date("G") < 5)
		$datef = strtotime("today 5 AM");
	else
		$datef = strtotime("tomorrow 5 AM");
	$session = $mysql->query("select * from bar_session where dateend=$datef and IDuser=" . $_SESSION['frozen-id']);
	if($session->num_rows > 0)
	{
		$inf = $session->fetch_array(MYSQLI_ASSOC);
	}else{
		$mysql->query("insert into bar_session(IDevent,IDuser,datestart,dateend) values ('$event','".$_SESSION['frozen-id']."','".time()."','$datef')");
		$inf = $mysql->query("select * from bar_session where ID=" . $mysql->insert_id)->fetch_array(MYSQLI_ASSOC);
	}

	if($_GET['cat'] != "")
	{
		$cat = (int) $_GET['cat'];
		if($mysql->query("select * from bar_categories where ID=$cat")->num_rows > 0)
		{
			$qua = (int) $_GET['qua'];
			if(($qua == 1 || $qua == 5 || $qua == 10) && $mysql->query("select sum(quantity) as sum from bar_products where IDevent=$event and category=" . $cat)->fetch_array(MYSQLI_ASSOC)['sum'] >= $qua)
			{
				$mysql->query("insert into bar_products(IDevent,category,quantity,IDuser,date,register) values ('$event','$cat','-$qua','".$_SESSION['frozen-id']."','".time()."','".$inf['ID']."')");
			}
		}
	}

	if($_GET['del'] != "")
	{
		$del = (int) $_GET['del'];
		$mysql->query("delete from bar_products where IDevent=$event and quantity < 0 and ID=$del and IDuser=" . $_SESSION['frozen-id']);	
	}

	if($_GET['close'] == "y" && $_GET['hash'] == $_SESSION['frozen-hash'])
	{
		$mysql->query("update bar_session set dateend='".time()."' where ID='".$inf['ID']."'");
?>
<div class="alert alert-success">Registrul a fost inchis cu succes.</div><meta http-equiv="refresh" content="0;URL='dashboard.php?page=barregisters'" />
<?php
	}
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Inregistreaza vanzari</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
<?php
$query = $mysql->query("select * from bar_categories order by name asc");
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	$sum = $mysql->query("select sum(quantity) as sum from bar_products where IDevent=$event and category=" . $f['ID'])->fetch_array(MYSQLI_ASSOC);
	$sum = (int) $sum['sum'];
?>
                            <p>
                                <label><?php echo $f['name']; ?><br /><small><?php echo (int) $sum; ?> buc. disponibile</small></label>
                                <span class="field">
<?php
if($sum >= 1)
{
?>
<a href="?event=<?php echo $event; ?>&page=barregister&cat=<?php echo $f['ID']; ?>&qua=1" class="btn btn-primary" style="color:white;">1 buc</a>
<?php
}
if($sum >= 5)
{
?>&nbsp;&nbsp;<a href="?event=<?php echo $event; ?>&page=barregister&cat=<?php echo $f['ID']; ?>&qua=5" class="btn btn-primary" style="color:white;">5 buc</a>
<?php
}
if($sum >= 10)
{
?>
&nbsp;&nbsp;<a href="?event=<?php echo $event; ?>&page=barregister&cat=<?php echo $f['ID']; ?>&qua=10" class="btn btn-primary" style="color:white;">10 buc</a>
<?php
}
if($sum == 0)
{
?><em>Nu exista acest produs in stoc.</em>
<?php
}
?>
                                </span>
                            </p>
<?php
}
?>                                                    
                    </form>
                </div>
            </div>

               <h4 class="widgettitle">Informatii vanzari</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data vanzarii</th>
                            <th>Bucati</th>
                            <th>Produs</th>
                            <th>Cine a vandut</th>
                            <th>Actiuni</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$sql = $mysql->query("select * from bar_products where IDevent=$event and register=".$inf['ID']." order by ID desc");
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$user = get_info($f['IDuser']);
?>
                        <tr>
                            <td><?php echo return_time($f['date']); ?></td>
                            <td><?php echo $f['quantity']; ?></td>
                            <td><?php echo $mysql->query("select name from bar_categories where ID='".$f['category']."'")->fetch_array(MYSQLI_ASSOC)['name']; ?></td>
                            <td><a href="dashboard.php?page=profile&id=<?php echo $user['ID'] ?>"><?php echo $user['name']; ?></a></td>
                            <td><a href="dashboard.php?event=<?php echo $event; ?>&page=barregister&del=<?php echo $f['ID']; ?>" class="btn btn-primary" style="color:white;">Sterge</a></td>
                        </tr>
<?php
	}
}
?>
                    </tbody>
                </table>
<?php
}
?>
