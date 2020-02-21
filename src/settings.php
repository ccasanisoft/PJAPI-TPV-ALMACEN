<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
         'determineRouteBeforeAppMiddleware' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],


        // Database connection settings
        "db" => [
            "host" => "localhost",
            "dbname" => "api",
            "user" => "root",
            "pass" => ""
        ],


        "peticion"=>[
            "header" => header('Content-Type: application/json')
            
        ]



    ],
];
