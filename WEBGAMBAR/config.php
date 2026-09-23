<?php
const TICKET_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tickets.json';

function loadEnvFile(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

loadEnvFile(__DIR__ . DIRECTORY_SEPARATOR . '.env');

function envValue(string $name, string $fallback = ''): string
{
    $value = getenv($name);
    return $value === false || $value === '' ? $fallback : $value;
}

define('ADMIN_PASSWORD', envValue('RAMAYANA_ADMIN_PASSWORD'));
define('MAIL_FROM', envValue('RAMAYANA_MAIL_FROM'));

define('DB_HOST', envValue('RAMAYANA_DB_HOST', '127.0.0.1'));
define('DB_NAME', envValue('RAMAYANA_DB_NAME', 'ramayana_db'));
define('DB_USER', envValue('RAMAYANA_DB_USER', 'root'));
define('DB_PASSWORD', envValue('RAMAYANA_DB_PASSWORD'));

define('SMTP_HOST', envValue('RAMAYANA_SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', (int) envValue('RAMAYANA_SMTP_PORT', '587'));
define('SMTP_ENCRYPTION', envValue('RAMAYANA_SMTP_ENCRYPTION', 'tls'));
define('SMTP_USERNAME', envValue('RAMAYANA_SMTP_USERNAME'));
define('SMTP_PASSWORD', envValue('RAMAYANA_SMTP_PASSWORD'));
define('SMTP_FROM', envValue('RAMAYANA_SMTP_FROM', SMTP_USERNAME));

require_once __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';

function sendEmailSmtp(string $to, string $subject, string $body, ?string &$errorMessage = null): bool
{
    $errorMessage = '';
    $username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
    $password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
    $from = defined('SMTP_FROM') ? SMTP_FROM : ($username !== '' ? $username : MAIL_FROM);

    if ($username === '' || $password === '' || $username === 'your_email@gmail.com' || $password === 'your_16_char_app_password') {
        $errorMessage = 'Konfigurasi SMTP belum lengkap.';
        return false;
    }

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($from, 'Ramayana');
        $mail->addAddress($to);
        $mail->Subject = $subject;

        if (str_contains($body, '<') && str_contains($body, '>')) {
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
        } else {
            $mail->isHTML(false);
            $mail->Body = $body;
            $mail->AltBody = $body;
        }

        return $mail->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('PHPMailer error: ' . $e->getMessage());
        $errorMessage = $e->getMessage();
        return false;
    }
}

function ensureTicketStorage(): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    $pdo = db();
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS tickets (' .
            'id INT AUTO_INCREMENT PRIMARY KEY,' .
            'token VARCHAR(30) NOT NULL UNIQUE,' .
            'nama VARCHAR(100) NOT NULL,' .
            'kelas VARCHAR(20) NOT NULL,' .
            'jumlah_tiket INT NOT NULL,' .
            'waktu_reservasi VARCHAR(100) NOT NULL,' .
            'nomor_handphone VARCHAR(30) NOT NULL,' .
            'email VARCHAR(150) NOT NULL,' .
            'status VARCHAR(30) NOT NULL DEFAULT "belum digunakan",' .
            'dibuat_pada DATE NOT NULL,' .
            'dipindai_pada DATE NULL' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    $pdo->exec(
        'ALTER TABLE tickets ' .
            'MODIFY dibuat_pada DATE NOT NULL, ' .
            'MODIFY dipindai_pada DATE NULL'
    );

    migrateJsonTickets($pdo);
    $ready = true;
}

function ensureTicketQuota(): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    db()->exec(
        'CREATE TABLE IF NOT EXISTS ticket_quota (' .
            'id TINYINT UNSIGNED PRIMARY KEY,' .
            'max_tickets INT UNSIGNED NOT NULL DEFAULT 30,' .
            'sold_count INT UNSIGNED NOT NULL DEFAULT 0,' .
            'reset_at DATE NOT NULL DEFAULT (CURRENT_DATE)' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'ALTER TABLE ticket_quota MODIFY reset_at DATE NOT NULL DEFAULT (CURRENT_DATE)'
    );
    db()->exec(
        'INSERT IGNORE INTO ticket_quota (id, max_tickets, sold_count) VALUES (1, 30, 0)'
    );
    $ready = true;
}

function getTicketQuota(): array
{
    ensureTicketQuota();
    return db()->query(
        'SELECT max_tickets, sold_count, reset_at FROM ticket_quota WHERE id = 1'
    )->fetch();
}

