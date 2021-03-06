<?php

if (!function_exists('json_decode')) {
  throw new Exception('KueApi needs the JSON PHP extension.');
}

use \Predis\Client;

/**
 * A php rest client for Kue's API
 * @link http://learnboost.github.com/kue/
 * @link https://github.com/learnboost/kue
 *
 * @author John Smart <smart@telly.com>
 */
class KueApi {

	const PRIORITY_LOW = 10;
	const PRIORITY_NORMAL = 0;
	const PRIORITY_MEDIUM = -5;
	const PRIORITY_HIGH = -10;
	const PRIORITY_CRITICAL = -15;

	/**
	 * Version.
	 */
	const VERSION = '0.2.0';

	protected $client;

	/**
	 * Construct an API handler
	 * @param \Predis\Client $client
	 */
	public function __construct(Predis\Client $client) {
		$this->client = $client;
	}

	/**
	 * Post a job to the queue
	 * @param string $type		Whatever name you like
	 * @param array $data		Whatever data you like
	 * @param string $priority	"low", "normal", "high", "critical"
	 * @param int $maxAttempts	Number of times the job will be attempted
	 * @return int		The job id
	 */
	public function createJob($type, $data, $priority = KueApi::PRIORITY_NORMAL, $maxAttempts = 2) {
		if (empty($type)) {
			throw new InvalidArgumentException("Empty job types not allowed");
		}

		$maxAttempts = intval($maxAttempts);
		if (!$maxAttempts) {
			throw new InvalidArgumentException("It is silly to post job with 0 attempts");
		}
		$priority = intval($priority);

		$id = $this->client->incr('q:ids');
		if (!is_int($id)) {
			throw new RuntimeException("Unable to createJob, id not set by redis");
		}

		$result = $this->client->sadd('q:job:types', $type);

		$result = $this->client->hmset(
			'q:job:' . $id,
			'id', $id,
			'state', 'inactive',
			'type', $type,
			'data', json_encode($data),
			'priority', $priority,
			'created_at', time() * 1000
		);

		$this->client->zadd(
			'q:jobs',
			$priority,
			$id
		);

		$this->client->zadd(
			'q:jobs:inactive',
			$priority,
			$id
		);

		$this->client->zadd(
			'q:jobs:' . $type . ':inactive',
			$priority,
			$id
		);

		$this->client->lpush(
			'q:' . $type . ':jobs',
			1
		);

		return $id;
	}


}
