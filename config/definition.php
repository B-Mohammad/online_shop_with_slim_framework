<?php

use App\Database;

return [
    Database::class => function () {
        return new Database(host: 'localhost', dbname: 'online_shop', user: 'root', pass: '');
    },
    
    'secret' => "8e4baba57ab5ebbdde59ff06c34df2fd260daf6d7fc1b8c0e4066ed0074a8606",
    'algorithm' => 'HS256',
];
