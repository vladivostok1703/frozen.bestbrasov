<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from assigned where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$f = $sql->fetch_array(MYSQLI_ASSOC);

if($_GET['preia']=="da")
	$mysql->query("update assigned set IDresponsible='".$_SESSION['frozen-id']."' where ID='$id'");

if($_GET['delid']!="")
{
	$delid = (int) $_GET['delid'];
	if($f['IDresponsible'] == $_SESSION['frozen-id'])
	{
		$check = $mysql->query("select * from messages where IDresponsible='".$_SESSION['frozen-id']."' and IDassigned='".$id."' and ID='".$delid."'");
		if($check->num_rows > 0)
		{
			$mysql->query("delete from messages where ID='".$delid."'");
			$st = $mysql->query("select * from messages where message like '%<em>%' and IDassigned='".$id."' and IDactivity='".$f['IDactivity']."' order by timestamp desc limit 0,1");
			if($st->num_rows == 0)
			{
				$mysql->query("update assigned set status=0 where ID='".$f['ID']."'");
			}else{
				$ft = $st->fetch_array(MYSQLI_ASSOC);
				$status = str_replace(".","",explode(" ",strip_tags($ft['message']))[3]);
				if($status == "telefon")
					$mysql->query("update assigned set status=1 where ID='".$f['ID']."'");
				if($status == "email")
					$mysql->query("update assigned set status=2 where ID='".$f['ID']."'");
				if($status == "intalnire")
					$mysql->query("update assigned set status=3 where ID='".$f['ID']."'");
				if($status == "respins")
					$mysql->query("update assigned set status=4 where ID='".$f['ID']."'");
				if($status == "confirmat")
					$mysql->query("update assigned set status=5 where ID='".$f['ID']."'");
			}
		}
	}elseif($_SESSION['frozen-access'] == 2)
	{
		$mysql->query("delete from messages where ID='".$delid."'");
		$st = $mysql->query("select * from messages where message like '%<em>%' and IDassigned='".$id."' and IDactivity='".$f['IDactivity']."' order by timestamp desc limit 0,1");
		if($st->num_rows == 0)
		{
			$mysql->query("update assigned set status=0 where ID='".$f['ID']."'");
		}else{
			$ft = $st->fetch_array(MYSQLI_ASSOC);
			$status = str_replace(".","",explode(" ",strip_tags($ft['message']))[3]);
			if($status == "telefon")
				$mysql->query("update assigned set status=1 where ID='".$f['ID']."'");
			if($status == "email")
				$mysql->query("update assigned set status=2 where ID='".$f['ID']."'");
			if($status == "intalnire")
				$mysql->query("update assigned set status=3 where ID='".$f['ID']."'");
			if($status == "respins")
				$mysql->query("update assigned set status=4 where ID='".$f['ID']."'");
			if($status == "confirmat")
				$mysql->query("update assigned set status=5 where ID='".$f['ID']."'");
		}

	}
}
$sql = $mysql->query("select * from assigned where ID='$id'");
$f = $sql->fetch_array(MYSQLI_ASSOC);
$en = $mysql->query("select * from entities where ID='".$f['IDentity']."'");
$d = $en->fetch_array(MYSQLI_ASSOC);
?>
       <div class="pageheader">
              <form class="searchbar">
                <a href="dashboard.php?page=activity&id=<?php echo $f['IDactivity']; ?>" class="btn btn-primary" style="color:white;">Inapoi la sesiunea de sunat</a>
              </form> 
<div class="pageicon"><span class="iconfa-envelope"></span></div>
            <div class="pagetitle">
		<h5><?php $si=$mysql->query("select name from categories where ID='".$d['category']."'"); $fi = $si->fetch_array(MYSQLI_ASSOC);echo $fi['name']; ?></h5>
                <h1><?php echo $d['name']; ?></h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <div class="row">
                      <div class="col-md-8">
                        <ul class="timelinelist">
                            <li>
