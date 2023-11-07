<?php
require_once  './vendor/autoload.php';

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

$validator = new EmailValidator();
$a = $validator->isValid("example@gmail.", new RFCValidation()); //true
if($a){
	echo "email hợp lệ";
}else{
	echo "email ko hợp lệ";
}



