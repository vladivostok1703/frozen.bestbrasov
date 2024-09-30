        
        <div class="pageheader">
	    <div class="pageicon"><span class="iconfa-laptop"></span></div>
            <div class="pagetitle">
		<h5>Bine ai revenit</h5>
                <h1>Acasa</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <div class="row">
                    <div id="dashboard-left" class="col-md-3">
                        
                        <h4 class="widgettitle"><span class="glyphicon glyphicon-envelope glyphicon-white"></span> Sesiuni Active</h4>
                        <div class="widgetcontent nopadding"  style="overflow-x: hidden;overflow-y: auto;height:450px;">
                            <ul class="commentlist">
<?php
$sql = $mysql->query("select * from activities where date_end > ".time()." order by ID desc limit 0,10");
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
?>
                               <li>
                                    <div class="comment-info" style="margin-left:0px;">
                                        <h4><b><a href="dashboard.php?page=activity&id=<?php echo $f['ID']; ?>" ><?php echo $f['name']; ?></a></b></h4>
					<p><b>Data:</b> <?php echo date("j.m.Y",$f['date_start']); ?> - <?php echo date("j.m.Y",$f['date_end']); ?><br /><b>Coordonator:</b> <a href="dashboard.php?page=profile&id=<?php echo $f['coordinator']; ?>" ><?php echo get_info($f['coordinator'])['name'];?></a><br /><b>Eveniment:</b> <a href="dashboard.php?page=activities&cat=<?php echo $f['IDevent'] ?>" ><?php echo get_event_info($f['IDevent'])['name']; ?></a><br /><b>Entitati contactate:</b> <?php echo $mysql->query("select * from assigned where status>0 and IDactivity='".$f['ID']."'")->num_rows; ?>/<?php echo $mysql->query("select * from assigned where IDactivity='".$f['ID']."'")->num_rows; ?></p>
                                    </div>
                                </li>
<?php
	}
}
?>
                            </ul>
                        </div>
                        
                        <br />
                        
                        
                    </div><!--col-md-8-->


                    <div id="dashboard-left" class="col-md-3">
                        
                        <h4 class="widgettitle"><span class="glyphicon glyphicon-tasks glyphicon-white"></span> Ultimele Actualizari</h4>
                        <div class="widgetcontent nopadding" style="overflow-x: hidden;overflow-y: auto;height:450px;">
                            <ul class="commentlist">
<?php
$sql = $mysql->query("select * from messages where message like '%<em>%' order by timestamp desc limit 0,50");
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$user = get_info($f['IDresponsible']);
		$entity = mfetch("select * from entities where ID='".$f['IDentity']."'");
		$activity = mfetch("select * from activities where ID='".$f['IDactivity']."'");
?>
                               <li>
                                    <div class="comment-info" style="margin-left:0px;">
                                        <h4><b><a  href="dashboard.php?page=messages&id=<?php echo $f['IDassigned']; ?>"><?php echo $entity['name']; ?></a></b></h4>
                                        <p><i>in <a  href="dashboard.php?page=activity&id=<?php echo $activity['ID']; ?>"><?php echo $activity['name']; ?></a> de <a  href="dashboard.php?page=profile&id=<?php echo $user['ID'] ?>"><?php echo $user['name']; ?></a>  (<?php echo return_time($f['timestamp']); ?>)</i></p><p><?php echo $f['message']; ?></p>
                                    </div>
                                </li>
<?php
	}
}
?>
                            </ul>
                        </div>
                        
                        <br />
                        
                        
                    </div><!--col-md-8-->

                    <div id="dashboard-left" class="col-md-3">
                        
                        <h4 class="widgettitle"><span class="glyphicon glyphicon-comment glyphicon-white"></span> Ultimele Comentarii</h4>
                        <div class="widgetcontent nopadding" style="overflow-x: hidden;overflow-y: auto;height:450px;">
                            <ul class="commentlist">
<?php
$sql = $mysql->query("select * from messages where message not like '%<em>%' order by timestamp desc limit 0,50");
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$user = get_info($f['IDresponsible']);
		$entity = $mysql->query("select * from entities where ID='".$f['IDentity']."'");
		$entity = $entity->fetch_array(MYSQLI_ASSOC);
		$activity = $mysql->query("select * from activities where ID='".$f['IDactivity']."'");
		$activity = $activity->fetch_array(MYSQLI_ASSOC);
