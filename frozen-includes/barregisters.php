     <div class="pageheader">
<div class="pageicon"><span class="iconfa-glass"></span></div>

            <div class="pagetitle">
                <h5>Vizualizeaza registrele de vanzari</h5>
                <h1>Toate registrele</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
              <ul class="peoplegroup">
<?php
$all = 1;
$id = (int) $_GET['cat'];
if($id > 0)
{
	
	$q = $mysql->query("select * from events where dateend>".time()." and ID='$id'");
	if($q->num_rows > 0)
		$all = 0;
}

$c = $mysql->query("select * from bar_session");
?>
                    <li <?php if($all == 1){ ?>class="active"<?php } ?>><a href="dashboard.php?page=barregisters">Toate (<?php echo $c->num_rows; ?>)</a></li>
<?php
	$q = $mysql->query("select ID, name,datestart,dateend from events order by dateend desc");
	if($q->num_rows > 0)
	{
		while($f = $q->fetch_array(MYSQLI_ASSOC))
		{
			$c = $mysql->query("select count(*) as count from bar_session where IDevent='".$f['ID']."'");
			$cc = $c->fetch_array(MYSQLI_ASSOC);
			if($id == $f['ID'])
				echo '<li class="active"><a href="dashboard.php?page=barregisters&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
			else
				echo '<li><a href="dashboard.php?page=barregisters&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
		}
	}
?>
                </ul>
<?php
if($_GET['cat']!="")
{
	$query = $mysql->query("select * from bar_session where IDevent='".$_GET['cat']."' order by ID desc");
}else{
	$query = $mysql->query("select * from bar_session order by ID desc");
}

$nr = $query->num_rows;

if($nr > 0)
{
?>
                <div class="peoplelist">

<?php
$i=0;
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	$user = get_info($f['IDuser']);
	$i++;
	if($i % 4 == 1)
	{
		echo '<div class="row">';
	}
?>
                        <div class="col-md-3">
                            <div class="peoplewrapper">
                                <div class="peopleinfo" style="margin-left:0px;">
                                    <h4><a href="?page=barview&id=<?php echo $f['ID']; ?>">Registru #<?php echo $f['ID']; ?></a></h4>
					
                                    <ul>
					<li><strong>Eveniment:</strong> <?php echo $mysql->query("select name from events where ID='".$f['IDevent']."'")->fetch_array(MYSQLI_ASSOC)['name']; ?>
					<li><strong>Responsabil:</strong> <a href="dashboard.php?page=profile&id=<?php echo $user['ID'] ?>"><?php echo $user['name']; ?></a></li>
 					<li><strong>Perioada:</strong> <?php echo date("j.m.Y",$f['datestart']); ?> - <?php echo date("j.m.Y",$f['dateend']); ?> (<?php echo date("G:i:s",$f['datestart']); ?> - <?php echo date("G:i:s",$f['dateend']); ?>)</li>
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
