<!DOCTYPE html>
<html>
<head>
    <title>Список книг</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/styles_list.css">
</head>
<body>
<header>
    <h1>Книжный магазин</h1>
</header>

<div class="book-section">
    {% for book in books %}
    <div class="book">
        <img src="/bookstore/public/assets/images/{{ book.cover }}"
             class="book-cover"
             onerror="this.onerror=null; this.src='/bookstore/public/assets/images/noImage2.png'">
        <h3 class="book-title">{{ book.title }}</h3>
        <span class="book-author">{{ book.author }}</span>
        <div class="price">{{ book.price }} $</div>
        <a href="/bookstore/public/books/view/{{ book.id }}" class="details-link">Подробнее</a>
    </div>
    {% endfor %}
</div>
<footer>
    <p>© 2025 Книжный магазин. Все права защищены.</p>
</footer>
</body>
</html>