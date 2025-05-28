<!DOCTYPE html>
<html>
<head>
    <title>{{ user.username }}'s Profile</title>
</head>
<body>
<div class="user-details">
    <div class="user-info">
        <h3 class="user-username">{{ user.username }}</h3>
        <span class="user-email">Email: {{ user.email }}</span>
        <span class="user-verified">
            {% if user.is_verified %}
                Verified: Yes
            {% else %}
                Verified: No
            {% endif %}
        </span>

        {% if user.books %}
        <div class="books-list">
            <h4>Books:</h4>
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
