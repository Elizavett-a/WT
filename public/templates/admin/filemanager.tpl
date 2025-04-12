<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Файловый менеджер{% endblock %}</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
</head>
<body>
<div class="container">
    {% block content %}
    <div class="filemanager-container mt-4">
        <h2>{{ title | default('Файловый менеджер') }}</h2>

        {# Панель навигации #}
        <div class="filemanager-nav mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    {% if parent_path %}
                    <a href="?path={{ parent_path | url_encode }}" class="btn btn-outline-secondary">
                        <i class="fa fa-level-up"></i> На уровень выше
                    </a>
                    {% endif %}
                </div>

                <form method="get" class="form-inline">
                    <input type="hidden" name="path" value="{{ current_path | default('') }}">
                    <div class="input-group">
                        <input type="text" name="new_folder" class="form-control" placeholder="Имя новой папки">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-folder"></i> Создать
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {# Сообщения об ошибках/успехе #}
        {% if message %}
        <div class="alert alert-{{ message_type | default('info') }} alert-dismissible fade show">
            {{ message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {% endif %}

        {# Таблица с файлами #}
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th>Имя</th>
                            <th class="text-right">Размер</th>
                            <th>Изменен</th>
                            <th class="text-center">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in items | default([]) %}
                        <tr>
                            <td>
                                {% if item.is_dir %}
                                <i class="fa fa-folder text-warning"></i>
                                <a href="?path={{ item.path | url_encode }}">{{ item.name }}</a>
                                {% else %}
                                <i class="fa fa-file-{{ item.icon | default('o') }} text-muted"></i>
                                {{ item.name }}
                                {% endif %}
                            </td>
                            <td class="text-right">
                                {% if not item.is_dir %}
                                {{ item.size | default('0') | filesizeformat }}
                                {% else %}
                                -
                                {% endif %}
                            </td>
                            <td>{{ item.modified | date('d.m.Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    {% unless item.is_dir %}
                                    <a href="?download={{ item.path | url_encode }}" class="btn btn-outline-primary" title="Скачать">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    {% endunless %}

                                    <button class="btn btn-outline-danger delete-btn"
                                            data-path="{{ item.path | url_encode }}"
                                            data-name="{{ item.name }}"
                                            title="Удалить">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {% else %}
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Папка пуста</td>
                        </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {# Форма загрузки файлов #}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Загрузить файлы</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="path" value="{{ current_path | default('') }}">
                    {% csrf %}

                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" name="files[]" multiple class="custom-file-input" id="fileInput">
                            <label class="custom-file-label" for="fileInput">Выберите файлы</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-upload"></i> Загрузить
                    </button>
                </form>
            </div>
        </div>
    </div>

    {# Модальное окно подтверждения удаления #}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Подтверждение удаления</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Вы действительно хотите удалить <strong id="deleteFileName"></strong>?</p>
                    <p class="text-danger"><small>Это действие нельзя отменить.</small></p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="?delete" id="deleteForm">
                        <input type="hidden" name="path" id="deleteFilePath">
                        {% csrf %}

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {% endblock %}

    {% block scripts %}
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Обработка клика по кнопке удаления
            $('.delete-btn').click(function(e) {
                e.preventDefault();

                var path = $(this).data('path');
                var name = $(this).data('name');

                $('#deleteFileName').text(name);
                $('#deleteFilePath').val(path);
                $('#deleteModal').modal('show');
            });

            // Обновление label при выборе файлов
            $('.custom-file-input').on('change', function() {
                var files = $(this)[0].files;
                var label = $(this).next('.custom-file-label');;

                if (files.length > 1) {
                    label.text('Выбрано файлов: ' + files.length);
                } else if (files.length === 1) {
                    label.text(files[0].name);
                } else {
                    label.text('Выберите файлы');
                }
            });
        });
    </script>
    {% endblock %}
</div>
</body>
</html>