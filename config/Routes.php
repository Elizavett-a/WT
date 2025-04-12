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
    ],
    '/books/edit/{id}' => [
        'controller' => 'BookController',
        'action' => 'editAction'
    ],
    '/books/update/{id}' => [
        'controller' => 'BookController',
        'action' => 'updateAction'
    ],
    '/admin' => [
        'controller' => 'AdminController',
        'action' => 'handleRequest'
    ],
    '/admin/{action}' => [
        'controller' => 'AdminController',
        'action' => 'handleRequest'
    ],
];