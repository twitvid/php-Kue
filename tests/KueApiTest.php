<?php

require_once 'PHPUnit/Autoload.php';
require __DIR__ . "/../vendor/autoload.php";

/**
 * Test class for KueApi
 */
class KueApiTest extends PHPUnit_Framework_TestCase {

	public $redis;

	public function setUp() {
		$this->redis = new Predis\Client('tcp://127.0.0.1:6379');
	}

	public function testCreateJob() {
		$kue = new KueApi($this->redis);
		$jobId = $kue->createJob('email', array (
			'title' => 'welcome email for tj',
			'to' => 'tj@learnboost.com',
			'template' => 'welcome-email',
		));
		$this->assertTrue(is_int($jobId), "new job id was not returned");
	}
}
