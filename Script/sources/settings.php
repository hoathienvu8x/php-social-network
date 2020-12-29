<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	$time = time()+86400;
	$exp_time = time()-86400;

	if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) { 
		$query = sprintf('SELECT * from users where username = "%s" and password ="%s"', mysql_real_escape_string($_COOKIE['username']), mysql_real_escape_string($_COOKIE['password']));
		if(mysql_fetch_row(mysql_query($query))) {
			
			$TMPL_old = $TMPL; $TMPL = array();
			$TMPL['url'] = $confUrl;
			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			if($_GET['b'] == 'security') {
				$skin = new skin('settings/security'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=80&d=mm" />';		
				$TMPL['username'] = $data['user'];
				
				if(isset($_POST['pwd']) && !empty($_POST['pwd'])) {
					$pwd = md5($_POST['pwd']);
					$query = 'UPDATE `users` SET password = \''.$pwd.'\' WHERE username = \''.$_COOKIE['username'].'\'';
					mysql_query($query);
					setcookie('password', md5($_POST['pwd']), $time);
					header("Location: ".$confUrl."/index.php?a=settings&b=security&m=s");
				}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>Password successfully changed, you can use your new password now.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				
				$settings .= $skin->make();
			} elseif($_GET['b'] == 'avatar') {
				$skin = new skin('settings/avatar'); $settings = '';
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=80&d=mm" />';
				$TMPL['username'] = $data['user'];
				$maxsize = $resultSettings[11];
				
				if(isset($_FILES['fileselect']['name'])) {
					foreach ($_FILES['fileselect']['error'] as $key => $error) {
					$ext = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_EXTENSION);
					$size = $_FILES['fileselect']['size'][$key];
					$extArray = explode(',', $resultSettings[12]);
					
						if (in_array($ext, $extArray) && $size < $maxsize && $size > 0) {
							$rand = mt_rand();
							$tmp_name = $_FILES['fileselect']['tmp_name'][$key];
							$name = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_FILENAME);
							$fullname = $_FILES['fileselect']['name'][$key];
							$size = $_FILES['fileselect']['size'][$key];
							$type = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_EXTENSION);
							move_uploaded_file($tmp_name, 'uploads/avatars/'.$rand.'.'.mysql_real_escape_string($name).'.'.mysql_real_escape_string($type));
							
							$query = sprintf("UPDATE users SET image = '%s' WHERE idu = '%s'",
								$rand.'.'.mysql_real_escape_string($name).'.'.mysql_real_escape_string($type),
								mysql_real_escape_string($data['id']));
								mysql_query($query);
							
							$queryLastRow = "SELECT * FROM `files` ORDER by `id` DESC LIMIT 1";
							$execLastRow = mysql_fetch_row(mysql_query($queryLastRow));
							
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=s");
						} elseif($_FILES['fileselect']['name'][$key] == '') { 
							//Daca nu este selectata nici o fila.
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=nf");
						} elseif($size > $maxsize || $size == 0) { 
							//Daca fila are dimensiunea mai mare decat dimensiunea admisa, sau egala cu 0.
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=fs");
						} else { 
							//Daca formatul filei nu este un format admis.
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=wf");
						}
					}
				}
				if(isset($_POST['deleteimg'])) {
					$query = sprintf("UPDATE users SET image = '' WHERE idu = '%s'",
							mysql_real_escape_string($data['id']));
							mysql_query($query);
							unlink('uploads/avatars/'.$data['image']);
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=de");
				}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Image Saved!</h5>
										<p>Your profile picture have been changed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'nf') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>You did not selected any files to be uploaded, or the selected file(s) are empty.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'fs') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p><strong>The selected file</strong> size must not exceed <strong>'.round($maxsize / 1048576, 2).'</strong> MB.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'wf') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The selected file format is not supported. Upload <strong>'.$resultSettings[12].'</strong> file format.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'de') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Image Removed!</h5>
										<p>Your profile picture has been removed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				$settings .= $skin->make();
			} else {
				$skin = new skin('settings/general'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				$TMPL['currentName'] = $request[4]; $TMPL['currentEmail'] = $request[3]; $TMPL['currentLocation'] = $request[5]; $TMPL['currentWebsite'] = $request[6]; $TMPL['currentBio'] = $request[7]; $TMPL['currentFacebook'] = $request[9]; $TMPL['currentTwitter'] = $request[10];  $TMPL['currentGplus'] = $request[11];
				if($request[13] == '1') {
					$TMPL['on'] = 'selected="selected"';
				} elseif($request[13] == '2') {
					$TMPL['semi'] = 'selected="selected"';
				} else {
					$TMPL['off'] = 'selected="selected"';
				}
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=80&d=mm" />';
				$TMPL['username'] = $data['user'];
				
				if (isset($_POST['general'])) {
					if(filter_var($_POST['website'], FILTER_VALIDATE_URL) || empty($_POST['website'])) {
						if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || empty($_POST['email'])) {
							if(strlen($_POST['bio']) <= 160) {
								// Updating the Values
								if(isset($_POST['name']) || isset($_POST['email']) || isset($_POST['location']) || isset($_POST['website']) || isset($_POST['bio']) || isset($_POST['dropdown'])) {
									//$query = 'UPDATE `users` SET name = \''.$_POST['name'].'\', email = \''.$_POST['email'].'\', location = \''.$_POST['location'].'\', facebook = \''.$_POST['facebook'].'\', twitter = \''.$_POST['twitter'].'\', gplus = \''.$_POST['gplus'].'\', website = \''.$_POST['website'].'\', bio = \''.strip_tags($_POST['bio']).'\' WHERE username = \''.$_COOKIE['username'].'\'';
									
									$query = sprintf("UPDATE `users` SET name = '%s', email = '%s', location = '%s', facebook = '%s', twitter = '%s', gplus = '%s', website = '%s', bio = '%s', private = '%s' WHERE username = '%s'",
													mysql_real_escape_string($_POST['name']),
													mysql_real_escape_string($_POST['email']),
													mysql_real_escape_string(strip_tags($_POST['location'])),
													mysql_real_escape_string(strip_tags($_POST['facebook'])),
													mysql_real_escape_string(strip_tags($_POST['twitter'])),
													mysql_real_escape_string(strip_tags($_POST['gplus'])),
													mysql_real_escape_string(strip_tags($_POST['website'])),
													mysql_real_escape_string(strip_tags($_POST['bio'])),
													mysql_real_escape_string($_POST['dropdown']),
													mysql_real_escape_string($_COOKIE['username']));
									mysql_query($query);
									header("Location: ".$confUrl."/index.php?a=settings&m=s");
								}
							} else {
								header("Location: ".$confUrl."/index.php?a=settings&m=b");
							}
						} else {
							header("Location: ".$confUrl."/index.php?a=settings&m=e");
						}
					} else {
						header("Location: ".$confUrl."/index.php?a=settings&m=w");
					}
				}
				$backgrounds = array('0', '1', '2', '3', '4');
				if(isset($_GET['bg'])) {
					if(in_array($_GET['bg'], $backgrounds)) {
						$queryBg = sprintf("UPDATE `users` SET background = '%s' WHERE username = '%s'", mysql_real_escape_string($_GET['bg']), mysql_real_escape_string($_COOKIE['username']));
						mysql_query($queryBg);
						header("Location: ".$confUrl."/index.php?a=settings&m=bs");
					} else {
						header("Location: ".$confUrl."/index.php?a=settings&m=be");
					}
				}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>Settings successfully saved.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'b') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The Bio description should be 160 characters or less.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'e') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>Please enter a valid email.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'w') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>Please enter a valid URL format.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'bs') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>The background has been successfully changed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'be') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The background could not be changed.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				}
				
				$settings .= $skin->make();
			}
		
			$TMPL = $TMPL_old; unset($TMPL_old);
			$TMPL['settings'] = $settings;
			
			if(isset($_GET['logout']) == 1) {
				setcookie('username', '', $exp_time);
				setcookie('password', '', $exp_time);
				header("Location: ".$confUrl."/index.php?a=welcome");
				}
			}
		}			
	
	$TMPL['userBackground'] = (!empty($data['background'])) ? ' style="background-image: url('.$confUrl.'/images/backgrounds/'.$data['background'].'.png)"' : '';
	$TMPL['title'] = 'Settings - '.$resultSettings[0];

	$skin = new skin('settings/content');
	return $skin->make();
}
?>