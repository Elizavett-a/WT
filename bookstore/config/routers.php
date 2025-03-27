<?php
return [
    '/' => ['controller' => 'BookController', 'action' => 'home'],
    '/search' => ['controller' => 'BookController', 'action' => 'search'],
    '/category/{slug}' => ['controller' => 'BookController', 'action' => 'category']
];