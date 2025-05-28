<?php
declare(strict_types=1);

namespace App\Models;

class Book {
    private ?int $id;
    private string $title;
    private string $author;
    private string $cover;
    private float $price;
    private string $createdAt;
    private string $updatedAt;

    /** @var Category[] */
    private array $categories = [];

    /** @var User[] */
    private array $users = [];

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = (string)($data['title'] ?? '');
        $this->author = (string)($data['author'] ?? '');
        $this->cover = (string)($data['cover'] ?? 'noImage2.png');
        $this->price = isset($data['price']) ? (float)$data['price'] : 0.0;
        $this->createdAt = (string)($data['created_at'] ?? date('Y-m-d H:i:s'));
        $this->updatedAt = (string)($data['updated_at'] ?? date('Y-m-d H:i:s'));
    }

    public function toArray(): array {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'cover' => $this->cover,
            'price' => $this->price,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];

        if (!empty($this->categories)) {
            $data['categories'] = array_map(fn($c) => $c->getId(), $this->categories);
        }

        if (!empty($this->users)) {
            $data['users'] = array_map(fn($u) => $u->getId(), $this->users);
        }

        return $data;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getCover(): string {
        return $this->cover;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    /** @return Category[] */
    public function getCategories(): array {
        return $this->categories;
    }

    /** @return User[] */
    public function getUsers(): array {
        return $this->users;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function setAuthor(string $author): self {
        $this->author = $author;
        return $this;
    }

    public function setCover(string $cover): self {
        $this->cover = $cover;
        return $this;
    }

    public function setPrice(float $price): self {
        $this->price = $price;
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

    public function addCategory(Category $category): void {
        $this->categories[] = $category;
    }

    public function addUser(User $user): void {
        $this->users[] = $user;
    }

    public function clearCategories(): void {
        $this->categories = [];
    }

    public function clearUsers(): void {
        $this->users = [];
    }
}