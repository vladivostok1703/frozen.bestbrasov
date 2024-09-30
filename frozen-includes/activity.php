<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from activities where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$d = $sql->fetch_array(MYSQLI_ASSOC);
if($_GET['del']=="y" && ($d['coordinator'] == $_SESSION['frozen-id'] || $_SESSION['frozen-access'] == 2) && $_GET['hash'] == $_SESSION['frozen-hash'])
{
	$mysql->query("delete from activities where ID='$id'");
	$mysql->query("delete from assigned where IDactivity='$id'");
	$mysql->query("delete from messages where IDactivity='$id'");
	?><meta http-equiv="refresh" content="0;URL='dashboard.php?page=activities'" />   <?php	
}elseif($_POST['assign']=="y")
{
	$ss = $mysql->query("select ID from assigned where IDactivity='" . $id . "' and IDresponsible=0");
	if($ss->num_rows != 0)
	{
		while($f = $ss->fetch_array(MYSQLI_ASSOC))
		{
			if($_POST['assign' . $f['ID']] == "y")
			{
				$mysql->query("update assigned set IDresponsible='".$_SESSION['frozen-id']."' where ID='".$f['ID']."'");
			}
		}
	}
}elseif($_POST['assignadmin']=="y" && ($_SESSION['frozen-id'] == $d['coordinator'] || $_SESSION['frozen-access'] == 2))
{
	$user = $mysql->query("select ID,email from users where ID='".(int) $_POST['user']."'");
	$ss = $mysql->query("select ID,IDentity from assigned where IDactivity='" . $id . "' and IDresponsible=0");
	if($ss->num_rows != 0 && $user->num_rows == 1)
	{
		$entities = array();
		$user = $user->fetch_array(MYSQLI_ASSOC);
		while($f = $ss->fetch_array(MYSQLI_ASSOC))
		{
			if($_POST['assign' . $f['ID']] == "y")
			{
				$mysql->query("update assigned set IDresponsible='".$user['ID']."' where ID='".$f['ID']."'");
				$entity = $mysql->query("select name from entities where ID='".$f['IDentity']."'");
				$entities[] = $entity->fetch_array(MYSQLI_ASSOC)['name'];
			}
		}
		if(!empty($entities))
		{
			$message = "Salutari,\n\n" . $_SESSION['frozen-name'] . " ti-a adaugat urmatoarele entitati in cadrul sesiunii de sunat " . html_entity_decode($d['name'],ENT_QUOTES) . ": " . implode(", ",$entities) . ".\n\nPentru a incepe activitatea, intra pe http://frozen.bestbrasov.ro/dashboard.php?page=activity&id=" . $id . "#tabs-3.\n\nSpor la sunat!";
			mail($user['email'],"[FRozen] Ti-au fost adaugate entitati noi in cadrul sesiunii de sunat " . html_entity_decode($d['name'],ENT_QUOTES),$message,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
		}
	}
}elseif(($_GET['ext']==1 || $_GET['ext']== 2)  && ($_SESSION['frozen-id'] == $d['coordinator'] || $_SESSION['frozen-access'] == 2)  && $_GET['hash'] == $_SESSION['frozen-hash']){
	if($_GET['ext']==1)
		$mysql->query("update activities set date_end=date_end+" . 60*60*24*7 . " where ID=$id");
	elseif($_GET['ext']==2)
		$mysql->query("update activities set date_end=date_end+" . 60*60*24*30 . " where ID=$id");

	$sql = $mysql->query("select * from activities where ID='$id'");
	$d = $sql->fetch_array(MYSQLI_ASSOC);

}elseif($_POST['add']=="y" && $d['date_end'] + 60*60*24 > time())
{
	$sql = $mysql->query("select ID from categories");
	$cat = array();
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		if($_POST['cat' . $f['ID']]=="y")
		{
			$q = $mysql->query("select * from entities where category='".$f['ID']."'");
			while($qq = $q->fetch_array(MYSQLI_ASSOC))
			{
				if($mysql->query("select IDentity from assigned where IDentity='".$qq['ID']."' and IDactivity='".$id."'")->num_rows == 0)
					$mysql->query("insert into assigned(IDactivity,IDentity,IDresponsible,status) values ('$id','".$qq['ID']."','0','0')");
			}
		}
	}
}elseif($_POST['go']=="y" && ($d['coordinator'] == $_SESSION['frozen-id'] || $_SESSION['frozen-access'] == 2) && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$link_booklet = htmlentities($_POST['link_booklet'],ENT_QUOTES);
	$link_phone = htmlentities($_POST['link_phone'],ENT_QUOTES);
	$link_email = htmlentities($_POST['link_email'],ENT_QUOTES);
	$link_request = htmlentities($_POST['link_request'],ENT_QUOTES);
	if($name != "")
	{
		$mysql->query("update activities set name='$name',link_booklet='$link_booklet',link_phone='$link_phone',link_email='$link_email',link_request='$link_request' where ID=$id");
	}
	$sql = $mysql->query("select * from activities where ID='$id'");
	$d = $sql->fetch_array(MYSQLI_ASSOC);
}

