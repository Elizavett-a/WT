<?php
declare(strict_types=1);

namespace Config;

class Routes {
    public static function getRoutes(): array {
        return [
            '/' => [
                'controller' => 'BookController',
                'action' => 'listAction'
            ],
            '/books' => [
                'controller' => 'BookController',
                'action' => 'listAction'
            ],
            '/login' => [
                'controller' => 'UserController',
                'action' => 'loginAction'
            ],
            '/register' => [
                'controller' => 'UserController',
                'action' => 'registerAction'
            ],
            '/login/post' => [
                'controller' => 'UserController',
                'action' => 'loginPostAction'
            ],
            '/register/post' => [
                'controller' => 'UserController',
                'action' => 'registerPostAction'
            ],
            '/logout' => [
                'controller' => 'UserController',
                'action' => 'logoutAction'
            ],
	        '/users/profile' => [
                'controller' => 'UserController',
                'action' => 'profileAction'
            ],
            '/users/send-verification' => [
                'controller' => 'UserController',
                'action' => 'verifyEmailAction'
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
            '/books/admin' => [
                'controller' => 'AdminController',
                'action' => 'indexAction'
            ],
            '/books/admin/files' => [
                'controller' => 'AdminController',
                'action' => 'fileViewAction'
            ],
            '/books/admin/view/{path}' => [
                'controller' => 'AdminController',
                'action' => 'fileViewAction'
            ],
            '/books/admin/create-file' => [
                'controller' => 'AdminController',
                'action' => 'createFileAction'
            ],
            '/books/admin/create-directory' => [
                'controller' => 'AdminController',
                'action' => 'createDirectoryAction'
            ],
            '/admin/delete/{path}' => [
                'controller' => 'AdminController',
                'action' => 'deleteAction'
            ],
            '/books/admin/update' => [
                'controller' => 'AdminController',
                'action' => 'updateAction'
            ]
        ];
    }
}
