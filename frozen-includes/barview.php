<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from bar_session where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$inf = $sql->fetch_array(MYSQLI_ASSOC);
$event = $inf['IDevent'];
?><div class="pageheader">
<div class="pageicon"><span class="iconfa-glass"></span></div>

            <div class="pagetitle">
                <h5>Vizualizeaza registru de la bar</h5>
                <h1>Vizualizeaza registru</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
if($_SESSION['frozen-access'] == 2)
{
if($mysql->query("select * from events where dateend > ".time()." and ID=$event order by dateend ASC")->num_rows == 1)
{

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
<a href="?event=<?php echo $event; ?>&page=barview&id=<?php echo $id; ?>&cat=<?php echo $f['ID']; ?>&qua=1" class="btn btn-primary" style="color:white;">1 buc</a>
<?php
}
if($sum >= 5)
{
?>&nbsp;&nbsp;<a href="?event=<?php echo $event; ?>&page=barview&id=<?php echo $id; ?>&cat=<?php echo $f['ID']; ?>&qua=5" class="btn btn-primary" style="color:white;">5 buc</a>
<?php
}
if($sum >= 10)
{
?>
&nbsp;&nbsp;<a href="?event=<?php echo $event; ?>&page=barview&id=<?php echo $id; ?>&cat=<?php echo $f['ID']; ?>&qua=10" class="btn btn-primary" style="color:white;">10 buc</a>
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
<?php
}
}
?>
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
                            <td><?php if($_SESSION['frozen-access'] == 2){ ?><a href="dashboard.php?event=<?php echo $event; ?>&page=barview&id=<?php echo $id; ?>&del=<?php echo $f['ID']; ?>" class="btn btn-primary" style="color:white;">Sterge</a><?php }else{echo "<em>Nici o actiune disponibila.</em>";} ?></td>
                        </tr>
<?php
	}
}
?>
                    </tbody>
                </table>
