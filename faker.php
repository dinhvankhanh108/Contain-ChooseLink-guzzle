<?php
require_once  './vendor/autoload.php';

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

// generate data by accessing properties
echo "NAME:" . $faker->name . '<br/>';
  // 'Lucy Cechtelar';
echo "ADDRESS:" . $faker->address . '<br/>';
  // "426 Jordy Lodge
  // Cartwrightshire, SC 88120-6700"
echo "TEXT:" . $faker->text . '<br/>';
  // Dolores sit sint laboriosam dolorem culpa et autem. Beatae nam sunt fugit
  // et sit et mollitia sed.
  // Fuga deserunt tempora facere magni omnis. Omnis quia temporibus laudantium
  // sit minima sint.

//random 10 name
echo "-----RANDOM 10 NAME--------" . '<br/>';
for ($i=1; $i <= 10; $i++) { 
	echo "NAME" . $i . ":" . $faker->name . '<br/>';
}
