<?php
session_start();
require_once __DIR__ . '/config.php';

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Lebetkeun alamat email anu leres.';
    } else {
        $otp = (string) random_int(100000, 999999);
        $subject = 'Kode verifikasi login Ramayana';
        $body = "Kode verifikasi login Anda adalah: {$otp}\n\n" .
            "Kode ini berlaku selama 10 menit. Jika Anda tidak meminta kode ini, abaikan email ini.";

        $smtpError = '';
        $sent = sendEmailSmtp($email, $subject, $body, $smtpError);

        if ($sent) {
            $_SESSION['pending_email'] = $email;
            $_SESSION['login_otp'] = $otp;
            $_SESSION['login_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
            $_SESSION['login_otp_expires'] = time() + 600;
            unset($_SESSION['login_debug_otp'], $_SESSION['login_debug_email']);
            header('Location: verify_email.php');
            exit;
        }

        unset($_SESSION['pending_email'], $_SESSION['login_otp'], $_SESSION['login_otp_hash'], $_SESSION['login_otp_expires'], $_SESSION['login_debug_otp'], $_SESSION['login_debug_email']);
        error_log('Gagal mengirim OTP ke ' . $email . ': ' . $smtpError);
        $error = 'Kode teu tiasa dikirim ka email éta. Pastikeun alamatna leres, tuluy cobian deui. Upami tetep gagal, pariksa koneksi SMTP atanapi folder Spam.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAMAYANA: RUHAK DINAGARA ALENGKA</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <style>
        :root {
            --bg-1: #2a130d;
            --bg-2: #6e321e;
            --primary: #d86b2f;
            --primary-2: #f0a23a;
            --white: #fff8e9;
            --text: #f4dfbd;
            --muted: #c9a888;
            --line: rgba(242, 183, 112, 0.28);
            --success: #8dd28d;
            --shadow: 0 25px 70px rgba(39, 15, 7, 0.58);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at 12% 8%, rgba(235, 130, 44, .28), transparent 28%),
                radial-gradient(circle at 88% 22%, rgba(245, 200, 115, .14), transparent 24%),
                linear-gradient(135deg, #24120d 0%, #4f2315 48%, #24120d 100%);
            color: var(--text);
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .page-wrap {
            width: 100%;
            max-width: 1080px;
        }

        .auth-panel {
            display: flex;
            min-height: 680px;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(50, 22, 12, 0.72);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .hero {
            flex: 1.2;
            background:
                linear-gradient(135deg, rgba(99, 40, 22, 0.97), rgba(207, 88, 37, 0.9)),
                linear-gradient(160deg, #32150d, #8b431f);
            padding: 64px 52px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 22px;
        }

        .hero>* {
            position: relative;
            z-index: 1;
        }

        .badge {
            display: inline-block;
            width: fit-content;
            padding: 8px 14px;
            margin-bottom: 20px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.08);
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .hero h1 {
            margin: 0 0 18px;
            font-size: clamp(2.4rem, 4vw, 4rem);
            line-height: 1.1;
            letter-spacing: -0.06em;
            color: var(--white);
        }

        .hero p {
            margin: 0;
            max-width: 440px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.02rem;
            line-height: 1.8;
        }

        .feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .feature-item {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 42px;
            background: rgba(47, 22, 13, 0.82);
        }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .form-box h2 {
            margin: 0 0 8px;
            text-align: center;
            font-size: clamp(2rem, 2.8vw, 2.8rem);
            color: var(--white);
        }

        .subtitle {
            text-align: center;
            margin-bottom: 28px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .input-wrap {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            color: #f5dfbb;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        input {
            width: 100%;
            padding: 15px 16px;
            border-radius: 14px;
            border: 1px solid rgba(242, 183, 112, 0.28);
            background: rgba(38, 17, 10, 0.72);
            color: var(--white);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        select {
            width: 100%;
            padding: 15px 16px;
            border-radius: 14px;
            border: 1px solid rgba(242, 183, 112, 0.28);
            background: rgba(38, 17, 10, 0.72);
            color: var(--white);
            font: 1rem Georgia, "Times New Roman", serif;
            outline: none;
        }

        select:focus {
            border-color: rgba(240, 162, 58, 0.9);
            box-shadow: 0 0 0 4px rgba(240, 162, 58, 0.18);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        input::placeholder {
            color: rgba(148, 163, 184, 0.8);
        }

        input:focus {
            border-color: rgba(240, 162, 58, 0.9);
            box-shadow: 0 0 0 4px rgba(240, 162, 58, 0.18);
            transform: translateY(-1px);
        }

        button {
            width: 100%;
            margin-top: 6px;
            padding: 16px 18px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            color: var(--white);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(216, 107, 47, 0.34);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(109, 94, 252, 0.48);
            filter: brightness(1.03);
        }

        .result {
            margin-top: 28px;
            padding: 18px 16px;
            border-radius: 12px;
            background: rgba(38, 17, 10, 0.9);
            border: 1px solid rgba(242, 183, 112, 0.22);
            line-height: 1.8;
            font-size: 0.98rem;
        }

        .result strong {
            color: #fff;
        }

        .result.success {
            border-color: rgba(52, 211, 153, 0.5);
            color: #a7f3d0;
        }

        .result.error {
            border-color: rgba(248, 113, 113, 0.5);
            color: #fecaca;
        }

        .reservation-summary {
            display: grid;
            gap: 4px;
            margin-top: 14px;
            color: #f2d5ac;
            font-size: 0.9rem;
        }

        .reservation-summary strong {
            color: var(--primary-2);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 22px;
            color: var(--primary-2);
            font-size: 0.8rem;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--white);
            text-decoration: underline;
        }

        @media (max-width: 850px) {
            .auth-panel {
                flex-direction: column;
                min-height: auto;
            }

            .hero,
            .form-side {
                padding: 32px 22px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px;
            }

            .form-box h2 {
                font-size: 1.9rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrap">
        <div class="auth-panel">
            <div class="hero">
                <span class="badge">Pertunjukan Wayang</span>
                <h1>RAMAYANA:<br>RUHAK DINAGARA ALENGKA</h1>
                <p>Saksikan kisah agung Ramayana dalam pertunjukan wayang yang membawa legenda, keberanian, dan kebijaksanaan ke atas panggung.</p>
                <div class="feature-list">
                    <span class="feature-item">Seni Tradisi</span>
                    <span class="feature-item">Pintonan Langsung</span>
                    <span class="feature-item">Pesen Tiket</span>
                </div>
            </div>

            <div class="form-side">
                <div class="form-box">
                    <a class="back-link" href="home.php">&larr; Balik ka kaca utama</a>
                    <h2>Asup ku Email</h2>
                    <div class="subtitle">Lebetkeun email pikeun nampi e-tiket jeung barcode sanggeus meuli.</div>

                    <form method="POST" action="Login.php">
                        <div class="input-wrap">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" placeholder="nama@email.com" autocomplete="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <button type="submit">Teraskeun ka Pesenan</button>
                    </form>

                    <?php if ($error !== ''): ?>
                        <div class="result error" aria-live="polite"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>