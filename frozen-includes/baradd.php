<div class="pageheader">
<div class="pageicon"><span class="iconfa-glass"></span></div>

            <div class="pagetitle">
                <h5>Aprovizioneaza stocul pentru un eveniment</h5>
                <h1>Aprovizionare stoc</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

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
                               <input type="hidden" name="page" value="baradd"><button class="btn btn-primary">Selecteaza</button>
                            </p>
                    </form>
                </div>
            </div>
<?php
$event = (int) $_GET['event'];
if($mysql->query("select * from events where dateend > ".time()." and ID=$event order by dateend ASC")->num_rows == 1)
{
	if($_GET['del'] != "")
	{
		$del = (int) $_GET['del'];
		if($_SESSION['frozen-acces'] != 2)
		{
			$mysql->query("delete from bar_products where IDevent=$event and quantity > 0 and ID=$del and IDuser=" . $_SESSION['frozen-id']);
		}else{
			$mysql->query("delete from bar_products where IDevent=$event and quantity > 0 and ID=$del");
		}		
	}

	if($_POST['go'] == "y")
	{
		$query = $mysql->query("select * from bar_categories order by name asc");
		$text = array();
		$edit = 0;
		while($f = $query->fetch_array(MYSQLI_ASSOC))
		{
			$prod = (int) $_POST['cat_' . $f['ID']];
			if($prod > 0)
			{
				$text[] = $prod . " " . $f['name'];
				$mysql->query("insert into bar_products(IDevent,category,quantity,IDuser,date) values ('$event','$f[ID]','$prod','" . $_SESSION["frozen-id"] . "','".time()."')");
				$edit = 1;
			}
		}
		if($edit == 1)
		{
?>
		<div class="alert alert-success">Produsele au fost adaugate cu succes.</div>
<?php
		}
	}

?>
            <div class="widgetbox">
                <h4 class="widgettitle">Aprovizionare</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
<?php
$query = $mysql->query("select * from bar_categories order by name asc");
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	$sum = $mysql->query("select sum(quantity) as sum from bar_products where IDevent=$event and category=" . $f['ID'])->fetch_array(MYSQLI_ASSOC);
?>
                            <p>
                                <label><?php echo $f['name']; ?></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="number" name="cat_<?php echo $f['ID']; ?>" placeholder="<?php echo (int) $sum['sum']; ?> buc. momentan in baza de date">
                                </span>
                            </p>
<?php
}
?>                                                    
                            <p class="stdformbutton">
                               <input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>

               <h4 class="widgettitle">Informatii aprovizionare</h4>
                <table class="table responsive">
                    <thead>
                        <tr>
                            <th>Data aprovizionarii</th>
                            <th>Bucati</th>
                            <th>Produs</th>
                            <th>Cine a aprovizionat</th>
                            <th>Actiuni</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$sql = $mysql->query("select * from bar_products where IDevent=$event and quantity > 0 order by ID desc");
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
                            <td><a  href="dashboard.php?page=profile&id=<?php echo $user['ID'] ?>"><?php echo $user['name']; ?></a></td>
                            <td><?php if($f['IDuser'] == $_SESSION['frozen-id'] || $_SESSION['frozen-access'] == 2) { ?><a href="dashboard.php?event=<?php echo $event; ?>&page=baradd&del=<?php echo $f['ID']; ?>" class="btn btn-primary" style="color:white;">Sterge</a><?php }else{echo "<em>Nici o actiune disponibila</em>"; } ?></td>
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
