<?php

// header( 'Content-Type: application/json; charset=utf-8' );
$response = file_get_contents( 'php://input' );
$response = json_decode( $response, true );

// $response = $_POST ?? "";

// if ( !empty( $response ) ) {
// 	$api  = @$_POST["api"];
// 	$json = @$_POST["json"];
// } else {
// 	$response = file_get_contents( 'php://input' );
// 	$response = json_decode( $response, true );
// 	$api      = $response['api'];
// 	$json     = $response['json'];
// }
// $data3 = GetAPIData( $api, $json, "GET" ); 
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( ["a" => 12] );
// echo json_encode( $response );

die();