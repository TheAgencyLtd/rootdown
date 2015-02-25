<?php
require 'vendor/autoload.php';

use \Slim\Slim;
use \Rootdown\Site as Site;

/////////////////

$app  = new Slim;

$app->get(':path+', function($path) use ($app){

  $page = Site::page($path);

  if($page){
    $app->render($page->template(), array(
      "page" => $page
    ));
  } else {
    $app->render('404.php', array(), 404);
  }

});

$app->run();
