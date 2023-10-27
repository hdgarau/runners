<?php
    return [
        'default' => env('RUNNER_CONNECTION','mysql'),
        'path' => [ database_path ('runners') ],
        'table' => 'runners'
    ];