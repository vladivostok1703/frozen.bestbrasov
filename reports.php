<?php 
include "frozen-core/config.php";
include "frozen-core/libraries/tcpdf/tcpdf.php";
if(!is_connected())
{
	header("Location: index.php");
	die();
}
init_user();
$id = (int) $_GET['id'];
$sql = $mysql->query("select * from activities where ID='$id'");
if($sql->num_rows == 0)
{
	header("Location: dashboard.php");
	die();
}else{
	$fe = $sql->fetch_array(MYSQLI_ASSOC);
	$name = $fe['name'];
	$date = date("j.m.Y") . " la ora " . date("H:i:s");
	$dates = date("j.m.Y",$fe['date_start']);
	$datee = date("j.m.Y",$fe['date_end']);

	$event = mfetch("select * from events where ID='".$fe['IDevent']."'");
	$eventn = $event['name'];

	$coord = get_info($fe['coordinator']);
	$coordn = ucwords($coord['name']);

	$pax = "";

	$paxq = $mysql->query("select users.name as name from users, messages where users.ID=messages.IDresponsible and messages.IDactivity='".$id."' and messages.timestamp>0 group by messages.IDresponsible order by users.name");
	if($paxq->num_rows == 1)
	{	
		$f = $paxq->fetch_array(MYSQLI_ASSOC);
		$pax .= "si a participat activ la ea " . ucwords($f['name']);
	}elseif($paxq->num_rows > 1)
	{
		$array = array();
		while($f = $paxq->fetch_array(MYSQLI_ASSOC))
		{
			$array[] = ucwords($f['name']);
		}
		$membersp = $array[0];
		for($i=1;$i<count($array)-1;$i++)
			$membersp .= ", " . $array[$i];
		$membersp .= " si " . $array[count($array)-1];
		$pax .= "si au participat activ la ea " . $membersp;
	
	}else{
		$pax .= "si nu a fost nimeni inregistrat ca participant activ la ea";
	}

	$entm = "";
	$entities = array();
	$q = $mysql->query("select categories.name,categories.ID from categories, entities, assigned where assigned.IDentity=entities.ID and assigned.IDactivity='$id' and categories.ID=entities.category group by categories.name order by categories.name");

	while($l = $q->fetch_array(MYSQLI_ASSOC))
		$entities[] = $l['name']; 

	if(!empty($entities))
	{
		$catp = $entities[0];
		for($i=1;$i<count($entities)-1;$i++)
			$catp .= ", " . $entities[$i];
		$catp .= " si " . $entities[count($entities)-1] . ".";
		$entm .= "Entitatile contactate au fost din urmatoarele categorii: " . $catp;
	}else
		$entm .= "Nici o categorie de entitati nu a fost importata."; 

	$matused = "";
	if($fe['link_booklet'] == "" && $fe['link_phone'] == "" && $fe['link_email'] == "" && $fe['link_request'] == "")
	{
		$matused .= "In cadrul sesiunii de sunat nu a fost folosit nici un material.";
	}else{
		if($fe['link_booklet'] != "")
		{
			$matused .= 'In cadrul sesiunii de sunat a fost folosita mapa de eveniment disponibila <a href="'.$fe['link_booklet'].'" style="text-decoration:none;color:#0866c6;">aici</a>.';
			if($fe['link_phone'] != "" || $fe['link_email'] != "" || $fe['link_request'] != "")
			{
				$matused .= " De asemenea, pentru a usura procesul de FR, au fost folosite urmatoarele materiale ajutatoare: ";
				$array = array();
				if($fe['link_phone'] != "")
					$array[] = '<a href="'.$fe['link_phone'].'" style="text-decoration:none;color:#0866c6;">template discutie telefon</a>';
				if($fe['link_email'] != "")
					$array[] = '<a href="'.$fe['link_email'].'" style="text-decoration:none;color:#0866c6;">template discutie email</a>';
				if($fe['link_request'] != "")
					$array[] = '<a href="'.$fe['link_request'].'" style="text-decoration:none;color:#0866c6;">cerere sponsorizare</a>';

				$matp = $array[0];
				for($i=1;$i<count($array)-1;$i++)
					$matp .= ", " . $array[$i];

				if(count($array) > 1)
				$matp .= " si " . $array[count($array)-1];

				$matp.= ".";

				$matused .= $matp;
			}
		}elseif($fe['link_phone'] != "" || $fe['link_email'] != "" || $fe['link_request'] != "")
		{
			$matused .= "Pentru a usura procesul de FR, au fost folosite urmatoarele materiale ajutatoare: ";
			$array = array();
			if($fe['link_phone'] != "")
				$array[] = '<a href="'.$fe['link_phone'].'" style="text-decoration:none;color:#0866c6;">template discutie telefon</a>';
			if($fe['link_email'] != "")
				$array[] = '<a href="'.$fe['link_email'].'" style="text-decoration:none;color:#0866c6;">template discutie email</a>';
			if($fe['link_request'] != "")
				$array[] = '<a href="'.$fe['link_request'].'" style="text-decoration:none;color:#0866c6;">cerere sponsorizare</a>';

			$matp = $array[0];
			for($i=1;$i<count($array)-1;$i++)
				$matp .= ", " . $array[$i];

			if(count($array) > 1)
			$matp .= " si " . $array[count($array)-1];

			$matp.= ".";

			$matused .= $matp;
		}
	}

	$entcont = $mysql->query("select * from assigned where IDactivity='".$id."' and status>0")->num_rows;
	$entprop = $mysql->query("select * from assigned where IDactivity='".$id."'")->num_rows;
	$entconf = $mysql->query("select * from assigned where IDactivity='".$id."' and status=5")->num_rows;
	$entreje = $mysql->query("select * from assigned where IDactivity='".$id."' and status=4")->num_rows;
	$entdela = $mysql->query("select * from assigned where IDactivity='".$id."' and (status=1 or status=2 or status=3)")->num_rows;

	$entc = "";
	if($entcont > 0)
	{
		$entc .= ", mai precis: ";
		$array = array();
		$query = $mysql->query("select entities.name as name from entities,assigned where assigned.IDactivity='".$id."' and assigned.status>0 and assigned.IDentity=entities.ID order by entities.name");
		while($f = $query->fetch_array(MYSQLI_ASSOC))
		{
			$array[] = $f['name'];
		}
		$entp = $array[0];
		for($i=1;$i<count($array)-1;$i++)
			$entp .= ", " . $array[$i];
		if(count($array) > 1)
			$entp .= " si " . $array[count($array)-1];
		$entc .= $entp . ".";
	}else{
		$entc .= ".";
	}

	$entconft = "";
	if($entconf > 0)
	{
		$entconft .= ", mai precis: ";
		$array = array();
		$query = $mysql->query("select entities.name as name from entities,assigned where assigned.IDactivity='".$id."' and assigned.status=5 and assigned.IDentity=entities.ID order by entities.name");
		while($f = $query->fetch_array(MYSQLI_ASSOC))
		{
			$array[] = $f['name'];
		}
		$entp = $array[0];
		for($i=1;$i<count($array)-1;$i++)
			$entp .= ", " . $array[$i];

		if(count($array) > 1)
			$entp .= " si " . $array[count($array)-1];
		$entconft .= $entp . ".";
	}else{
		$entconft .= ".";
	}

	$entrejet = "";
	if($entreje > 0)
	{
		$entrejet .= ", mai precis: ";
		$array = array();
		$query = $mysql->query("select entities.name as name from entities,assigned where assigned.IDactivity='".$id."' and assigned.status=4 and assigned.IDentity=entities.ID order by entities.name");
		while($f = $query->fetch_array(MYSQLI_ASSOC))
		{
			$array[] = $f['name'];
		}
		$entp = $array[0];
		for($i=1;$i<count($array)-1;$i++)
			$entp .= ", " . $array[$i];

		if(count($array) > 1)
			$entp .= " si " . $array[count($array)-1];
		$entrejet .= $entp . ".";
	}else{
		$entrejet .= ".";
	}

	$entdelat = "";
	if($entdela > 0)
	{
		$entdelat .= ", mai precis: ";
		$array = array();
		$query = $mysql->query("select entities.name as name from entities,assigned where assigned.IDactivity='".$id."' and (assigned.status=1 or assigned.status=2 or assigned.status=3) and assigned.IDentity=entities.ID order by entities.name");
		while($f = $query->fetch_array(MYSQLI_ASSOC))
		{
			$array[] = $f['name'];
		}
		$entp = $array[0];
		for($i=1;$i<count($array)-1;$i++)
			$entp .= ", " . $array[$i];

		if(count($array) > 1)
			$entp .= " si " . $array[count($array)-1];
		$entdelat .= $entp . ".";
	}else{
		$entdelat .= ".";
	}

	$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . $id . " and assigned.IDentity=entities.ID and assigned.status>0 order by entities.name");
	if($sql->num_rows > 0)
	{
		$tabelcon = <<<EOD
<span style="font-size:12px;text-align:justify;">Tabelul urmator afiseaza toate entitatile care au fost contactate in cadrul sesiunii de sunat si statusul lor la data exportarii raportului.</span><br /><br />
<table style="width:100%;font-size:12px;border: 1px solid #ddd;" cellpadding="5">
<tr>
	<td style="border: 1px solid #ddd;color:white;background-color:#333333">Nume entitate</td>
	<td style="border: 1px solid #ddd;color:white;background-color:#333333">Categorie</td>
	<td style="border: 1px solid #ddd;color:white;background-color:#333333">Responsabil</td>
	<td style="border: 1px solid #ddd;color:white;background-color:#333333">Ultimul status</td>
	<td style="border: 1px solid #ddd;color:white;background-color:#333333">Ultimul update</td>
</tr>
EOD;
	
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{
	$tabelcon .= '<tr>';
	
	$s = $mysql->query("select name,category from entities where ID='".$f['IDentity']."'"); 
	$ss = $s->fetch_array(MYSQLI_ASSOC); 
	$tabelcon .= '<td style="border: 1px solid #ddd;">' . trim($ss['name']) . '</td>';

	$q = $mysql->query("select name from categories where ID='".$ss['category']."'"); 
	$qq = $q->fetch_array(MYSQLI_ASSOC); 
	$tabelcon .= '<td style="border: 1px solid #ddd;">' . $qq['name'] . '</td>';

	$q = $mysql->query("select name from users where ID='".$f['IDresponsible']."'");
	$qq = $q->fetch_array(MYSQLI_ASSOC);
	$tabelcon .= '<td style="border: 1px solid #ddd;">' . ucwords($qq['name']) . '</td>';

	if($f['status']==1) 
		$tabelcon .= '<td style="border: 1px solid #ddd;">Telefon</td>';
	elseif($f['status']==2) 
		$tabelcon .= '<td style="border: 1px solid #ddd;">Email</td>';
	elseif($f['status']==3) 
		$tabelcon .= '<td style="border: 1px solid #ddd;">Intalnire</td>';
	elseif($f['status']==4) 
		$tabelcon .= '<td style="border: 1px solid #ddd;">Respins</td>';
	elseif($f['status']==5) 
		$tabelcon .= '<td style="border: 1px solid #ddd;">Confirmat</td>';

	$s = $mysql->query("select timestamp from messages where timestamp>0 and IDentity='".$f['IDentity']."' and IDactivity='".$id."' order by timestamp desc");
	$d = $s->fetch_array(MYSQLI_ASSOC);
	if($d['timestamp'] > 0)
	$tabelcon .= '<td style="border: 1px solid #ddd;">' . date("j.m.Y",$d['timestamp']) . "</td>";

		$tabelcon .= "</tr>";
}

		$tabelcon .= <<<EOD
</table>
EOD;
	}else{
		$tabelcon = <<<EOD
<span style="font-size:12px;text-align:justify;">La data exportarii raportului, nu a fost contactata nici o entitate.</span>
EOD;
	}

