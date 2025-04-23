document.addEventListener('DOMContentLoaded', function () {
    const createFileBtn = document.querySelector('.create-file');
    const createDirectoryBtn = document.querySelector('.create-directory');
    const fileManagerContainer = document.querySelector('.file-manager-container');

    const currentDir = fileManagerContainer.dataset.currentDir || '';

    function createFileOnServer(name) {
        const data = new URLSearchParams();
        data.append('name', name);

        const url = '/bookstore/public/books/admin/create-file/' + encodeURIComponent(currentDir);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data,
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Файл успешно создан");
                    window.location.reload();
                } else {
                    alert("Ошибка создания файла: " + (result.error || "Неизвестная ошибка"));
                }
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
                alert("Ошибка при выполнении запроса");
            });
        window.location.reload();
    }

    function createDirectoryOnServer(name) {
        const data = new URLSearchParams();
        data.append('name', name);

        const url = '/bookstore/public/books/admin/create-directory';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data,
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Директория успешно создана");
                    window.location.reload();
                } else {
                    alert("Ошибка создания директории: " + (result.error || "Неизвестная ошибка"));
                }
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
                alert("Ошибка при выполнении запроса");
            });
        window.location.reload();
    }

    createFileBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const fileName = prompt("Введите имя нового файла:");
        if (!fileName) return;

        createFileOnServer(fileName);
    });

    createDirectoryBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const dirName = prompt("Введите имя новой директории:");
        if (!dirName) return;

        createDirectoryOnServer(dirName);
    });
});
