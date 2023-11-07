<?php
require_once __DIR__ . "/../vendor/autoload.php";

// declare(strict_types=1);
$_SERVER['SERVER_ADDR'] = "::1";

use \GuzzleHttp\TFP;
use \GuzzleHttp\Client;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter;
use function PHPSTORM_META\type;

define("EXPIRED_LIMIT", "1 hour");
$SSS = [ "6" => "SAAG[6]", "7" => "SOSP[7]", "8" => "SOUP[8]" ];

// define("ERR_PTNLOGIN_01_01_01", "ERR_PTNLOGIN_01_01_01");
// define("ERR_PTNLOGIN_01_01_02", "ERR_PTNLOGIN_01_01_02");
// define("ERR_PTNLOGIN_01_01_03", "ERR_PTNLOGIN_01_01_03");
// define("ERR_PTNLOGIN_01_01_11", "ERR_PTNLOGIN_01_01_11");
// define("ERR_PTNLOGIN_01_01_12", "ERR_PTNLOGIN_01_01_12");
// define("ERR_PTNLOGIN_01_01_13", "ERR_PTNLOGIN_01_01_13");
// //STEP2
// define("ERR_PTNLOGIN_02_01_01", "ERR_PTNLOGIN_02_01_01");
// define("ERR_PTNLOGIN_02_01_02", "ERR_PTNLOGIN_02_01_02");
// define("ERR_PTNLOGIN_02_02_01", "ERR_PTNLOGIN_02_02_01");
// define("ERR_PTNLOGIN_02_02_02", "ERR_PTNLOGIN_02_02_02");
// define("ERR_PTNLOGIN_02_02_03", "ERR_PTNLOGIN_02_02_03");
// define("ERR_PTNLOGIN_03_01_01", "ERR_PTNLOGIN_03_01_01");
// define("ERR_PTNLOGIN_03_01_11", "ERR_PTNLOGIN_03_01_11");

// define("ERR_PTNLOGIN_04_01_01", "ERR_PTNLOGIN_04_01_01");
// require_once __DIR__ . "/../../../common_files/smtp_mail.php";
require_once __DIR__ . "/../../../member.sorimachi.co.jp/lib/common.php";


class FormatNumberTest extends \PHPUnit\Framework\TestCase
{

	public function testNumberFormat()
	{

				$value = "10000";
				// $a = number_format($s1);
				$a = number_format($value);
				self::assertSame("10,000", $a);
	}

	public function testNumberFormat1()
	{

				$value = "0";
				// $a = number_format($s1);
				$a = number_format($value);
				self::assertSame("0", $a);
	}

	public function testNumberFormat2()
	{

				$value = 0;
				// $a = number_format($s1);
				$a = number_format($value);
				self::assertSame(0, $a);
	}

	public function testNumberFormat3()
	{

				$value = "abc";
				// $a = number_format($s1);
				$a = number_format($value);
				self::assertSame("abc", $a);
	}

	public function testformatDate(){
		$dateTimeString = "2023/01/08 00:00:00"; 
		$changeFormat = "Y-m-d";
		$flag = true;
		$date = new DateTime($dateTimeString);
		$date = $date->format($changeFormat);
	
		$a = $flag ? $this->changeFormat($date, $changeFormat) : $date;
		return $a;
	
	}
	
	public function changeFormat($date, $changeFormat) {
		switch ($changeFormat) {
			case 'Y-m-d':
			case 'Y/m/d':
				return preg_replace('/^((((19|[2-9]\d)\d{2}).+(0[13578]|1[02]).+(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2}).+(0[13456789]|1[012])\/(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\/02\/(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\/02\/29))$/', '$3年$5月$6日', $date);            
			case 'd-m-Y':
			case 'd/m/Y':
				return preg_replace('/^(((0[1-9]|[12]\d|3[01]).+(0[13578]|1[02]).+((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30).+(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/', '$3日$4月$5年', $date);
		}
		return $date;
	}

	function testABC () {
		$flag = true;
		$arrYMD = ["2023/02/03", "2023/02/09", "2023/02/06"];
		usort($arrYMD, function($a, $b) {
			$dateTimestamp1 = strtotime($a);
			$dateTimestamp2 = strtotime($b);

			return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
		});

		$a = ($arrYMD[count($arrYMD) - 1]);
		$a = 123;
	}
}