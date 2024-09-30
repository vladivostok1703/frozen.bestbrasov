<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from entities where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
?>
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Adauga un contact nou in baza de date</h5>
                <h1>Adauga contact nou</h1>
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
	
	$ss = $mysql->query("select * from contacts where IDentity=$id and name='$name'");

	if($name == "")
	{
		?><div class="alert alert-error"><b>Eroare:</b> Numele nu poate ramane gol.</div><?php
	}elseif($ss->num_rows != 0){
		?><div class="alert alert-error"><b>Eroare:</b> Contactul exista deja in baza de date.</div><?php
	}else{
		$mysql->query("insert into contacts(IDentity,name,position,email,phone) values ('$id','$name','$position','$email','$phone')");
?>
		<div class="alert alert-success">Contactul a fost adaugat in baza de date. Apasa <a href="<?php echo $_POST['referer']; ?>"><b>aici</b></a> pentru a te intoarce.</div>
<?php
	}
}
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Adauga un contact nou</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Nume <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name">
                                </span>
                            </p>
                            <p>
                                <label>Pozitie</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="position">
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
                            <p class="stdformbutton">
                                <input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>
