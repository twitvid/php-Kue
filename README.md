php-kue
=======

A producer client for Kue, a redis queue implementation

Originally brought to you by the fine developers @ [Telly.com](http://telly.com/)

Now maintained by the just as fine developers @ [Thredmeup.com](http://threadmeup.com/)

# Installation


Add to your composer.json file : 
```
"require": {
        "threadmeup/php-kue": "^0.2.0"
    },
```

# Usage

####Configure:

```

$this->kueRedis = new Client([
            'host' => CACHE_HOST,
            'port' => CACHE_HOST_PORT,
            'database' => CACHE_DATABASE
        ]);
$this->kue = new KueApi($this->kueRedis);
```
        
####Add Job to Queue:
```
$responseFromKue =  $this->kue->createJob(
	<yarn queue name>,                    (Queue Name specified in Yarn: Required)
	array(
		'movement_id' => $movementId  (Data Yarn is expecting: Required)
	),
	<priority level>,                      (Queue priority level, default is Normal: Optional)
	<maximum attempts>                    (# of times job will be attempted in active status: Optional)
        		
));;

```
####Results:
```
var_export($responseFromKue);
Job <id number> has been added to queue
```


