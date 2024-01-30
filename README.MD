# RUNNERS TO LARAVEL

## DESCRIPTION

Package from laravel to store the processes runned. You can run N times a procces (once by default)

## INSTALATION

```
composer require hdgarau/runners

# if you use DB Model (eloquent by default)
php artisan runner:tables
php artisan migrate

```
## HANDLER CLASS

### CONFIG FILE

You can define your own model to store data runner.
Package provides two models. "eloquent" (by default) and "file"
You can define the model on enviroment variable RUNNER_MODEL. That one must implements iRunnerModel.

```
<?php
    return [
        'default' => env('RUNNER_MODEL','eloquent'),
        'path' => database_path ('runners/'),
        'table' => 'runners',
        'models' => [
            'eloquent' => [
                'class' => \Hdgarau\Runners\RunnerModel::class,
                'params' => []
            ],
            'file' => [
                'class' => \Hdgarau\Runners\RunnerFileModel::class,
                'params' => [ storage_path('runner-data.json') ]
            ],
        ]
    ];
```

### RUNNERS METHODS
```
\Hdgarau\Runners\RunnerHandler       

# Execute without check. if $store is false, Runner will not registred
static public function run( string $className, array $params = [],bool $store = true ) : bool

# Execute if it never was executed
static public function once( string $className, array $params = [] ) : bool

# Execute if it was count times executed lower than $times 
static public function times( string $className, int $times, array $params = [] ) : bool

```

## CONSOLE

### CREATE A NEW PROCESS

Make a new runner
```
php artisan make:runner [NAME]
```

Make a new runner (that one will run every times)

```
php artisan make:runner --always [NAME]
```

### RUN
Run once all runners made
```
php artisan runner
```
### RESET STATUS

Delete all store

```
php artisan runner:clear
```


