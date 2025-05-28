<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\EntityManager;
use App\Models\User;
use App\Models\Book;
use PDO;

class UserRepository {
    private EntityManager $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll(): array {
        $stmt = $this->em->query("SELECT * FROM users");
        $users = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($data);
        }
        return $users;
    }

    public function countAllUsers(): int {
        $stmt = $this->em->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    public function findById(int $id): ?User {
        $stmt = $this->em->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        $user = new User($data);

        $stmt = $this->em->query("
            SELECT b.* FROM books b
            JOIN user_books ub ON b.id = ub.book_id
            WHERE ub.user_id = :user_id
        ", ['user_id' => $id]);

        while ($bookData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user->addBook(new Book($bookData));
        }

        return $user;
    }

    public function findByUsername(string $username): ?User {
        $stmt = $this->em->query("SELECT * FROM users WHERE username = :username", ['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function save(User $user): void {
        $data = $user->toArray();

        if ($user->getId() === null) {
            unset($data['id']);
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO users ($columns) VALUES ($placeholders)";
            $stmt = $this->em->prepare($sql);
            $stmt->execute($data);

            $user->setId((int)$this->em->lastInsertId());
        } else {
            $updates = [];
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = :$key";
                }
            }

            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $this->em->prepare($sql);
            $stmt->execute($data);
        }
    }

    public function findByEmail(string $email): ?User {
        $stmt = $this->em->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function findByToken(string $token): ?User {
        $stmt = $this->em->query("SELECT * FROM users WHERE token = :token", ['token' => $token]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function delete(int $id): bool {
        $stmt = $this->em->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}