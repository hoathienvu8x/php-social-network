<?php
require_once('./includes/config.php');
require_once('./includes/functions.php');
error_reporting(0);
header('Content-Type: text/plain; charset=utf-8;');

mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);

$confUrl = $conf['url'];

$username = $_GET['username'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(isset($username) && !empty($username)) {
	
	$username = urlencode($username);
	$query = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($username));
	$result = mysql_fetch_row(mysql_query($query));
		
	if(!empty($result[0])) {
		
		$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
								mysql_real_escape_string($result[0]));
		
		$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
								mysql_real_escape_string($result[0]));
								
		$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
		$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
		
		echo '{"apiVersion":"1.0", "data":{ "username":"'.$result[1].'", "name":"'.$result[4].'", "location":"'.$result[5].'", "website":"'.$result[6].'", "bio":"'.$result[7].'", "date":"'.$result[8].'", "image":"'.$result[12].'", "followers":"'.$resultFollowers.'", "following":"'.$resultFollowing.'" } }';
	} else {
		echo '{"apiVersion":"1.0", "data":{ "error":"The \'username\' requested is not available." } }';
	}
} else {
	echo '{"apiVersion":"1.0", "data":{ "error":"You need to specify the \'username\' parameter" } }';
}
mysql_close();
?>