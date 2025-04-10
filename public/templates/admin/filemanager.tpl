<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <link rel="stylesheet" href="/bookstore/public/assets/css/styles_admin.css">
</head>
<body>
    <div class="container">
        <h1>File Manager</h1>
        
        <div class="breadcrumbs">
            <a href="/bookstore/public/admin">Home</a>
            {% set pathParts = currentPath|split('/') %}
            {% for part in pathParts %}
                {% if part %}
                    / <a href="/bookstore/public/admin?path={{ pathParts|slice(0, loop.index)|join('/') }}">{{ part }}</a>
                {% endif %}
            {% endfor %}
        </div>

        {% if isEditMode %}
            <form action="/bookstore/public/admin/edit" method="post">
                <input type="hidden" name="path" value="{{ currentPath }}">
                <textarea name="content" class="file-content">{{ fileContent }}</textarea>
                <div class="form-actions">
                    <button type="submit" class="btn">Save</button>
                    <a href="/bookstore/public/admin?path={{ currentPath|dirname }}" class="btn cancel">Cancel</a>
                </div>
            </form>
        {% else %}
            <div class="actions">
                <form action="/bookstore/public/admin/create-folder" method="post" class="inline-form">
                    <input type="hidden" name="path" value="{{ currentPath }}">
                    <input type="text" name="folder_name" placeholder="New folder name" required>
                    <button type="submit" class="btn">Create Folder</button>
                </form>

                <form action="/bookstore/public/admin/upload" method="post" enctype="multipart/form-data" class="inline-form">
                    <input type="hidden" name="path" value="{{ currentPath }}">
                    <input type="file" name="file" required>
                    <button type="submit" class="btn">Upload</button>
                </form>
            </div>

            <table class="file-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in items %}
                        <tr>
                            <td>
                                {% if item.is_dir %}
                                    📁 <a href="/bookstore/public/admin?path={{ item.path }}">{{ item.name }}</a>
                                {% else %}
                                    <a href="/bookstore/public/admin/view?path={{ item.path }}">{{ item.name }}</a>
                                {% endif %}
                            </td>
                            <td>
                                {% if not item.is_dir %}
                                    {{ item.size|format_bytes }}
                                {% else %}
                                    -
                                {% endif %}
                            </td>
                            <td>{{ item.modified }}</td>
                            <td class="actions">
                                {% if not item.is_dir %}
                                    <a href="/bookstore/public/admin/view?path={{ item.path }}" class="btn">View</a>
                                {% endif %}
                                <a href="/bookstore/public/admin/delete?path={{ item.path }}" class="btn danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        {% endif %}
    </div>
</body>
</html>