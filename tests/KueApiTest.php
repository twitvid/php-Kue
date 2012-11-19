<?php

require_once 'PHPUnit/Autoload.php';
$base = realpath(dirname(__FILE__) . '/..');
require "$base/lib/KueApi.php";

/**
 * Test class for KueApi
 */
class KueApiTest extends PHPUnit_Framework_TestCase {

	public function testPush() {
		$kue = new KueApi('127.0.0.1', 3000);
		try {
			$jobId = $kue->postJob('email', array (
				'title' => 'welcome email for tj',
				'to' => 'tj@learnboost.com',
				'template' => 'welcome-email',
			));
			$this->assertTrue(is_int($jobId), "new job id was not returned");
		} catch (KueApiException $kae) {
			echo 'Kue error message: ' . $kae->getMessage() . "\n";
		}
	}

	public function testGet() {
		$kue = new KueApi('127.0.0.1', 3000);
		try {
			$result = $kue->api('job/1');
			$this->assertEquals("1", $result['id']);
		} catch (KueApiException $kae) {
			echo 'Kue error message: ' . $kae->getMessage() . "\n";
		}
	}
}
