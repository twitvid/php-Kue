php-kue
=======

A php rest client for the Node.js redis library kue.
**NOTE**: this package only handles kue insertion and kue stats. Atomic processing of the queue is not supported.

Brought to you by the fine developers @ [Telly.com](http://telly.com/)

# Installation

```
composer require php-kue
```

# Usage

Queueing a job is simple:

```php
$kue = new KueApi('127.0.0.1', 3000);
$jobId = $kue->postJob('email', array (
	'title' => 'welcome email for tj',
	'to' => 'tj@learnboost.com',
	'template' => 'welcome-email',
));

Results:
```php
var_export($result);
3
```

Generalized access is similar to Facebook's SDK:

```php
$kue = new KueApi('127.0.0.1', 3000);
try {
	$result = $kue->api('job/1');
} catch (KueApiException $kae) {
	error_log('Kue error message: ' . $kae->getMessage());
}
```

Results:
```php
var_export($result);
array (
  'id' => '1',
  'type' => 'email',
  'data' =>
  array (
    'title' => 'welcome email for tj',
    'to' => 'tj@learnboost.com',
    'template' => 'welcome-email',
  ),
  'priority' => 0,
  'progress' => 0,
  'state' => 'inactive',
  'created_at' => '1353352708708',
  'updated_at' => '1353352708708',
  'attempts' =>
  array (
    'remaining' => NULL,
    'max' => '2',
  ),
)
```

## @TODO

 * Support for processing queue from PHP
 * Support for authentication
 * Support for https
 * Better error handling