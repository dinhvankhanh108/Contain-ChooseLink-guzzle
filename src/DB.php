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
require_once __DIR__ . "/../../../common_files/connect_db.php";

class DB
{
    public static function connectDB($name) {
        $Conn = ConnectTouroku();
        return $Conn;
    }
}