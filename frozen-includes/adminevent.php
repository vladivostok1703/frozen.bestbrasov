<?php
if($_POST['go']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$datestart = @explode("/",htmlentities($_POST['datestart'],ENT_QUOTES));
	$dateend = @explode("/",htmlentities($_POST['dateend'],ENT_QUOTES));
	$dstart = (int) @mktime("0","0","0",$datestart[0],$datestart[1],$datestart[2]);
	$dend = (int) @mktime("0","0","0",$dateend[0],$dateend[1],$dateend[2]);
	if($dstart > 0 && $dend > 0 && $dend >= $dstart)
	{
		$mysql->query("insert into events(name,datestart,dateend) values ('$name','$dstart','$dend')");
	}
}elseif($_POST['edit']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$id = (int) $_GET['edit'];
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$datestart = @explode("/",htmlentities($_POST['datestart'],ENT_QUOTES));
	$dateend = @explode("/",htmlentities($_POST['dateend'],ENT_QUOTES));
	$dstart = (int) @mktime("0","0","0",$datestart[0],$datestart[1],$datestart[2]);
	$dend = (int) @mktime("0","0","0",$dateend[0],$dateend[1],$dateend[2]);
	if($dstart > 0 && $dend > 0 && $dend >= $dstart)
	{
		$mysql->query("update events set name='$name',datestart='$dstart',dateend='$dend' where ID='$id'");
	}	
}elseif($_GET['del']!="" && $_SESSION['frozen-hash'] == $_GET['hash'])
{
	$id = (int) $_GET['del'];
	$mysql->query("delete from events where ID='$id'");
	$q = $mysql->query("select ID from activities where IDevent='$id'");
	while($f = $q->fetch_array(MYSQLI_ASSOC))
	{
		$activity = $f['ID'];
		$q2 = $mysql->query("select * from assigned where IDactivity='$activity'");
		while($f2 = $q2->fetch_array(MYSQLI_ASSOC))
		{
			$assigned = $f2['ID'];
			$q3 = $mysql->query("select * from messages where IDassigned='$assigned'");
			while($f3 = $q3->fetch_array(MYSQLI_ASSOC))
			{
				$messages = $f3['ID'];
				$mysql->query("delete from messages where ID='$messages'");
			}
			$mysql->query("delete from assigned where ID='$assigned'");
		}
		$mysql->query("delete from activities where ID='$activity'");
	}
}
?>
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-cogs"></span></div>

            <div class="pagetitle">
                <h5>Evenimentele pentru care se fac sesiuni de sunat</h5>
                <h1>Administrare evenimente</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
$edit = (int) $_GET['edit'];
$query = $mysql->query("select * from events where ID='$edit'");
if($query->num_rows == 0)
{
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Adauga eveniment nou</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name">
                                </span>
                            </p>

                            <p>
                                <label>Data inceput</label>
                                <span class="field">
                                    <input id="datestart" type="text" placeholder="mm/dd/yyyy" name="datestart" class="form-control">
                                </span>
                            </p>

                            <p>
                                <label>Data final</label>
                                <span class="field">
                                    <input id="dateend" type="text" placeholder="mm/dd/yyyy" name="dateend" class="form-control">
                                </span>
                            </p>
                                                    
                            <p class="stdformbutton">
                               <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>
<?php
}else{
	$f = $query->fetch_array(MYSQLI_ASSOC);
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Editeaza eveniment</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $f['name']; ?>">
                                </span>
                            </p>

                            <p>
                                <label>Data inceput</label>
                                <span class="field">
                                    <input id="datestart" type="text" placeholder="mm/dd/yyyy" name="datestart" class="form-control" value="<?php echo date("m/d/Y",$f['datestart']); ?>">
                                </span>
                            </p>

                            <p>
                                <label>Data final</label>
                                <span class="field">
                                    <input id="dateend" type="text" placeholder="mm/dd/yyyy" name="dateend" class="form-control" value="<?php echo date("m/d/Y",$f['dateend']); ?>">
                                </span>
                            </p>
                                                    
                            <p class="stdformbutton">
                               <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="edit" value="y"><button class="btn btn-primary">Editeaza</button>
                            </p>
                    </form>
                </div>
            </div>
<?php
}
?>

<?php
$query = $mysql->query("select * from events order by dateend desc");
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
	if($i % 4 == 1)
	{
		echo '<div class="row">';
	}
?>
                        <div class="col-md-3">
                            <div class="peoplewrapper">
                                <div class="peopleinfo" style="margin-left:0px;">
                                    <h4><?php echo $f['name']; ?></h4>
                                    <ul>
					<li><?php echo date("j.m.Y",$f['datestart']) ?> - <?php echo date("j.m.Y",$f['dateend']); ?></li>
 					<li><a href="dashboard.php?page=adminevent&edit=<?php echo $f['ID']; ?>">Editeaza</a>&nbsp;&nbsp;<a href="dashboard.php?page=adminevent&del=<?php echo $f['ID']; ?>&hash=<?php echo $_SESSION['frozen-hash']; ?>">Sterge</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
<?php
	if($i % 4 == 0 || $i == $nr)
	{
		echo '</div>';
	}
}
}
?>
