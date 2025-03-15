<?php

use App\Database;

return [
    Database::class => function () {
        return new Database(host: 'localhost', dbname: 'online_shop', user: 'root', pass: '');
    }
];
