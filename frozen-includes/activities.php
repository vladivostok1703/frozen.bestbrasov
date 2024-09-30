       <div class="pageheader">
              <form class="searchbar">
                <a href="dashboard.php?page=addactivity" class="btn btn-primary" style="color:white;">Adauga sesiune noua</a>
              </form> 

	    <div class="pageicon"><span class="iconfa-envelope"></span></div>		
            <div class="pagetitle">
                <h5>Lista sesiuni active de sunat</h5>
                <h1>Sesiuni de sunat</h1>
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

$c = $mysql->query("select * from activities");
?>
                    <li <?php if($all == 1){ ?>class="active"<?php } ?>><a href="dashboard.php?page=activities">Toate (<?php echo $c->num_rows; ?>)</a></li>
<?php
	$q = $mysql->query("select ID, name,datestart,dateend from events order by dateend desc");
	if($q->num_rows > 0)
	{
		while($f = $q->fetch_array(MYSQLI_ASSOC))
		{
			$c = $mysql->query("select count(*) as count from activities where IDevent='".$f['ID']."'");
			$cc = $c->fetch_array(MYSQLI_ASSOC);
			if($id == $f['ID'])
				echo '<li class="active"><a href="dashboard.php?page=activities&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
			else
				echo '<li><a href="dashboard.php?page=activities&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
		}
	}
?>
                </ul>
                <div class="peoplelist">

<?php
if($all == 0)
	$query = $mysql->query("select * from activities where IDevent=$id order by ID desc");
else
	$query = $mysql->query("select * from activities order by ID desc");
$nr = $query->num_rows;
$i=0;
if($nr > 0)
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
		                            <h4><a href="dashboard.php?page=activity&id=<?php echo $f['ID']; ?>"><?php echo $f['name']; ?></a></h4>
		                            <ul>
						<li><?php echo date("j.m.Y",$f['date_start']); ?> - <?php echo date("j.m.Y",$f['date_end']); ?></li>
						<li><div style="width:100%;"><div style="float:left;width:50%"><strong>Confirmate:</strong> <?php echo $mysql->query("select * from assigned where IDactivity='".$f['ID']."' and status=5")->num_rows; ?></div><div style="float:right;text-align:right;width:50%"><strong>Contactate:</strong> <?php echo $mysql->query("select * from assigned where IDactivity='".$f['ID']."' and status>0")->num_rows; ?>/<?php echo $mysql->query("select * from assigned where IDactivity='".$f['ID']."'")->num_rows; ?></div><div style="clear:both"></div></div></li>
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
