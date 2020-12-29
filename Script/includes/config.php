<?php
error_reporting(0);
#error_reporting(E_ALL ^ E_NOTICE);

$conf = $TMPL = array();
$conf['host'] = 'localhost';
$conf['user'] = 'YOURDBUSER';
$conf['pass'] = 'YOURDBPASS';
$conf['name'] = 'YOURDBNAME';
$conf['url'] = 'http://yourdomain.com'; #<-- Enter the Installation URL (e.g: http://pricop.info/newfolder);
$conf['mail'] = 'example@example.com'; #<-- Enter your Admin Email.

$action = array('admin'			=> 'admin',
				'message'		=> 'message',
				'me'			=> 'me',
				'settings'		=> 'settings',
				'mentions'		=> 'mentions',
				'messages'		=> 'messages',
				'profile'		=> 'profile',
				'discover'		=> 'discover',
				'search'		=> 'search',
				'recover'		=> 'recover',
				
				// Start the ToS pages
				'privacy'       => 'page',
				'disclaimer'	=> 'page',
				'contact'       => 'page',
				'tos'			=> 'page',
				'api'			=> 'page',
				);
				
/* if(get_magic_quotes_gpc()) {
	function strips($v) {return is_array($v)?array_map('strips',$v):stripslashes($v);}
	$_GET = strips($_GET);
} */
?>