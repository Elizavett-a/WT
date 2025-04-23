<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/bookstore/public/assets/css/fileview.css">
    <title>Просмотр файла</title>
</head>
<body>
<div class="file-view-container">
    <h1>Просмотр файла: {{ filePath|escape }}</h1>
    <button id="backBtn">← Назад</button>

    <form method="post">
        <textarea id="editor" data-file-path="{{ filePath|escape }}" name="content" rows="120" cols="80">{{ content|escape }}</textarea>
        <div class="action-buttons">
            <button id="saveBtn" type="button">Сохранить изменения</button>
            <button id="cancelBtn" type="button">Отмена изменений</button>
        </div>
    </form>
</div>
<script src="/bookstore/public/assets/js/admin.js"></script>
</body>
</html>
