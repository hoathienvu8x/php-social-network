<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	global $confMail;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$time = time()+86400;
	$exp_time = time()-86400;
	
	$TMPL_old = $TMPL; $TMPL = array();
	$skin = new skin('welcome/form'); $form = '';
	$TMPL['url'] = $confUrl;
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		header("Location: ".$confUrl."/index.php?a=me");
	}
	require_once('./includes/recaptchalib.php');
	
	if($resultSettings[5] == 1) {
		$TMPL['captcha'] = recaptcha_get_html($resultSettings[6]);
	}
	if(isset($_POST['register'])) {
		if(!empty($_POST['regName']) && !empty($_POST['regPass']) && !empty($_POST['regEmail'])) {
			if(strlen($_POST['regName']) >= 3 && strlen($_POST['regName']) <= 32) {
				if(ctype_alnum($_POST['regName'])) {
					
					if (filter_var($_POST['regEmail'], FILTER_VALIDATE_EMAIL)) {
						$querySearch = sprintf("SELECT username from users where username = '%s'",
						mysql_real_escape_string(strtolower($_POST['regName'])));
						$resultSearch = mysql_fetch_row(mysql_query($querySearch));
						if(strtolower($_POST['regName']) == $resultSearch[0] || strtolower($_POST['regName']) == 'anonymous') {
							$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>This username already exist, please choose another one.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
						} else {
							if($resultSettings[5] == 1) {
								if ($_POST["recaptcha_response_field"]) {
								$resp = recaptcha_check_answer ($resultSettings[7],
															$_SERVER["REMOTE_ADDR"],
															$_POST["recaptcha_challenge_field"],
															$_POST["recaptcha_response_field"]);
															
									if ($resp->is_valid) {
											$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`) VALUES ('%s', '%s', '%s');",
															mysql_real_escape_string(strtolower($_POST['regName'])),
															md5(mysql_real_escape_string($_POST['regPass'])),
															mysql_real_escape_string($_POST['regEmail']));
															mysql_query($createQuery);
											
											$username = $_POST['regName'];
											$password = md5($_POST['regPass']);
											
											setcookie("username", str_replace(' ', '', strtolower($username)), $time);
											setcookie("password", $password, $time);
											if($resultSettings[13] == '1') {
												@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
											}
											
											header("Location: ".$confUrl."/index.php?a=me");
									}
								}
							} else {
										$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`, `date`) VALUES ('%s', '%s', '%s', '%s');",
														mysql_real_escape_string(strtolower($_POST['regName'])),
														md5(mysql_real_escape_string($_POST['regPass'])),
														mysql_real_escape_string($_POST['regEmail']),
														date("Y-m-d H:i:s"));
														mysql_query($createQuery);
										
										$username = $_POST['regName'];
										$password = md5($_POST['regPass']);
										
										setcookie("username", str_replace(' ', '', strtolower($username)), $time);
										setcookie("password", $password, $time);
										
										if($resultSettings[13] == '1') {
											@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
										}
										
										header("Location: ".$confUrl."/index.php?a=me");
							}
						}
					} else {
						$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>Invalid email format.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
					}
				} else {
					$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>The username must contain only letters and numbers.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
				}
			} else {
				$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>The username must be between 3 and 32 characters.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
			}
		} else {
			$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>All fields must be completed.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
		}
	}
	if(isset($_POST['login'])) {
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		
		setcookie("username", str_replace(' ', '', strtolower($username)), $time);
		setcookie("password", $password, $time);

		if(loginCheck($_POST['username'], md5($_POST['password']))) {
			header("Location: ".$confUrl."/index.php?a=me");
		} else {
			$TMPL['message'] = '<div class="divider"></div>
								<div class="notification-box notification-box-error">
								<h5>Error!</h5>
								<p>Invalid log-in credentials.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
		}
	}
	$form .= $skin->make();
	
	$skin = new skin('welcome/latest'); $latest = '';
		
	$queryLatest = "SELECT * FROM users WHERE image <> '' ORDER BY idu DESC LIMIT 14";
	$resultLatest = mysql_query($queryLatest);
	
	while($TMPL = mysql_fetch_assoc($resultLatest)) {
		
		$TMPL['url'] = $confUrl;
		$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="64" height="64" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=64&d=mm" />';md5($result[3]);
		
		$latest .= $skin->make();
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['form'] = $form;
	$TMPL['latest'] = $latest;
	
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $resultSettings[0];
	
	$TMPL['ad1'] = $resultSettings[2];
	$TMPL['ad2'] = $resultSettings[3];
	
	$skin = new skin('welcome/content');
	return $skin->make();
}
?>