<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

$getId = sprintf("SELECT * FROM users WHERE idu = '%s'", mysql_real_escape_string($_POST['u']));
$resultId = mysql_fetch_row(mysql_query($getId));

if(isset($_POST['loadmore'])) {

	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	
	if($_POST['f'] == 'followers') {
		$val1 = 'follower';
		$val2 = 'leader';
	} elseif($_POST['f'] == 'following') {
		$val1 = 'leader';
		$val2 = 'follower';
	}
	$queryFollowers = sprintf("SELECT %s FROM relations WHERE %s = '%s'", $val1, $val2,
							mysql_real_escape_string($resultId[0]));
	$array = array();
	
	$resFol = mysql_query($queryFollowers);
	while($row = mysql_fetch_assoc($resFol)) {
		$array[] = $row["$val1"];
	}
	$followers_separated = implode(",", $array);
	
	$loadmore=$_POST['loadmore'];
	$queryMsg = sprintf("SELECT * FROM users WHERE idu IN (%s) AND idu < '%s' ORDER BY idu DESC LIMIT %s", $followers_separated, mysql_real_escape_string($loadmore), $resultSettings[1]);

	$newArr = array();
	
	$result = mysql_query($queryMsg);
	while($row = mysql_fetch_array($result)) {
		$email = md5($row['email']);
		$author = $row['username']; 
		$location = $row['location'];
		$bio = $row['bio'];
		
		if($_POST['f'] == 'followers') {
			$val3 = ($resultId[0] == $data['id']) ? '<div class="follow-container-follow"><a href="'.$confUrl.'/index.php?a=profile&u='.$author.'&r=1"><div class="follow-button">follow</div></a></div>' : '';
		} elseif($_POST['f'] == 'following') {
			$val3 = ($resultId[0] == $data['id']) ? '<div class="follow-container-follow"><a href="'.$confUrl.'/index.php?a=profile&u='.$author.'&r=2"><div class="follow-button">unfollow</div></a></div>' : '';
		}
		
		$follow = $val3;
		$getImg = (!empty($row['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" width="58" height="58" />' : '<img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=58&d=mm" />';
		echo '
		<div class="message">
			<div class="message-picture">
				<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$getImg.'</a>
			</div>
			<div class="message-top-container">
				<div class="message-top">
					<div class="message-author">
						<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$author.'</a>
					</div>
					<div class="timeago'.$b.'" title="'.$time.'">
						'.$location.'
					</div>
				</div>
			</div>
			<div class="message-container">
				<div class="message-message">
					'.$bio.$follow.'
				</div>
			</div>
		</div>
		';
		$newArr[] = $row['idu'];
	}
	
	while($min = mysql_fetch_assoc($result)) {
		$newArr[] = $min['idu'];
	}

	if(array_key_exists($resultSettings[1] - 1, $newArr)) {


	echo '<div id="more'.min($newArr).'" class="morebox">
			<div id="'.min($newArr).'" class="more"><div class="more-button">More results</div></div>
		  </div>';


	} else {

	echo '<div class="morebox">
			<div class="more"><div class="more-button">No more results</div></div>
		  </div>';
	}
}
mysql_close();
?>