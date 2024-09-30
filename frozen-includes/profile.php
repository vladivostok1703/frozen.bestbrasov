<?php
if(!check_exists($_GET['id']))
{
	include "frozen-includes/404.php";
	die();
}
$id = (int) $_GET['id'];
$info = get_info($id);
?> 
      <div class="pageheader">
<div class="pageicon"><span class="iconfa-user"></span></div>
            <div class="pagetitle">
                <h5><?php echo $info['access']==1 ? "Utilizator" : "Administrator"; ?></h5>
                <h1><?php echo $info['name']; ?></h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <div class="row">
                      <div class="col-md-9">
			    <div class="widgetbox">
				<h4 class="widgettitle">Informatii Utilizator</h4>
				<div class="widgetcontent nopadding">
				    <form class="stdform stdform2" method="post" action="forms.html">
				            <p>
				                <label>Nume</label>
				                <span class="field">
				                    <?php echo $info['name']; ?>
				                </span>
				            </p>
				            
				            <p>
				                <label>Email</label>
				                <span class="field">
				                    <?php echo $info['email']; ?>
				                </span>
				            </p>

				            <p>
				                <label>Ultima activitate</label>
				                <span class="field">
				                    <?php if($info['lastactivityr'] > 0) { echo return_time($info['lastactivityr']); }else{echo "<em>Niciodata</em>";} ?>
				                </span>
				            </p>							<p>															<label>Email autorizat</label>								<span class="field">								<?php										$autorizat = $mysql->query("select * from authorized where email='".$info['email']."'");		if($autorizat->num_rows > 0)		    echo '<span style="color:green">da</span>';		else		    echo '<span style="color:red">nu</span>';?>								</span>																					</p>							
				            <p>
				                <label>Medalii</label>
				                <span class="field" id="medalii"><?php 
$awards = $mysql->query("select gamification.name, gamification.photo from gamification, gamificationallocation where gamification.ID = gamificationallocation.IDgamification and gamificationallocation.IDuser='$id' order by gamification.orderc");
if($awards->num_rows > 0)
{
	while($award = $awards->fetch_array(MYSQLI_ASSOC))
	{
?><img data-toggle="tooltip" data-original-title="<?php echo $award['name']; ?>" src="<?php echo $award['photo']; ?>" width="25" />&nbsp;&nbsp;
<?php
	}
}else{
	echo '<i>Utilizatorul nu are nici o medalie momentan.</i>';
}
?>
				                   
				                </span>
				            </p>

				            <p>
				                <label>Administrator bar</label>
				                <span class="field"><?php if($mysql->query("select * from bar_admin where IDuser='".$id."'")->num_rows == 1){echo "da";}else{echo "nu";} ?></span>
				            </p>

				            <p>
				                <label>Sesiuni de sunat</label>
				                <span class="field">
<?php
$sql = $mysql->query("select activities.date_start as ds, activities.ID, activities.name as activity from activities, assigned where assigned.IDactivity=activities.ID and assigned.IDresponsible='".$info['ID']."' and assigned.status>0 order by activities.date_end desc, activities.name");
$activities = array();
if($sql->num_rows > 0)
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$my = date("m.Y",$f['ds']);
		if(empty($activities[$my]))
			$activities[$my] = array();
		$con = mfetch("select count(*) as c from assigned where IDresponsible='".$id."' and IDactivity='".$f['ID']."' and status > 0")['c'];
		$fin = mfetch("select count(*) as c from assigned where IDresponsible='".$id."' and IDactivity='".$f['ID']."' and status = 5")['c'];
		if($con == 1)
			$con = "una contactata";
		else
			$con .= " contactate";
		if($fin == 1)
			$fin = "una adusa";
		else
			$fin = "$fin aduse";
		$activities[$my][] = '<a href="dashboard.php?page=activity&id='.$f['ID'].'">' . $f['activity'] . '</a> ('.$con.' / '.$fin.')';
	}
if(!empty($activities))
{
	$c = count($activities);
	$i=0;
	foreach($activities as $activity=>$names)
	{
		$i++;
		echo "<b>".cdate($activity)."</b><br />";
		$names = array_unique($names);
		foreach($names as $name)
			echo "&bull; " . $name . "<br />";
		if($i < $c)
			echo "<br />";
	}
}else{
	echo "<em>Utilizatorul nu a contactat nicio entitate pana in prezent.</em>";
}
?>
				                </span>
				            </p>
				    </form>
				</div><!--widgetcontent-->
			    </div><!--widget-->									

                      </div><!--col-md-8-->

                    	<div class="col-md-3 ">
                        
                        	<img src="<?php echo $info['photo']!="" ? $info['photo'] : "frozen-design/images/photos/nothumb.png"; ?>" style="width:100%;">
                            
                        </div><!--col-md-4-->
 
                    </div><!--row-->