?>
                               <li>
                                    <div class="comment-info" style="margin-left:0px;">
                                        <h4><b><a href="dashboard.php?page=messages&id=<?php echo $f['IDassigned']; ?>" ><?php echo $entity['name']; ?></a></b></h4>
                                        <p><i>in <a  href="dashboard.php?page=activity&id=<?php echo $activity['ID']; ?>"><?php echo $activity['name']; ?></a> de <a  href="dashboard.php?page=profile&id=<?php echo $user['ID'] ?>"><?php echo $user['name']; ?></a> (<?php echo return_time($f['timestamp']); ?>)</i></p>
                                        <p style="white-space:pre-line"><?php echo $f['message'] ?></p>
                                    </div>
                                </li>
<?php
	}
}
?>
                            </ul>
                        </div>
                        
                        <br />
                        
                        
                    </div><!--col-md-8-->
                    
                    <div id="dashboard-right" class="col-md-3">
                        
                        <div class="tabbedwidget tab-primary">
                            <ul>
                                <li><a href="#tabs-1"><span class="iconfa-envelope"></span></a></li>
				<li><a href="#tabs-2"><span class="iconfa-star"></span></a></li>
                            </ul>
                            <div id="tabs-1" class="nopadding" style="overflow-x: hidden;overflow-y: auto;height:450px;">
                                <h5 class="tabtitle">Sesiunile la care particip</h5>
                                <ul class="userlist">
<?php
///
$info = get_info($_SESSION['frozen-id']);
$sql = $mysql->query("select activities.date_start as ds, activities.ID, activities.name as activity from activities, assigned where assigned.IDactivity=activities.ID and assigned.IDresponsible='".$info['ID']."' and assigned.status>0 order by activities.date_end desc, activities.name");
$activities = array();
if($sql->num_rows > 0)
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$my = date("m.Y",$f['ds']);
		if(empty($activities[$my]))
			$activities[$my] = array();
		$con = mfetch("select count(*) as c from assigned where IDresponsible='".$_SESSION['frozen-id']."' and IDactivity='".$f['ID']."' and status > 0")['c'];
		$fin = mfetch("select count(*) as c from assigned where IDresponsible='".$_SESSION['frozen-id']."' and IDactivity='".$f['ID']."' and status = 5")['c'];
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
?>
                                    <li>
                                        <div>
                                            <div class="uinfo" style="margin-left:0px;">
                                                <h5><?php echo cdate($activity); ?></h5>
                                                <span><?php
		$names = array_unique($names);
		$j=0;
		foreach($names as $name)
		{
			$j++;
			echo "&bull; " . $name;
			if($j < count($names))
				echo "<br />";
		}
 ?></span><br />
                                            </div>
                                        </div>
                                    </li>
<?php
	}
}
///
?>
				<li>
                                        <div>
                                            <div class="uinfo" style="margin-left:0px;">
                                                <span>Apasa <a href="dashboard.php?page=activities">aici</a> pentru a vedea toate sesiunile de sunat.</span>
                                            </div>
                                        </div>		
				</li>
                                </ul>
                            </div>
                           <div id="tabs-2" class="nopadding" style="overflow-x: hidden;overflow-y: auto;height:450px;">
                                <h5 class="tabtitle">Medaliile mele</h5>
                                <ul class="userlist">
<?php
$query = $mysql->query("select gamification.name,gamification.description,gamification.photo from gamification,gamificationallocation where gamificationallocation.IDgamification=gamification.ID and IDuser='".$_SESSION['frozen-id']."' order by orderc asc");
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
?>
                                    <li>
                                        <div>
                                            <img src="<?php echo $f['photo'] ?>" alt="" class="pull-left" />
                                            <div class="uinfo">
                                                <span><h5><a href="#"><?php echo $f['name']; ?></a></h5><?php echo $f['description']; ?></span><br />
                                            </div>
                                        </div>
                                    </li>
<?php
}
?>
				<li>
                                        <div>
                                            <div class="uinfo" style="margin-left:0px;">
                                                <span>Apasa <a href="dashboard.php?page=gamification">aici</a> pentru a vedea toate medaliile disponibile.</span>
                                            </div>
                                        </div>		
				</li>
                                </ul>
                            </div>
                        </div><!--tabbedwidget-->
                        
                        <br />
                                                
                    </div><!-- col-md-4 -->
                </div><!--row-->
                

