<?php
namespace App\Models;

class Category {
    public $id;
    public $name;
    public $slug;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
    }
}