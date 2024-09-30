<?php 
include "frozen-core/config.php";

if($_SESSION['frozen-name'] == "")
{
	$_SESSION['frozen-url'] = get_full_url();
	header("Location: index.php");
	exit();
}
if($_SESSION['frozen-url'] != "")
{
	$url = $_SESSION['frozen-url'];
	$_SESSION['frozen-url'] = "";
	header("Location: $url");
	die();
}

if(!is_connected())
{
	header("Location: index.php");
	die();
}
init_user();
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>FRozen - Fundraising Web Application</title>

<link rel="stylesheet" href="frozen-design/css/style.default.css" />
<link rel="stylesheet" href="frozen-design/css/responsive-tables.css">
<link rel="stylesheet" href="frozen-design/css/dashboard.css">
    
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="frozen-design/js/html5shiv.js"></script>
<script src="frozen-design/js/respond.min.js"></script>
<![endif]-->

<script src="frozen-design/js/jquery-1.10.2.min.js"></script>
<script src="frozen-design/js/jquery-migrate-1.2.1.min.js"></script>
<script src="frozen-design/js/jquery-ui-1.10.3.min.js"></script>

<script src="frozen-design/js/bootstrap.min.js"></script>

<script src="frozen-design/js/modernizr.min.js"></script>
<script src="frozen-design/js/jquery.cookies.js"></script>
<script src="frozen-design/js/jquery.uniform.min.js"></script>
<script src="frozen-design/js/flot/jquery.flot.min.js"></script>
<script src="frozen-design/js/flot/jquery.flot.resize.min.js"></script>
<script src="frozen-design/js/responsive-tables.js"></script>
<script src="frozen-design/js/jquery.slimscroll.js"></script>
<script src="frozen-design/js/jquery.tagsinput.min.js"></script>

<script src="frozen-design/js/custom.js"></script>

<?php
if($_GET['page'] == "adminauth" || $_GET['page'] == "adminevent" || $_GET['page']=="addactivity")
{
	?><script src="frozen-design/js/forms.js"></script><?php
}
?>

<!--[if lte IE 8]>
<script src="frozen-design/js/excanvas.min.js"></script>
<![endif]-->

</head>

<body>

<div id="mainwrapper" class="mainwrapper">
    
    <div class="header">
        <div class="logo">
		<span class="title">FRozen</span><br />
		<span class="subtitle">Fundraising Web Application</span>
	</div>
        <div class="headerinner">
            <ul class="headmenu">
                <li class="odd">
<?php
$sql = $mysql->query("select * from activities where date_end > ".time()." order by ID desc limit 0,10");
?>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="count"><?php echo $sql->num_rows; ?></span>
                        <span class="head-icon head-message"></span>
                        <span class="headmenu-label">Sesiuni active</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Sesiuni de sunat active</li>
<?php
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
?>
	<li><a href="dashboard.php?page=activity&id=<?php echo $f['ID']; ?>" target="_blank"><span class="glyphicon glyphicon-envelope"></span> <strong><?php echo $f['name']; ?></strong> <small class="muted"> (<?php echo date("j.m.Y",$f['date_start']); ?> - <?php echo date("j.m.Y",$f['date_end']); ?>)</small></a></li>
<?php
	}
}
?>

                        <li class="viewmore"><a href="dashboard.php?page=activities">Vezi toate sesiunile de sunat</a></li>
                    </ul>
                </li>
                <li>
<?php
$query = $mysql->query("select * from users where lastactivity > " . (time() - 3600));
?>
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <span class="count"><?php echo $query->num_rows; ?></span>
                    <span class="head-icon head-users"></span>
                    <span class="headmenu-label">Utilizatori online</span>
                    </a>
                    <ul class="dropdown-menu newusers">
<li class="nav-header">Utilizatori online</li>
<?php
while($f = $query->fetch_array(MYSQLI_ASSOC))
{
	$mins = return_time($f['lastactivity']);
?>
                        <li>
                            <a href="dashboard.php?page=profile&id=<?php echo $f['ID']; ?>">
                                <img src="<?php echo $f['photo']; ?>" alt="" class="userthumb" />
                                <strong><?php echo $f['name']; ?></strong>
                                <small><?php echo $mins ; ?></small>
                            </a>
                        </li>
<?php
}
?>
                    </ul>
                </li>
                <li class="odd">
