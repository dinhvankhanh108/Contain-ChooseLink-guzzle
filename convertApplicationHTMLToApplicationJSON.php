<?php
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
function convertApplicationHTMLToApplicationJSON()
{

	$api = 'users';
	$json = '{
		"users": {
			"data": [
				{
					"name": "user_cd",
					"value": "'. '100000002101' .'",
					"operator": "="
				}
			],
			"fields":"tel1, tel2, tel3, user_cd"
		}
	}';
	$client = new Client();
	// $response = $client->request('GET', 'http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php', ['Content-Type' => 'application/json']);
	$response = $client->request('POST', 'http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php', [
		'Content-Type' => 'application/json',
		'form_params' => [
			'api' => $api,
			'json' => $json,
		]
	]);
	$status =  $response->getStatusCode(); // 200
	// echo '<br/>';
	header("Content-Type: application/json");

	$getHeaderLine =  $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
	$response = $response->getBody(); // return data 

	try {
		/* json parse package */
		$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

		//remove \ufeff from a string
		$stmts = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);

		//similar JSON.parse in js
		$stmts = $parser->parse($stmts);

		$prettyPrinter = new PrettyPrinter\Standard;
		$stmts = $prettyPrinter->prettyPrintFile($stmts);

		//convert json to array
		echo '<pre>';
		print_r($stmts);
		echo '<pre>';
		// $stmts = json_decode($stmts, true);
		// echo $stmts['status'];
	} catch (PhpParser\Error $e) {
		echo 'Parse Error: ', $e->getMessage();
	}
}

convertApplicationHTMLToApplicationJSON();