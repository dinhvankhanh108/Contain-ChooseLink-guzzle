<?php
require_once  './vendor/autoload.php';

// The argument specifies the directory where the ".env" file is located 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

//get data from file .env
echo  getenv('URL');    // http://example.com 
echo  $_ENV['URL'];     // http://example.com 
echo  $_SERVER['URL'];  // http://example.com
print_r($_ENV);
print_r($_SERVER);


