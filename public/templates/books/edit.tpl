<!DOCTYPE html>
<html>
<head>
    <title>Редактировать {{ book.title }}</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/edit.css">
    <style>
        .categories-section {
            margin: 15px 0;
        }
        .category-tag {
            display: inline-block;
            background: #e0e0e0;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 15px;
            font-size: 14px;
        }
        .category-select {
            margin-top: 10px;
        }
        .current-categories {
            margin: 10px 0;
        }
    </style>
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

        <div class="form-group categories-section">
            <label>Категории</label>

            <div class="current-categories">
                {% if book.categories %}
                <p>Текущие категории:</p>
                {% for category in categories %}
                <span class="category-tag">{{ category.name }} </span>
                {% endfor %}
                {% else %}
                <p>Нет назначенных категорий</p>
                {% endif %}
            </div>

            <div class="category-select">
                <label for="categories">Добавить категории:</label>
                <select id="categories" name="categories[]" multiple class="form-control">
                    {% for category in all_categories %}
                    <option value="{{ category.id }}"
                            {% if book.categories and category.id in book.categories %}selected{% endif %}>
                        {{ category.name }}
                    </option>
                    {% endfor %}
                </select>
                <small>Удерживайте Ctrl/Cmd для выбора нескольких категорий</small>
            </div>

            <div class="new-category" style="margin-top: 10px;">
                <label for="new_category">Или создать новую:</label>
                <input type="text" id="new_category" name="new_category" class="form-control" placeholder="Название новой категории">
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