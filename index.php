<?php
require 'vendor/autoload.php';

use \Slim\Slim;
use \Rootdown\Rootdown;

/////////////////

$rd   = new Rootdown;
$app  = new Slim;

$app->get('/', function () use ($rd) {
  $rd->render('/index.md', 'home.php');
});

$app->get('/:pages+', function($pages) use ($rd){
  $rd->render();
});

$app->run();
