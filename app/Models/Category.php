<?php
declare(strict_types=1);

namespace App\Models;

class Category {
    private ?int $id;
    private string $name;
    private ?string $description;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $data = []) {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->name = (string)($data['name'] ?? '');
        $this->description = isset($data['description']) ? (string)$data['description'] : null;
        $this->createdAt = (string)($data['created_at'] ?? date('Y-m-d H:i:s'));
        $this->updatedAt = (string)($data['updated_at'] ?? date('Y-m-d H:i:s'));
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
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
}