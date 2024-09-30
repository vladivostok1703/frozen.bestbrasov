<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from contacts where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$f = $sql->fetch_array(MYSQLI_ASSOC);
?>
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Editeaza un contact din baza de date</h5>
                <h1>Editeaza contact</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
<?php
if($_POST['go']=="y")
{
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$email = htmlentities($_POST['email'],ENT_QUOTES);
	$phone = htmlentities($_POST['phone'],ENT_QUOTES);
	$position = htmlentities($_POST['position'],ENT_QUOTES);

	$ss = $mysql->query("select * from contacts where name='$name' and ID!=$id");

	if($name == "")
	{
		?><div class="alert alert-error"><b>Eroare:</b> Numele nu poate ramane gol.</div><?php
	}elseif($ss->num_rows != 0){
		?><div class="alert alert-error"><b>Eroare:</b> Contactul exista deja in baza de date.</div><?php
	}else{
		$mysql->query("update contacts set name='$name',position='$position',email='$email',phone='$phone' where ID='$id'");
?>
		<div class="alert alert-success">Contactul a fost editat. Apasa <a href="<?php echo $_POST['referer']; ?>"><b>aici</b></a> pentru a te intoarce.</div>
<?php
	}
	$sql = $mysql->query("select * from contacts where ID='$id'");
	$f = $sql->fetch_array(MYSQLI_ASSOC);
}
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Adauga un contact nou</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" value="<?php echo $f['name']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Pozitie</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="position" value="<?php echo $f['position']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Telefon</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="phone" value="<?php echo $f['phone']; ?>">
                                </span>
                            </p>
                            <p>
                                <label>Email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="email" value="<?php echo $f['email']; ?>">
                                </span>
                            </p>
                            <p class="stdformbutton">
                                <input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Editeaza</button>
                            </p>
                    </form>
                </div>
            </div>
