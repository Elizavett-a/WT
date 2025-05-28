<!DOCTYPE html>
<html>
<head>
    <title>Профиль пользователя {{ user.username }}</title>
</head>
<body>
<div class="user-details">
    <div class="user-info">
        <h3 class="user-username">{{ user.username }}</h3>
        <span class="user-email">Email: {{ user.email }}</span>
        <span class="user-verified">
            {% if user.is_verified %}
                Почта подтверждена
            {% else %}
                Почта не подтверждена
            {% endif %}
        </span>

        {% if user.books %}
        <div class="books-list">
            <h4>Книги:</h4>
            {% for book in user.books %}
            <a href="/bookstore/public/books/view/{{ book.id }}" class="book-tag">{{ book.title }}</a>
            {% endfor %}
        </div>
        {% endif %}

        <div class="action-buttons">
        {% if user.is_verified %}
        {% else %}             <a href="/bookstore/public/users/send-verification" class="edit-link">Подтвердить email</a>
            {% endif %}
            <a href="/bookstore/public" class="back-link">Назад</a>
               <div class="action-buttons">
        </div>
    </div>
    <div class="clearfix"></div>
</div>
</body>
</html>
