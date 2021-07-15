<?php
namespace Tests;

use App\Container;

require_once '../vendor/autoload.php';


$dic = new Container();

echo '<pre>';

var_dump($dic->get('Tests\User'));
var_dump($dic->get('Tests\Repertory'));

echo '</pre>';