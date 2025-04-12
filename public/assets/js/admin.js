function showCreateFolderModal() {
    document.getElementById('createFolderModal').style.display = 'block';
}

function showUploadModal() {
    document.getElementById('uploadModal').style.display = 'block';
}

function showEditModal(filePath) {
    fetch('/bookstore/public/admin/file-content?path=' + encodeURIComponent(filePath))
        .then(response => response.text())
        .then(content => {
            document.getElementById('editFilePath').value = filePath;
            document.getElementById('fileContent').value = content;
            document.getElementById('editModal').style.display = 'block';
        });
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDelete(path, isDir) {
    if (confirm(`Are you sure you want to delete ${isDir ? 'folder' : 'file'} "${path.split('/').pop()}"?`)) {
        fetch('/bookstore/public/admin/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ path: path })
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Error deleting file');
            }
        });
    }
}

// Handle form submissions
document.getElementById('createFolderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/bookstore/public/admin/create-folder', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Error creating folder');
        }
    });
});

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/bookstore/public/admin/upload', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Error uploading file');
        }
    });
});

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const filePath = document.getElementById('editFilePath').value;
    const content = document.getElementById('fileContent').value;

    fetch('/bookstore/public/admin/save-file', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            path: filePath,
            content: content
        })
    }).then(response => {
        if (response.ok) {
            hideModal('editModal');
        } else {
            alert('Error saving file');;
        }
    });
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}