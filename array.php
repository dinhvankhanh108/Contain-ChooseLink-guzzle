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
	$client = new Client(['cookies' => true]);
	$response = $client->request('GET', 'http://www.sorizo.net.test:6062/usersupport/change_user/post-member.php', ['Content-Type' => 'application/json']);
	// echo $response->getStatusCode(); // 200
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
		$stmts = json_decode($stmts, true);
		// echo $stmts['status'];
	} catch (PhpParser\Error $e) {
		echo 'Parse Error: ', $e->getMessage();
	}
	return $stmts;
}

$a = convertApplicationHTMLToApplicationJSON();
print_r($a);
die();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<a href="http://member:6062/reg/regist_certify.php">zzz</a>

</body>
</html>