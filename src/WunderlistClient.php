<?php

namespace Wunderlist;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class WunderlistClient {

    /**
     * @var Client
     */
    private $guzzle;

    /**
     * Constructor
     *
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle) {
        $this->guzzle = $guzzle;
    }

    /**
     * Returns all the lists
     *
     * @return mixed
     */
    public function getLists() {
        try {
            $response = $this->guzzle->get('lists');
        }
        catch(ClientException $e) {
            return array();
        }
        return json_decode($response->getBody());
    }

    /**
     * Returns a specific list
     *
     * @param $id
     *
     * @return mixed
     */
    public function getList($id) {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Incorrect ID specified.');
        }

        try {
            $response = $this->guzzle->get('lists/' . $id);
        }
        catch (ClientException $e) {
            return NULL;
        }

        return json_decode($response->getBody());
    }

    /**
     * Return all the tasks of a given list
     *
     * @param $list_id
     *
     * @return array()
     */
    public function getListTasks($list_id) {
        if (!$list_id) {
            return array();
        }

        try {
            $response = $this->guzzle->get('tasks', ['query' => ['list_id' => $list_id]]);
        }
        catch (ClientException $e) {
            return array();
        }

        return json_decode($response->getBody());
    }

    /**
     * Creates a new task
     *
     * @param string $name
     * @param string $list_id
     * @param array $task
     *
     * @return mixed
     */
    public function createTask($name, $list_id, $task = []) {
        $task['name'] = $name;
        $task['list_id'] = $list_id;
        try {
            $response = $this->guzzle->post('tasks', ['body' => json_encode($task)]);
        }
        catch (ClientException $e) {
            return false;
        }

        return json_decode($response->getBody());
    }

    /**
     * Completes a task
     *
     * @param $task_id
     * @param $revision
     * @return mixed
     */
    public function completeTask($task_id, $revision) {
        try {
            $response = $this->guzzle->patch('tasks/' . $task_id, ['body' => json_encode(['revision' => (int) $revision, 'completed' => true])]);
        }
        catch (ClientException $e) {
            return false;
        }

        return json_decode($response->getBody());
    }
}