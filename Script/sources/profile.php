<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$getId = sprintf("SELECT idu,username,email,private,background FROM users WHERE username = '%s'", mysql_real_escape_string($_GET['u']));
	$resultId = mysql_fetch_row(mysql_query($getId));
	
	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	$qPP = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", $resultId[0], $data['id']);
	$rPP = mysql_fetch_row(mysql_query($qPP));

	if(($data == false && $resultId[3] == '1') || ($data == true && $resultId[3] == '1' && $_COOKIE['username'] !== $resultId[1])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/private'); $error = '';
		
		$error .= $skin->make();
	
	} elseif (($data == false && $resultId[3] == '2' && $rPP == '') || ($data == true && $resultId[3] == '2' && $_COOKIE['username'] !== $resultId[1] && $rPP == '')) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/private'); $error = '';
		
		$error .= $skin->make();
	}
	elseif(!empty($resultId[1])) {
	
		$TMPL['subtitle'] = 'profile';
		if($_GET['f'] == 'followers') {
			$TMPL['subtitle'] = 'followers';
			$TMPL['followedvar'] = '&f=followers';
		} elseif($_GET['f'] == 'following') {
			$TMPL['subtitle'] = 'following';	
			$TMPL['followedvar'] = '&f=following';
		}
		
		if($_GET['f'] == 'followers' || $_GET['f'] == 'following') {
			$TMPL_old = $TMPL; $TMPL = array();
			$skin = new skin('profile/follow'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			if($_GET['f'] == 'followers') {
				$val1 = 'follower';
				$val2 = 'leader';
			} elseif($_GET['f'] == 'following') {
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
			
			$queryMsg = sprintf("SELECT * FROM users WHERE idu IN (%s) ORDER BY idu DESC LIMIT %s", $followers_separated, $resultSettings[1]);
			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="58" height="58" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=58&d=mm" />';md5($result[3]);
				
				if($resultSettings[9] == '0') {
					$TMPL['time'] = date("c", strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '2') {
					$TMPL['time'] = ago(strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '3') {
					$date = strtotime($TMPL['time']);
					$TMPL['time'] = date('Y-m-d', $date);
					$TMPL['b'] = '-standard';
				}
				
				$TMPL['url'] = $confUrl;
				
				if($_GET['f'] == 'followers') {
					$val3 = ($resultId[0] == $data['id']) ? '<div class="follow-container-follow"><a href="'.$confUrl.'/index.php?a=profile&u='.$TMPL['username'].'&r=1"><div class="follow-button">follow</div></a></div>' : '';
				} elseif($_GET['f'] == 'following') {
					$val3 = ($resultId[0] == $data['id']) ? '<div class="follow-container-follow"><a href="'.$confUrl.'/index.php?a=profile&u='.$TMPL['username'].'&r=2"><div class="follow-button">unfollow</div></a></div>' : '';		
				}
				$TMPL['follow'] = $val3;
				
				$newArr[] = $TMPL['idu'];
				$rows .= $skin->make();
			}
		
			$skin = new skin('profile/profile'); $profile = '';
			
			// Get Profile Data
			$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="100" height="100" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=100&d=mm" />';md5($result[3]);
			$TMPL['username'] = $result[1];
			$TMPL['url'] = $confUrl;
			$TMPL['website'] = (!empty($result[6])) ? '<div class="profile-description"><a href="'.$result[6].'" target="_blank" rel="nofollow">'.$result[6].'</a></div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="profile-description">'.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="profile-bio">'.$result[7].'</div>' : '';
			
			if($result[9] !== '' || $result[10] !== '' || $result[11]) {
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">facebook profile</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">twitter profile</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">google+ profile</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}
			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
			
			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
			
			// Follow Unfollow buttons
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
			$resultRelation = mysql_query($queryRelation);
			
			if(mysql_num_rows($resultRelation) == 1) { // Verifica daca userul pe care vrea sa-l urmareasca nu e defapt el insusi
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="follow-button">unfollow</div></a><a href="'.$confUrl.'/index.php?a=messages&pre='.$TMPL['username'].'"><div class="sendpm-button">send pm</div></a></div>' : '';
			} elseif(mysql_num_rows($resultRelation) == 0) {
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">follow</div></a><a href="'.$confUrl.'/index.php?a=messages&pre='.$TMPL['username'].'"><div class="sendpm-button">send pm</div></a></div>' : '';
			}
			
			$profile .= $skin->make();
			$public = '_follow';
		}
		else if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
			$TMPL_old = $TMPL; $TMPL = array();
			$skin = new skin('profile/rows'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string($resultId[0]), $resultSettings[1]);

			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
				$censArray = explode(',', $resultSettings[4]);
				$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
				
				$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=50&d=mm" />';md5($result[3]);
				
				if($resultSettings[9] == '0') {
					$TMPL['time'] = date("c", strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '2') {
					$TMPL['time'] = ago(strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '3') {
					$date = strtotime($TMPL['time']);
					$TMPL['time'] = date('Y-m-d', $date);
					$TMPL['b'] = '-standard';
				}
				if(substr($TMPL['video'], 0, 3) == 'yt:') {
				$TMPL['video'] = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
				$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				} else if(substr($TMPL['video'], 0, 3) == 'vm:') {
					$TMPL['video'] = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
					$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				}
				if(!empty($TMPL['media'])) {
					$TMPL['media'] = '<a href="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" /></a>';
					$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				}
				$TMPL['reportButton'] = '<div class="report-button sud" title="Report this message" id="'.$TMPL['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
				$TMPL['delReply'] = ($TMPL['username'] == $_COOKIE['username']) 
				? 
				'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$TMPL['id'].'">Delete</a></div>'
				: 
				'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
				$TMPL['url'] = $confUrl;
				$newArr[] = $TMPL['id'];
				$rows .= $skin->make();
			}
			
			$skin = new skin('profile/profile'); $profile = '';
			
			// Get Profile Data
			$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="100" height="100" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=100&d=mm" />';md5($result[3]);
			$TMPL['username'] = $result[1];
			$TMPL['url'] = $confUrl;
			$TMPL['website'] = (!empty($result[6])) ? '<div class="profile-description"><a href="'.$result[6].'" target="_blank" rel="nofollow">'.$result[6].'</a></div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="profile-description">'.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="profile-bio">'.$result[7].'</div>' : '';
			
			
			if($result[9] !== '' || $result[10] !== '' || $result[11]) {
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">facebook profile</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">twitter profile</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">google+ profile</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}
			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
			
			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
			
			// Follow Unfollow buttons
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
			$resultRelation = mysql_query($queryRelation);
			
			if(mysql_num_rows($resultRelation) == 1) { // Verifica daca userul pe care vrea sa-l urmareasca nu e defapt el insusi
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="follow-button">unfollow</div></a><a href="'.$confUrl.'/index.php?a=messages&pre='.$TMPL['username'].'"><div class="sendpm-button">send pm</div></a></div>' : '';
				if($_GET['r'] == 2) {
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">follow</div></a></div>' : '';
					$delete = sprintf("DELETE FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
					mysql_query($delete);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=fu");
				}
			} elseif(mysql_num_rows($resultRelation) == 0) {
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">follow</div></a><a href="'.$confUrl.'/index.php?a=messages&pre='.$TMPL['username'].'"><div class="sendpm-button">send pm</div></a></div>' : '';
				if($_GET['r'] == 1) {
					if($resultId[0] !== $data['id']) { // Verifica daca userul pe care vrea sa-l urmareasca nu e defapt el insusi
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="follow-button">unfollow</div></a></div>' : '';
					$insert = sprintf("INSERT INTO relations (`id`, `leader`, `follower`) VALUES ('', '%s', '%s')", mysql_real_escape_string($resultId[0]), mysql_real_escape_string($data['id']));
					mysql_query($insert);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=ff");
					}
				}
			}
			
			$profile .= $skin->make();
			
			$skin = new skin('profile/top'); $top = '';
			
			$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=50&d=mm" />';
			$TMPL['username'] = $result[1];
			$TMPL['currentUser'] = $_GET['u'];
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			
		} else {
			$TMPL_old = $TMPL; $TMPL = array();
			$skin = new skin('profile/rows2'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string($resultId[0]), $resultSettings[1]);

			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
				$censArray = explode(',', $resultSettings[4]);
				$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
				
				$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=50&d=mm" />';md5($result[3]);
				
				if($resultSettings[9] == '0') {
					$TMPL['time'] = date("c", strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '2') {
					$TMPL['time'] = ago(strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '3') {
					$date = strtotime($TMPL['time']);
					$TMPL['time'] = date('Y-m-d', $date);
					$TMPL['b'] = '-standard';
				}
				if(substr($TMPL['video'], 0, 3) == 'yt:') {
					$TMPL['video'] = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
					$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				} else if(substr($TMPL['video'], 0, 3) == 'vm:') {
					$TMPL['video'] = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
					$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				}
				if(!empty($TMPL['media'])) {
					$TMPL['media'] = '<a href="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" /></a>';
					$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
				}
				$TMPL['url'] = $confUrl;
				$newArr[] = $TMPL['id'];
				$rows .= $skin->make();
			}
			
			$skin = new skin('profile/profile'); $profile = '';
			
			// Get Profile Data
			$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="100" height="100" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=100&d=mm" />';md5($result[3]);
			$TMPL['username'] = $result[1];
			$TMPL['url'] = $confUrl;
			$TMPL['website'] = (!empty($result[6])) ? '<div class="profile-description"><a href="'.$result[6].'" target="_blank" rel="nofollow">'.$result[6].'</a></div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="profile-description">'.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="profile-bio">'.$result[7].'</div>' : '';
			
			if($result[9] !== '' || $result[10] !== '' || $result[11]) {
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">facebook profile</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">twitter profile</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">google+ profile</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}
			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
			
			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
			
			$profile .= $skin->make();
			
			$skin = new skin('profile/top2'); $top = '';
			
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			$public = '_public';
		}
	} else {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/error'); $error = '';
		
		$error .= $skin->make();
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	$TMPL['top'] = $top;
	$TMPL['profile'] = $profile;
	$TMPL['public'] = $public; // selecteaza js-ul pt. public, fara auth
	$TMPL['error'] = $error;
	
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
	if($_GET['m'] == 'ms') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-info">
							<h5>Message deleted!</h5>
							<p>The message was successfully deleted.</p>
							<a href="#" class="notification-close notification-close-info">x</a>
							</div>';
	} elseif($_GET['m'] == 'me') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-error">
							<h5>Something went wrong!</h5>
							<p>We couldn\'t delete the message you\'ve selected.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
	} elseif($_GET['m'] == 'ff') {
		$TMPL['follow_popup'] = '<div class="popup-body-posted">You are now following '.$resultId[1].'. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
	} elseif($_GET['m'] == 'fu') {
		$TMPL['follow_popup'] = '<div class="popup-body-posted">You have unfollowed '.$resultId[1].'. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
	}
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	$TMPL['userBackground'] = (!empty($resultId[4])) ? ' style="background-image: url('.$confUrl.'/images/backgrounds/'.$resultId[4].'.png)"' : '';
	$TMPL['username'] = $resultId[1];
	$TMPL['userid'] = $resultId[0];
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $_GET['u'].' - '.$resultSettings[0];

	$skin = new skin('profile/content');
	return $skin->make();
}
?>