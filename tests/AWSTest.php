<?php
require_once __DIR__ . "/../vendor/autoload.php";

// declare(strict_types=1);
$_SERVER['SERVER_ADDR'] = "::1";

use \GuzzleHttp\TFP;
use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;

define("EXPIRED_LIMIT", "1 hour");
$SSS = [ "6" => "SAAG[6]", "7" => "SOSP[7]", "8" => "SOUP[8]" ];

// define("ERR_PTNLOGIN_01_01_01", "ERR_PTNLOGIN_01_01_01");
// define("ERR_PTNLOGIN_01_01_02", "ERR_PTNLOGIN_01_01_02");
// define("ERR_PTNLOGIN_01_01_03", "ERR_PTNLOGIN_01_01_03");
// define("ERR_PTNLOGIN_01_01_11", "ERR_PTNLOGIN_01_01_11");
// define("ERR_PTNLOGIN_01_01_12", "ERR_PTNLOGIN_01_01_12");
// define("ERR_PTNLOGIN_01_01_13", "ERR_PTNLOGIN_01_01_13");
// //STEP2
// define("ERR_PTNLOGIN_02_01_01", "ERR_PTNLOGIN_02_01_01");
// define("ERR_PTNLOGIN_02_01_02", "ERR_PTNLOGIN_02_01_02");
// define("ERR_PTNLOGIN_02_02_01", "ERR_PTNLOGIN_02_02_01");
// define("ERR_PTNLOGIN_02_02_02", "ERR_PTNLOGIN_02_02_02");
// define("ERR_PTNLOGIN_02_02_03", "ERR_PTNLOGIN_02_02_03");
// define("ERR_PTNLOGIN_03_01_01", "ERR_PTNLOGIN_03_01_01");
// define("ERR_PTNLOGIN_03_01_11", "ERR_PTNLOGIN_03_01_11");

// define("ERR_PTNLOGIN_04_01_01", "ERR_PTNLOGIN_04_01_01");
// require_once __DIR__ . "/../../../common_files/smtp_mail.php";
require_once __DIR__ . "/../../../member.sorimachi.co.jp/lib/common.php";

class AWSTest extends \PHPUnit\Framework\TestCase
{
	public function testAPIAWS()
	{
		$api  = 'users';
		$json = '{
					"users": {
						"data": [
							{
								"name": "user_cd",
								"value": "' . '100000002101' . '",
								"operator": "="
							}
						],
						"fields":"tel1, tel2, tel3, user_cd"

					}
				}';

