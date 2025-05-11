<?php
declare(strict_types=1);

namespace App\Models;

class User {
    private ?int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private array $books = [];
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $data = []) {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->username = (string)($data['username'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->passwordHash = (string)($data['password_hash'] ?? '');
        $this->createdAt = (string)($data['created_at'] ?? '');
        $this->updatedAt = (string)($data['updated_at'] ?? '');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
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

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    public function getBooks(): array {
        return $this->books;
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

    public function setCreatedAt(string $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(string $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function addBook(Book $book): void {
        $this->books[] = $book;
    }

    public function clearBooks(): void {
        $this->books = [];
    }
}