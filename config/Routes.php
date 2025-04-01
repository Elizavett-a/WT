<?php
return [
    '/' => [
        'controller' => 'BookController',
        'action' => 'listAction'
    ],
    '/books' => [
        'controller' => 'BookController',
        'action' => 'listAction'
    ],
    '/books/view/{id}' => [
        'controller' => 'BookController',
        'action' => 'viewAction'
    ]
];