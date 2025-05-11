<!DOCTYPE html>
<html>
<head>
    <title>Список книг</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/list.css">
    <style>
        .categories-container {
            margin: 8px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .category-tag {
            background: #e0e0e0;
            color: #333;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
        .debug-info {
            color: #999;
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
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