<?php
if($f['IDresponsible'] == $_SESSION['frozen-id'] || $_SESSION['frozen-access']==2 || $f['IDresponsible'] == 0)
{
?>
                                <div class="tl-icon">
                                    <i class="iconfa-link"></i>
                                </div>
<?php
	if($_POST['go']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'] && $_POST['message']!="")
	{
		$message = htmlentities($_POST['message'],ENT_QUOTES);
		$mysql->query("insert into messages(IDassigned,IDentity,IDresponsible,IDactivity,message,timestamp) values ('".$id."','".$d['ID']."','".$_SESSION['frozen-id']."','".$f['IDactivity']."','$message','".time()."')");
		if($f['IDresponsible'] == 0)
			$mysql->query("update assigned set IDresponsible='".$_SESSION['frozen-id']."' where ID='$id'");
	}
	elseif($_GET['ch']!="" && $_GET['hash'] == $_SESSION['frozen-hash'])
	{
		$ch = (int) $_GET['ch'];
		if($ch == 1 || $ch == 2 || $ch == 3 || $ch == 4 || $ch == 5)
		{
			if($mysql->query("select * from assigned where status!=$ch and ID=$id")->num_rows > 0)
			{
				$mysql->query("update assigned set status=$ch where ID='".$id."'");
				$message = '<em>Status updatat in ';
				if($ch == 1)
					$message .= '<span style="color:#8D5898">telefon</span>.</em>';
				if($ch == 2)
					$message .= '<span style="color:#E08B06">email</span>.</em>';
				if($ch == 3)
					$message .= '<span style="color:#0866C6">intalnire</span>.</em>';
				if($ch == 4)
					$message .= '<span style="color:#E85963">respins</span>.</em>';
				if($ch == 5)
					$message .= '<span style="color:#36C88C">confirmat</span>.</em>';
				$mysql->query("insert into messages(IDassigned,IDentity,IDresponsible,IDactivity,message,timestamp) values ('".$id."','".$d['ID']."','".$_SESSION['frozen-id']."','".$f['IDactivity']."','$message','".time()."')");
				if($f['IDresponsible'] == 0)
					$mysql->query("update assigned set IDresponsible='".$_SESSION['frozen-id']."' where ID='$id'");
			}
		}
	}
?>
                                <div class="tl-post">
                                   <div class="tl-texta">
<a href="dashboard.php?page=messages&id=<?php echo $id . "&hash=" . $_SESSION['frozen-hash']; ?>&ch=1" class="btn btn-primary" style="color:white;"><i class="iconfa-phone"></i> Telefon</a>&nbsp;&nbsp;
<a href="dashboard.php?page=messages&id=<?php echo $id . "&hash=" . $_SESSION['frozen-hash']; ?>&ch=2" class="btn btn-primary" style="color:white;"><i class="iconfa-envelope"></i> Email</a>&nbsp;&nbsp;
<a href="dashboard.php?page=messages&id=<?php echo $id . "&hash=" . $_SESSION['frozen-hash']; ?>&ch=3" class="btn btn-primary" style="color:white;"><i class="iconfa-group"></i> Intalnire</a>&nbsp;&nbsp;
<a href="dashboard.php?page=messages&id=<?php echo $id . "&hash=" . $_SESSION['frozen-hash']; ?>&ch=4" class="btn btn-primary" style="color:white;"><i class="iconfa-thumbs-down"></i> Respins</a>&nbsp;&nbsp;
<a href="dashboard.php?page=messages&id=<?php echo $id . "&hash=" . $_SESSION['frozen-hash']; ?>&ch=5" class="btn btn-primary" style="color:white;"><i class="iconfa-thumbs-up"></i> Confirmat</a>
</div>
                                </div>
                            </li>  
                            <li>
                                <div class="tl-icon">
                                    <i class="iconfa-pencil"></i>
                                </div>
                                <div class="tl-post">
                                    <div class="tl-texta">
					<form action="" method="post">
		                                <textarea id="tapost" class="form-control input-block-level" placeholder="Ce update-uri ai in legatura cu entitatea?" name="message"></textarea>
						<br /><input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="go" value="y"><input type="submit" value="Adauga" class="btn btn-primary">
					</form>
                                    </div>
                                </div><!--tl-post-->
                            </li>
<?php
}
?>
<?php
$sql = $mysql->query("select * from messages where IDassigned='$id' order by timestamp desc");
if($sql->num_rows > 0)
{
	while($i = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$user = $mysql->query("select * from users where ID='".$i['IDresponsible']."'");
		$user = $user->fetch_array(MYSQLI_ASSOC);
?>
                           <li>
                                <div class="tl-icon">
                                    <i class="iconfa-<?php if(stristr($i['message'],"<em>")){echo "link";}else{echo "pencil";} ?>"></i>
                                </div>
                                <div class="tl-post">
                                    <div class="tl-author">
                                        <div class="tl-thumb"><img src="<?php echo $user['photo']; ?>" alt="Poza lui <?php echo $user['name']; ?>" /></div>
                                        <h5><a href="dashboard.php?page=profile&id=<?php echo $user['ID']; ?>"><?php echo $user['name']; ?></a> <small><?php echo return_time($i['timestamp']);  ?> <?php 

if($f['IDresponsible'] == $_SESSION['frozen-id'] || $_SESSION['frozen-access'] == 2)
{
	?><a href="dashboard.php?page=messages&id=<?php echo $id;?>&delid=<?php echo $i['ID']; ?>">(Sterge)</a><?php
}
 ?></small></h5>
                                    </div>
                                    <div class="tl-body">
                                        <p style="white-space:pre-line"><?php echo $i['message']; ?></p>
                                    </div>
                                </div><!--tl-post-->
                            </li>   
<?php
	}
}
?>
                          
                        </ul>                        
                      </div><!--col-md-8-->

                    	<div class="col-md-4">

			    <div class="widgetbox">
				<div class="headtitle">
					<h4 class="widgettitle">Detalii entitate in sesiunea de sunat</h4>
				</div>
				<div class="widgetcontent">
					<strong>Status:</strong> <?php if($f['status']==0) 

	echo "Nesetat";

elseif($f['status']==1) 

	echo "<span style='color:#8D5898'>Telefon</span>";
elseif($f['status']==2) 
	echo "<span style='color:#E08B06'>Email</span>";
elseif($f['status']==3) 
	echo "<span style='color:#0866C6'>Intalnire</span>";
elseif($f['status']==4) 
	echo "<span style='color:#E85963'>Respins</span>";
elseif($f['status']==5) 
	echo "<span style='color:#36C88C'>Confirmat</span>";
?><br />
					<strong>Responsabil:</strong> <a href="dashboard.php?page=profile&id=<?php echo $f['IDresponsible']; ?>"><?php $resp = get_info($f['IDresponsible'])['name']; if($resp!="") {echo $resp;}else{echo "-";} ?></a> <?php if($f['IDresponsible'] != $_SESSION['frozen-id']) { ?>(<a href="dashboard.php?page=messages&id=<?php echo $id; ?>&preia=da">preia entitatea</a>)<?php } ?>
				</div><!--widgetcontent-->
			    </div><!--widget-->

			    <div class="widgetbox">
				<div class="headtitle">
			            <div class="btn-group">
			                <button data-toggle="dropdown" class="btn dropdown-toggle">Optiuni <span class="caret"></span></button>
			                <ul class="dropdown-menu">
			                  <li><a href="dashboard.php?page=editentity&id=<?php echo $d['ID']; ?>">Editeaza</a></li>
			                </ul>
			            </div>
					<h4 class="widgettitle">Informatii entitate</h4>
				</div>
				<div class="widgetcontent">
				            <?php if($d['phone']!="") { ?>
				                <strong>Telefon:</strong> <?php echo $d['phone']; ?><br />
				            <?php } ?>

				            <?php if($d['email']!="") { ?>
				                <strong>Email:</strong> <?php echo $d['email']; ?><br />
				            <?php } ?>

				            <?php if($d['city']!="") { ?>
				                <strong>Oras:</strong> <?php echo $d['city']; ?><br />
				            <?php } ?>

				            <?php if($d['website']!="") { ?>
				                <strong>Site:</strong> <?php echo '<a href="' . $d['website'] . '" target="_blank">Click</a>'; ?>
				            <?php } ?>
				</div><!--widgetcontent-->
			    </div><!--widget-->

                        <div class="widgetbox">                        
		                <div class="headtitle">
			            <div class="btn-group">
			                <button data-toggle="dropdown" class="btn dropdown-toggle">Optiuni <span class="caret"></span></button>
			                <ul class="dropdown-menu">
			                  <li><a href="dashboard.php?page=addnotes&id=<?php echo $f['IDentity']; ?>">Adauga notite</a></li>
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
	$mysql->query("delete from notes where ID='$delnotes' and entity='".$f['IDentity']."'");
}
$en = $mysql->query("select * from notes where entity=".$f['IDentity']." order by ID desc");
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
$query = $mysql->query("select * from contacts where IDentity='".$d['ID']."' order by name");
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
		                    <h4 class="widgettitle">Comentarii din alte sesiuni</h4>
		                </div>
                     		<div class="widgetcontent" style="overflow-x: hidden;overflow-y: auto;max-height:150px;">
				<p>
<?php
$en = $mysql->query("select * from messages where IDentity=".$f['IDentity']." and IDassigned <> ".$id." order by ID desc");
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
