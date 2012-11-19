<?php

if (!function_exists('curl_init')) {
  throw new Exception('KueApi needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('KueApi needs the JSON PHP extension.');
}

/**
 * A php rest client for Kue's API
 * @link http://learnboost.github.com/kue/
 * @link https://github.com/learnboost/kue
 *
 * @author John Smart <smart@telly.com>
 */
class KueApi {

	/**
	 * Version.
	 */
	const VERSION = '0.1.0';

	/**
	 * Default options for curl.
	 */
	public $curl_opts = array(
		CURLOPT_CONNECTTIMEOUT	=> 10,
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_TIMEOUT			=> 60,
		CURLOPT_USERAGENT		=> 'kue-php-0.1.0',
		CURLOPT_HEADER			=> 1,
		CURLOPT_HTTPHEADER		=> array(
			// disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
			// for 2 seconds if the server does not support this header.
			'Expect:',
			'Content-Type: application/json'
		)
	);

	protected $hostName;

	/**
	 * Construct an API handler
	 * @param string $hostName		The host name for the Kue server
	 * @param int $port             Optional. The port to use
	 */
	public function __construct($hostName, $port = 3000) {
		$this->hostName = $hostName;
		if (substr($this->hostName, -1) !== '/') {
			// trailing slash
			$this->hostName = $this->hostName . '/';
		}

		$this->curl_opts[CURLOPT_PORT] = intval($port);
	}

	/**
	 * Post a job to the queue
	 * @param string $type		Whatever name you like
	 * @param array $data		Whatever data you like
	 * @param string $priority	"normal", "high"
	 * @param int $maxAttempts	Number of times the job will be attempted
	 * @return int		The job id
	 * @throws KueApiException
	 */
	public function postJob($type, $data, $priority = "normal", $maxAttempts = 2) {
		$maxAttempts = intval($maxAttempts);
		if (!$maxAttempts) {
			throw new KueApiException("It is silly to post job with 0 attempts");
		}

		$result = $this->api("/job", array(
			"type" => $type,
			"data" => $data,
			"options" => array(
				"attempts" => $maxAttempts,
				"priority" => $priority
			)
		), "POST");

		if (!isset($result['message'])) {
			throw new KueApiException("Unexpected response: " . json_encode($result));
		} else if (!isset($result['id'])) {
			throw new KueApiException("Job not created: " . json_encode($result));
		}

		return intval($result["id"]);
	}


	/**
	 * Make an api call
	 * @param string $path			The procedure name to call
	 * @param array $params			Optional. Parameters to send.
	 * @param string $method		Optional. Defualt: GET
	 * @return array		The result data
	 * @throws KueApiException
	 */
	public function api($path, array $params = array(), $method = "GET") {
		if (substr($path, 0, 1) === '/') {
			// no starting slash
			$path = substr($path, 1);
		}

		$url = 'http://' . $this->hostName . $path;

		if ($method === "POST") {
			$ch = curl_init($url);
			$opts = $this->curl_opts;
			curl_setopt_array($ch, $opts);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		} else {
			$url .= '?' . self::queryToStr($params, true);
			$ch = curl_init($url);
			$opts = $this->curl_opts;
			curl_setopt_array($ch, $opts);
		}

		$result = curl_exec($ch);

//		curl_close($ch);

		if ($result === false) {
			// API returned non-200 response
			$ex = KueApiException::CreateFromCurl($ch);
			throw $ex;
		}

		// parse headers
		list($headers, $content) = explode("\r\n\r\n", $result, 2);
		$headers	= self::stringToHeader($headers);

		$json = json_decode($content, true, 32);
		if ($json === false) {
			throw new KueApiException('Kai response is invalid, unexpected', 500);
		}

		return $json;
	}

	/**
	 * Log an error message
	 * @param string $msg
	 */
	protected function log($msg) {
		error_log($msg);
	}

	/**
	 * Turns associative array into a query string
	 * @param array $query
	 * @param bool $sort		Set true to sort the query string by key
	 * @return string
	 */
	protected static function queryToStr(array $query, $sort = false) {
		$queryStr = array();
		foreach ($query as $key => $val) {
			$key	= rawurlencode($key);
			$val	= rawurlencode($val);
			$queryStr[$key] = $key . '=' . $val;
		} unset($val);

		if ($sort) {
			ksort($queryStr);
		}

		return implode('&', $queryStr);
	}

	/**
	 * Parse a header string to an associatvie array
	 * @static
	 * @param string $headerStr
	 * @return array
	 */
	protected static function stringToHeader($headerStr) {
		$headers	= array();

		$headerStr	= explode("\r\n", $headerStr);
		foreach ($headerStr as $header) {
			$header = explode(": ", $header, 2);
			if (count($header) === 2) {
				$headers[$header[0]]	= $header[1];
			}
		}

		return $headers;
	}
}

/**
 * An exception thrown when a call to the Kue API results in an error
 */
class KueApiException extends Exception {
	protected static $ERROR_CODES = array(
		// put better explanations of error codes?
	);

	/**
	 * Construct a TopsyApiException from a curl handle
	 * @static
	 * @param mixed $ch		A Curl Handle
	 * @return KueApiException
	 */
	public static function CreateFromCurl($ch) {
		$errorCode = curl_errno($ch);
		$errorMessage = curl_error($ch);
		if (empty($errorMessage) && isset(self::$ERROR_CODES[$errorCode])) {
			$errorMessage	= self::$ERROR_CODES[$errorCode];
		}
		return new KueApiException($errorMessage, $errorCode);
	}
}