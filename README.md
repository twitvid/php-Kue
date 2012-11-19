php-kue
=======

A producer client for Kue, a redis queue implementation

Brought to you by the fine developers @ [Telly.com](http://telly.com/)

# Installation

```
composer require php-kue
```

# Usage

Queueing a job is simple:

```php
$kue = new KueApi('127.0.0.1', 6379);
$jobId = $kue->createJob('email', array (
	'title' => 'welcome email for tj',
	'to' => 'tj@learnboost.com',
	'template' => 'welcome-email',
));

Results:
```php
var_export($result);
3
```
## @TODO

 * Support for processing queue from PHP
 * Better error handling