
     <div class="pageheader">
<div class="pageicon"><span class="iconfa-briefcase"></span></div>
            <div class="pagetitle">
                <h5>Importa entitati in baza de date</h5>
                <h1>Importa entitati</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">

            <div class="widgetbox">
                <h4 class="widgettitle">Informatii</h4>
                <div class="widgetcontent">
			<p>Importul de entitati se face prin fisiere <i>.CSV (Comma Separated Values), comma delimited</i>. Structura de coloane pentru import este urmatoarea:<br /><br />
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Nume companie</th>
			<th>Oras</th>
			<th>Link website</th>
			<th>Telefon</th>
			<th>Email</th>
			<th>Adresa</th>
			<th>Numele pers. de contact</th>
			<th>Functie pers. de contact</th>
			<th>Email pers. de contact</th>
			<th>Telefon pers. de contact</th>
		</tr>
	</thead>
</table><b>Nota 1:</b> In cazul in care numele unei entitati corespunde cu una existenta deja in baza de date, informatiile vor fi actualizate conform datelor din fisierul incarcat.<br /><b>Nota 2:</b> Ultimele trei coloane despre persoana de contact pot sa nu fie completate.<br /><b>Nota 3:</b> Pentru detaliile despre entitate, oricare camp in afara de nume poate sa fie omis.</p>
                </div>
            </div>

<?php
if($_POST['go']=="y")
{
	$file = $_FILES['fisier'];
	$category = (int) $_POST['category'];
	
	$ss = $mysql->query("select * from categories where ID=$category");

	if($ss->num_rows == 0)
	{
		?><div class="alert alert-error"><b>Eroare:</b> Categoria selectata nu exista.</div><?php
	}elseif($file['type']!="text/csv"){
		?><div class="alert alert-error"><b>Eroare:</b> Tipul fisierului selectat este incorect.</div><?php
	}elseif($file['error']!=0){
		?><div class="alert alert-error"><b>Eroare:</b> A aparut o eroare la incarcarea fisierului.</div><?php
	}else{
		$file = fopen($file['tmp_name'],"r");
		$ent = 0;
		$con = 0;
		while(!feof($file))
		{
			$content = fgetcsv($file);
			$name = trim(htmlentities($content[0],ENT_QUOTES));
			$q = $mysql->query("select * from entities where name='$name'");
			$pname = trim(htmlentities($content[6],ENT_QUOTES));
			$ppos = trim(htmlentities($content[7],ENT_QUOTES));
			$pemail = trim(htmlentities($content[8],ENT_QUOTES));
			$ptel = trim(htmlentities($content[9],ENT_QUOTES));
			$city = trim(htmlentities($content[1],ENT_QUOTES));
			$link = trim(htmlentities($content[2],ENT_QUOTES));
			$phone = trim(htmlentities($content[3],ENT_QUOTES));
			$email = trim(htmlentities($content[4],ENT_QUOTES));	
			$address = trim(htmlentities($content[5],ENT_QUOTES));
			if($q->num_rows == 0 && $name != "")
			{
				$qu = $mysql->query("insert into entities(name,city,website,phone,email,address,category,last_edited,edited_by) values ('$name','$city','$link','$phone','$email','$address','$category','".time()."','" . $_SESSION['frozen-id'] . "')");
				$ent++;
				if($pname != "")
				{
					$ident = $mysql->insert_id;
					$qp = $mysql->query("select * from contacts where IDentity='$ident' and name='".$pname."'");
					if($qp->num_rows == 0)
					{
						$mysql->query("insert into contacts(IDentity,name,position,email,phone) values ('".$ident."','$pname','$ppos','$pemail','$ptel')");
						$con++;
					}
				}
			}
			if($q->num_rows == 1 && $name != "")
			{
				$fi = $q->fetch_array(MYSQLI_ASSOC);
				$mysql->query("update entities set name='$name',city='$city',website='$link',phone='$phone',email='$email',address='$address',last_edited='".time()."',edited_by='".$_SESSION['frozen-id']."' where ID='" . $fi['ID'] . "'");
				if($pname != "")
				{
					$ident = $fi['ID'];
					$qp = $mysql->query("select * from contacts where IDentity='$ident' and name='".$pname."'");

					if($qp->num_rows == 0)
					{
						$mysql->query("insert into contacts(IDentity,name,position,email,phone) values ('".$ident."','$pname','$ppos','$pemail','$ptel')");
						$con++;
					}
				}
			}
		}
		fclose($file);
?>
		<div class="alert alert-success">Importul s-a realizat cu succes. <?php echo $ent; ?> entitati si <?php echo $con; ?> contacte au fost adaugate in baza de date.</div>
<?php
	}
}
?>
            <div class="widgetbox">
                <h4 class="widgettitle">Importa entitati</h4>
                <div class="widgetcontent nopadding">
                    <form class="stdform stdform2" method="post" action="" enctype="multipart/form-data">
                            <p>
                                <label>Fisier <span style="color:red;">*</span></label>
                                <span class="field">
		                   <input type="file" name="fisier" style="width:100%;">
                                </span>
                            </p>
                            <p>
                                <label>Categorie de entitati <span style="color:red;">*</span></label>
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
                            <p class="stdformbutton">
                                <input type="hidden" name="go" value="y"><button class="btn btn-primary">Importa</button>
                            </p>
                    </form>
                </div>
            </div>
