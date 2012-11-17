<?php

$base = realpath(dirname(__FILE__) . '/..');
require "$base/lib/KueApi.php";

/**
 * Test class for KueApi
 */
class KueApiTest extends PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$topsy = new TopsyApi(self::API_KEY, self::HOST);
		$this->assertStringStartsWith(self::HOST, $topsy->curl_opts[CURLOPT_USERAGENT]);
	}
}
