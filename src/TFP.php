<?php

namespace GuzzleHttp;

use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;

/**
 * @final
 */
class TFP
{
    public static function GetAPIData($api, $json, $request)
	{
		$STFSApiAccessURI = $_ENV['STFS_API_ACCESS_URI'] ?? "http://192.168.3.213:80";
		$STFSApiAccessID = $_ENV['STFS_API_ACCESS_ID'] ?? "test_admin";
		$STFSApiAccessPW = $_ENV['STFS_API_ACCESS_PW'] ?? "test_pass";

		$port = (strpos($STFSApiAccessURI, ":") !== false) ? explode(":", $STFSApiAccessURI)[1] : "80";
		$port = (strpos($port, "/") !== false) ? explode("/", $STFSApiAccessURI)[0] : $port;

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_PORT => $port,
			CURLOPT_URL => $STFSApiAccessURI . "/api/" . $api,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => $request,
			CURLOPT_POSTFIELDS => $json,
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Content-Type: application/json",
				"Host: " . str_replace(array("http://", "https://"), "", $STFSApiAccessURI),
				"X-Authorization: " . base64_encode($STFSApiAccessID . ":" . $STFSApiAccessPW)
			),
			"X-HTTP-Method-Override: " . $request
		));

		$response = json_decode(curl_exec($curl), true);
		$err = curl_error($curl);
		curl_close($curl);
		return ($err) ? "Error #:" . $err : $response;
	}

    public static function GetFirstByField($res, $field, $parr = "")
	{
		if (($field == "error" || $field == "err_msg") &&
			$parr == "" && count($res) == 2 && $res["message"] != ""
		) {
			return $res["message"];
		}
		if ($parr == $field) {
			return $res;
		}
		foreach ($res as $key => $val) {
			if (is_array($val)) {
				$parr = ($key == "0") ? $parr : $key;
				$val = TFP::GetFirstByField($val, $field, $parr);
				if ($val != "") {
					return $val;
				}
			} elseif ($key == $field) {
				return $val;
			}
		}
		return "";
	}

    public static function getAPIDataAWS($api, $json, $method = "GET")
	{
		$client = new Client();
		$response = $client->request('POST', 'http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php', [
			'Content-Type' => 'application/json',
			'form_params'  => [
				'api'  => $api,
				'json' => $json,
				'method' => $method,
			]
		]);
		$status   = $response->getStatusCode(); // 200
		// header("Content-Type: application/json");

		$getHeaderLine = $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
		$response      = $response->getBody(); // return data 

		try {
			/* json parse package */
			$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

			//remove \ufeff from a string
			$stmts = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);

			//similar JSON.parse in js
			$stmts = $parser->parse($stmts);

			$prettyPrinter = new PrettyPrinter\Standard;
			$stmts         = $prettyPrinter->prettyPrintFile($stmts);

			//convert json to array
			// echo '<pre>';
			// print_r($stmts);
			// echo '<pre>';
			$stmts = json_decode($stmts, true);
			// echo $stmts['status'];
		} catch ( PhpParser\Error $e ) {
			echo 'Parse Error: ', $e->getMessage();
		}

		return $stmts;
	}
}