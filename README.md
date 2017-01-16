php-kue
=======

A producer client for Kue, a redis queue implementation

Originally brought to you by the fine developers @ [Telly.com](http://telly.com/)

Now maintained by the just as fine developer.

# Installation


Add to your composer.json file : 
```
"require": {
        "cgrafton/php-kue": "^0.2.0"
    },
```

# Usage

####Configure:

```

$kueConfig = new Client([
    'host' => CACHE_HOST,
    'port' => CACHE_HOST_PORT,
    'database' => CACHE_DATABASE
]);
$kue = new KueApi($kueConfig);
```
        
####Add Job to Queue:
```
$queueName = 'my_queue_name';
$messageData = array('foo' => 'bar');
$priorityLevel = 'my_queue_name'; // Queue priority level, default is Normal: Optional
$queueName = 'my_queue_name';
$maxAttempts = 10; // (# of times job will be attempted in active status: Optional)
$responseFromKue =  $kue->createJob(
    $queueName,
    $messageData,
    $priorityLevel,
    $maxAttempts
);

```
####Results:
```
var_export($responseFromKue);
Job {kue_job_number} has been added to queue
```
