<?php
    return [
        'default' => env('RUNNER_MODEL','eloquent'),
        'path' => database_path ('runners/'),
        'path-allways' => database_path ('runners/allways/'),
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