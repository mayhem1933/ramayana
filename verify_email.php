<?php
session_start();
require_once __DIR__ . '/config.php';

$pendingEmail = (string) ($_SESSION['pending_email'] ?? '');
$error = '';

if ($pendingEmail === '' || empty($_SESSION['login_otp_expires'])) {
    header('Location: Login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $storedOtp = isset($_SESSION['login_otp']) ? (string) $_SESSION['login_otp'] : '';
    $storedHash = isset($_SESSION['login_otp_hash']) ? (string) $_SESSION['login_otp_hash'] : '';

    if (time() > (int) $_SESSION['login_otp_expires']) {
        $error = 'Kode geus kadaluwarsa. Mangga menta kode anyar.';
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $error = 'Kode verifikasi kudu 6 angka.';
    } elseif (!hash_equals($storedOtp, $otp) && !password_verify($otp, $storedHash)) {
        $error = 'Kode verifikasi lepat.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_email'] = $pendingEmail;
        call_user_func('recordLogin', $pendingEmail);
        unset($_SESSION['pending_email'], $_SESSION['login_otp'], $_SESSION['login_otp_hash'], $_SESSION['login_otp_expires'], $_SESSION['login_debug_otp'], $_SESSION['login_debug_email']);
        header('Location: riwayat.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <style>
        :root {
            --ink: #24120d;
            --brown: #522716;
            --orange: #e28738;
            --gold: #f5c873;
            --cream: #fff4dc;
            --muted: #d9b996;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--cream);
            background:
                radial-gradient(circle at 12% 8%, rgba(226, 135, 56, .22), transparent 28%),
                radial-gradient(circle at 88% 22%, rgba(245, 200, 115, .14), transparent 24%),
                linear-gradient(135deg, #24120d 0%, #4f2315 48%, #24120d 100%);
            font-family: Georgia, "Times New Roman", serif;
        }

        .panel {
            width: min(100%, 520px);
            padding: 48px;
            border: 1px solid rgba(245, 200, 115, .3);
            border-radius: 20px;
            background: rgba(82, 39, 22, .8);
            box-shadow: 0 25px 70px rgba(0, 0, 0, .35);
        }

        .label {
            color: var(--gold);
            font: 700 .75rem/1.2 Arial, sans-serif;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 12px;
            font-size: clamp(2rem, 7vw, 3.2rem);
        }

        p {
            color: var(--muted);
            line-height: 1.7;
        }

        strong {
            color: var(--cream);
            overflow-wrap: anywhere;
        }

        label {
            display: block;
            margin: 28px 0 8px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 1px solid #a96a3c;
            border-radius: 8px;
            color: var(--cream);
            background: #32170f;
            font: inherit;
            font-size: 1.4rem;
            letter-spacing: .3em;
            text-align: center;
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 15px;
            border: 0;
            border-radius: 8px;
            color: #32170f;
            background: var(--gold);
            cursor: pointer;
            font-weight: 700;
        }

        .error {
            margin-top: 18px;
            padding: 12px;
            border: 1px solid #d47d68;
            border-radius: 8px;
            color: #ffd5c8;
            font: .95rem Arial, sans-serif;
        }

        .back {
            display: inline-block;
            margin-top: 22px;
            color: var(--gold);
            font: .9rem Arial, sans-serif;
        }
    </style>
</head>

<body>
    <main class="panel">
        <div class="label">Verifikasi kaamanan</div>
        <h1>Pariksa email anjeun</h1>
        <p>Kami ngirim kode 6 digit ka <strong><?php echo htmlspecialchars($pendingEmail, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
        <form method="POST" action="verify_email.php">
            <label for="otp">Kode verifikasi</label>
            <input id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
            <button type="submit">Verifikasi jeung asup</button>
        </form>
        <?php if ($error !== ''): ?>
            <div class="error" aria-live="polite"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <a class="back" href="Login.php">&larr; Paké email séjén</a>
    </main>
</body>

</html>