function reserveTicketSlot(): bool
{
    ensureTicketQuota();
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $quota = $pdo->query(
            'SELECT max_tickets, sold_count FROM ticket_quota WHERE id = 1 FOR UPDATE'
        )->fetch();

        if ($quota === false || (int) $quota['sold_count'] >= (int) $quota['max_tickets']) {
            $pdo->rollBack();
            return false;
        }

        $statement = $pdo->prepare(
            'UPDATE ticket_quota SET sold_count = sold_count + 1 WHERE id = 1'
        );
        $statement->execute();
        $pdo->commit();
        return true;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function releaseTicketSlot(): void
{
    ensureTicketQuota();
    db()->exec(
        'UPDATE ticket_quota SET sold_count = GREATEST(sold_count - 1, 0) WHERE id = 1'
    );
}

function resetTicketQuota(): void
{
    ensureTicketQuota();
    db()->exec(
        'UPDATE ticket_quota SET sold_count = 0, reset_at = CURRENT_DATE WHERE id = 1'
    );
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    return $pdo;
}

function recordLogin(string $email): void
{
    try {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS login_logs (' .
                'id INT AUTO_INCREMENT PRIMARY KEY,' .
                'email VARCHAR(150) NOT NULL,' .
                'logged_in_at DATE NOT NULL DEFAULT (CURRENT_DATE)' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        db()->exec(
            'ALTER TABLE login_logs MODIFY logged_in_at DATE NOT NULL DEFAULT (CURRENT_DATE)'
        );

        $statement = db()->prepare(
            'INSERT INTO login_logs (email) VALUES (:email)'
        );
        $statement->execute(['email' => $email]);
    } catch (PDOException $exception) {
        error_log('Gagal menyimpan log login: ' . $exception->getMessage());
    }
}

function authenticateAdmin(string $password): bool
{
    try {
        $admins = db()->query(
            'SELECT password_hash FROM admins WHERE is_active = 1'
        )->fetchAll();

        foreach ($admins as $admin) {
            if (password_verify($password, $admin['password_hash'])) {
                return true;
            }
        }
    } catch (PDOException $exception) {
        error_log('Gagal memeriksa akun admin dari database: ' . $exception->getMessage());
    }

    return ADMIN_PASSWORD !== '' && hash_equals(ADMIN_PASSWORD, $password);
}

function migrateJsonTickets(PDO $pdo): void
{
    if (!file_exists(TICKET_FILE)) {
        return;
    }

    $tickets = json_decode(file_get_contents(TICKET_FILE), true);
    if (!is_array($tickets) || !$tickets) {
        return;
    }

    $statement = $pdo->prepare(
        'INSERT IGNORE INTO tickets ' .
            '(token, nama, kelas, jumlah_tiket, waktu_reservasi, nomor_handphone, email, status, dibuat_pada, dipindai_pada) ' .
            'VALUES (:token, :nama, :kelas, :jumlah_tiket, :waktu_reservasi, :nomor_handphone, :email, :status, :dibuat_pada, :dipindai_pada)'
    );

    foreach ($tickets as $ticket) {
        if (empty($ticket['token'])) {
            continue;
        }

        $statement->execute([
            ':token' => (string) $ticket['token'],
            ':nama' => (string) ($ticket['nama'] ?? ''),
            ':kelas' => (string) ($ticket['kelas'] ?? ''),
            ':jumlah_tiket' => (int) ($ticket['jumlah_tiket'] ?? 1),
            ':waktu_reservasi' => (string) ($ticket['waktu_reservasi'] ?? ''),
            ':nomor_handphone' => (string) ($ticket['nomor_handphone'] ?? ''),
            ':email' => (string) ($ticket['email'] ?? ''),
            ':status' => (string) ($ticket['status'] ?? 'belum digunakan'),
            ':dibuat_pada' => (string) ($ticket['dibuat_pada'] ?? date('Y-m-d')),
            ':dipindai_pada' => !empty($ticket['dipindai_pada']) ? (string) $ticket['dipindai_pada'] : null,
        ]);
    }
}

function readTickets(): array
{
    ensureTicketStorage();
    return db()->query(
        'SELECT token, nama, kelas, jumlah_tiket, waktu_reservasi, nomor_handphone, email, status, dibuat_pada, dipindai_pada ' .
            'FROM tickets ORDER BY dibuat_pada ASC, id ASC'
    )->fetchAll();
}

function writeTickets(array $tickets): void
{
    ensureTicketStorage();
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $pdo->exec('DELETE FROM tickets');
        $statement = $pdo->prepare(
            'INSERT INTO tickets ' .
                '(token, nama, kelas, jumlah_tiket, waktu_reservasi, nomor_handphone, email, status, dibuat_pada, dipindai_pada) ' .
                'VALUES (:token, :nama, :kelas, :jumlah_tiket, :waktu_reservasi, :nomor_handphone, :email, :status, :dibuat_pada, :dipindai_pada)'
        );

        foreach ($tickets as $ticket) {
            $statement->execute([
                ':token' => (string) ($ticket['token'] ?? ''),
                ':nama' => (string) ($ticket['nama'] ?? ''),
                ':kelas' => (string) ($ticket['kelas'] ?? ''),
                ':jumlah_tiket' => (int) ($ticket['jumlah_tiket'] ?? 1),
                ':waktu_reservasi' => (string) ($ticket['waktu_reservasi'] ?? ''),
                ':nomor_handphone' => (string) ($ticket['nomor_handphone'] ?? ''),
                ':email' => (string) ($ticket['email'] ?? ''),
                ':status' => (string) ($ticket['status'] ?? 'belum digunakan'),
                ':dibuat_pada' => (string) ($ticket['dibuat_pada'] ?? date('Y-m-d')),
                ':dipindai_pada' => !empty($ticket['dipindai_pada']) ? (string) $ticket['dipindai_pada'] : null,
            ]);
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function makeTicketToken(): string
{
    return 'RMA-' . strtoupper(bin2hex(random_bytes(5)));
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
