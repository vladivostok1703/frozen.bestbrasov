     <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Adauga o entitate noua in baza de date</h5>
                <h1>Adauga entitate noua</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
if($_POST['go']=="y")
{
	$category = (int) $_POST['category'];
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$city = htmlentities($_POST['city'],ENT_QUOTES);
	$website = htmlentities($_POST['website'],ENT_QUOTES);
	$phone = htmlentities($_POST['phone'],ENT_QUOTES);
	$email = htmlentities($_POST['email'],ENT_QUOTES);
	$address = htmlentities($_POST['address'],ENT_QUOTES);

	$sql = $mysql->query("select * from categories where ID='$category'");
	$ss = $mysql->query("select name from entities where name='$name'");
	if($sql->num_rows == 0)
	{
		?><div class="alert alert-error"><b>Eroare:</b> Categoria selectata este incorecta.</div><?php
	}elseif($ss->num_rows != 0){
		?><div class="alert alert-error"><b>Eroare:</b> Entitatea exista deja in baza de date.</div><?php
	}elseif($name == ""){
		?><div class="alert alert-error"><b>Eroare:</b> Numele entitatii nu poate ramane gol.</div><?php
	}else{
		$_SESSION['frozen-category'] = $category;
		$mysql->query("insert into entities(name,city,website,phone,email,address,category,last_edited,edited_by) values ('$name','$city','$website','$phone','$email','$address','$category','".time()."','".$_SESSION['frozen-id']."')");
		$idul = $mysql->insert_id;
?>
                        <div class="alert alert-success">Entitatea a fost adaugata in baza de date. Click <a href="dashboard.php?page=entity&id=<?php echo $idul; ?>"><b>aici</b></a> pentru a o vedea.</div>
<?php
	}
}
?>

            <div class="widgetbox">
                <h4 class="widgettitle">Adauga o entitate noua in baza de date</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Categorie <span style="color:red;">*</span></label>
                                <span class="field">
                                      <select name="category" style="width:99%" class="chzn-select" tabindex="2">
                                  <option value=""></option> 
<?php
$sql = $mysql->query("select * from categories order by name");
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	$s = $mysql->query("select count(*) as count from entities where category='".$f['ID']."'");
	$fi = $s->fetch_array(MYSQLI_ASSOC);
	if($fi['count']==1)
		$count = "o entitate";
	else
		$count = $fi['count'] . " entitati";
	if($_SESSION['frozen-category'] == $f['ID'])
		echo '<option value="'.$f['ID'].'" selected>'.$f['name'].' (' . $count . ')</option>';
	else
		echo '<option value="'.$f['ID'].'">'.$f['name'].' (' . $count . ')</option>';
}
?>
                                </select>
                                </span>
                            </p>
                            <p>
                                <label>Nume entitate <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" autofocus>
                                </span>
                            </p>
                            <p>
                                <label>Telefon</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="phone">
                                </span>
                            </p>
                            <p>
                                <label>Email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="email">
                                </span>
                            </p> 
                            <p>
                                <label>Oras</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="city">
                                </span>
                            </p>
                            <p>
                                <label>Site</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="website">
                                </span>
                            </p>
                            <p>
                                <label>Adresa sediu</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="address">
                                </span>
                            </p>                                                   
                            <p class="stdformbutton">
                                <input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>
