<?php
include("../includes/config.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);


if(isset($_POST['lastmsg'])) {
	$lastmsg=$_POST['lastmsg'];
	$query = sprintf("select * from users WHERE idu < '$lastmsg' order by idu desc limit 20",
					mysql_real_escape_string($lastmsg));
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
	$unique = $row['idu'];
	$username = $row['username'];
	$email = $row['email'];
	
	echo '<div class="admin-rows"><div class="one columns">'.$unique.'</div><div class="three columns">'.$username.'</div><div class="seven columns">'.$email.'</div><div class="one columns"><a href="'.$conf['url'].'/index.php?a=admin&b=users&delete='.$unique.'"><img src="'.$conf['url'].'/images/icons/delete.png" /></a></div></div>'; 

	}

	if($username) {
?>

	<div id="more<?php echo $unique; ?>" class="morebox-admin">
	<a href="#" id="<?php echo $unique; ?>" class="more-admin">more</a>
	</div>

<?php
	} else {
?>
	<div class="morebox-admin">
	No more results
	</div>
<?php
	}
}
mysql_close();
?>