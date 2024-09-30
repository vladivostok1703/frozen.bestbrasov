<?php
include "frozen-core/config.php";
if($_SESSION['frozen-email']!="")
{
	header("Location: dashboard.php");
	exit();
}
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>FRozen - Fundraising Web Application</title>

<link rel="stylesheet" href="frozen-design/css/style.default.css" type="text/css" />
<link rel="stylesheet" href="frozen-design/css/login.css" style="text/css" />
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->

<script src="frozen-design/js/jquery-1.10.2.min.js"></script>
<script src="frozen-design/js/jquery-migrate-1.2.1.min.js"></script>
<script src="frozen-design/js/jquery-ui-1.10.3.min.js"></script>
<script src="frozen-design/js/bootstrap.min.js"></script>
<script src="frozen-design/js/modernizr.min.js"></script>
<script src="frozen-design/js/jquery.cookies.js"></script>
<script src="frozen-design/js/custom.js"></script>

<body class="loginpage">
<div class="loginpanel">
    <div class="loginpanelinner">
        
        <div class="logo">
		<span class="title">FRozen</span><br />
		<span class="subtitle">Fundraising Web Application</span>
	</div>

        <center style="padding-top:50px;"><a class="login" href="<?php echo $authUrl; ?>" style="color:white;font-size:25px;font-weight:bold;">Conectare prin Google</a></center>

<?php
if($_GET['authorized']=="false")
{
?><br /><br />
<center><span class="error-message">EROARE<br />Contul nu este autorizat.</span></center>
<?php } ?>

    </div>
</div>

<div class="loginfooter">
    <p>Copyright &copy; 2016-<?php echo date("Y"); ?> <a href="index.php">FRozen</a> - versiunea <?php echo $frversion; ?>.<br />Creat de <a href="mailto:dragos.gaftoneanu@gmail.com">Dragos Gaftoneanu</a><br /></p>
</div>

</body>
</html>