<?php
$sql = $mysql->query("select * from messages where message like '%<em>%' order by timestamp desc limit 0,10");
?>
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <span class="count"><?php echo $sql->num_rows; ?></span>
                    <span class="head-icon head-bar"></span>
                    <span class="headmenu-label">Ultimele actualizari</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Ultimele actualizari</li>
<?php
if($sql->num_rows > 0)
{
	while($f = $sql->fetch_array(MYSQLI_ASSOC))
	{
		$entity = $mysql->query("select * from entities where ID='".$f['IDentity']."'");
		$entity = mfetch("select * from entities where ID='".$f['IDentity']."'");
		$activity = mfetch("select * from activities where ID='".$f['IDactivity']."'");
		?><li><a href="dashboard.php?page=messages&id=<?php echo $f['IDassigned']; ?>"><span class="glyphicon glyphicon-align-left"></span> <strong><?php echo $activity['name']; ?>:</strong> Status "<?php echo str_replace(".","",explode(" ",strip_tags($f['message']))[3]); ?>" pentru <?php echo $entity['name']; ?> - <small class="muted"> <?php echo return_time($f['timestamp']); ?></small></a></li><?php
	}
}
?>
                    </ul>
                </li>
                <li class="right"  style="border-left:0px;">
                    <div class="userloggedinfo">
                        <img src="<?php echo $_SESSION['frozen-photo']; ?>" alt="Poza lui <?php echo $_SESSION['frozen-name']; ?>" />
                        <div class="userinfo">
                            <h5><?php echo $_SESSION['frozen-name']; ?> <small><br /><?php echo $_SESSION['frozen-email']; ?></small></h5>
                            <ul>
                                <li><a href="dashboard.php?page=profile&id=<?php echo $_SESSION['frozen-id']; ?>">Profilul meu</a></li>
                                <li><a href="logout.php">Deconectare</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul><!--headmenu-->
        </div>
    </div>
    
    <div class="leftpanel">
        
        <div class="leftmenu">        
            <ul class="nav nav-tabs nav-stacked">
            	<li class="nav-header">Navigare</li>
                <li><a href="dashboard.php"><span class="iconfa-laptop"></span> Acasa</a></li>
<?php
if($_SESSION['frozen-access'] == 2)
{
?>
                <li class="dropdown"><a href=""><span class="iconfa-cogs"></span> Administrare platforma</a>
                	<ul>
                        <li><a href="dashboard.php?page=adminauth">Autorizari accese pe platforma</a></li>
                    	<li><a href="dashboard.php?page=adminevent">Evenimente</a></li>
			<li><a href="dashboard.php?page=admincategory">Categorii de entitati</a></li>
                    	<li><a href="dashboard.php?page=adminuser">Utilizatori inregistrati</a></li>
                    </ul>
                </li>
<?php
}
?>
<?php
if($_SESSION['frozen-access'] == 2 || is_bar_admin())
{
?>
                <li class="dropdown"><a href=""><span class="iconfa-glass"></span> Administrare bar</a>
                	<ul>
                        	<li><a href="dashboard.php?page=barregister">Registru nou</a></li>
                        	<li><a href="dashboard.php?page=barregisters">Toate registrele</a></li>
                        	<li><a href="dashboard.php?page=baradd">Aprovizionare stoc</a></li>
                        	<li><a href="dashboard.php?page=barcat">Categorii de bauturi</a></li>

			</ul>
                </li>
<?php
}
?>

                <li><a href="dashboard.php?page=activities"><span class="iconfa-envelope"></span> Sesiuni de sunat</a></li>
                <li><a href="dashboard.php?page=entities"><span class="iconfa-briefcase"></span> Entitati</a></li>
                <li><a href="dashboard.php?page=users"><span class="iconfa-user"></span> Utilizatori</a></li>
                <li><a href="dashboard.php?page=changelog"><span class="iconfa-calendar"></span> Changelog</a></li>
                <li><a href="logout.php"><span class="iconfa-off"></span> Deconectare</a></li>
            </ul>
        </div><!--leftmenu-->
        
    </div><!-- leftpanel -->
    
    <div class="rightpanel">
