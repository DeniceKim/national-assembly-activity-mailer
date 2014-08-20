<?
	@header("Content-type: text/html; charset=utf-8");
	@header("P3P : CP=\"ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC\"");
	if(PMSYSTEM_CHECK != "!#DSS@#!SAADTUUF&&%&*") { PM_ERROR("허가되지 않는 접근입니다.","불법적인 SYSTEM 파일접근을 허가하지 않습니다."); exit; }

	// 도메인 정보 세팅
	$urlb = $_SERVER["HTTP_HOST"]; 
	$sub_domain = split("\.",$urlb); 
	for($i=0;$i<count($sub_domain);$i++) {
		if($sub_domain[$i] != 'www' && $sub_domain[$i] != 'com' && $sub_domain[$i] != 'net' && $sub_domain[$i] != 'kr' && !($sub_domain[$i] == 'co' && $sub_domain[$i+1] == 'kr')) {
			$aurl .= "."; 
			$aurl .= $sub_domain[$i];
		}
	}
	$raw_url = explode(".",$aurl);
	for($i=1;$i<count($raw_url);$i++){
		$location_url .= $raw_url[$i];
		$location_url .= ".";
	}
	$raw_url = $raw_url[count($raw_url)-1];
	if($sub_domain[count($sub_domain)-1] == 'com'){
		$end_url = 'com';
	} else if($sub_domain[count($sub_domain)-1] == 'net'){
		$end_url = 'net';
		echo "<script>location.href='http://".$location_url."com'</script>";
		exit;
	} else if($sub_domain[count($sub_domain)-1] == 'kr' && $sub_domain[count($sub_domain)-2] != 'co'){
		$end_url = 'kr';
		echo "<script>location.href='http://".$location_url."com'</script>";
		exit;
	} else if($sub_domain[count($sub_domain)-2] == 'co' && $sub_domain[count($sub_domain)-1] == 'kr'){
		$end_url = 'co.kr';
		echo "<script>location.href='http://".$location_url."com'</script>";
		exit;
	}

	// 서버설정 셋팅
	$PMSYSTEM['PAGE_URL'] = "http://cfy.".$raw_url.".".$end_url;
	$PMSYSTEM['Path'] = "D:/52. CodeForYeouido";
	
	$PMSYSTEM['Session_Path'] = "sess";
	$PMSYSTEM['MAIN'] = "/index.php";
	$PMSYSTEM['URL_PATH'] = "/";
	$PMSYSTEM["HTTP_REFERER"] = str_replace("www.","",strtolower($_SERVER["HTTP_REFERER"]));

	// 도메인별 타이틀
	if($raw_url == '4scour'){
		$PMSYSTEM['TITLE_HEADER'] = '통합자료검색 4Scour';
	}elseif($raw_url == '5chaja'){
		$PMSYSTEM['TITLE_HEADER'] = '별의 별 자료 다~~~찾아드리오!! 다찾아닷컴!';
	}

	// 기본유저 정보 수집
	$USERX['BROWSER_IS'] = eregi("MSIE",$_SERVER[HTTP_USER_AGENT]);
	$USERX['IP'] = ip2long($_SERVER['REMOTE_ADDR']);
	$USERX['PHP_SELF'] = $_SERVER['PHP_SELF'];

	// 커스텀함수로드
	require_once ("function.php");

	// DB연결시도
	@sqlsrv_close($connect);
	$connect = PM_DBCONNECT();

	// 키워드 리스트 관련 항목 세팅
	if(!$_REQUEST['page']) { $PMLIST['PAGE'] = 1; } else { $PMLIST['PAGE'] = $_REQUEST['page']; }
	$PMLIST['FILTER'] = PM_DELHTML($_REQUEST['filter']);
	$PMLIST['PROC'] = PM_DELHTML($_REQUEST['proc']);
	$PMLIST['SKWD'] = PM_DELHTML($_REQUEST['search_kwd']);
	$PMLIST['LOAD'] = PM_DELHTML($_REQUEST['load']);
	$PMLIST['INC'] = PM_DELHTML($_REQUEST['inc']);

	// 세션설정 (세션은 3일동안 유효하게 설정)
	if(!is_dir($PMSYSTEM['Path']."/".$PMSYSTEM['Session_Path'])) {
		@mkdir($PMSYSTEM['Path']."/".$PMSYSTEM['Session_Path'], 0777);
		@chmod($PMSYSTEM['Path']."/".$PMSYSTEM['Session_Path'], 0777);
	}

	// 서브도메인 로그인 인식
	ini_set("session.cookie_domain", ".".$raw_url.".".$end_url);

	// 로그인확인 & 접속 갱신
	$session_name = 'PMLIST_SESS';
	@session_name( $session_name );
	@session_save_path( $PMSYSTEM['Path']."/".$PMSYSTEM['Session_Path'] );

	session_set_cookie_params(0, "/"); 
	session_cache_limiter('no-cache, must-revalidate'); 

	if( version_compare( PHP_VERSION, '5.1.2', 'lt' ) && isset( $_COOKIE[$session_name] ) && eregi( "\r|\n", $_COOKIE[$session_name] ) ) {
		die('DB CONNECT ERROR');
	}
	@session_start();
	ini_set("session.cookie_domain", ".4scour.com");
	$PMSYSTEM['SESS'] = session_id();

	// 회원정보가져오기
	/*if($PMLIST['SETUP'] != "logout") {
		if(isset($_SESSION['mem_idx'])) {
			$MEM = PM_MEMBER();
		} else {
			$MEM['level'] = 0;
			//if($PMLIST['INC']!='login') header("Location: /login");
		}
	} elseif($PMLIST['INC']!='login') {
		//echo "<script>location.href = '/login';</script>";
	}*/

	$UA = PM_GETBROWSER();
?>