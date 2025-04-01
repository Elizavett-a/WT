<!DOCTYPE html>
<html>
<head>
    <title>{{ book.title }}</title>
    <style>
        .book-details {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .book-cover {
            max-width: 300px;
            float: left;
            margin-right: 20px;
        }
        .book-info {
            overflow: hidden;
        }
    </style>
</head>
<body>
<div class="book-details">
    <img src="/assets/images/{{ book.cover }}" alt="{{ book.title }}" class="book-cover">
    <div class="book-info">
        <h1>{{ book.title }}</h1>
        <p><strong>Автор:</strong> {{ book.author }}</p>
        <p><strong>Цена:</strong> {{ book.price }} $</p>
        <a href="/books">← Вернуться к списку</a>
    </div>
    <div style="clear: both;"></div>
</div>
</body>
</html>