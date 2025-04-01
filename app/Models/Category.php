<?php
namespace App\Models;

class Category {
    private mixed $id;
    private mixed $name;
    private mixed $slug;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Category
     */
    public function setId(mixed $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): mixed
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Category
     */
    public function setName(mixed $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug(): mixed
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return Category
     */
    public function setSlug(mixed $slug): static
    {
        $this->slug = $slug;
        return $this;
    }
}