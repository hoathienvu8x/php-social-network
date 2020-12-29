<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$queryMsg = sprintf("SELECT * FROM users WHERE username RLIKE '%s' OR name RLIKE '%s' ORDER BY idu DESC LIMIT %s", mysql_real_escape_string(str_replace(' ', '|', $_GET['u'])), mysql_real_escape_string(str_replace(' ', '|', $_GET['u'])), $resultSettings[1]);
	$resultMsg = mysql_query($queryMsg);
	if(empty($_GET['u']) || strlen($_GET['u']) <= 2) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('search/error'); $error = '';
		$errormsg = 'sorry, your query must be at least 3 characters long.';
		$error .= $skin->make();
	} elseif(mysql_num_rows($resultMsg)) {
		$errormsg = 'results for <strong>'.$_GET['u'].'</strong>';
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('search/rows'); $rows = '';

		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		
		$newArr = array();
		
		while($TMPL = mysql_fetch_assoc($resultMsg)) {
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="58" height="58" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=58&d=mm" />';md5($result[3]);
			
			$TMPL['view'] = '<div class="follow-container-follow"><a href="'.$confUrl.'/index.php?a=profile&u='.$TMPL['username'].'"><div class="view-button">view profile</div></a></div>';
			$TMPL['delReply'] = ($TMPL['username'] == $_COOKIE['username']) 
			? 
			'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$TMPL['id'].'">Delete</a></div>'
			: 
			'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			$TMPL['url'] = $confUrl;
			$newArr[] = $TMPL['idu'];
			$rows .= $skin->make();
		}	

		$skin = new skin('search/random'); $random = '';
		
		$queryLatest = "SELECT * FROM users WHERE image <> '' ORDER BY RAND() LIMIT 12";
		$resultLatest = mysql_query($queryLatest);
		
		while($TMPL = mysql_fetch_assoc($resultLatest)) {
			
			$TMPL['url'] = $confUrl;
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=50&d=mm" />';md5($result[3]);
			
			$random .= $skin->make();
		}
	} else {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('search/error'); $error = '';
		
		$error .= $skin->make();
	}

	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	$TMPL['public'] = $public; // selecteaza js-ul pt. public, fara auth
	$TMPL['error'] = $error;
	$TMPL['random'] = $random;
	
	$hideResult = mysql_num_rows($resultMsg);
	$TMPL['hide'] = ($hideResult < $resultSettings[1]) ? 'style="display: none;"' : '';
	
	$TMPL['idn'] = @min($newArr);
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		if(isset($_GET['d'])) {
			$queryDel = sprintf("DELETE FROM messages WHERE id = '%s' AND uid = '%s'", mysql_real_escape_string($_GET['d']), mysql_real_escape_string($data['id']));
			$resultDel = mysql_query($queryDel);
			if($resultDel) {
				header("Location: ".$confUrl."/index.php?a=me&m=ms");
			} else {
				header("Location: ".$confUrl."/index.php?a=me&m=me");
			}
		}
	}
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	$TMPL['query'] = htmlentities($_GET['u'], ENT_QUOTES, "UTF-8");
	$TMPL['people'] = $errormsg;
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Search - '.$resultSettings[0];

	$skin = new skin('search/content');
	return $skin->make();
}
?>