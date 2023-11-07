<?php
require_once __DIR__ . "/../vendor/autoload.php";

// declare(strict_types=1);
$_SERVER['SERVER_ADDR'] = "::1";

use \GuzzleHttp\TFP;
use \GuzzleHttp\Common;
use \GuzzleHttp\DB;
use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;

define("EXPIRED_LIMIT", "1 hour");
$SSS = [ "6" => "SAAG[6]", "7" => "SOSP[7]", "8" => "SOUP[8]" ];

// define("ERR_PTNLOGIN_04_01_01", "ERR_PTNLOGIN_04_01_01");
// require_once __DIR__ . "/../../../common_files/smtp_mail.php";
require_once __DIR__ . "/../../../common_files/STFSApiAccess.php";

class MemberTest extends \PHPUnit\Framework\TestCase
{

	public function testNumberFormat()
	{
		$serial_no = "5050117007116360";
		$tel = "0258330000";
		$user_cd = "100000002102";
		$service_product = $version = $message = "";
		// $Login_UserLogin = DB::connectDB($serial_no);
		$Login_UserLogin = Common::Login_UserLogin($serial_no, $user_cd, $tel, $service_product, $version, $message);

		self::assertSame("10,000", "10,000");
	}
}