if($_POST['delentities']=="y" && $_SESSION['frozen-access']==2)
{
	$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . (int) $_GET['id'] . " and assigned.IDentity=entities.ID order by entities.name");
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		if(isset($_POST['v' . $f['ID']]))
		{
			$mysql->query("delete from assigned where ID='".$f['ID']."' and IDactivity='$id'");
			$mysql->query("delete from messages where IDassigned='".$f['ID']."'");
		}
	}
}
?>
       <div class="pageheader">
              <form class="searchbar">
                <a href="reports.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-primary" style="color:white;">Exporta raport PDF</a>
              </form> 
		<div class="pageicon"><span class="iconfa-envelope"></span></div>
            <div class="pagetitle">
		<h5><?php echo date("j.m.Y",$d['date_start']); ?> - <?php echo date("j.m.Y",$d['date_end']); ?></h5>
                <h1><?php echo $d['name']; ?></h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <div class="row">
                      <div class="col-md-8">
                        <div class="tabbedwidget tab-primary">
                            <ul>
                                <li><a href="#tabs-1">Informatii</a></li>
                                <li><a href="#tabs-2">Entitati</a></li>
                                <li><a href="#tabs-3">Entitatile mele</a></li>
<?php
if(($_SESSION['frozen-id'] == $d['coordinator'] || $_SESSION['frozen-access'] == 2))
{
?>
                                <li><a href="#tabs-4">Adauga entitati</a></li>
                                <li><a href="#tabs-5">Administrare</a></li>
<?php
}
?>
                            </ul>
                            <div id="tabs-1">
                                <strong>Coordonator:</strong> <?php $n = $mysql->query("select name,email from users where ID='".$d['coordinator']."'"); $data = $n->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=profile&id='.$d['coordinator'].'">' . $data['name'] . '</a>'; ?><br />
				<strong>Email:</strong> <?php echo $data['email']; ?><br /><br />
				<strong>Eveniment:</strong> <?php $n = $mysql->query("select name from events where ID='".$d['IDevent']."'"); echo $n->fetch_array(MYSQLI_ASSOC)['name']; ?><br />
				<strong>Data sesiunii de sunat:</strong> <?php echo date("j.m.Y",$d['date_start']); ?> - <?php echo date("j.m.Y",$d['date_end']); ?><br /><strong>Categorii de entitati:</strong> <?php 
$entities = array();
$q = $mysql->query("select categories.name,categories.ID from categories, entities, assigned where assigned.IDentity=entities.ID and assigned.IDactivity='$id' and categories.ID=entities.category order by categories.name");

while($l = $q->fetch_array(MYSQLI_ASSOC))
	$entities[] = '<a href="dashboard.php?page=entities&cat='.$l['ID'].'">' . $l['name'] . '</a>'; 

if(!empty($entities))
	echo implode(", ",array_unique($entities));
else
	echo "<em>Nici o categorie nu a fost selectata.</em>"; ?><br /><br />
				<strong>Link mapa:</strong> <?php echo $d['link_booklet']!="" ? '<a href="'.$d['link_booklet'].'" target="_blank">' . $d['link_booklet'] . "</a>" : "<em>Nesetat</em>"; ?><br />
				<strong>Link discutie telefon:</strong> <?php echo $d['link_phone']!="" ? '<a href="'.$d['link_phone'].'" target="_blank">' . $d['link_phone'] . "</a>" : "<em>Nesetat</em>"; ?><br />
				<strong>Link template email:</strong> <?php echo $d['link_email']!="" ? '<a href="'.$d['link_email'].'" target="_blank">' . $d['link_email'] . "</a>" : "<em>Nesetat</em>"; ?><br />
				<strong>Link cerere de sponsorizare:</strong> <?php echo $d['link_request']!="" ? '<a href="'.$d['link_request'].'" target="_blank">' . $d['link_request'] . "</a>" : "<em>Nesetat</em>"; ?><br /><br />
                            </div>
                            <div id="tabs-2" style="overflow-x: hidden;overflow-y: auto;height:450px;">
