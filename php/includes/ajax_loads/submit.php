<?
define("PMSYSTEM_CHECK","!#DSS@#!SAADTUUF&&%&*");
require_once ("../system/system.php");

header('P3P: CP="CAO PSA CONi OTR OUR DEM ONL"');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Pragma: no-cache");
header("Cache-Control: no-store,no-cache,must-revalidate");
header('Cache-Control: post-check=0, pre-check=0', FALSE);

$filter_arr = json_decode(str_replace('\\"','"',$PMLIST['FILTER']), true);

$name = PM_DELHTML($filter_arr['name']);
$email = PM_DELHTML($filter_arr['email']);
$city = PM_DELHTML($filter_arr['city']);
$doffi = PM_DELHTML($filter_arr['doffi']);

if(strpos($UA['name'],'Internet Explorer') !== false && $UA['version'] < 9){
	$new_arr = '{"name":"'.$name.'","email":"'.$email.'","city":"'.$city.'","dist":"'.$dist.'","doffi":"'.$doffi.'"}';
	$filter_dr = json_decode($new_arr,true);

	$name = PM_DELHTML($filter_dr['name']);
	$email = PM_DELHTML($filter_dr['email']);
	$city = PM_DELHTML($filter_dr['city']);
	$doffi = PM_DELHTML($filter_dr['doffi']);
}

$qry = sqlsrv_query($connect,"SELECT COUNT(Email) cn FROM MailList WHERE Email = '".$email."'");
$row = sqlsrv_fetch_array($qry);
if($row['cn'] > 0){
	sqlsrv_query($connect,"UPDATE MailList SET Name = '".$name."', District = '".$doffi."', CityCompare = '".$city."', DistrictCompare = '".$city.$doffi."', WhenEditted = '".date('Y-m-d')."' WHERE Email = '".$email."'");
	echo 'ok';
} else {
	sqlsrv_query($connect,"INSERT INTO MailList (Name, Email, District, CityCompare, DistrictCompare) VALUES ('".$name."', '".$email."', '".$doffi."', '".$city."', '".$city.$doffi."')");
	echo 'ok';
}?>