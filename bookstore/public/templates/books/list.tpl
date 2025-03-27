<!DOCTYPE html>
<html>
<head>
    <title>Книги</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h1>Список книг из probook_db</h1>
<div class="books">
    {% for book in books %}
    <div class="book">
        <h2>{{ book.title }}</h2>
        <p>Автор: {{ book.author }}</p>
        <p>Цена: {{ book.price }} $</p>
        <a href="/books/{{ book.id }}">Подробнее</a>
    </div>
    {% endfor %}
</div>
</body>
</html>