<?php
$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . (int) $_GET['id'] . " and assigned.IDentity=entities.ID order by entities.name");
if($sql->num_rows == 0)
{
	echo '<em>Nu ai nici o entitate adaugata.</em>';
}else{
?>
<?php if($_SESSION['frozen-access'] == 2) { ?><form action="" method="post"><?php } ?>
<table class="table table-bordered">
	<thead>
	<tr style="text-transform:uppercase;">
		<?php if($_SESSION['frozen-access'] == 2) { ?><th></th> <?php } ?>
		<th>Entitate</th>
		<th>Categorie</th>
		<th>Responsabil</th>
		<th>Status</th>
		<th>Updatat</th>
	</tr>
	</thead>
	<tbody>
<?php
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	?>
	<tr>
		<?php if($_SESSION['frozen-access'] == 2) { ?><td><input type="checkbox" name="v<?php echo $f['ID'] ?>" name="y"> <?php } ?>
		<td><?php $s = $mysql->query("select name,category from entities where ID='".$f['IDentity']."'"); $ss = $s->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=messages&id=' . $f['ID'] . '">' . $ss['name'] . '</a>'; ?></td>
		<td><?php $q = $mysql->query("select name from categories where ID='".$ss['category']."'"); $qq = $q->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=entities&cat='.$ss['category'].'">' . $qq['name'] . '</a>'; ?></td>
		<td><?php $q = $mysql->query("select name from users where ID='".$f['IDresponsible']."'"); echo '<a href="dashboard.php?page=profile&id='.$f['IDresponsible'].'">' . $q->fetch_array(MYSQLI_ASSOC)['name'] . '</a>'; ?></td>
		<td><?php 

if($f['status']==0) 

	echo "";

elseif($f['status']==1) 

	echo "<strong style='color:#8D5898'>Telefon</strong>";
elseif($f['status']==2) 
	echo "<strong style='color:#E08B06'>Email</strong>";
elseif($f['status']==3) 
	echo "<strong style='color:#0866C6'>Intalnire</strong>";
elseif($f['status']==4) 
	echo "<strong style='color:#E85963'>Respins</strong>";
elseif($f['status']==5) 
	echo "<strong style='color:#36C88C'>Confirmat</strong>";
?></td>
<td><?php $s = $mysql->query("select timestamp from messages where IDentity='".$f['IDentity']."' and IDactivity='".$id."' order by timestamp desc");$d = $s->fetch_array(MYSQLI_ASSOC); if($d['timestamp']>0){echo date("j.m.Y",$d['timestamp']);} ?></td>
	</tr>
	</tbody>


	<?php
}
?>
</table>
<?php if($_SESSION['frozen-access'] == 2) { ?> <input type="hidden" name="delentities" value="y"><input type="submit" class="btn btn-primary" value="Sterge entitatile selectate">
</form><?php } ?>
<?php } ?>
                            </div>
                            <div id="tabs-3" style="overflow-x: hidden;overflow-y: auto;height:450px;">
<?php
$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . (int) $_GET['id'] . " and assigned.IDentity=entities.ID and assigned.IDresponsible='".$_SESSION['frozen-id']."' order by entities.name");
if($sql->num_rows == 0)
{
	echo '<em>Nu exista nici o entitate adaugata.</em>';
}else{
?>
<table class="table table-bordered">
	<thead>
	<tr style="text-transform:uppercase;">
		<th>Entitate</th>
		<th>Categorie</th>
		<th>Status</th>
		<th>Updatat</th>
	</tr>
	</thead>
	<tbody>
<?php
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	?>
	<tr>
		<td><?php $s = $mysql->query("select name,category from entities where ID='".$f['IDentity']."'"); $ss = $s->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=messages&id=' . $f['ID'] . '">' . $ss['name'] . '</a>'; ?></td>
		<td><?php $q = $mysql->query("select name from categories where ID='".$ss['category']."'"); $qq = $q->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=entities&cat='.$ss['category'].'">' . $qq['name'] . '</a>'; ?></td>
		<td><?php 
if($f['status']==0) 
	echo "";
elseif($f['status']==1) 
	echo "<strong style='color:#8D5898'>Telefon</strong>";
elseif($f['status']==2) 
	echo "<strong style='color:#E08B06'>Email</strong>";
elseif($f['status']==3) 
	echo "<strong style='color:#0866C6'>Intalnire</strong>";
elseif($f['status']==4) 
	echo "<strong style='color:#E85963'>Respins</strong>";
elseif($f['status']==5) 
	echo "<strong style='color:#36C88C'>Confirmat</strong>";
?></td>
<td><?php $s = $mysql->query("select timestamp from messages where IDentity='".$f['IDentity']."' and IDactivity='".$id."' order by timestamp desc");$d = $s->fetch_array(MYSQLI_ASSOC); if($d['timestamp']>0){echo date("j.m.Y",$d['timestamp']);} ?></td>
	</tr>
	</tbody>
	<?php
}
?>
</table>
<?php } ?>
                            </div>
