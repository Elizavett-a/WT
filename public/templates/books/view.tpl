<!DOCTYPE html>
<html>
<head>
    <title>{{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/styles_view.css">
    <!-- Подключаем иконки Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <!--<a href="/bookstore/public/books/delete/{{ book.id }}" class="delete-link" title="Удалить">
                <i class="fas fa-trash-alt"></i>
            </a>-->
            <a href="/bookstore/public/books" class="back-link">Вернуться к списку</a>
        </div>
    </div>
    <div style="clear: both;"></div>
</div>
</body>
</html>