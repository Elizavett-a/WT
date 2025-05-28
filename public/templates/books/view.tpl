<!DOCTYPE html>
<html>
<head>
    <title>{{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/assets/css/view.css">
</head>
<body>
<div class="book-details">
    <img src="/bookstore/assets/images/{{ book.cover }}"
         class="book-cover"
         onerror="this.onerror=null; this.src='/bookstore/assets/images/noImage2.png'">
    <div class="book-info">
        <h3 class="book-title">{{ book.title }}</h3>
        <span class="book-author">{{ book.author }}</span>
        <div class="price">{{ book.price }} $</div>

        {% if book.categories %}
        <div class="categories-list">
            <h4>Категории:</h4>
            {% for category in categories %}
            <span class="category-tag">{{ category.name }}</span>
            {% endfor %}
        </div>
        {% endif %}

        <div class="action-buttons">
            <a href="/bookstore/public/books/edit/{{ book.id }}" class="edit-link">Редактировать</a>
            <a href="/bookstore/public/books" class="back-link">Вернуться к списку</a>
        </div>
    </div>
    <div style="clear: both;"></div>
</div>
</body>
</html>