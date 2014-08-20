<?
define(PMSYSTEM_CHECK,"!#DSS@#!SAADTUUF&&%&*");

header('P3P: CP="CAO PSA CONi OTR OUR DEM ONL"');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Pragma: no-cache");
header("Cache-Control: no-store,no-cache,must-revalidate");
header('Cache-Control: post-check=0, pre-check=0', FALSE);

/* 시스템 함수 호출 */
include('/includes/system/system.php');

/* 헤더 */
include('/includes/header.php');

/* 본문 */
if($_REQUEST['search_submit']){
	include('/includes/contents/list.php');
} elseif($PMLIST['INC']){
	include('/includes/contents/'.$PMLIST['INC'].'.php');
} else {
	include('/includes/contents/main.php');
}

/* 푸터 */
include('/includes/footer.php');
?>