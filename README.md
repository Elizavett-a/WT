# Website Probook

A robust MVC-based web application for managing a book catalog, user accounts, and book assignments. The project includes a user-friendly frontend, a secure admin panel with file management capabilities, and a MySQL database for dynamic data handling. Built with PHP, it follows the MVC architectural pattern, incorporating routers, controllers, models, services, and a templating engine, with HTTP Basic Authentication for admin access.

## Features

- Frontend: Browse books, view categories, and assign books to users, with data dynamically loaded from a MySQL database.

- Admin Panel: Accessible at /admin, secured with HTTP Basic Authentication (via .htaccess and .htpasswd). Provides a file manager for uploading, downloading, previewing, editing, and deleting files (images, CSS, HTML templates, and JS) in designated directories.

- Database: MySQL database with tables for users, books, categories, and many-to-many relationships (user_books, book_categories). Includes SQL migration scripts for schema creation and test data.

- Authentication: User login and registration forms with validation, "remember me" functionality, and CAPTCHA integration.

- MVC Architecture: Structured with routers, controllers, models, repositories, and services. Uses a templating engine for rendering views, with hardcoded data in services (no DB dependency in early stages).

- CRUD Operations: Full Create, Read, Update, Delete functionality for users, books, and categories via repositories.
