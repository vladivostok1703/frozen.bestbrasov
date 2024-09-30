<?php
$id = (int) $_GET['id'];
if($_POST['adds']=="y")
{
	$activity = (int) $_POST['activity'];
	if($mysql->query("select * from assigned where IDactivity=".$activity." and IDentity=".$id)->num_rows == 0)
	{
		$mysql->query("insert into assigned(IDactivity,IDentity,IDresponsible,status) values ('$activity','$id','".$_SESSION['frozen-id']."','0')");
	}
}
$sql = $mysql->query("select * from entities where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$d = $sql->fetch_array(MYSQLI_ASSOC);
?>
       <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
		<h5><?php $si=$mysql->query("select name from categories where ID='".$d['category']."'"); $fi = $si->fetch_array(MYSQLI_ASSOC);echo $fi['name']; ?></h5>
                <h1><?php echo $d['name']; ?></h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <div class="row">
                      <div class="col-md-8">
			    <div class="widgetbox">
				<div class="headtitle">
			            <div class="btn-group">
			                <button data-toggle="dropdown" class="btn dropdown-toggle">Optiuni <span class="caret"></span></button>
			                <ul class="dropdown-menu">
			                  <li><a href="dashboard.php?page=editentity&id=<?php echo $d['ID']; ?>">Editeaza</a></li>
<?php
if($_SESSION['frozen-access']==2)
{
?>			                  <li class="divider"></li>
			                  <li><a href="dashboard.php?page=entities&del=<?php echo $d['ID']; ?>">Sterge</a></li>
<?php
}
?>
			                </ul>
			            </div>
					<h4 class="widgettitle">Informatii</h4>
				</div>
				<div class="widgetcontent nopadding">
				    <form class="stdform stdform2" method="post" action="forms.html">
				            <p>
				                <label>Nume</label>
				                <span class="field">
				                    <?php echo $d['name']; ?>
				                </span>
				            </p>
				            <p>
				                <label>Telefon</label>
				                <span class="field">
				                    <?php echo $d['phone']!="" ? $d['phone'] : "<em>nesetat</em>"; ?>
				                </span>
				            </p>

				            <p>
				                <label>Email</label>
				                <span class="field">
				                    <?php echo $d['email']!="" ? $d['email'] : "<em>nesetat</em>"; ?>
				                </span>
				            </p>

				            <p>
				                <label>Oras</label>
				                <span class="field">
				                    <?php echo $d['city']!="" ? $d['city'] : "<em>nesetat</em>"; ?>
				                </span>
				            </p>
				            <p>
				                <label>Adresa</label>
				                <span class="field">
				                    <?php echo $d['address']!="" ? $d['address'] : "<em>nesetat</em>"; ?>
				                </span>
				            </p>
				            <p>
				                <label>Site</label>
				                <span class="field">
				                    <?php echo $d['website']!="" ? '<a target="_blank" href="'.$d['website'].'">' . $d['website'].'</a>' : "<em>nesetat</em>"; ?>
				                </span>
				            </p>
				            <p>
				                <label>Ultima editare</label>
				                <span class="field">
				                    <?php echo date("j.m.Y",$d['last_edited']); ?> la <?php echo date("g:i:s A",$d['last_edited']); ?> de catre <a href="dashboard.php?page=profile&id=<?php echo $d['edited_by']; ?>"><?php $si = $mysql->query("select name from users where ID='".$d['edited_by']."'"); $fi = $si->fetch_array(MYSQLI_ASSOC); echo $fi['name']; ?></a>
				                </span>
				            </p>
				    </form>
				</div><!--widgetcontent-->
			    </div><!--widget-->

                        <div class="widgetbox">                        
                        <div class="headtitle">
                            <h4 class="widgettitle">Sesiuni de sunat</h4>
                        </div>
                        <div class="widgetcontent">
<?php
$sql = $mysql->query("select activities.date_start as ds, activities.ID, activities.name as activity, assigned.IDresponsible, users.name as user from activities, assigned,users where assigned.IDactivity=activities.ID and assigned.IDentity='".$d['ID']."' and assigned.IDresponsible>0 and users.ID=assigned.IDresponsible order by activities.date_end desc, activities.name");
$activities = array();
if($sql->num_rows > 0)
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$my = date("m.Y",$f['ds']);
		if(empty($activities[$my]))
			$activities[$my] = array();
		$activities[$my][] = '<a href="dashboard.php?page=activity&id='.$f['ID'].'">' . $f['activity'] . '</a> de catre <a href="dashboard.php?page=profile&id='.$f['IDresponsible'].'">'.$f['user'].'</a>';
	}
if(!empty($activities))
{
	$c = count($activities);
	$i=0;
	foreach($activities as $activity=>$names)
	{
		$i++;
		echo "<b>$activity</b><br />";
		foreach($names as $name)
			echo "&bull; " . $name . "<br />";
		if($i < $c)
			echo "<br />";
	}
}else{
	echo "<em>Entitatea nu a fost contactata pana acum.</em>";
}
?>
                        </div><!--widgetcontent-->
                        </div><!--widgetbox-->
                        



                      </div><!--col-md-8-->

                    	<div class="col-md-4">

                        <div class="widgetbox">                        
		                <div class="headtitle">
		                    <h4 class="widgettitle">Adaugare entitate in sesiune</h4>
		                </div>
                     		<div class="widgetcontent">
				<p>
