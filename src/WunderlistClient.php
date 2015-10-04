<?php

namespace Wunderlist;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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
     * Check the response status code.
     *
     * @param ResponseInterface $response
     * @param int $expectedStatusCode
     *
     * @throws \RuntimeException on unexpected status code
     */
    private function checkResponseStatusCode(ResponseInterface $response, $expectedStatusCode)
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode !== $expectedStatusCode) {
            throw new \RuntimeException('Wunderlist API returned status code ' . $statusCode . ' expected ' . $expectedStatusCode);
        }
    }

    /**
     * Returns all the lists
     *
     * @return array
     */
    public function getLists() {
        $response = $this->guzzle->get('lists');
        $this->checkResponseStatusCode($response, 200);
        return json_decode($response->getBody(), true);
    }

    /**
     * Returns a specific list
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getList($id) {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('The list id must be numeric.');
        }

        $response = $this->guzzle->get('lists/' . $id);
        $this->checkResponseStatusCode($response, 200);
        return json_decode($response->getBody(), true);
    }

    /**
     * Return all the tasks of a given list
     *
     * @param int $list_id
     *
     * @return array()
     */
    public function getListTasks($list_id) {
        if (!is_numeric($list_id)) {
            throw new \InvalidArgumentException('The list id must be numeric.');
        }

        $response = $this->guzzle->get('tasks', ['query' => ['list_id' => $list_id]]);
        $this->checkResponseStatusCode($response, 200);
        return json_decode($response->getBody());
    }

    /**
     * Creates a new task
     *
     * @param string $name
     * @param int $list_id
     * @param array $task
     *
     * @return mixed
     */
    public function createTask($name, $list_id, $task = []) {
        if (!is_numeric($list_id)) {
            throw new \InvalidArgumentException('The list id must be numeric.');
        }
        $task['name'] = $name;
        $task['list_id'] = $list_id;
        $response = $this->guzzle->post('tasks', ['body' => json_encode($task)]);
        $this->checkResponseStatusCode($response, 201);
        return json_decode($response->getBody());
    }

    /**
     * Completes a task
     *
     * @param int $task_id
     * @param int $revision
     * @return mixed
     */
    public function completeTask($task_id, $revision) {
        if (!is_numeric($task_id)) {
            throw new \InvalidArgumentException('The list id must be numeric.');
        } elseif (!is_numeric($revision)) {
            throw new \InvalidArgumentException('The revision must be numeric.');
        }

        $response = $this->guzzle->patch('tasks/' . $task_id, ['body' => json_encode(['revision' => (int) $revision, 'completed' => true])]);
        $this->checkResponseStatusCode($response, 200);
        return json_decode($response->getBody());
    }
}