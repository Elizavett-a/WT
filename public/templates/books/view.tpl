<!DOCTYPE html>
<html>
<head>
    <title>{{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/view.css">
    <style>
        .categories-list {
            margin: 20px 0;
            padding: 0;
        }
        .categories-list h4 {
            margin-bottom: 10px;
            color: #555;
        }
        .category-tag {
            display: inline-block;
            background-color: #f0f0f0;
            color: #333;
            padding: 5px 10px;
            margin-right: 8px;
            margin-bottom: 8px;
            border-radius: 15px;
            font-size: 14px;
            text-decoration: none;
        }
        .category-tag:hover {
            background-color: #e0e0e0;
        }
    </style>
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