<?php
if($_GET['page'] == "")
{
	include "frozen-includes/index.php";
}elseif($_GET['page'] == "profile"){
	include "frozen-includes/profile.php";
}elseif($_GET['page'] == "users"){
	include "frozen-includes/users.php";
}elseif($_GET['page'] == "adminauth" && $_SESSION['frozen-access'] == 2){
	include "frozen-includes/adminauth.php";
}elseif($_GET['page'] == "adminuser" && $_SESSION['frozen-access'] == 2){
	include "frozen-includes/adminuser.php";
}elseif($_GET['page'] == "admincategory" && $_SESSION['frozen-access'] == 2){
	include "frozen-includes/admincategory.php";
}elseif($_GET['page'] == "adminevent" && $_SESSION['frozen-access'] == 2){
	include "frozen-includes/adminevent.php";
}elseif($_GET['page'] == "barcat" && ($_SESSION['frozen-access'] == 2 || is_bar_admin())){
	include "frozen-includes/barcat.php";
}elseif($_GET['page'] == "baradd" && ($_SESSION['frozen-access'] == 2 || is_bar_admin())){
	include "frozen-includes/baradd.php";
}elseif($_GET['page'] == "barregister" && ($_SESSION['frozen-access'] == 2 || is_bar_admin())){
	include "frozen-includes/barregister.php";
}elseif($_GET['page'] == "barregisters" && ($_SESSION['frozen-access'] == 2 || is_bar_admin())){
	include "frozen-includes/barregisters.php";
}elseif($_GET['page'] == "barview" && ($_SESSION['frozen-access'] == 2 || is_bar_admin())){
	include "frozen-includes/barview.php";
}elseif($_GET['page']=="entities"){
	include "frozen-includes/entities.php";
}elseif($_GET['page']=="addentity"){
	include "frozen-includes/addentity.php";
}elseif($_GET['page']=="editentity"){
	include "frozen-includes/editentity.php";
}elseif($_GET['page']=="entity"){
	include "frozen-includes/entity.php";
}elseif($_GET['page']=="addcontact"){
	include "frozen-includes/addcontact.php";
}elseif($_GET['page']=="editcontact"){
	include "frozen-includes/editcontact.php";
}elseif($_GET['page']=="activities"){
	include "frozen-includes/activities.php";
}elseif($_GET['page']=="addactivity"){
	include "frozen-includes/addactivity.php";
}elseif($_GET['page']=="activity"){
	include "frozen-includes/activity.php";
}elseif($_GET['page']=="messages"){
	include "frozen-includes/messages.php";
}elseif($_GET['page']=="addnotes"){
	include "frozen-includes/addnotes.php";
}elseif($_GET['page']=="changelog"){
	include "frozen-includes/changelog.php";
}elseif($_GET['page']=="import"){
	include "frozen-includes/import.php";
}elseif($_GET['page']=="gamification"){
	include "frozen-includes/gamification.php";
}else{
	include "frozen-includes/404.php";
}
?>
                <div class="footer">
                    <div class="footer-left">
                        <span>FRozen - versiunea <a href="dashboard.php?page=changelog"><?php echo $frversion; ?></a></span>
                    </div>
                    <div class="footer-right">
                        <span>Creat de <a href="mailto:dragos.gaftoneanu@gmail.com">Dragos Gaftoneanu</a></span>
                    </div>
                </div><!--footer-->
                
            </div><!--maincontentinner-->
        </div><!--maincontent-->
    </div><!--rightpanel-->
    
</div><!--mainwrapper-->
<script type="text/javascript">
    jQuery(document).ready(function() {
        
        // tabbed widget
        jQuery('.tabbedwidget').tabs();
        
         jQuery('#medalii img').tooltip();
    
    });
</script>
</body>
</html>
