<?php
if($_SESSION['frozen-access']==2)
{
	$del = (int) $_GET['del'];
	$mysql->query("delete from entities where ID=$del");
	$mysql->query("delete from contacts where IDentity=$del");
	$mysql->query("delete from assigned where IDentity=$del");
	$mysql->query("delete from messages where IDentity=$del");
	$mysql->query("delete from notes where entity=$del");
}
?>
       <div class="pageheader">
              <form class="searchbar"><input type="text" name="search" placeholder="Cauta entitate" value="<?php echo htmlentities($_GET['search'],ENT_QUOTES); ?>"> &nbsp;&nbsp; 
                <a href="dashboard.php?page=import" class="btn btn-primary" style="color:white;">Importa entitati</a>&nbsp;&nbsp;<a href="dashboard.php?page=addentity" class="btn btn-primary" style="color:white;">Adauga entitate noua</a>
              <input type="hidden" name="page" value="entities"><input type="hidden" name="cat" value="<?php echo (int) $_GET['cat']; ?>"></form> 
		<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Lista entitati</h5>
                <h1>Entitati</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">   
              <ul class="peoplegroup">
<?php
$all = 1;
$id = (int) $_GET['cat'];
$search = htmlentities($_GET['search'],ENT_QUOTES);
if($id > 0)
{
	
	$q = $mysql->query("select * from categories where ID='$id'");
	if($q->num_rows > 0)
		$all = 0;
}

if($search != "")
	$c = $mysql->query("select count(*) as count from entities where name like '%$search%'");
else
	$c = $mysql->query("select count(*) as count from entities");
$cc = $c->fetch_array(MYSQLI_ASSOC);
?>
                    <li <?php if($all == 1){ ?>class="active"<?php } ?>><a href="dashboard.php?page=entities">Toate (<?php echo $cc['count']; ?>)</a></li>
<?php
	$q = $mysql->query("select ID, name from categories order by name");
	if($q->num_rows > 0)
	{
		while($f = $q->fetch_array(MYSQLI_ASSOC))
		{
			$c = $mysql->query("select count(*) as count from entities where category='".$f['ID']."'");
			$cc = $c->fetch_array(MYSQLI_ASSOC);
			if($id == $f['ID'])
				echo '<li class="active"><a href="dashboard.php?page=entities&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
			else
				echo '<li><a href="dashboard.php?page=entities&cat='.$f['ID'].'">'.$f['name'].' ('.$cc['count'].')</a></li>';
		}
	}
?>
                </ul>
                <div class="peoplelist">

<?php
if($all == 0)
{
	if($search != "")
		$query = $mysql->query("select * from entities where category=$id and name like '%$search%' order by name");
	else
		$query = $mysql->query("select * from entities where category=$id order by name");
}else{
	if($search != "")
		$query = $mysql->query("select * from entities where name like '%$search%' order by name");
	else
		$query = $mysql->query("select * from entities order by name");
}
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
		                            <h4><a href="dashboard.php?page=entity&id=<?php echo $f['ID']; ?>"><?php echo $f['name']; ?></a></h4>
		                            <ul>
						<li><span>Categorie:</span> <?php $ni = $mysql->query("select name from categories where ID='".$f['category']."'")->fetch_array(MYSQLI_ASSOC);echo $ni['name'];  ?></li>
						<li><span>Numar de contactari: </span> <?php echo $mysql->query("select * from assigned where IDentity='".$f['ID']."' and status>0")->num_rows; ?></li>
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