///

	$sql = $mysql->query("select assigned.* from assigned,entities where assigned.IDactivity=" . $id . " and assigned.IDentity=entities.ID and assigned.status>0 order by entities.name");
	if($sql->num_rows > 0)
	{
		$coninfo = <<<EOD
<span style="font-size:12px;text-align:justify;">In cele ce urmeaza vor fi prezentate detalii despre fiecare entitate contactata.</span>
EOD;
	
while($f = $sql->fetch_array(MYSQLI_ASSOC))
{	
	$s = $mysql->query("select name from entities where ID='".$f['IDentity']."'"); 
	$ss = $s->fetch_array(MYSQLI_ASSOC); 

	$q = $mysql->query("select name from users where ID='".$f['IDresponsible']."'");
	$qq = $q->fetch_array(MYSQLI_ASSOC);

	$st = $mysql->query("select * from messages where timestamp>0 and IDentity='".$f['IDentity']."' and IDassigned='".$f['ID']."' and message like '%<em>%' order by ID asc");

	if($st->num_rows > 0)
	{

		$coninfo .= '<br /><br /><span style="font-size:12px;text-align:justify;"><span style="text-decoration:none;color:#0866c6;font-weight:bold;">'.trim($ss['name']).' (Responsabil: '.ucwords($qq['name']).')</span></span>';

		while($fi = $st->fetch_array(MYSQLI_ASSOC))
		{
			$status = str_replace(".","",explode(" ",strip_tags($fi['message']))[3]);
			if($status == "telefon")
				$coninfo .= '<br /><span style="font-size:12px;text-align:justify;">&bull; Entitatea a fost marcata ca fiind contactata telefonic pe data de '.date('j.m.Y',$fi['timestamp']).' la ora '.date('H:i:s',$fi['timestamp']).'</span>';
			if($status == "email")
				$coninfo .= '<br /><span style="font-size:12px;text-align:justify;">&bull; Entitatea a fost marcata ca fiind contactata prin email pe data de '.date('j.m.Y',$fi['timestamp']).' la ora '.date('H:i:s',$fi['timestamp']).'</span>';
			if($status == "intalnire")
				$coninfo .= '<br /><span style="font-size:12px;text-align:justify;">&bull; Entitatea a fost marcata ca fiind chemata la o intalnire pe data de '.date('j.m.Y',$fi['timestamp']).' la ora '.date('H:i:s',$fi['timestamp']).'</span>';
			if($status == "respins")
				$coninfo .= '<br /><span style="font-size:12px;text-align:justify;">&bull; Entitatea a fost marcata ca a respins oferta de colaborare pe data de '.date('j.m.Y',$fi['timestamp']).' la ora '.date('H:i:s',$fi['timestamp']).'</span>';
			if($status == "confirmat")
				$coninfo .= '<br /><span style="font-size:12px;text-align:justify;">&bull; Entitatea a fost marcata ca a confirmat oferta de colaborare pe data de '.date('j.m.Y',$fi['timestamp']).' la ora '.date('H:i:s',$fi['timestamp']).'</span>';
		}
	}
}

	}else{
		$coninfo = <<<EOD
<span style="font-size:12px;text-align:justify;">La data exportarii raportului, nu a fost contactata nici o entitate.</span>
EOD;
	}
	

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($_SESSION['frozen-name']);
	$pdf->SetTitle('Raport de sesiune de sunat #' . time());

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	$pdf->setFontSubsetting(true);
	$pdf->SetFont('dejavusans', '', 14, '', true);

	$pdf->AddPage();

	$html = <<<EOD
