<?php
// header("Content-Type: application/json charset=shift_jis");
// header('Content-type: text/html; charset=shift_jis');
require_once "vendor/autoload.php";

use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;

/** 
 * METHOD1: get API return data content-type: application/html then convert string content-type: application/html to application/json
 *
 * @author Khanh
 * @return array when convert string json
 */
function getAPIDataAWS($api, $json, $method = "GET")
{
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => json_encode(array(
			'api'  => $api,
			'json' => $json,
			'method' => $method,
		)),
		CURLOPT_CONNECTTIMEOUT => 0,
		CURLOPT_TIMEOUT => 4000,
		CURLOPT_HTTPHEADER => array(
			"Accept: */*",
			"Cache-Control: no-cache",
			"Content-Type: application/json charset=shift_jis",
			"X-HTTP-Method-Override: POST"
	)));

	$response = curl_exec($curl);
	$response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
	$response = json_decode($response, true);
	curl_close($curl);
	return $response;
}

$json = '{
    "req_his":{
        "data":[
            {"name":"req_his_cd","value":"300068876771","operator":"="},
        ],
        "fields":"user_cd,users,req_his_cd,print_ymd,req_syu_kb,req_syu_nm,req_his_sumi_fg,req_his_sumi_nm,seikyu",
        "sort":"print_ymd desc",
    },
    "req_his_d":{
        "data":[
            {"name":"un_syu_kb","value":"1,2,3","operator":"in"}
        ],
        "fields":"req_his_dno,un_syu_kb,un_syu_nm,hs_ymd,d_no,dm_no,ushin_cd,shin_nm,uri_tan,uri_su,hon_kin,zei_kin,zei_ritu"
    },
}';
$api = "req_his";
$method = "GET";
$res = getAPIDataAWS($api, $json, $method);
echo '<pre>';
print_r($res);