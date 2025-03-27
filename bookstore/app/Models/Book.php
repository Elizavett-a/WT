<?php
namespace App\Models;

class Book {
    public $id;
    public $title;
    public $author;
    public $cover;
    public $price;
    public $category_id;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'];
        $this->author = $data['author'];
        //$this->cover = $data['cover'] ?? 'noImage2.png';
        $this->price = $data['price'];
        //$this->category_id = $data['category_id'];
    }
}