<form action="" method="post">
<select name="activity">
<?php
$sql = $mysql->query("select * from activities where date_end>".time()." order by name asc");
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	if($mysql->query("select * from assigned where IDactivity=".$f['ID']." and IDentity=".(int) $_GET['id'])->num_rows == 0)
	{
		echo '<option value="'.$f['ID'].'">'.$f['name'].'</option>';
	}
}
?></select><br /><br />
<input type="hidden" name="adds" value="y"><input type="submit" value="Adauga" class="btn btn-primary">
</form>
</p>
				</div><!--widgetcontent-->
			    </div><!--widget-->

                        <div class="widgetbox">                        
		                <div class="headtitle">
			            <div class="btn-group">
			                <button data-toggle="dropdown" class="btn dropdown-toggle">Optiuni <span class="caret"></span></button>
			                <ul class="dropdown-menu">
			                  <li><a href="dashboard.php?page=addnotes&id=<?php echo $id; ?>">Adauga notite</a></li>
			                </ul>
			            </div>
		                    <h4 class="widgettitle">Notite entitate</h4>
		                </div>
                     		<div class="widgetcontent nopadding">
				    <form class="stdform stdform2" >
<?php
if($_SESSION['frozen-access'] == 2)
{
	$delnotes = (int) $_GET['delnote'];
	$mysql->query("delete from notes where ID='$delnotes' and entity='$id'");
}
$en = $mysql->query("select * from notes where entity=$id order by ID desc");
if($en->num_rows > 0)
{
	while($fen = $en->fetch_array(MYSQLI_ASSOC))
	{
		$user = get_info($fen['user']);
?>
				            <p>
				                <label><a href="dashboard.php?page=profile&id=<?php echo $user['ID']; ?>"><?php echo $user['name']; ?></a> <span style="font-weight:normal"><?php if($_SESSION['frozen-access'] == 2) { echo '<a href="dashboard.php?page=entity&id='.$id.'&delnote='.$fen['ID'].'">(sterge)</a>'; } ?></span></label>
				                <span class="field">
				                   <?php echo $fen['note']; ?>
				                </span>
				            </p>
<?php
	}
}else{
	echo '<p><i style="padding:15px;">Nicio notita momentan.</i></p>';
}
?>
					</form>
				</div><!--widgetcontent-->
			    </div><!--widget-->
                        
<?php
$query = $mysql->query("select * from contacts where IDentity='$id' order by name");
if($query->num_rows > 0)
{
	while($f = $query->fetch_array(MYSQLI_ASSOC))
	{
?>
                        <div class="widgetbox">                        
		                <div class="headtitle">
			            <div class="btn-group">
			                <button data-toggle="dropdown" class="btn dropdown-toggle">Optiuni <span class="caret"></span></button>
			                <ul class="dropdown-menu">
			                  <li><a href="dashboard.php?page=editcontact&id=<?php echo $f['ID']; ?>">Editeaza</a></li>
<?php
if($_SESSION['frozen-access']==2)
{
?>			                  <li class="divider"></li>
			                  <li><a href="dashboard.php?page=entity&id=<?php echo $d['ID']; ?>&del=<?php echo $f['ID']; ?>">Sterge</a></li>
<?php
}
?>
			                </ul>
			            </div>
		                    <h4 class="widgettitle">Persoana de contact</h4>
		                </div>
                     		<div class="widgetcontent">
						    <strong>Nume:</strong> <?php echo $f['name']; ?>

				                    <?php if($f['position']!=""){ ?><br /><strong>Pozitie:</strong> <?php echo $f['position']; ?><?php } ?>
				                    <?php if($f['phone']!=""){ ?><br /><strong>Telefon:</strong> <?php echo $f['phone']; ?><?php } ?>
				                    <?php if($f['email']!=""){ ?><br /><strong>Email:</strong> <?php echo $f['email']; ?><?php } ?>
				</div><!--widgetcontent-->
			    </div><!--widget-->
<?php
}}
?>
		<a href="dashboard.php?page=addcontact&id=<?php echo $d['ID']; ?>" class="btn btn-primary" style="color:white;">Adauga contact nou</a><br /><br />

                        <div class="widgetbox">                        
		                <div class="headtitle">
		                    <h4 class="widgettitle">Comentarii din sesiuni</h4>
		                </div>
                     		<div class="widgetcontent" style="overflow-x: hidden;overflow-y: auto;max-height:150px;">
				<p>
<?php
$en = $mysql->query("select * from messages where IDentity=".$id." order by ID desc");
if($en->num_rows > 0)
{
	while($fen = $en->fetch_array(MYSQLI_ASSOC))
	{
		$sql = $mysql->query("select * from activities where ID='".$fen['IDactivity']."'");
		$act = $sql->fetch_array(MYSQLI_ASSOC);
		$user = get_info($fen['user']);
		echo "<b><a target='_blank' href='dashboard.php?page=messages&id=".$fen['IDassigned']."'>" . $act['name'] . "</a></b> (".return_time($fen['timestamp']).")<br /><em>" . $fen['message'] . "</em><br /><br />"; 
	}
}else{
	echo '<p><i style="padding:5px;">Niciun comentariu momentan.</i></p>';
}
?></p>
				</div><!--widgetcontent-->
			    </div><!--widget-->
                           
                        </div><!--col-md-4-->
 
                    </div><!--row-->