<span style="font-size:24pt;color:#0866c6;text-align:center;">Raport de sesiune de sunat</span><br />
<span style="font-size:20pt;color:#0866c6;text-align:center;">$name</span><br /><br /><br />
<span style="font-style:italic;font-size:10px;text-align:justify;"><span style="color:#0866c6">Nota:</span> Prezentul raport prezinta situatia pentru sesiunea de sunat $name la data de $date. Toate informatiile si anexele atasate sunt confidentiale si protejate prin lege. </span><br /><br />
<span style="font-size:12px;text-align:justify;">In perioada $dates - $datee s-a desfasurat sesiunea de sunat $name pentru evenimentul $eventn. Activitatea a fost coordonata de catre $coordn $pax.</span><br /><br />
<span style="font-size:12px;text-align:justify;">$entm</span><br /><br />
<span style="font-size:12px;text-align:justify;font-weight:bold;color:#0866c6;">Materiale folosite</span><br />
<span style="font-size:12px;text-align:justify;">$matused</span><br /><br />
<span style="font-size:12px;text-align:justify;font-weight:bold;color:#0866c6;">Entitati contactate</span><br />
<span style="font-size:12px;text-align:justify;">Pe parcursul sesiunii de sunat au fost contactate un numar de $entcont entitati din $entprop propuse initial$entc</span><br /><br />
<span style="font-size:12px;text-align:justify;">Dintre acestea, un numar de $entconf entitati au confirmat$entconft</span><br /><br />
<span style="font-size:12px;text-align:justify;">De asemenea, un numar de $entreje entitati au respins oferta de colaborare$entrejet</span><br /><br />
<span style="font-size:12px;text-align:justify;">Nu ne-au trimis un raspuns final un numar de $entdela entitati contactate$entdelat</span><br /><br />
<span style="font-size:12px;text-align:justify;">Mai multe informatii se pot regasi in anexele atasate. </span><br /><br />
EOD;

	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

	$pdf->AddPage();

	$html = <<<EOD
<span style="font-size:24pt;color:#0866c6;text-align:center;">Anexa 1. Tabel entitati contactate</span><br /><br />
$tabelcon
EOD;
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

	$pdf->AddPage();

	$html = <<<EOD
<span style="font-size:24pt;color:#0866c6;text-align:center;">Anexa 2. Detalii entitati contactate</span><br /><br />
$coninfo
EOD;
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


	$pdf->Output('raport_de_sesiune_de_sunat_'.time().'.pdf', 'I');
}
