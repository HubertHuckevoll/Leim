<?php

error_reporting(E_ALL ^ E_NOTICE);
require_once($_SERVER["DOCUMENT_ROOT"].'/cardboard/cbLoader.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/cardboard.kupc/autoload.php');

$k = new cbIterateM();

echo '<pre>';

$k->start(function($payload)
{

  if ($payload['item'] == 'article.txt')
  {
    $source = $payload['rootedPathToItem'];
    
    $parts = trim($payload['parentFolder'], '/');
    $parts = explode('/', $parts);
    $articleBox = $parts[array_key_first($parts)];
    $articleName = $parts[array_key_last($parts)];
    $targetPath = getPathFS(CB_DATA_ROOT.$articleBox.'/text/');
    @mkdir($targetPath);
    $target = $targetPath.$articleName.'.txt';

    echo $source.' '.$target.'<br>';
    @copy($source, $target);
  }

});

echo '</pre>';

?>
