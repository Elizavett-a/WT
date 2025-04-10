<!DOCTYPE html>
<html>
<head>
    <title>Редактировать {{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/styles_edit.css">
</head>
<body>
<div class="edit-container">
    <h2 class="edit-title">Редактировать книгу</h2>

    <form action="/bookstore/public/books/update/{{ book.id }}" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Название</label>
            <input type="text" id="title" name="title" value="{{ book.title }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="author">Автор</label>
            <input type="text" id="author" name="author" value="{{ book.author }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="price">Цена ($)</label>
            <input type="number" step="0.01" id="price" name="price" value="{{ book.price }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="cover">Обложка</label>
            <input type="file" id="cover" name="cover" class="form-control">
            <div class="current-cover">
                <p>Текущая обложка:</p>
                <img src="/bookstore/public/assets/images/{{ book.cover }}"
                     onerror="this.onerror=null; this.src='/bookstore/public/assets/images/noImage2.png'">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-save">Сохранить</button>
            <a href="/bookstore/public/books/view/{{ book.id }}" class="btn btn-cancel">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>