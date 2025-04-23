<!DOCTYPE html>
<html>
<head>
    <title>{{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/view.css">
</head>
<body>
<div class="book-details">
    <img src="/bookstore/public/assets/images/{{ book.cover }}"
         class="book-cover"
         onerror="this.onerror=null; this.src='/bookstore/public/assets/images/noImage2.png'">
    <div class="book-info">
        <h3 class="book-title">{{ book.title }}</h3>
        <span class="book-author">{{ book.author }}</span>
        <div class="price">{{ book.price }} $</div>
        <div class="action-buttons">
            <a href="/bookstore/public/books/edit/{{ book.id }}" class="edit-link">Редактировать</a>
            <a href="/bookstore/public/books" class="back-link">Вернуться к списку</a>
        </div>
    </div>
    <div style="clear: both;"></div>
</div>
</body>
</html>