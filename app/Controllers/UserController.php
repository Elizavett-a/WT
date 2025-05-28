<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Services\MailService;
use App\TemplateEngine;

class UserController extends BaseController {
    private UserService $userService;
    private MailService $mailService;

    public function __construct(TemplateEngine $engine, UserService $userService, MailService $mailService) {
        parent::__construct($engine);
        $this->userService = $userService;
        $this->mailService = $mailService;
    }

    public function loginAction(): void {
        if (isset($_SESSION['user'])) {
            header('Location: /bookstore/public');
            exit;
        }

        if (isset($_COOKIE['remember_token'])) {
            $user = $this->userService->validateRememberToken($_COOKIE['remember_token']);
            if ($user) {
                $this->startUserSession($user);
                $this->mailService->sendLoginNotification($user->getEmail());
                header('Location: /bookstore/public');
                exit;
            }
        }

        $error = $_SESSION['error'] ?? [];
        $username = $_SESSION['username'] ?? '';
        unset($_SESSION['error'], $_SESSION['username']);

        $this->render('books/login.tpl', [
            'error' => $error,
            'username' => $username
        ]);
    }
    
public function profileAction(): void {
    if (!isset($_SESSION['user'])) {
        header('Location: /bookstore/public/login');
        exit;
    }

    try {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        
        if ($userId === 0) {
            throw new \RuntimeException('Не удалось определить ID пользователя');
        }

        $user = $this->userService->findById($userId);
        
        if ($user === null) {
            throw new \RuntimeException('Пользователь не найден');
        }

        // Форматируем даты, проверяя их тип
        $createdAt = $user->getCreatedAt();
        $lastLogin = $user->getLastLogin();

        $data = [
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'created_at' => $createdAt instanceof \DateTimeInterface 
                    ? $createdAt->format('Y-m-d H:i:s') 
                    : 'N/A',
                'last_login' => $lastLogin instanceof \DateTimeInterface 
                    ? $lastLogin->format('Y-m-d H:i:s') 
                    : 'Никогда',
                'is_verified' => $user->isVerified()
            ],
            'page_title' => 'Профиль пользователя ' . $user->getUsername(),
            'messages' => $this->getFlashMessages()
        ];

        echo $this->templateEngine->render('books/account.tpl', $data);

    } catch (\Exception $e) {
        error_log('Profile action error: ' . $e->getMessage());
        $this->setFlashMessage('error', 'Произошла ошибка при загрузке профиля');
        header('Location: /bookstore/public');
        exit;
    }
}

private function getFlashMessages(): array {
    $messages = [
        'success' => $_SESSION['flash_success'] ?? null,
        'error' => $_SESSION['flash_error'] ?? null
    ];
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    return $messages;
}

private function setFlashMessage(string $type, string $message): void {
    $_SESSION["flash_$type"] = $message;
}

    public function registerAction(): void {
        if (isset($_SESSION['user'])) {
            header('Location: /bookstore/public');
            exit;
        }

        $error = $_SESSION['error'] ?? [];
        unset($_SESSION['error']);

        $this->render('books/register.tpl', [
            'error' => $error
        ]);
    }

    public function loginPostAction(): void {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        $response = $this->userService->processLogin([
            'username' => $username,
            'password' => $password,
            'remember_me' => $rememberMe
        ]);

        if ($response['success']) {
            $this->startUserSession($response['data']['user'], $rememberMe);
            $this->mailService->sendLoginNotification($response['data']['user']->getEmail());
            header('Location: /bookstore/public');
            return;
        }

        $_SESSION['username'] = $username;
        $_SESSION['error'] = $response['message'];
        header('Location: /bookstore/public/login');
    }

    public function registerPostAction(): void {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['hashed_password'] ?? '';
        $salt = $_POST['salt'] ?? '';

        $response = $this->userService->processRegistration([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'captcha' => $_POST['g-recaptcha-response'] ?? ''
        ]);

        if ($response['success']) {
            $user = $this->userService->findByEmail($email);
            if ($user) {
                $this->mailService->sendVerificationEmail($user->getEmail(), $user->getToken());
                $_SESSION['flash_success'] = 'Регистрация успешна! Пожалуйста, проверьте ваш email для подтверждения.';
                header('Location: /bookstore/public/login');
                return;
            }
        }

        $_SESSION['error'] = $response['message'];
        header('Location: /bookstore/public/register');
    }

    public function verifyEmailAction(string $token): void {
        $user = $this->userService->validateVerificationToken($token);
        if ($user) {
            $user->setIsVerified(true);
            $user->setToken(null);
            $this->userService->save($user);
            $_SESSION['flash_success'] = 'Email успешно подтвержден! Теперь вы можете войти в систему.';
        } else {
            $_SESSION['flash_error'] = 'Неверная или устаревшая ссылка подтверждения.';
        }
        header('Location: /bookstore/public/login');
    }

    public function resendVerificationAction(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: /bookstore/public/login');
            return;
        }

        $user = $this->userService->findById((int)$_SESSION['user']['id']);
        if ($user && !$user->isVerified()) {
            $token = $user->getToken() ?? $this->userService->generateToken();
            $user->setToken($token);
            $this->userService->save($user);

            $this->mailService->sendVerificationEmail($user->getEmail(), $token);
            $_SESSION['flash_success'] = 'Письмо с подтверждением отправлено повторно.';
        }

        header('Location: /bookstore/public/profile');
    }

    public function logoutAction(): void {
        $userData = $_SESSION['user'] ?? null;
        if ($userData) {
            $user = $this->userService->findById($userData['id']);
            if ($user) {
                $user->setToken(null);
                $this->userService->save($user);
            }
        }

        session_unset();
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        header('Location: /bookstore/public/login');
    }

    private function startUserSession(User $user, bool $rememberMe = false): void {
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        ];

        if ($rememberMe) {
            $token = $this->userService->generateRememberToken($user);
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
        }
    }
}
