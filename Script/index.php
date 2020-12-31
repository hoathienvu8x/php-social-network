<?php
require_once('./includes/config.php');
require_once('./includes/skins.php');
require_once('./includes/functions.php');

mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
	
if(isset($_GET['a']) && isset($action[$_GET['a']])) {
	$page_name = $action[$_GET['a']];
} else {
	$page_name = 'welcome';
}

require_once("./sources/{$page_name}.php");

$confUrl = $conf['url'];
$confMail = $conf['mail'];

$TMPL['content'] = PageMain();

if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
	
	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	$getImg = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=80&d=mm" />';
		$TMPL['userStatus'] =  'My Account';
		$TMPL['welcomeStatus'] = '
		<a href="'.$confUrl.'/index.php?a=me&logout=1"><div class="menu_btn nord-est" title="Log out"><img src="'.$confUrl.'/images/logout.png" /></div></a>
		<a href="'.$confUrl.'/index.php?a=settings"><div class="menu_btn nord" title="Settings"><img src="'.$confUrl.'/images/settings.png" /></div></a>
		<a href="'.$confUrl.'/index.php?a=messages"><div class="menu_btn nord" title="Messages"><img src="'.$confUrl.'/images/message.png" /></div></a>
		<a href="'.$confUrl.'/index.php?a=mentions"><div class="menu_btn nord" title="Mentions"><img src="'.$confUrl.'/images/mentions.png" /></div></a>
		<a href="'.$confUrl.'/index.php?a=me"><div class="menu_btn nord" title="New message"><img src="'.$confUrl.'/images/new.png" /></div></a>
		<a href="'.$confUrl.'/index.php?a=me"><div class="menu"><div class="menu_img">'.$getImg.'</div><div class="menu_name">Hello <strong>'.$data['user'].'</strong></div></div></a>';
	} else {
		$TMPL['userStatus'] = 'Log In / Register';
		$TMPL['welcomeStatus'] = '<a href="'.$confUrl.'/index.php?a=welcome"><div class="menu_btn nord" title="Register"><img src="'.$confUrl.'/images/register.png" /></div></a>
								  <a href="#"><div class="menu_visitor">Hello <strong>Visitor</strong></div></a>';
	}
} else { 
	$TMPL['userStatus'] = 'Log In / Register';
	$TMPL['welcomeStatus'] = '<a href="'.$confUrl.'/index.php?a=welcome#log-in"><div class="menu_btn nord-est" title="Register / Login"><img src="'.$confUrl.'/images/register.png" /></div></a>
							  <a href="#"><div class="menu_visitor">Hello <strong>Visitor</strong></div></a>';
}

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
$TMPL['footer'] = $resultSettings[0];
$TMPL['url'] = $conf['url'];
$TMPL['ad1'] = $resultSettings[2];
$TMPL['ad2'] = $resultSettings[3];
$TMPL['msgLimit'] =  $resultSettings[10];

$skin = new skin('wrapper');
echo $skin->make();

mysql_close();
?>