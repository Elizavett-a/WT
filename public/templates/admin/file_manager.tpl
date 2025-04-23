<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/bookstore/public/assets/css/admin.css">
    <title>Файловый менеджер</title>
</head>
<body>
<div class="file-manager-container" data-current-dir="{{ currentDir }}">
    <h1>Файловый менеджер</h1>

    {% if currentDir != '' %}
    <p>Текущая директория: {{ currentDir }}</p>
    {% else %}
    <p>Корневая директория</p>
    {% endif %}

    <ul class="file-list">
        {% for file in files %}
        <li>
            <a href="/bookstore/public/admin/view/{{ file.path }}">
                {{ file.name }}
            </a>
            ({{ file.size }}, изменён: {{ file.modified }})
            <button type="button" class="btn delete-btn" data-path="{{ file.path }}">
                Удалить
            </button>
        </li>
        {% endfor %}
    </ul>

    <div class="action-buttons">
        <a class="btn create-file" href="/bookstore/public/admin/create-file/{{ currentDir }}">Создать файл</a>
        <a class="btn create-directory" href="/bookstore/public/admin/create-directory/{{ currentDir }}">Создать директорию</a>
    </div>
</div>
<script src="/bookstore/public/assets/js/create.js"></script>
<script src="/bookstore/public/assets/js/delete.js"></script>
</body>
</html>
