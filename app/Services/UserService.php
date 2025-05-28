<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

public function processLogin(array $input): array {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $rememberMe = $input['remember_me'] ?? false;

    $user = $this->userRepository->findByUsername($username);
    if (!$user) {
        return $this->errorResponse('Invalid credentials');
    }

    if (!$user->getPasswordHash()) { 
        return $this->errorResponse('Account setup incomplete. Please reset your password.');
    }

    if (!$this->verifyPassword($password, $user->getPasswordHash(), $user->getSalt())) {
        return $this->errorResponse('Invalid credentials');
    }

    if (!$user->isVerified()) {
        return $this->errorResponse('Please verify your email before logging in');
    }

    $this->updateLastLogin($user);

    return $this->successResponse('Login successful', ['user' => $user]);
}

    public function processRegistration(array $input): array {
        if (!$this->validateRegistrationInput($input)) {
            return $this->errorResponse('All fields are required');
        }

        try {
            $user = $this->registerUser([
                'username' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'salt' => $input['salt']
            ]);
            return $this->successResponse('Registration successful', ['user' => $user]);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
    }

    private function validateLoginInput(array $input): bool {
        return !empty($input['username']) && !empty($input['password']);
    }

    private function validateRegistrationInput(array $input): bool {
        return !empty($input['name'])
            && !empty($input['email'])
            && !empty($input['password'])
            && !empty($input['salt']);
    }

    private function errorResponse(string $message, int $code = 400): array {
        return ['success' => false, 'code' => $code, 'message' => $message];
    }

    private function successResponse(string $message, array $data = []): array {
        return ['success' => true, 'code' => 200, 'message' => $message, 'data' => $data];
    }

    public function authenticate(string $username, string $password): ?User {
        $user = $this->userRepository->findByUsername($username);

        if ($user && $this->verifyPassword($password, $user->getPasswordHash(), $user->getSalt())) {
            return $user;
        }

        return null;
    }

    public function registerUser(array $userData): User {
        if ($this->userRepository->findByEmail($userData['email'])) {
            throw new \RuntimeException("User with this email already exists");
        }

        $passwordHash = $this->hashPassword($userData['password'], $userData['salt']);

        $user = new User([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password_hash' => $passwordHash,
            'salt' => $userData['salt'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'is_verified' => false,
            'token' => $this->generateToken()
        ]);

        $this->userRepository->save($user);
        return $user;
    }

    public function generateRememberToken(User $user): string {
        $token = $this->generateToken();
        $user->setToken($token);
        $this->userRepository->save($user);
        return $token;
    }

    public function validateRememberToken(string $token): ?User {
        $user = $this->userRepository->findByToken($token);
        if ($user) {
            $newToken = $this->generateRememberToken($user);
            setcookie('remember_token', $newToken, time() + 60*60*24*30, '/', '', false, true);
            return $user;
        }
        return null;
    }

    public function validateVerificationToken(string $token): ?User {
        return $this->userRepository->findByToken($token);
    }

    public function generateToken(): string {
        return hash('sha256',
            microtime(true) .
            (string)mt_rand() .
            $this->userRepository->countAllUsers() .
            (string)memory_get_usage()
        );
    }

    public function findById(int $id): ?User {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email): ?User {
        return $this->userRepository->findByEmail($email);
    }

    public function save(User $user): void {
        $this->userRepository->save($user);
    }

    private function hashPassword(string $password, string $salt): string {
        return hash('sha256', $password . $salt);
    }

	private function verifyPassword(string $password, string $hash, string $salt): bool {
	    $computedHash = hash('sha256', $password . $salt);
	    return $computedHash === $hash;
	}
}
