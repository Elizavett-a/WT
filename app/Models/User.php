<?php
declare(strict_types=1);

namespace App\Models;

class User {
    private ?int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private ?string $token;
    private string $salt;
    private ?string $lastLogin;
    private string $createdAt;
    private string $updatedAt;
    private bool $isVerified;
    private array $books = [];

    public function __construct(array $data = []) {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->username = (string)($data['username'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->passwordHash = (string)($data['password_hash'] ?? '');
        $this->token = isset($data['token']) ? (string)$data['token'] : null;
        $this->salt = (string)($data['salt'] ?? '');
        $this->lastLogin = isset($data['lastlogin']) ? (string)$data['lastlogin'] : null;
        $this->createdAt = (string)($data['created_at'] ?? '');
        $this->updatedAt = (string)($data['updated_at'] ?? '');
        $this->isVerified = isset($data['is_verified']) && (bool)$data['is_verified'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'token' => $this->token,
            'salt' => $this->salt,
            'lastlogin' => $this->lastLogin,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'is_verified' => $this->isVerified ? 1 : 0
        ];
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function getSalt(): string {
        return $this->salt;
    }

    public function getLastLogin(): ?string {
        return $this->lastLogin;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    public function getBooks(): array {
        return $this->books;
    }

    public function isVerified(): bool {
        return $this->isVerified;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function setPasswordHash(string $passwordHash): self {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function setToken(?string $token): self {
        $this->token = $token;
        return $this;
    }

    public function setSalt(string $salt): self {
        $this->salt = $salt;
        return $this;
    }

    public function setLastLogin(?string $lastLogin): self {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function setCreatedAt(string $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(string $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setIsVerified(bool $isVerified): self {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function addBook(Book $book): void {
        $this->books[] = $book;
    }

    public function clearBooks(): void {
        $this->books = [];
    }
}