<?php

require 'vendor/autoload.php';
require_once 'keys.php';

use Wunderlist\WunderlistClient as Wunderlist;

$wunderlist = new Wunderlist($client_id, $access_token);

echo deliver($wunderlist);

function deliver($wunderlist) {

    if (!isset($_POST['task_id']) || !isset($_POST['revision'])) {
        return json_encode(['error' => 'Bad request']);
    }

    $response = $wunderlist->completeTask($_POST['task_id'], $_POST['revision']);
    if ($response->revision === (int) $_POST['revision'] + 1 && $response->completed === true) {
        return json_encode(['completed' => true]);
    }

    return json_encode(['error' => 'Unknown error']);
}
