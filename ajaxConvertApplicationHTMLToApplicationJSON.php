<?php 
require_once "vendor/autoload.php";

use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;


//METHOD2: use ajax to get API return data content-type: application/html then convert string content-type: application/html to application/json
// $response = file_get_contents('http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php');
$client = new Client();
$response = $client->request('GET', 'http://www.hp-sorizo.apn.mym.sorimachi.biz/TFP/test1.php', ['Content-Type' => 'application/json']);
$response = $response->getBody();
if (!empty($_POST['store'])) {
	$response = $_POST['response'];
	echo json_encode($response['status']);
	die();
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
	var response = '<?php echo ($response) ?>';
	console.log(response);
	response = response.replace(/[\u200B-\u200D\uFEFF]/g, '');
	response = JSON.parse(response);
	console.log(response);
	// $(document).ready(function() {
	// 	// reload();
	// 	// set SESSION for php when open browser first
	$.ajax({
		type: "POST",
		dataType: "json",
		url: '',
		data: {
			store: "store",
			response: response
		},
		dataType: "json",
		success: function(response) {
			console.log((response));
			// console.log($('#general'));
			// location.reload();
			// $('#scLoading').hide();
		},
		complete: function() {

		}
	});
</script>