<?
/* 1. DB연결함수 */
function PM_DBCONNECT() {
	global $connect;
	@sqlsrv_close($connect);
	$connectionInfo = array("Database"=>"Code4Yeouido","UID"=>"code4user","PWD"=>"k10208!@","CharacterSet"=>'UTF-8');
	$connect = @sqlsrv_connect("tkr56ug2sf.database.windows.net",$connectionInfo) or PM_ERROR("DB 접속시 에러가 발생했습니다. 접속자가 폭주중이거나 잠시 점검중일수 있습니다.", "관리자에게 문의하시기 바랍니다.");
	//@sqlsrv_query("SET CHARACTER SET utf8");
	unset( $GLOBALS['conf']['db_db'], $GLOBALS['conf']['db_host'], $GLOBALS['conf']['db_id'], $GLOBALS['conf']['db_pw'] );
	return $connect;
}

/* 2. 문자형 커스텀 함수 */
function PM_DELHTML($str) {
	$str = trim($str);
	if($str == "undefined") { $str = ""; }
	$search = array ('@<script[^>]*?>.*?</script>@si', '@<[/!]*?[^<>]*?>@si', '@&(quot|#34);@i', '@&(amp|#38);@i', '@&(lt|#60);@i', '@&(gt|#62);@i',
	'@&(nbsp|#160);@i','@&(iexcl|#161);@i','@&(cent|#162);@i','@&(pound|#163);@i','@&(copy|#169);@i','@&#(d+);@e');
	$replace = array ('','','"','&','<','>',' ',chr(161),chr(162),chr(163),chr(169),'chr(1)');
	return preg_replace($search, $replace, $str);
}

/* 3. 에러처리 함수 */
function PM_ERROR($str, $str2) { 
	?><script> alert("<?=$str?>\n<?=$str2?>"); </script><? exit;
}

/* 4. UTF-8 문자열을 컷트하는 함수 */
function PM_UTF8CUT($str, $len, $checkmb=false, $tail='...') {
	$len += 3;
	preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);
	$m = $match[0];
	$slen = strlen($str); // length of source string
	$tlen = strlen($tail); // length of tail string
	$mlen = count($m); // length of matched characters
	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;
	$ret = array();
	$count = 0;
	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		if ($count + $tlen > $len) break;
		$ret[] = $m[$i];
	}
	return join('', $ret).$tail;
}

/* 5. Strong 함수 */
function PM_STRONG($text, $value1, $value2, $option = 0) {
	if($value1 == $value2 || ($option == 1 && $value1 > $value2)) { ?><strong><? }
		?><?=$text?><?
	if($value1 == $value2 || ($option == 1 && $value1 > $value2)) { ?></strong><? }
}

/* 6. 회원정보 불러오기 */
function PM_MEMBER() {
	global $_SESSION, $connect;
	if($_SESSION[mem_idx]) {
		$MEM = sqlsrv_fetch_array(sqlsrv_query("select * from `admin_users` where `idx`='".$_SESSION[mem_idx]."'"));
		if(!$MEM['idx']) {
			unset($MEM);
			$MEM[level] = 0;
		}
	} else $MEM[level] = 0;
	return $MEM;
}

function PM_MAILSEND($to, $name, $from, $subject, $message) {
	$admin_email = $from;
	$admin_name  = iconv("utf-8","euc-kr",$name);

	$mailto = $to;
	$CONTENT = iconv("utf-8","euc-kr",$message);
	$SUBJECT = iconv("utf-8","euc-kr",$subject);

	$header  = "Return-Path: ".$admin_email."\n";
	$header .= "From: =?EUC-KR?B?".base64_encode($admin_name)."?= <".$admin_email.">\n";
	$header .= "MIME-Version: 1.0\n";
	$header .= "X-Priority: 3\n";
	$header .= "X-MSMail-Priority: Normal\n";
	$header .= "X-Mailer: FormMailer\n";
	$header .= "Content-Transfer-Encoding: base64\n";
	$header .= "Content-Type: text/html;\n \tcharset=euc-kr\n";
	//$header.="cc:birthdayarchive@php.net\n";  //CC to
	//$header.="bcc:kim3001@hanmail.net,rocio79@naver.com\n"; //BCCs to

	$subject  = "=?EUC-KR?B?".base64_encode($SUBJECT)."?=\n";
	$contents = $CONTENT;

	$message = base64_encode($contents);
	//flush();
	mail($mailto, $subject, $message, $header);
}

function PM_GETBROWSER() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
 
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) { $platform = 'linux'; }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) { $platform = 'mac'; }
    elseif (preg_match('/windows|win32/i', $u_agent)) { $platform = 'windows'; }
     
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { $bname = 'Internet Explorer'; $ub = "MSIE"; } 
    elseif(preg_match('/Firefox/i',$u_agent)) { $bname = 'Mozilla Firefox'; $ub = "Firefox"; } 
    elseif(preg_match('/Chrome/i',$u_agent)) { $bname = 'Google Chrome'; $ub = "Chrome"; } 
    elseif(preg_match('/Safari/i',$u_agent)) { $bname = 'Apple Safari'; $ub = "Safari"; } 
    elseif(preg_match('/Opera/i',$u_agent)) { $bname = 'Opera'; $ub = "Opera"; } 
    elseif(preg_match('/Netscape/i',$u_agent)) { $bname = 'Netscape'; $ub = "Netscape"; } 
     
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
     
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){ $version= $matches['version'][0]; }
        else { $version= $matches['version'][1]; }
    }
    else { $version= $matches['version'][0]; }
     
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    return array('userAgent'=>$u_agent, 'name'=>$bname, 'version'=>$version, 'platform'=>$platform, 'pattern'=>$pattern);
}
?>