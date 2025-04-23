document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            if (!confirm('Вы действительно хотите удалить этот элемент?')) return;
            const path = button.getAttribute('data-path');
            window.location.href = '/bookstore/public/books/admin/delete/' + encodeURIComponent(path);
        });
    });
});