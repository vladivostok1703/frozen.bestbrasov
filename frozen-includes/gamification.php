        
        <div class="pageheader">
<div class="pageicon"><span class="iconfa-star"></span></div>
            <div class="pagetitle">
		<h5>Medalii pe care le poti castiga in FRozen</h5>
                <h1>Medalii posibile</h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
		 <div class="peoplelist">
<?php
	$query = $mysql->query("select * from gamification order by orderc");
$nr = $query->num_rows;
$i=0;
if($nr > 0)
	while($f = $query->fetch_array(MYSQLI_ASSOC))
	{
		$i++;
		if($i % 4 == 1)
		{
			echo '<div class="row">';
		}
		?>
		                <div class="col-md-3">
		                    <div class="peoplewrapper">
					<div class="thumb"><img src="<?php echo $f['photo']; ?>" alt="" style="width:35px;" /></div>
		                        <div class="peopleinfo" style="margin-left:50px;">
		                            <h4><a href="#"><?php echo $f['name']; ?></a></h4>
		                            <ul>
						<li><?php echo $f['description']; ?></li>
		                            </ul>
		                        </div>
		                    </div>
		                </div>
	<?php
		if($i % 4 == 0 || $i == $nr)
		{
			echo '</div>';
		}
	}
