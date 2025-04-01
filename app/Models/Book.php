<?php
namespace App\Models;

class Book {
    private mixed $id;
    private mixed $title;
    private mixed $author;
    private mixed $cover;
    private mixed $price;
    private mixed $category_id;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->author = $data['author'] ?? '';
        $this->cover = $data['cover'] ?? 'noImage2.png';
        $this->price = $data['price'] ?? 0.0;
        $this->category_id = $data['category_id'] ?? null;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'cover' => $this->cover,
            'price' => $this->price,
            'category_id' => $this->category_id
        ];
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

    public function getCategoryId(): ?int {
        return $this->category_id;
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

    public function setCategoryId(?int $category_id): self {
        $this->category_id = $category_id;
        return $this;
    }
}