<?php
$id = (int) $_GET['id'];
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
                <h5>Editeaza o entitate din baza de date</h5>
                <h1>Editeaza entitate</h1>
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
	$ss = $mysql->query("select name from entities where name='$name' and ID!=$id");
	if($sql->num_rows == 0)
	{
		?><div class="alert alert-error"><b>Eroare:</b> Categoria selectata este incorecta.</div><?php
	}elseif($ss->num_rows != 0){
		?><div class="alert alert-error"><b>Eroare:</b> Entitatea exista deja in baza de date.</div><?php
	}elseif($name == ""){
		?><div class="alert alert-error"><b>Eroare:</b> Numele entitatii nu poate ramane gol.</div><?php
	}else{
		$mysql->query("update entities set name='$name',city='$city',website='$website',phone='$phone',email='$email',address='$address',
category='$category',last_edited='".time()."',edited_by='".$_SESSION['frozen-id']."' where ID='$id'");
?>
                        <div class="alert alert-success">Entitatea a fost editata.  Click <a href="<?php echo $_POST['referer']; ?>"><b>aici</b></a> pentru a te intoarce.</div>
<?php
	}
	$sql = $mysql->query("select * from entities where ID='$id'");
	$d = $sql->fetch_array(MYSQLI_ASSOC);
}
?>

            <div class="widgetbox">
                <h4 class="widgettitle">Editeaza entitate</h4>
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
		$count = "o companie";
	else
		$count = $fi['count'] . " companii";
	if($d['category']==$f['ID'])
		echo '<option selected value="'.$f['ID'].'">'.$f['name'].' ('.$count.')</option>';
	else
		echo '<option value="'.$f['ID'].'">'.$f['name'].' ('.$count.')</option>';
}
	
?>
                                </select>
                                </span>
                            </p>
                            <p>
                                <label>Nume entitate <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $d['name']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Telefon</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="phone" value="<?php echo $d['phone']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="email" value="<?php echo $d['email']; ?>">
                                </span>
                            </p> 
                            <p>
                                <label>Oras</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="city" value="<?php echo $d['city']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Site</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="website" value="<?php echo $d['website']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Adresa sediu</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="address" value="<?php echo $d['address']; ?>">
                                </span>
                            </p>                                                   
                            <p class="stdformbutton">
                                <input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Editeaza</button>
                            </p>
                    </form>
                </div>
            </div>
