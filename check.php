<?php

require 'vendor/autoload.php';
require_once 'keys.php';

use GuzzleHttp\Client;
use Wunderlist\WunderlistClient as Wunderlist;

$guzzle = new Client(
    [
        'base_uri' => 'https://a.wunderlist.com/api/v1/',
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Client-ID' => $client_id,
            'X-Access-Token' => $access_token,
        ]
    ]
);
$wunderlist = new Wunderlist($guzzle);

echo deliver($wunderlist);

function deliver($wunderlist) {

    if (!isset($_POST['task_id']) || !isset($_POST['revision'])) {
        return json_encode(['error' => 'Bad request']);
    }

    try {
        $response = $wunderlist->completeTask($_POST['task_id'], $_POST['revision']);
        if ($response->revision === (int) $_POST['revision'] + 1 && $response->completed === true) {
            return json_encode(['completed' => true]);
        }

        return json_encode(['error' => 'Unknown error']);
    }
    catch (\Exception $e) {
        return json_encode(['error' => $e->getMessage()]);
    }
}
