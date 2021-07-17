<?php
namespace Tests;

use App\Container;
use App\Definition;

require_once '../vendor/autoload.php';


$dic = new Container();

echo '<pre>';

$definition = new Definition('\Tests\Repertory');
var_dump($definition->getParameters());

echo '</pre>';