<?php 
require_once "vendor/autoload.php";

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

$client = new Client();
$promise = $client->request('GET', 'http://www.sorizo.net:8013/drm/text.php');
echo $promise->getHeaderLine('content-type'); 
$promise = $promise->getBody();
echo $promise;
// $promise = json_decode($promise,true);

// $promise->then(
//     function (ResponseInterface $res) {
//         echo $res->getStatusCode() . 1234 . "\n";
//     },
//     function (RequestException $e) {
//         echo $e->getMessage() . 111 . "\n";
//         echo $e->getRequest()->getMethod();
//     }
// );