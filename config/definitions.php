<?php

use App\Database;

ini_set('display_errors', 'on');

return [

    Database::class => function() {

        return new Database(host: $_ENV['DB_HOST'],
                            name: $_ENV['DB_NAME'],
                            user: $_ENV['DB_USER'],
                            password: $_ENV['DB_PASS']);
    }
];
