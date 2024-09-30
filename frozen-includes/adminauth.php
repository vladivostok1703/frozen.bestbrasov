<?php
if($_POST['go']=="y" && $_SESSION['frozen-hash'] == $_POST['hash'])
{
	$emails = htmlentities($_POST['emails'],ENT_QUOTES);
	$change = 0;
	if($emails != "")
	{
		$message = "Accesele la platforma FRozen au fost actualizate de catre " . $_SESSION['frozen-name'] . " dupa cum urmeaza:\n\n";
		$email = array_unique(@explode(",",$emails));
		$sql = $mysql->query("select email from authorized");
		if($sql->num_rows > 0)
		{
			$r = array();
			while($f = $sql->fetch_array(MYSQLI_ASSOC))
			{
				if(!in_array($f['email'],$email))
				{
					$change = 1;
					$mysql->query("delete from authorized where email='".$f['email']."'");
					$r[] = $f['email'];
				}
			}
			if(!empty($r))
			{
				$message .= "Restrictionari: " . implode(", ",$r) . "\n\n";
			}
		}
		$a = array();
		foreach($email as $e)
		{
			if (filter_var($e, FILTER_VALIDATE_EMAIL) && stristr($e,"@gmail.com") && $mysql->query("select ID from authorized where email='$e'")->num_rows == 0)
			{
				$mysql->query("insert into authorized(email) values ('$e')");
				$a[] = $e;
				$change = 1;
				mail($e,"[FRozen] Accesul ti-a fost permis la platforma","Salutari,\n\n" . $_SESSION['frozen-name'] . " ti-a permis accesul la platforma FRozen.\n\nTe poti conecta acum cu contul tau de GMail la adresa http://frozen.bestbrasov.ro/.\n\nO zi super!","From: frozen@bestbrasov.ro\r\nX-Mailer: php");
			}
		}
		if(!empty($a))
		{
			$message .= "Autorizari: " . implode(", ",$a) . "\n\n";
		}
		if($change == 1)
		{
			$sql = $mysql->query("select email from users where access = 2");
			while($f = $sql->fetch_array(MYSQLI_ASSOC))
				mail($f['email'], "[FRozen] Modificari accese",$message,"From: frozen@bestbrasov.ro\r\nX-Mailer: php");
		}
	}
}
$query = $mysql->query("select email from authorized order by email asc");
$emails = array();
while($f = $query->fetch_array(MYSQLI_ASSOC))
	$emails[] = $f['email'];
?>       
        <div class="pageheader">
<div class="pageicon"><span class="iconfa-cogs"></span></div>
            <div class="pagetitle">
                <h5>Oameni care au acces la Frozen pe email</h5>
                <h1>Administrare autorizari</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
            
            <div class="widgetbox">
                <h4 class="widgettitle">Administrare autorizari</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="">
                            <p>
                                <label>Email-uri autorizate</label>
                                <span class="field">
                                    <input name="emails" id="tags" class="input-large" value="<?php echo implode(",",$emails); ?>" />
                                </span>
                            </p>
                                                    
                            <p class="stdformbutton">
                                <input type="hidden" name="hash" value="<?php echo $_SESSION['frozen-hash']; ?>"><input type="hidden" name="go" value="y"><button class="btn btn-primary">Salveaza</button>
                            </p>
                    </form>
                </div>
            </div>
