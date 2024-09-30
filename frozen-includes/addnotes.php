<?php
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from entities where ID='$id'");
if($sql->num_rows == 0)
{
	include "frozen-includes/404.php";
	die();
}
$d = $sql->fetch_array(MYSQLI_ASSOC);
?>     <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Adauga o notita noua la entitate</h5>
                <h1>Adauga notite</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
if($_POST['go']=="y")
{
	$note = htmlentities($_POST['note'],ENT_QUOTES);
	if($note != "")
	{
		$mysql->query("insert into notes(note,user,entity) values ('$note','".$_SESSION['frozen-id']."','$id')");
?>
		<div class="alert alert-success">Notita a fost adaugata.  Click <a href="<?php echo $_POST['referer']; ?>"><b>aici</b></a> pentru a te intoarce.</div>
<?php
	}
}
?>

            <div class="widgetbox">
                <h4 class="widgettitle">Adauga notite</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Companie</label>
                                <span class="field">
                                    <?php echo $d['name']; ?>
                                </span>
                            </p> 		
                            <p>
                                <label>Notita</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="note" autofocus>
                                </span>
                            </p>                                            
                            <p class="stdformbutton">
                                <input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>
