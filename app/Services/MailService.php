<?php
declare(strict_types=1);

namespace App\Services;

class MailService {
    private array $smtpConfig;

    public function __construct() {
        $this->loadConfig();
    }

    private function loadConfig(): void {
        $config = parse_ini_file(__DIR__ . '/../../config/smtp.ini', true);
        if ($config === false) {
            throw new \RuntimeException('Failed to load SMTP configuration file.');
        }
        $this->smtpConfig = $config;
    }

    public function sendEmail(string $to, string $subject, string $template, array $data = []): bool {
        $headers = [
            'From' => "{$this->smtpConfig['smtp']['from_name']} <{$this->smtpConfig['smtp']['from_email']}>",
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8'
        ];

        $body = $this->renderTemplate($template, $data);

        if ($this->smtpConfig['smtp']['host'] === 'localhost') {
            $this->logEmail($to, $subject, $body);
            return true;
        }

        return mail($to, $subject, $body, $headers);
    }

    private function renderTemplate(string $template, array $data): string {
        $templatePath = __DIR__ . "/../../templates/emails/{$template}.html";
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Email template {$template} not found");
        }

        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    private function logEmail(string $to, string $subject, string $body): void {
        $logMessage = "To: $to\nSubject: $subject\n\n$body\n\n";
        file_put_contents(__DIR__ . '/../../email.log', $logMessage, FILE_APPEND);
    }

    public function sendVerificationEmail(string $email, string $token): void {
        $this->sendEmail(
            $email,
            'Подтверждение email',
            'verification',
            ['verification_link' => "http://localhost/bookstore/public/verify-email/$token"]
        );
    }

    public function sendLoginNotification(string $email): void {
        $this->sendEmail(
            $email,
            'Новый вход в аккаунт',
            'login_notification',
            ['login_time' => date('Y-m-d H:i:s')]
        );
    }
}