CREATE DATABASE IF NOT EXISTS probook_bd;
USE probook_bd;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица категорий
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица книг
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    cover VARCHAR(255) DEFAULT 'noImage2.png',
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_author (author)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица для связи между пользователями и книгами
CREATE TABLE IF NOT EXISTS user_books (
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица для связи между книгами и категориями
CREATE TABLE IF NOT EXISTS book_categories (
    book_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (book_id, category_id),
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_book (book_id),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Категории
INSERT INTO categories (name, description) VALUES
('Художественная литература', 'Романы, повести, рассказы'),
('Фантастика', 'Научная фантастика и фэнтези'),
('Детективы', 'Детективные романы и триллеры'),
('Бизнес-литература', 'Книги по бизнесу и финансам'),
('Саморазвитие', 'Книги по личностному росту');

-- Пользователи
INSERT INTO users (username, email, password_hash) VALUES
('ivanov', 'ivanov@example.com', '$2y$10$examplehash1'),
('petrova', 'petrova@example.com', '$2y$10$examplehash2'),
('sidorov', 'sidorov@example.com', '$2y$10$examplehash3');

-- Книги
INSERT INTO books (title, author, cover, price) VALUES
('Мастер и Маргарита', 'Михаил Булгаков', 'master.jpg', 1500.00),
('1984', 'Джордж Оруэлл', '1984.jpg', 1200.00),
('Игра престолов', 'Джордж Мартин', 'got.jpg', 1800.00),
('Богатый папа, бедный папа', 'Роберт Кийосаки', 'richdad.jpg', 2000.00),
('7 навыков высокоэффективных людей', 'Стивен Кови', '7habits.jpg', 1700.00);
