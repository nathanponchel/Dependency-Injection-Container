<?php
namespace Tests;

use App\Container;

require_once '../vendor/autoload.php';


$container = new Container();

echo '<pre>';

/*
 * Always a new instance of class
 * */
$container->getDefinition(User::class)->setShared(false);
$user1 = $container->get(User::class);
$user2 = $container->get(User::class);
var_dump($user1, $user2); // Excepting two distinct IDs because (shared => false)

echo PHP_EOL;


/*
 * Alias between interface and class
 * */
$container->setAlias(DatabaseInterface::class, Database::class);
$db = $container->get(DatabaseInterface::class);
var_dump($db); //Excepting Database type and not DatabaseInterface because of the alias

echo PHP_EOL;


/*
 * Always same instance of class
 * */
$repertory = $container->get(Repertory::class);
var_dump($repertory);


echo '</pre>';