<?php
$sql = $mysql->query("select * from activities where ID='".(int) $_GET['id']."'");
$d = $sql->fetch_array(MYSQLI_ASSOC);
if(($_SESSION['frozen-id'] == $d['coordinator'] || $_SESSION['frozen-access'] == 2))
{
?>
                            <div id="tabs-4">
                        <form class="stdform stdform2" method="post" action="#tabs-2">
                            <table style="width:100%">
                                <tr><td><b>Categorii</b></td>
                                <td>
<?php
$sql = $mysql->query("select * from categories order by name");
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
		echo '<div class="checkbox"><input type="checkbox" name="cat'.$f['ID'].'" value="y"> ' . $f['name'] . "</div><br />";
}
?>
                                </td></tr>
                            </table>
	                                             
                            <p class="stdformbutton">
                                <input type="hidden" name="add" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                            </div>
                            <div id="tabs-5">
                        <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume sesiune <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $d['name']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Link mapa</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_booklet" value="<?php echo $d['link_booklet']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Link discutie telefon</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_phone"  value="<?php echo $d['link_phone']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Link template email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_email"  value="<?php echo $d['link_email']; ?>">
                                </span>
                            </p>   
                            <p>
                                <label>Link cerere de sponsorizare</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_request"  value="<?php echo $d['link_request']; ?>">
                                </span>
                            </p>  

                            <p>
                                <label>Extinde data finala a sesiunii</label>
                                <span class="field">
                                    <a href="dashboard.php?page=activity&id=<?php echo $id; ?>&ext=1&hash=<?php echo $_SESSION['frozen-hash']; ?>" class="btn btn-primary" style="color:white;">O saptamana</a>&nbsp;&nbsp;<a href="dashboard.php?page=activity&id=<?php echo $id; ?>&ext=2&hash=<?php echo $_SESSION['frozen-hash']; ?>" class="btn btn-primary" style="color:white;">O luna</a>
                                </span>
                            </p>
	                                             
                            <p class="stdformbutton">
                                <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Editeaza</button><br /><br /><em>Apasa <a href="dashboard.php?page=activity&id=<?php echo $id; ?>&del=y&hash=<?php echo $_SESSION['frozen-hash']; ?>"><b>aici</b></a> pentru a sterge sesiunea de sunat.</em>
                            </p>
                    </form>
                            </div>
<?php
}
?>
                        </div>
                        
                      </div><!--col-md-8-->

                    	<div class="col-md-4">
                        
                        <div class="widgetbox">                        
                        <div class="headtitle">
                            <h4 class="widgettitle">Participanti activi la sesiune</h4>
                        </div>
                        <div class="widgetcontent">
<?php
$pax = $mysql->query("select users.name as name, users.ID as ID from users, messages where users.ID=messages.IDresponsible and messages.IDactivity='".$id."' group by messages.IDresponsible order by users.name");
if($pax->num_rows)
{
	$array = array();
	while($f = $pax->fetch_array(MYSQLI_ASSOC))
	{
		$array[] = '<a target="_blank" href="dashboard.php?page=profile&id='.$f['ID'].'">' . $f['name'] . '</a>';
	}
	echo implode(", ",$array);
	
}else{
	echo "<em>Niciun participant momentan.</em>";
}
 ?>
                        </div><!--widgetcontent-->
                        </div><!--widgetbox-->

                        <div class="widgetbox">                        
                        <div class="headtitle">
                            <h4 class="widgettitle">Statistici entitati</h4>
                        </div>
                        <div class="widgetcontent" style="font-size:15px;font-weight:bold;">
			<table style="width:100%">
				<tr><td>Necontactate</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=0")->num_rows; ?></td></tr>
				<tr style="color:#8D5898"><td>Telefon</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=1")->num_rows; ?></td></tr>
				<tr style="color:#E08B06;"><td>Email</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=2")->num_rows; ?></td></tr>
				<tr style="color:#0866C6;"><td>Intalnire</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=3")->num_rows; ?></td></tr>
				<tr style="color:#E85963;"><td>Respins</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=4")->num_rows; ?></td></tr>
				<tr style="color:#36C88C;"><td>Confirmat</td><td><?php echo $mysql->query("select * from assigned where IDactivity=$id and status=5")->num_rows; ?></td></tr>

			</table>
                        </div><!--widgetcontent-->
                        </div><!--widgetbox-->
