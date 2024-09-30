     <div class="pageheader">
<div class="pageicon"><span class="iconfa-envelope"></span></div>
            <div class="pagetitle">
                <h5>Adauga o sesiune noua in baza de date</h5>
                <h1>Adauga sesiune noua</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

<?php
if($_POST['go']=="y")
{
	$event = (int) $_POST['event'];
	$sql = $mysql->query("select * from events where ID=$event and dateend>" . time());
	$name = htmlentities($_POST['name'],ENT_QUOTES);
	$datestart = @explode("/",htmlentities($_POST['datestart'],ENT_QUOTES));
	$dateend = @explode("/",htmlentities($_POST['dateend'],ENT_QUOTES));
	$dstart = (int) @mktime("0","0","0",$datestart[0],$datestart[1],$datestart[2]);
	$dend = (int) @mktime("0","0","0",$dateend[0],$dateend[1],$dateend[2]);
	$link_booklet = htmlentities($_POST['link_booklet'],ENT_QUOTES);
	$link_phone = htmlentities($_POST['link_phone'],ENT_QUOTES);
	$link_email = htmlentities($_POST['link_email'],ENT_QUOTES);
	$link_request = htmlentities($_POST['link_request'],ENT_QUOTES);

	if($name=="")
	{
		?><div class="alert alert-error"><b>Eroare:</b> Numele nu este completat.</div><?php
	}elseif($sql->num_rows == 0){
		?><div class="alert alert-error"><b>Eroare:</b> Evenimentul selectat este incorect.</div><?php
	}elseif($dstart == 0 || $dend == 0){
		?><div class="alert alert-error"><b>Eroare:</b> Datele selectate nu sunt corecte.</div><?php
	}elseif($dstart > $dend){
		?><div class="alert alert-error"><b>Eroare:</b> Datele selectate nu sunt corecte.</div><?php
	}else{
		$mysql->query("insert into activities(IDevent,name,date_start,date_end,coordinator,link_booklet,link_phone,link_email,link_request) values ('$event','$name','$dstart','$dend','".$_SESSION['frozen-id']."','$link_booklet','$link_phone','$link_email','$link_request')");
		$idul = $mysql->insert_id;
?>
                        <div class="alert alert-success">Sesiunea a fost adaugata in baza de date. Click <a href="dashboard.php?page=activity&id=<?php echo $idul; ?>"><b>aici</b></a> pentru a o vedea.</div>
<?php
	}
}
?>

            <div class="widgetbox">
                <h4 class="widgettitle">Adauga o sesiune noua de sunat</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Eveniment <span style="color:red;">*</span></label>
                                <span class="field">
                                      <select name="event" style="width:99%" class="chzn-select" tabindex="2">
                                  <option value=""></option> 
<?php
$sql = $mysql->query("select * from events where dateend>".time()." order by name");
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$f['ID'].'">'.$f['name'].'</option>';
}
?>
                                </select>
                                </span>
                            </p>
                            <p>
                                <label>Nume sesiune <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="name" autofocus>
                                </span>
                            </p>
                             <p>
                                <label>Data inceput <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input id="datestart" type="text" placeholder="mm/dd/yyyy" name="datestart" class="form-control">
                                </span>
                            </p>

                            <p>
                                <label>Data final <span style="color:red;">*</span></label>
                                <span class="field">
                                    <input id="dateend" type="text" placeholder="mm/dd/yyyy" name="dateend" class="form-control">
                                </span>
                            </p>
                            <p>
                                <label>Link mapa</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_booklet" autofocus>
                                </span>
                            </p>
                            <p>
                                <label>Link discutie telefon</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_phone" autofocus>
                                </span>
                            </p>
                            <p>
                                <label>Link template email</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_email" autofocus>
                                </span>
                            </p>   
                            <p>
                                <label>Link cerere de sponsorizare</label>
                                <span class="field">
                                    <input class="form-control input-lg" type="text" name="link_request" autofocus>
                                </span>
                            </p>                                            
                            <p class="stdformbutton">
                                <input type="hidden" name="go" value="y"><button class="btn btn-primary">Adauga</button>
                            </p>
                    </form>
                </div>
            </div>
