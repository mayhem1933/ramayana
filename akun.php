<?php
session_start();

if (empty($_SESSION['user_email'])) {
    header('Location: Login.php');
    exit;
}

$email = (string) $_SESSION['user_email'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = trim($_POST['email'] ?? '');

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Lebetkeun alamat email anu leres.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_email'] = $newEmail;
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
    <title>Akun | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <style>
        :root {
            --ink: #24120d;
            --orange: #e28738;
            --gold: #f5c873;
            --cream: #fff4dc;
            --muted: #d9b996;
            --line: rgba(245, 200, 115, .28);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 30px 18px;
            color: var(--cream);
            background:
                radial-gradient(circle at 12% 8%, rgba(226, 135, 56, .22), transparent 28%),
                radial-gradient(circle at 88% 22%, rgba(245, 200, 115, .14), transparent 24%),
                linear-gradient(135deg, #24120d 0%, #4f2315 48%, #24120d 100%);
            font-family: "Manrope", Arial, sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            opacity: .12;
            background-image: radial-gradient(var(--gold) .7px, transparent .7px);
            background-size: 18px 18px;
        }

        .account-panel {
            width: min(500px, 100%);
            margin: auto;
            padding: 30px 28px 26px;
            border: 1px solid var(--line);
            background: rgba(84, 40, 22, .75);
            box-shadow: 0 28px 60px rgba(16, 8, 5, .22);
            backdrop-filter: blur(2px);
        }

        .account-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 16px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--line);
        }

        .account-nav a {
            color: var(--muted);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .account-nav a:hover,
        .account-nav .active {
            color: var(--gold);
        }

        .account-nav .logout {
            color: #efaa8d;
        }

        .account-nav .admin-link {
            margin-left: 8px;
            padding-left: 18px;
            border-left: 1px solid var(--line);
        }

        .label {
            color: var(--orange);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 10px;
            color: var(--cream);
            font: 400 3.2rem/1 "Cormorant Garamond", Georgia, serif;
            text-align: center;
        }

        p {
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.6;
            text-align: center;
        }

        label {
            display: block;
            margin: 24px 0 8px;
            color: var(--cream);
            font-size: .78rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 13px;
            color: var(--cream);
            background: #2e150c;
            border: 1px solid #9b5a36;
            border-radius: 10px;
            font: inherit;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245, 200, 115, .18);
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 14px;
            color: var(--ink);
            background: var(--orange);
            border: 0;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s ease, background .2s ease;
        }

        button:hover {
            background: var(--gold);
            transform: translateY(-1px);
        }

        .error {
            margin-top: 16px;
            color: #ffd0c8;
        }

        .back {
            display: inline-block;
            margin-top: 22px;
            color: var(--gold);
            font-size: .8rem;
            text-decoration: none;
        }

        .logout-button {
            display: block;
            margin-top: 18px;
            padding: 13px;
            color: #ffd0c8;
            border: 1px solid rgba(239, 170, 141, .45);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
        }

        .logout-button:hover {
            color: var(--ink);
            background: #efaa8d;
        }

        @media (max-width: 520px) {
            body {
                padding: 16px 10px;
            }

            .account-panel {
                padding: 22px 16px 20px;
            }

            .back {
                width: 100%;
                padding: 12px;
                border: 1px solid var(--line);
                text-align: center;
            }

            .account-nav {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 6px;
                padding: 6px;
            }

            .account-nav a {
                min-height: 36px;
                display: grid;
                place-items: center;
                padding: 6px 3px;
                font-size: .58rem;
                text-align: center;
            }

            .account-nav .admin-link {
                margin-left: 0;
                padding-left: 3px;
                border-left: 0;
            }
        }
    </style>
</head>

<body>
    <main class="account-panel">
        <nav class="account-nav" aria-label="Navigasi utama">
            <a href="home.php">Imah</a>
            <a href="sinopsis.php">Ringkesan</a>
            <a href="tokoh.php">Palaku</a>
            <a href="beli_tiket.php">Tiket</a>
        </nav>
        <div class="label">Menu akun</div>
        <h1>Ganti email</h1>
        <p>Email ayeuna: <strong><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <form method="POST" action="akun.php">
            <label for="email">Email anyar</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            <button type="submit">Simpen Email Anyar</button>
        </form>
        <a class="logout-button" href="logout.php">Kaluar tina akun</a>
        <?php if ($error !== ''): ?><div class="error" aria-live="polite"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <a class="back" href="home.php">&larr; Balik ka Imah</a>
    </main>
</body>

</html>