		$res  = TFP::getAPIDataAWS($api, $json, "GET");
		// $res1 = TFP::GetAPIData($api, $json, "GET");
		$this->assertEquals(true, true);

	}

	function testCheckUsersValue_sn_tl()
	{
		// $user_cd = "100000002101";
		// $tel     = "0258330000";

		$user_cd = "200002756101";
		$tel     = "0899942326";
		if ( strlen($user_cd) == 0 || strlen($tel) == 0 ) {
			return false;
		}
		$json  = '{
					"users": {
						"data": [
							{
								"name": "user_cd",
								"value": "' . $user_cd . '",
								"operator": "="
							}
						],
						"fields":"tel1, tel2, tel3, user_cd"

					}
				}';
		// $res   = TFP::GetAPIData("users", $json, "GET");
		$res   = TFP::GetAPIDataAWS("users", $json, "GET");
		$count = (int) TFP::GetFirstByField($res, "count");

		if ( $count < 0 ) {
			goto Err;
		}

		$user = $res["users"][0];
		// check $tel in user info
		$listTel                = [ $user['tel1'], $user['tel2'], $user['tel3'] ];
		$listTel_trimmed_hyphen = array_map(function ($tel) {
			return str_replace('-', '', $tel);
		}, $listTel);

		$this->assertEquals(in_array($tel, $listTel_trimmed_hyphen), true);
		// return in_array($tel, $listTel_trimmed_hyphen);
		Err:
		die("error");
	}

	function testCheckUsersValue_sn_tl2()
	{
		$user_cd = "100000002101";
		$tel     = "0258330000";
		if ( strlen($user_cd) == 0 || strlen($tel) == 0 ) {
			return false;
		}
		$json  = '{
					"users": {
						"query": "user_cd=\'100000002101\' AND tel1 = \'0258-33-0000\' OR tel2 = \'0258-33-0000\' OR tel3 = \'0258-33-0000\'",
						"fields":"tel1, tel2, tel3"
					}
				}';
		$res   = TFP::GetAPIData("users", $json, "GET");
		$count = (int) TFP::GetFirstByField($res, "count");

		if ( $count < 0 ) {
			goto Err;
		}

		// $listTel = $res["users"][0];
		// check $tel in user info
		// $listTel = [$user['tel1'], $user['tel2'], $user['tel3']];
		// $listTel_trimmed_hyphen = array_map(function($tel) {
		// 	return str_replace('-', '', $tel);
		// }, $listTel);

		// $this->assertEquals(in_array($tel, $listTel_trimmed_hyphen), true);
		// return in_array($tel, $listTel_trimmed_hyphen);
		Err:
		die("error");
	}

	function testCheckUsersValue_sn_tl3()
	{
		$user_cd = "100000002101";
		$tel     = "0258330000";
		if ( strlen($user_cd) == 0 || strlen($tel) == 0 ) {
			return false;
		}

		$json  = '{
            "users": {
                "query": "user_cd = "%s" and "%s" IN (REPLACE(tel1, "-", ""), REPLACE(tel2, "-", ""), REPLACE(tel3, "-", "") )",
                "fields":"tel1, tel2, tel3, user_cd"
            }
        }';
		$json = sprintf($json, $user_cd, $tel);
		$res   = TFP::GetAPIData("users", $json, "GET");
		$count = (int) TFP::GetFirstByField($res, "count");

		if ( $count < 0 ) {
			goto Err;
		}

		// $listTel = $res["users"][0];
		// check $tel in user info
		// $listTel = [$user['tel1'], $user['tel2'], $user['tel3']];
		// $listTel_trimmed_hyphen = array_map(function($tel) {
		// 	return str_replace('-', '', $tel);
		// }, $listTel);

		// $this->assertEquals(in_array($tel, $listTel_trimmed_hyphen), true);
		// return in_array($tel, $listTel_trimmed_hyphen);
		Err:
		die("error");
	}
	function testReqHis2()
	{
		$json = '{
			"req_his":{
				"data":[
					{"name":"user_cd","value":"100198974910","operator":"="},
					{"name":"req_syu_kb","value":"3,4,54,61,62,64,70,801,802,803,804,805","operator":"not in"}
				],
				"fields":"user_cd,req_his_cd,print_ymd,req_syu_kb,req_syu_nm,req_his_sumi_fg,req_his_sumi_nm,seikyu",
				"sort":"print_ymd desc"
			},
			"req_his_d":{
				"data":[
					{"name":"un_syu_kb","value":"1,2,3","operator":"in"},
					{"name":"hs_ymd","value":"2023-01-01 00:00:00.000","operator":">="}
				],
				"fields":"req_his_dno,un_syu_kb,un_syu_nm,hs_ymd,d_no,dm_no,shin_nm",
				"sort":"req_his_dno asc"
			}
		}
		';
		$a = '';
		$res = TFP::GetAPIDataAWS("req_his", $json, "GET");

		foreach ($res["req_his"] as $key1 => $value1) {
			foreach ($value1["req_his_d"] as $key2 => $value2) {
				$a .= $value2["shin_nm"];
			}
		}

		$b = 123;
	}

	public function testReqHis()
	{
		$user_cd = '';
		// $json    = $this->inputJson("req_his", "aws");
		$json = '
					{
						"req_his":{
							"data":[
								{"name":"req_his_cd","value":"200000076861","operator":"="},
							],
							"fields":"user_cd,users,req_his_cd,print_ymd,req_syu_kb,req_syu_nm,req_his_sumi_fg,req_his_sumi_nm,seikyu",
							"sort":"print_ymd desc",
						},
						"req_his_d":{
							"data":[
								{"name":"un_syu_kb","value":"1,2,3","operator":"in"}
							],
							"fields":"req_his_dno,un_syu_kb,un_syu_nm,d_no,dm_no,ushin_cd,shin_nm,uri_tan,uri_su,hon_kin,zei_kin,zei_ritu"
						},
					}
					
				';
		// $req_his = TFP::GetAPIData("req_his", $json, "GET");
		// $req_his = TFP::GetAPIDataAWS("req_his", $json, "GET");

		$req_his = array
		(
			"status"      => "0",
			"message"     => "",
			"req_his"     => array
			(
				"0" => array
				(
					"user_cd"         => "100198974910",
					"req_his_cd"      => "300068376221",
					"print_ymd"       => "2023-08-10 00:00:00.000",
					"req_syu_kb"      => "51",
					"req_syu_nm"      => "納品請求書（インボイス）",
					"req_his_sumi_fg" => "0",
					"req_his_sumi_nm" => "未処理",
					"seikyu"          => "21800",
					"req_his_d"       => array
					(
						"0" => array
						(
							"req_his_dno" => "1",
							"un_syu_kb"   => "1",
							"un_syu_nm"   => "売上",
							"d_no"        => "200004995960",
							"dm_no"       => "1",
							"ushin_cd"    => "ACC00105",
							"shin_nm"     => "受託開発（その他）",
							"uri_tan"     => "10000",
							"uri_su"      => "1",
							"hon_kin"     => "10000",
							"zei_kin"     => "1000",
							"zei_ritu"    => "10",
						),
						"1" => array
						(
							"req_his_dno" => "2",
							"un_syu_kb"   => "1",
							"un_syu_nm"   => "売上",
							"d_no"        => "200004995960",
							"dm_no"       => "2",
							"ushin_cd"    => "ACC00105",
							"shin_nm"     => "受託開発（その他）",
							"uri_tan"     => "10000",
							"uri_su"      => "1",
							"hon_kin"     => "10000",
							"zei_kin"     => "800",
							"zei_ritu"    => "8",
						),
						"2" => array
						(
							"req_his_dno" => "2",
							"un_syu_kb"   => "1",
							"un_syu_nm"   => "売上",
							"d_no"        => "200004995960",
							"dm_no"       => "2",
							"ushin_cd"    => "ACC00105",
							"shin_nm"     => "受託開発（その他）",
							"uri_tan"     => "10000",
							"uri_su"      => "1",
							"hon_kin"     => "12000",
							"zei_kin"     => "1000",
							"zei_ritu"    => "10",
						)
					),
					"users"           => array
					(
						"kai_nm"   => "★テスト★ソリマチ（株）",
						"busyo_nm" => "経営管理室　業務部",
						"user_nm"  => "竹沢　友樹",
						"post_no"  => "940-0071",
						"pref_cd"  => "17",
						"pref_nm"  => "新潟県",
						"adr_city" => "長岡市",
						"adr_area" => "表町１－４－２４ソリマチ第３",
						"adr_addr" => "ビル",
						"tel"      => "0258-33-4435-2",
						"fax"      => "0258-33-2851",
						"mail"     => "takezawa@mail.sorimachi.co.jp",
					)
				)
			),
			"total_count" => "1",
			"count"       => "1",
		);

		if ( $req_his["count"] == 0 ) {
			goto Err;
		}
		$startTime          = microtime(true);
		$testCalculatorTax1 = $this->testCalculatorTax1($req_his["req_his"][0]["req_his_d"]);

		$time1 = 'Time:  ' . number_format((microtime(true) - $startTime), 4) . ' Seconds';

		$startTime = microtime(true);

		$testCalculatorTax2 = $this->testCalculatorTax2($req_his["req_his"][0]["req_his_d"]);
		$time2              = 'Time:  ' . number_format((microtime(true) - $startTime), 4) . ' Seconds';

		Err:
		echo die("error");
	}

	public function testNumberFormat($value): string
	{

				// $s1 = "10000";
				// $a = number_format($s1);
				return number_format($value);
				
	}
	public function testInsertHyphen($user_cd): string
	{

		return number_format($user_cd);
	}

	public function insertHyphen($user_cd): string {
		return preg_replace('/(\d{4})(\d{6})(\d)/', '$1-$2-$3', $user_cd);
	}

	public function testCalculatorTax($req_his_d): array
	{
		$zei_ritu = $req_his_d[0]["zei_ritu"];
		$listReq  = [];
		// foreach ($req_his_d as $key => $value) {
		// 	$listReq[] = $value["zei_ritu"];
		// }

		$list_zei_ritu = array_map(function ($val) {
			return $val["zei_ritu"];
		}, $req_his_d);

		return array_unique($listReq);
	}

	public function testCalculatorTax2($req_his_d)
	{
		$listDuplicate = [];
		$listSum       = [];
		$listSpectify  = [];

		$sum_hon_kin            = [];
		$sum_zei_ritu           = [];
		$listSum["sum_hon_kin"] = 0;
		$listSum["sum_zei_kin"] = 0;

		foreach ( $req_his_d as $key => $value ) {
			$zei_ritu = $value["zei_ritu"];

			if ( in_array($zei_ritu, $listDuplicate) ) {
				$sum_hon_kin[$zei_ritu] += $value["hon_kin"];
				$sum_zei_ritu[$zei_ritu] += $value["zei_kin"];
			} else {
				$sum_hon_kin[$zei_ritu]  = $value["hon_kin"];
				$sum_zei_ritu[$zei_ritu] = $value["zei_kin"];
			}
			$listDuplicate[]                                 = $zei_ritu;
			$listSpectify[$zei_ritu]["sum_spectify_hon_kin"] = $sum_hon_kin[$zei_ritu];
			$listSpectify[$zei_ritu]["sum_spectify_zei_kin"] = $sum_zei_ritu[$zei_ritu];
			$listSpectify[$zei_ritu]["zei_kin"]              = $zei_ritu;

			$listSum["sum_hon_kin"] += $value["hon_kin"];
			$listSum["sum_zei_kin"] += $value["zei_kin"];
		}

		$listSum["sum"]          = $listSum["sum_zei_kin"] + $listSum["sum_hon_kin"];
		$listSum["sum_spectify"] = $listSpectify;
		return $listSum;
	}

	public function testCalculatorTax1($req_his_d): array
	{
		$zei_ritu     = $req_his_d[0]["zei_ritu"];
		$listReq      = [];
		$sum_hon_kin  = 0;
		$sum_zei_ritu = 0;
		$listSum      = [];
		//list zei_ritu
		$list_zei_ritu = array_map(function ($val) {
			return $val["zei_ritu"];
		}, $req_his_d);
		$unique        = array_unique($list_zei_ritu);

		$list_zei_rituTest = array_map(function ($val) {
			$arr             = [];
			$arr["zei_ritu"] = $val["zei_ritu"];
			$arr["hon_kin"] = $val["hon_kin"];
			$arr["zei_kin"] = $val["zei_kin"];

			return $arr;
		}, $req_his_d);

		$listSum["sum_hon_kin"] = 0;
		$listSum["sum_zei_kin"] = 0;
		foreach ( $unique as $key1 => $value1 ) {
			$sum_hon_kin = 0;
			$sum_zei_kin = 0;

			foreach ( $list_zei_rituTest as $key2 => $value2 ) {
				if ( $value2["zei_ritu"] == $value1 ) {
					$sum_hon_kin += $value2["hon_kin"];
					$sum_zei_kin += $value2["zei_kin"];
				}
			}
			$listSum["sum_hon_kin"] += $sum_hon_kin;
			$listSum["sum_zei_kin"] += $sum_zei_kin;

			$listSum[$key1]["sum_spectify_hon_kin"] = $sum_hon_kin;
			$listSum[$key1]["sum_spectify_zei_kin"] = $sum_zei_kin;
			$listSum[$key1]["zei_ritu"]             = $value1;
			// $listReq[] = $value["zei_ritu"];
		}
		$listSum["sum"] = $listSum["sum_hon_kin"] + $listSum["sum_zei_kin"];

		return $listSum;
	}

	public function testSpeed()
	{
		
		$req_his = [];

		$startTime = microtime(true);
		$req_his   = $req_his["req_his"][0]["req_his_d"];
		$myArray   = [ 2, 3, 2, 4, 5, 3, 6, 3 ];

		$valueSum = [];

		foreach ( $myArray as $key => $num ) {
			if ( array_key_exists($num, $valueSum) ) {
				$valueSum[$num] += $num;
			} else {
				$valueSum[$num] = $num;
			}
		}

		$res = array_sum($valueSum);


		// Example usage
		// $myArray = [2, 3, 2, 4, 5, 3, 6, 3];
		// $result = sumSameValues($myArray);
		// echo $result;  // Output will be the sum of repeating values: 2 + 3 + 3 = 8



		// $list_zei_ritu = array_map(function ($val) {
		// 	return $val["zei_ritu"];
		// }, $req_his);
		// $unique        = array_unique($list_zei_ritu);


		$list          = [];
		$list_zei_ritu = array_map(function ($val) use (&$list) {
			if ( !in_array($val["zei_ritu"], $list) )
				array_push($list, $val["zei_ritu"]);
			return $list;
		}, $req_his);

		$time = 'Time:  ' . number_format((microtime(true) - $startTime), 4) . ' Seconds';

		$this->assertEquals(false, false);

	}
	public function testReqHisBK20230825()
	{
		$user_cd = '';
		// $json    = $this->inputJson("req_his", "aws");
		$json = '
					{
						"req_his":{
							"data":[{"name":"user_cd","value":"100000002101","operator":"="}]
						},
						"req_his_d":{},
						"req_his_s":{}
					}
				';
		// $req_his = TFP::GetAPIData("req_his", $json, "GET");
		$req_his = TFP::GetAPIDataAWS("req_his", $json, "GET");
		if ( $req_his["count"] == 0 ) {
			goto Err;
		}

		Err:
		echo die("error");

	}
	public function testUsers()
	{
		// for AWS
		$user_cd = '200000143411';
		$tel     = '0748628548';

		// for SERVERTEST
		$user_cd = '100000002101';
		$tel     = '0258330000';
		$json    = $this->inputJson("users", "test");
		$users   = TFP::GetAPIData("users", $json, "GET");
		if ( $users["count"] == 0 && $users["users"]["tori_kb"] == 1 ) {
			goto Err;
		}
		Err:
		echo die("error");
	}



	public function testTypeMember()
	{
		$ky = array(
			"status"      => 0,
			"message"     => "",
			"ky"          => array(
				"0" => array(
					"ky_no"     => "206000003069",
					"user_cd"   => "100000002104",
					"ky_syu_kb" => "6",
					"ky_syu_nm" => "ＳＡＡＧ",
					"ky_his"    => array(
						"0" => array(
							"ky_e_ymd"      => "2022-08-31 00:00:00.000",
							"ky_his_syu_kb" => "7",
							"ky_his_syu_nm" => "解約",
						),

						"1" => array(
							"ky_e_ymd"      => "2021-08-31 00:00:00.000",
							"ky_his_syu_kb" => "1",
							"ky_his_syu_nm" => "新規（有料）",
						)

					)
				),

				"1" => array(
					"ky_no"     => "206000003076",
					"user_cd"   => "100000002104",
					"ky_syu_kb" => "6",
					"ky_syu_nm" => "ＳＡＡＧ",
					"ky_his"    => array(
						"0" => array(
							"ky_e_ymd"      => "2023-08-31 00:00:00.000",
							"ky_his_syu_kb" => "1",
							"ky_his_syu_nm" => "新規（有料）",
						)

					)

				),

				"2" => array(
					"ky_no"     => "207000000034",
					"user_cd"   => "100000002104",
					"ky_syu_kb" => "7",
					"ky_syu_nm" => "ＳＯＳＰ",
					"ky_his"    => array(
						"0" => array(
							"ky_e_ymd"      => "2023-08-31 00:00:00.000",
							"ky_his_syu_kb" => "4",
							"ky_his_syu_nm" => "更新（有料）",
						),

						"1" => array(
							"ky_e_ymd"      => "2022-08-31 00:00:00.000",
							"ky_his_syu_kb" => "4",
							"ky_his_syu_nm" => "更新（有料）",
						),

						"2" => array(
							"ky_e_ymd"      => "2021-08-31 00:00:00.000",
							"ky_his_syu_kb" => "1",
							"ky_his_syu_nm" => "新規（有料）",
						),

					)

				),

				"3" => array(
					"ky_no"     => "208000000160",
					"user_cd"   => "100000002104",
					"ky_syu_kb" => "8",
					"ky_syu_nm" => "ＳＯＵＰ",
					"ky_his"    => array(
						"0" => array(
							"ky_e_ymd"      => "2022-12-31 00:00:00.000",
							"ky_his_syu_kb" => "7",
							"ky_his_syu_nm" => "解約",
						),

						"1" => array(
							"ky_e_ymd"      => "2021-12-31 00:00:00.000",
							"ky_his_syu_kb" => "5",
							"ky_his_syu_nm" => "更新（無料）",
						),

						"2" => array(
							"ky_e_ymd"      => "2020-12-31 00:00:00.000",
							"ky_his_syu_kb" => "1",
							"ky_his_syu_nm" => "新規（有料）",
						)

					)

				)

			),

			"total_count" => 4,
			"count"       => 4
		);

		$ky1 =
			array(
				"status"      => 0,
				"message"     => "",
				"ky"          => array(
					"0" => array(
						"ky_no"         => "206000000226",
						"user_cd"       => "100000002116",
						"ky_syu_kb"     => "6",
						"ky_syu_nm"     => "ＳＡＡＧ",
						"kai_nm"        => "ソリマチＷＥＢテストパートナー予備３",
						"user_nm"       => "パートナー予備３　テスト",
						"ky_e_ymd"      => "2026-07-31 00:00:00.000",
						"ky_his_syu_kb" => "1",
						"ky_his"        => array(
							"0" => array(
								"ky_no"         => "206000000226",
								"ky_his_ren"    => "1",
								"ky_his_syu_kb" => "1",
								"ky_his_syu_nm" => "新規（有料）",
								"ky_e_ymd"      => "2020-07-31 00:00:00.000",
							)

						)

					),

					"1" => array(
						"ky_no"     => "206000000233",
						"user_cd"   => "100000002116",
						"ky_syu_kb" => "7",
						"ky_syu_nm" => "ＳＡＡＧ",
						"kai_nm"    => "ソリマチＷＥＢテストパートナー予備３",
						"user_nm"   => "パートナー予備３　テスト",
						"ky_his"    => array(
							"0" => array(
								"ky_no"         => "206000000233",
								"ky_his_ren"    => "2",
								"ky_his_syu_kb" => "7",
								"ky_his_syu_nm" => "解約",
								"ky_e_ymd"      => "2018-07-31 00:00:00.000",
							),
							"1" => array(
								"ky_no"         => "206000000233",
								"ky_his_ren"    => "1",
								"ky_his_syu_kb" => "7",
								"ky_his_syu_nm" => "解約",
								"ky_e_ymd"      => "2017-07-31 00:00:00.000",
							)
						)
					)
				),
				"total_count" => 2,
				"count"       => 2
			);

		$json = $this->inputJson("ky", "test");

		$json = '
									{
										"ky":{
										"data":[
											{"name":"user_cd","value":"100008243801","operator":"="},
											{"name":"ky_syu_kb","value":"6,7,8","operator":"in"}
										],
										"fields":"ky_no,user_cd,ky_syu_kb,ky_syu_nm"
										},
										"ky_his":{
										"fields":"ky_e_ymd,ky_his_syu_kb,ky_his_syu_nm",
										"sort":"ky_his_ren desc"
										}
									}		
						';
		


		// $ky = TFP::GetAPIData("ky", $json, "GET");
		$ky = TFP::GetAPIDataAWS("ky", $json, "GET");
		$arrYMD = [];
		$num    = 0;
		$this->GetYMD($ky, "ky_e_ymd", "", $num, $arrYMD, "ky_syu_kb"); // return $num and $arr
		$maxYMD = $this->MaxYMD($arrYMD);
		$isSSS  = $this->isSSS($maxYMD);
		/////

		$this->assertEquals($isSSS, true);

		// if ( $isSSS )
		// 	goto Err;

		// Err:
		// die("err");
	}


	/**
	* Divide each category and stored in array when get API of (SAAG|SOSP|SOUP)
	*
	* @param array $res : array input
	* @param string $field : find field to push in array
	* @param int $count: count of elements array 
	* @param array $arr : results
	* @param string $type : find field to category. vd: ky_syu_kb = 6/7/8 => SAAG/SOSP/SOUP
	* 
	* @author Khanh
	* @return array param $arr and $count
	*/
	function GetYMD($res, $field, $parr = "", &$count = 0, &$arr = [], $type = "ky_syu_kb")
	{
		if (
			($field == "error" || $field == "err_msg") &&
			$parr == "" && count($res) == 2 && $res["message"] != ""
		) {
			return $res["message"];
		}
		if ( $parr == $field ) {
			return $res;
		}
		foreach ( $res as $key => $val ) {
			if ( $key === $type ) {
				$type = $val;
			}
			if ( is_array($val) ) {
				$parr = ($key == "0") ? $parr : $key;
				$val  = $this->GetYMD($val, $field, $parr, $count, $arr, $type);
				if ( $val != "" ) {
					return $val;
				}
			} elseif ( $key == $field ) {
				// print_r("key: " . $val);
				$arr[$type][$val] = $res["ky_his_syu_kb"];
				$count += 1;
			}
		}
	}

	function GetYMDBK($res, $field, $parr = "", &$count = 0, &$arr = [])
	{
		$tmp = "";
		if (
			($field == "error" || $field == "err_msg") &&
			$parr == "" && count($res) == 2 && $res["message"] != ""
		) {
			return $res["message"];
		}
		if ( $parr == $field ) {
			return $res;
		}
		foreach ( $res as $key => $val ) {
			if ( is_array($val) ) {
				$parr = ($key == "0") ? $parr : $key;
				$val  = $this->GetYMDBK($val, $field, $parr, $count, $arr);
				if ( $val != "" ) {
					return $val;
				}
			} elseif ( $key == $field ) {
				// print_r("key: " . $val);
				$arr[$val] = $res["ky_his_syu_kb"];
				$count += 1;
			}
		}
		return "";
	}
	
	/**
	* find max date in array of category (SAAG|SOSP|SOUP)
	*
	* @param array $arrYMD array of category getting from function GETYMD()
	* 
	* @author Khanh
	* @return array array of category is max date
	*/ 
	function MaxYMD($arrYMD)
	{
		$arrMaxYMD = [];
		foreach ( $arrYMD as $k => $arr ) {
			$maxYMD = "";
			foreach ( $arr as $key => $value ) {
				if ( strtotime($key) > strtotime($maxYMD) ) {
					$maxYMD = $key;
					$syu_kb = $value;
				}
			}
			$arrMaxYMD[$k][$maxYMD] = $syu_kb;
		}
		return $arrMaxYMD;
	}

	function MaxYMDBK($arrYMD)
	{
		//find latest date
		foreach ( $arrYMD as $k => $arr ) {
			foreach ( $arr as $key => $value ) {
				$arr2[$k][strtotime($key)] = $value;
			}
			$max = max(array_keys($arr2[$k]));

			foreach ( $arr2[$k] as $key => $value ) {
				if ( $key == $max ) {
					$MaxYMD[$k][date('Y-m-d H:i:s', $max)] = $value;
				}
			}
		}

		return $MaxYMD;
	}

	function isSSSBK($maxYMD)
	{
		foreach ( $maxYMD as $key => $value ) {
			if ( in_array(reset($value), [ 1, 2, 4, 5 ]) ) {
				return true;
			}
		}
		return false;
	}

	function isSSS($maxYMD)
	{
		$SSS    = $GLOBALS['SSS'];
		$result = [];
		foreach ( $maxYMD as $key => $value ) {
			if ( in_array(reset($value), [ 1, 2, 4, 5 ]) ) {
				$result[$key] = $SSS[$key];
			}
		}
		return implode("/", $result);
	}

	function inputJson($name, $server = "test"): string
	{
		$json = '';
		if ( $server == "aws" ) {
			switch ($name) {
				case "ky":
					$json = '
					{
						"ky":{
						  "data":[
							{"name":"user_cd","value":"100000002101","operator":"="},
							{"name":"ky_syu_kb","value":"6,7,8","operator":"in"}
						  ],
						  "fields":"ky_no,user_cd,ky_syu_kb,ky_syu_nm"
						},
						"ky_his":{
						  "fields":"ky_e_ymd,ky_his_syu_kb,ky_his_syu_nm",
						  "sort":"ky_his_ren desc"
						}
					  }		
					';
					break;
				case "req_his":
					$json = '
								{
									"req_his":{
										"data":[{"name":"user_cd","value":"100000002101","operator":"="}]
									},
									"req_his_d":{},
									"req_his_s":{}
								}
						';
					break;
				case "users":
					$json = '
					{
						"users": {
							"data": [
								{
									"name": "user_cd",
									"value": "' . "200000143411" . '",
									"operator": "="
								},
								{
									"name":"tel1",
									"value":"' . "0748628548" . '",
									"operator":"="
								}
							]
						}
					}
					';
					break;
			}
		} else {
			switch ($name) {
				case "ky":
					$json = '
									{
										"ky":{
										"data":[
											{"name":"user_cd","value":"100000002104","operator":"="},
											{"name":"ky_syu_kb","value":"6,7,8","operator":"in"}
										],
										"fields":"ky_no,user_cd,ky_syu_kb,ky_syu_nm"
										},
										"ky_his":{
										"fields":"ky_e_ymd,ky_his_syu_kb,ky_his_syu_nm",
										"sort":"ky_his_ren desc"
										}
									}		
						';
					break;
				case "req_his":
					$json = '
									{
										"req_his":{
											"data":[{"name":"user_cd","value":' . "100000002104" . ',"operator":"="}]
										},
										"req_his_d":{},
										"req_his_s":{}
									}
						';
					break;
				case "users":
					$json = '
									{
										"users": {
											"data": [
												{
													"name": "user_cd",
													"value": "' . "100000002101" . '",
													"operator": "="
												},
												{
													"name":"tel1",
													"value":"' . "0258330000" . '",
													"operator":"="
												}
											]
										}
									}
						';
					break;
			}
		}
		return $json;
	}
}