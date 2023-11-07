<?php
require_once  './vendor/autoload.php';

// $whoops = new \Whoops\Run;
// $whoops->allowQuit(false);
// $whoops->writeToOutput(false);
// $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
// $html = $whoops->handleException($e);

// $whoops = new \Whoops\Run;
// $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
// $whoops->register();
// try {
// $a = "1234l";
// $b = (array)"";
// echo $b;
// } catch (Exception $e) {	
// 	$whoops->handleException($e);
// 	// echo '<pre>';
// 	// print_r($e->getMessage());
// 	// print_r($html);
// 	// echo '<pre>';
// }
if (!empty($_POST["store"])) {
	$run     = new \Whoops\Run;
	$handler = new \Whoops\Handler\PrettyPageHandler;

	// Add some custom tables with relevant info about your application,
	// that could prove useful in the error page:
	$handler->addDataTable('Killer App Details', array(
		"Important Data" => 'images',
		"Thingamajig-id" => 'bad-thing'
	));

	// Set the title of the error page:
	$handler->setPageTitle("Whoops! There was a problem.");

	$run->pushHandler($handler);

	// Add a special handler to deal with AJAX requests with an
	// equally-informative JSON response. Since this handler is
	// first in the stack, it will be executed before the error
	// page handler, and will have a chance to decide if anything
	// needs to be done.
	if (\Whoops\Util\Misc::isAjaxRequest()) {
		$run->pushHandler(new \Whoops\Handler\JsonResponseHandler);
	}

	// Register the handler with PHP, and you're set!
	$run->register();

	// $b = (array)"";
	echo $b;
	die();
}
// try {
// } catch (\Throwable $th) {
// 	throw $th;
// }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		// set session for php when open browser
		$.ajax({
			// async: false,
			type: "POST",
			url: "",
			data: {
				store: "store"

			},
			beforeSend: function() {
				// $('#scLoading').show();
			},
			dataType: "json",
			success: function(response) {
				console.log((response));
			},
			complete: function() {}
		});
	})
</script>
<!-- ↑↑　<2022/31/08> <KhanhDinh> <add> -->