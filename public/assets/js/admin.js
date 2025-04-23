document.addEventListener("DOMContentLoaded", () => {
    const editor = document.getElementById("editor");
    const fileNameInput = document.getElementById("fileName");
    const saveButton = document.getElementById("saveBtn");
    const cancelButton = document.getElementById("cancelBtn");
    const backButton = document.getElementById("backBtn");
    const statusDiv = document.getElementById("saveStatus");

    let originalFileName = fileNameInput ? fileNameInput.value : "";
    let originalContent = editor.value;

    cancelButton.addEventListener("click", () => {
        editor.value = originalContent;
        if (fileNameInput) {
            fileNameInput.value = originalFileName;
        }
    });

    backButton.addEventListener("click", () => {
        window.history.back();
    });

    saveButton.addEventListener("click", () => {
        const filePath = editor.getAttribute("data-file-path");
        const content = editor.value;
        const newName = fileNameInput ? fileNameInput.value : originalFileName;

        const url = '/bookstore/public/books/admin/update/' + encodeURIComponent(filePath);

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "content=" + encodeURIComponent(content) + "&name=" + encodeURIComponent(newName)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerText = "Файл успешно сохранен!";
                    statusDiv.style.color = "green";
                    originalContent = content;
                    originalFileName = newName;
                } else {
                    statusDiv.innerText = "Ошибка при сохранении: " + (data.error || "Неизвестная ошибка");
                    statusDiv.style.color = "red";
                }
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
                statusDiv.innerText = "Ошибка при выполнении запроса";
                statusDiv.style.color = "red";
            });
    });
});
