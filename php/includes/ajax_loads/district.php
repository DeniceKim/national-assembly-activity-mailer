<?
define("PMSYSTEM_CHECK","!#DSS@#!SAADTUUF&&%&*");
require_once ("../system/system.php");

header('P3P: CP="CAO PSA CONi OTR OUR DEM ONL"');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Pragma: no-cache");
header("Cache-Control: no-store,no-cache,must-revalidate");
header('Cache-Control: post-check=0, pre-check=0', FALSE);

$filter_arr = json_decode(str_replace('\\"','"',$PMLIST['FILTER']), true);

$city = PM_DELHTML($filter_arr['city']);
$dist = PM_DELHTML($filter_arr['dist']);
$doffi = PM_DELHTML($filter_arr['doffi']);

if(strpos($UA['name'],'Internet Explorer') !== false && $UA['version'] < 9){
	$new_arr = '{"city":"'.$city.'","dist":"'.$dist.'","doffi":"'.$doffi.'"}';
	$filter_dr = json_decode($new_arr,true);

	$city = PM_DELHTML($filter_dr['city']);
	$dist = PM_DELHTML($filter_dr['dist']);
	$doffi = PM_DELHTML($filter_dr['doffi']);
}

if($PMLIST['PROC'] == 'city'){
	$qry = sqlsrv_query($connect,"SELECT Dist FROM DistrictInfo WHERE CityCompare = '".$city."' GROUP BY Dist");?><option value="none">시/군/구</option><?while($row = sqlsrv_fetch_array($qry)){?><option value="<?=$row['Dist']?>"><?=$row['Dist']?></option><?}
} else if($PMLIST['PROC'] == 'dist'){
	$qry = sqlsrv_query($connect,"SELECT Towns, DistOfficial FROM DistrictInfo WHERE CityCompare = '".$city."' AND Dist = '".$dist."'");?><option value="none">읍/면/동</option><?while($row = sqlsrv_fetch_array($qry)){
		$towns_exp[$row['DistOfficial']] = explode(',',$row['Towns']);
	}
	foreach($towns_exp as $doffi => $towns_row){
		foreach($towns_row as $town){?><option value="<?=$doffi?>"><?=trim($town)?></option><?}
	}
} else if($PMLIST['PROC'] == 'person'){
	$qry = sqlsrv_query($connect,"select * from MP where DistrictCompare = '".$city.$doffi."'");
	$row = sqlsrv_fetch_array($qry);
	echo $row['NameKr'].'||+=+||'.$city.' '.$doffi.'||+=+||'.$row['Party'].'||+=+||'.$row['Photo'];
}?>