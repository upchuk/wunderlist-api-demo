<?php

require 'vendor/autoload.php';
require_once 'keys.php';

use Wunderlist\WunderlistClient as Wunderlist;

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader);

$wunderlist = new Wunderlist($client_id, $access_token);

$tasks = $wunderlist->getListTasks($list_id);

echo $twig->render('list.html.twig', array('tasks' => $tasks));