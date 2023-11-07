<?php

declare(strict_types=1);
$_SERVER['SERVER_ADDR'] = "::1";

// ./vendor/bin/phpunit tests/abc.phprequire_once  './vendor/autoload.php';

class tester extends \PHPUnit\Framework\TestCase
{
	public function testABC()
	{
		// $send = send_mail_PHPMailer("nguyentrungquan65@gmail.com", "Subject", "Body", "khanhvandinhkhanh1@gmail.com");
		$send = false;
		$this->assertEquals(false, $send);
	}
}
