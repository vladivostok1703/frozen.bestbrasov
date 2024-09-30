<?php
if($_SESSION['frozen-access'] == 2)
{
	if($_POST['go']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
	{
		$name = htmlentities($_POST['name'],ENT_QUOTES);
		if($name != "")
		{
			$mysql->query("insert into bar_categories(name) values ('$name')");
		}
	}elseif($_POST['edit']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
	{
		$id = (int) $_GET['edit'];
		$name = htmlentities($_POST['name'],ENT_QUOTES);
		if($name != "")
		{
			$mysql->query("update bar_categories set name='$name' where ID='$id'");
		}	
	}elseif($_GET['del']!="" && $_SESSION['frozen-hash'] == $_GET['hash'])
	{
		$id = (int) $_GET['del'];
		$mysql->query("delete from bar_categories where ID='$id'");
	}
}
?>
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-glass"></span></div>

            <div class="pagetitle">
                <h5>Administrare categorii de bauturi in bar</h5>
                <h1>Categorii de bauturi</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
$edit = (int) $_GET['edit'];
$query = $mysql->query("select * from bar_categories where ID='$edit'");
if($query->num_rows == 0)
{
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Adauga categorie noua</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name">
                                </span>
                            </p>
                                                    
                            <?php if($_SESSION['frozen-access'] == 2) { ?><p class="stdformbutton">
                               <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p><?php }else{ ?><p class="stdformbutton">Doar administratorii FRozen pot actualiza categoriile de bauturi.</p><?php } ?>
                    </form>
                </div>
            </div>
<?php
}else{
	$f = $query->fetch_array(MYSQLI_ASSOC);
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Editeaza categorie</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $f['name']; ?>">
                                </span>
                            </p>
                                                    
                            <?php if($_SESSION['frozen-access'] == 2) { ?><p class="stdformbutton">
                               <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="edit" value="y"><button class="btn btn-primary">Editeaza</button>
                            </p><?php }else{ ?><p class="stdformbutton">Doar administratorii FRozen pot actualiza categoriile de bauturi.</p><?php } ?>
                    </form>
                </div>
            </div>
<?php
}
?>

<?php
$query = $mysql->query("select * from bar_categories order by name");
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
                                    <?php if($_SESSION['frozen-access'] == 2) { ?><ul>
 					<li><a href="dashboard.php?page=barcat&edit=<?php echo $f['ID']; ?>">Editeaza</a>&nbsp;&nbsp;<a href="dashboard.php?page=barcat&del=<?php echo $f['ID']; ?>&hash=<?php echo $_SESSION['frozen-hash']; ?>">Sterge</a></li>
                                    </ul><?php } ?>
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
