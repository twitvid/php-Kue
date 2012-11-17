php-kue
=======

A php rest client for the Node.js redis library kue

Brought to you by the fine developers @ [Telly.com](http://telly.com/)

# Usage

Usage is very simple, similar to Facebook's SDK:

```php
$kue = new KueApi('127.0.0.1:3000');
try {
	$result = $kue->api('job/3', 'GET');
} catch (KueApiException $kae) {
	error_log('Kue error message: ' . $kae->getMessage());
}
```

Results:
```php
var_export($result);
array (
  'id' => '3',
  'type' => 'email',
  'data' =>
  array (
    'title' => 'welcome email for tj',
    'to' => 'tj@learnboost.com',
    'template' => 'welcome-email',
  ),
  'priority' => -10,
  'progress' => '100',
  'state' => 'complete',
  'attempts' => NULL,
  'created_at' => '1309973155248',
  'updated_at' => '1309973155248',
  'duration' => '15002',
)
```

## @TODO

 * Support for authentication
 * Better error handling (ex: id not found)