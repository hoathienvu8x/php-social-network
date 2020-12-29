<?php
function getSettings($querySettings) {
	$querySettings = "SELECT * from settings";
	return $querySettings;
}

function sendMail($to, $title, $url, $from, $username, $password) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'Welcome to '.$title;
	$message = 'Thank you for joining <strong>'.$title.'</strong><br /><br />Your username: <strong>'.$username.'</strong><br />Your Password: <strong>'.$password.'</strong><br /><br />You can log-in at: <a href="'.$url.'" target="_blank">'.$title.'</a>';
	return @mail($to, $subject, $message, $headers);
}

function sendRecover($to, $title, $url, $from, $username, $salt) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'Password Recovery - '.$title;
	$message = 'A password recover was requested, if you didn\'t make this action please ignore this email. <br /><br />Your Username: <strong>'.$username.'</strong><br />Your Reset Key: <strong>'.$salt.'</strong><br /><br />You can reset your password by accessing the following link: <a href="'.$url.'/index.php?a=recover&r=1" target="_blank">'.$url.'/index.php?a=recover&r=1</a>';
	return @mail($to, $subject, $message, $headers);
}

function sendPM($to, $title, $url, $from, $username) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'New Private Message - '.$title;
	$message = 'You have a new private message from '.$username.', view it: <a href="'.$url.'/index.php?a=messages" target="_blank">'.$url.'/index.php?a=messages</a>';
	return @mail($to, $subject, $message, $headers);
}

function loginCheck($username, $password) {
	$query = sprintf('SELECT * from users where username = "%s" and password ="%s"', mysql_real_escape_string(strtolower($username)), mysql_real_escape_string($password));
	if(mysql_fetch_row(mysql_query($query))) {
		$result = mysql_fetch_row(mysql_query($query));
		$out['true'] = true;
		$out['id'] = $result[0];
		$out['user'] = $result[1];
		$out['mail'] = $result[3];
		$out['image'] = $result[12];
		$out['background'] = $result[15];
		return $out;
	} else {
		return false;
	}
}

function ago($i){
    $m = time()-$i; $o='just now';
    $t = array('year'=>31556926,'month'=>2629744,'week'=>604800,
'day'=>86400,'hour'=>3600,'minute'=>60,'second'=>1);
    foreach($t as $u=>$s){
        if($s<=$m){$v=floor($m/$s); $o="$v $u".($v==1?'':'s').' ago'; break;}
    }
    return $o;
}

function fsize($bytes) { #Determine the size of the file, and print a human readable value
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KiB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MiB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GiB';
   else return round($bytes / 1099511627776, 2).' TiB';
}
?>