<?php
if($d['date_end'] + 60*60*24 > time())
{

if($_SESSION['frozen-id'] == $d['coordinator'] || $_SESSION['frozen-access'] == 2)
{
?>                        <div class="widgetbox">                        
                        <div class="headtitle">
                            <h4 class="widgettitle">Adauga entitati</h4>
                        </div>
                        <div class="widgetcontent" style="overflow-x: hidden;overflow-y: auto;height:450px;">
<?php
$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . (int) $_GET['id'] . " and assigned.IDentity=entities.ID and assigned.IDresponsible=0 order by entities.name");
if($sql->num_rows == 0)
{
	echo '<em>Nu exista nici o entitate disponibila.</em>';
}else{
?>
<form action="" method="post">
<table class="table table-bordered">
	<tbody>
		<tr><td colspan="2">Responsabil: <select name="user"><option></option><?php
$qr = $mysql->query("select ID,name from users order by name");
while($f = $qr->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$f['ID'].'">'.$f['name'].'</option>';
}
?></select></td></tr>
<?php
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	?>
	<tr>
		<td style="text-align:center;"><input type="checkbox" name="assign<?php echo $f['ID']; ?>" value="y"></td>
		<td><?php $s = $mysql->query("select name,category from entities where ID='".$f['IDentity']."'"); $ss = $s->fetch_array(MYSQLI_ASSOC); echo $ss['name']; ?> (<?php $q = $mysql->query("select name from categories where ID='".$ss['category']."'"); $qq = $q->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=entities&cat='.$ss['category'].'">' . $qq['name'] . '</a>'; ?>)</td>
	</tr>
	</tbody>
	<?php
}
?>
</table>
<input type="hidden" name="assignadmin" value="y"><button class="btn btn-primary">Adauga</button>
</form>
<?php } ?>

                        </div><!--widgetcontent-->
                        </div><!--widgetbox-->
<?php
}else{
?>
                        <div class="widgetbox">                        
                        <div class="headtitle">
                            <h4 class="widgettitle">Alege entitati</h4>
                        </div>
                        <div class="widgetcontent" style="overflow-x: hidden;overflow-y: auto;height:450px;">
<?php
$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . (int) $_GET['id'] . " and assigned.IDentity=entities.ID and assigned.IDresponsible=0 order by entities.name");
if($sql->num_rows == 0)
{
	echo '<em>Nu exista nici o entitate disponibila.</em>';
}else{
?>
<form action="dashboard.php?page=activity&id=<?php echo (int) $_GET['id']; ?>#tabs-3" method="post">
<table class="table table-bordered">
	<tbody>
<?php
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	?>
	<tr>
		<td style="text-align:center;"><input type="checkbox" name="assign<?php echo $f['ID']; ?>" value="y"></td>
		<td><?php $s = $mysql->query("select name,category from entities where ID='".$f['IDentity']."'"); $ss = $s->fetch_array(MYSQLI_ASSOC); echo $ss['name']; ?> (<?php $q = $mysql->query("select name from categories where ID='".$ss['category']."'"); $qq = $q->fetch_array(MYSQLI_ASSOC); echo '<a href="dashboard.php?page=entities&cat='.$ss['category'].'">' . $qq['name'] . '</a>'; ?>)</td>
	</tr>
	</tbody>
	<?php
}
?>
</table>
<input type="hidden" name="assign" value="y"><button class="btn btn-primary">Adauga</button>
</form>
<?php } ?>

                        </div><!--widgetcontent-->
                        </div><!--widgetbox-->
<?php }
} ?>
 </div>
                    </div><!--row-->
