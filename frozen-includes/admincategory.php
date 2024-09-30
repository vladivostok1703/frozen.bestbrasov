<?php
if($_GET['del']!="" && $_GET['hash'] == $_SESSION['frozen-hash'])
{
	$del = (int) $_GET['del'];
	$mysql->query("delete from categories where ID='$del' and ID>1");
	$mysql->query("update entities set category=1 where category=$del");
}

if($_POST['edit']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$id = (int) $_GET['edit'];
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$ss = $mysql->query("select * from categories where name='$name' and ID!=$id");

	if($name != "" && $ss->num_rows == 0)
	{
		$mysql->query("update categories set name='$name' where ID='$id' and ID>1");
	}
}

if($_POST['go']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$ss = $mysql->query("select * from categories where name='$name'");

	if($name != "" && $ss->num_rows == 0)
	{
		$mysql->query("insert into categories(name) values ('$name')");
	}
}
?>
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-cogs"></span></div>
            <div class="pagetitle">
                <h5>Categoriile pentru companiile din baza de date</h5>
                <h1>Administrare categorii</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
$edit = (int) $_GET['edit'];
$query = $mysql->query("select * from categories where ID='$edit' and ID>1");
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

                <h4 class="widgettitle">Editeaza categoria</h4>

                <div class="widgetcontent nopadding">

                    <form class="stdform stdform2" method="post" action="">

                            <p>

                                <label>Nume</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $f['name']; ?>">
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
$query = $mysql->query("select * from categories order by name");
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
                                    <h4><?php echo $f['name']; ?> (<?php echo $mysql->query("select * from entities where category='".$f['ID']."'")->num_rows; ?>)</h4>
                                    <ul>
 					<li><?php if($f['ID'] != "1"){ ?><a href="dashboard.php?page=admincategory&edit=<?php echo $f['ID']; ?>">Editeaza</a>&nbsp;&nbsp;<a href="dashboard.php?page=admincategory&del=<?php echo $f['ID']; ?>&hash=<?php echo $_SESSION['frozen-hash']; ?>">Sterge</a><?php }else{ ?><em>Imposibil de editat sau sters</em><?php } ?></li>
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
