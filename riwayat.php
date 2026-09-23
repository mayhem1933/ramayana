<?php
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['user_email'])) {
    header('Location: Login.php');
    exit;
}

$userEmail = (string) $_SESSION['user_email'];
$userTickets = array_values(array_filter(readTickets(), static function (array $ticket) use ($userEmail): bool {
    return strcasecmp((string) ($ticket['email'] ?? ''), $userEmail) === 0;
}));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesenan | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
            opacity: .14;
            pointer-events: none;
            background-image: radial-gradient(var(--gold) .7px, transparent .7px);
            background-size: 17px 17px;
        }

        .nav {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px 24px;
            width: min(1120px, calc(100% - 48px));
            margin: auto;
            padding: 24px 0;
            border-bottom: 1px solid var(--line);
            background: rgba(36, 18, 13, .94);
            backdrop-filter: blur(12px);
        }

        .brand {
            color: var(--gold);
            font: 700 .78rem Arial, sans-serif;
            letter-spacing: .16em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px 22px;
            padding: 8px 0;
        }

        .links a {
            position: relative;
            color: var(--muted);
            font: 600 .72rem Arial, sans-serif;
            letter-spacing: .08em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .links a:hover,
        .links .active {
            color: var(--gold);
        }

        .links .active::after {
            position: absolute;
            right: 0;
            bottom: -8px;
            left: 0;
            height: 2px;
            content: "";
            background: var(--orange);
        }

        .login-status {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            padding: 6px 9px;
            border: 1px solid rgba(141, 210, 141, .25);
            border-radius: 999px;
            color: #a7f3d0;
            font: 700 .58rem Arial, sans-serif;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .login-status::before {
            content: "";
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 50%;
            background: #8dd28d;
            box-shadow: 0 0 0 4px rgba(141, 210, 141, .15);
        }

        main {
            width: min(1120px, calc(100% - 48px));
            margin: auto;
            padding: 72px 0 120px;
        }

        .eyebrow {
            color: var(--orange);
            font: 700 .72rem Arial, sans-serif;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 12px;
            color: var(--cream);
            font: 400 clamp(3rem, 7vw, 6.2rem)/.95 "Cormorant Garamond", Georgia, serif;
        }

        .lead {
            max-width: 650px;
            color: var(--muted);
            line-height: 1.8;
        }

        .history-list {
            display: grid;
            gap: 22px;
            margin-top: 40px;
        }

        .ticket-card {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 30px;
            padding: 30px;
            border: 1px solid var(--line);
            background: rgba(82, 39, 22, .58);
            box-shadow: 0 24px 50px rgba(17, 9, 6, .2);
        }

        .ticket-card h2 {
            margin: 0 0 20px;
            color: var(--gold);
            font: 600 2.2rem "Cormorant Garamond", Georgia, serif;
        }

        .ticket-data {
            display: grid;
            gap: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        .ticket-data strong {
            color: var(--cream);
        }

        .qr-box {
            display: grid;
            justify-items: center;
            gap: 12px;
            color: var(--orange);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-align: center;
            text-transform: uppercase;
        }

        .qr-box>div {
            padding: 10px;
            background: #fff;
        }

        .status {
            display: inline-block;
            margin-top: 20px;
            padding: 7px 10px;
            border: 1px solid var(--line);
            color: var(--gold);
            font-size: .72rem;
            text-transform: uppercase;
        }

        .empty {
            margin-top: 40px;
            padding: 30px;
            border: 1px dashed var(--line);
            color: var(--muted);
        }

        .footer {
            padding: 22px;
            color: #b88762;
            background: #1b0d08;
            font: .7rem Arial, sans-serif;
            letter-spacing: .12em;
            text-align: center;
            text-transform: uppercase;
        }

        .mobile-actions {
            position: fixed;
            right: 24px;
            top: 22px;
            bottom: auto;
            left: auto;
            z-index: 20;
            display: none;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 5px;
            width: min(520px, calc(100% - 48px));
            padding: 6px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(36, 18, 13, .94);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .28);
            backdrop-filter: blur(14px);
        }

        .mobile-actions a {
            min-height: 42px;
            display: grid;
            place-items: center;
            padding: 8px 12px;
            border-radius: 10px;
            color: var(--muted);
            font: 700 .62rem Arial, sans-serif;
            letter-spacing: .08em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .mobile-actions a.active,
        .mobile-actions a:hover {
            color: var(--ink);
            background: var(--gold);
        }

        @media (max-width: 760px) {
            .nav {
                position: sticky;
                top: 0;
                z-index: 20;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 10px;
                width: min(1120px, calc(100% - 24px));
                margin: 10px auto 0;
                padding: 14px 0;
                border-bottom-color: rgba(245, 200, 115, .18);
                border-radius: 16px 16px 0 0;
                background: rgba(36, 18, 13, .94);
                box-shadow: 0 14px 32px rgba(0, 0, 0, .28);
                backdrop-filter: blur(14px);
            }

            .brand {
                width: 100%;
                padding-top: 8px;
                text-align: center;
            }

            .login-status {
                align-self: flex-end;
            }

            .links {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                width: 100%;
                gap: 5px;
                padding: 5px;
                background: rgba(36, 18, 13, .82);
                border: 1px solid var(--line);
                border-radius: 12px;
            }

            .links a {
                display: grid;
                min-height: 40px;
                place-items: center;
                padding: 6px 2px;
                border-radius: 8px;
                font-size: .62rem;
                text-align: center;
            }

            .links a:hover,
            .links .active {
                background: rgba(245, 200, 115, .12);
            }

            .mobile-actions {
                position: fixed;
                right: 12px;
                bottom: 12px;
                left: 12px;
                top: auto;
                z-index: 20;
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 5px;
                padding: 5px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: rgba(36, 18, 13, .94);
                box-shadow: 0 14px 32px rgba(0, 0, 0, .28);
                backdrop-filter: blur(14px);
            }

            .mobile-actions a {
                display: grid;
                min-height: 44px;
                place-items: center;
                padding: 5px;
                border-radius: 10px;
                color: var(--muted);
                font-size: .62rem;
                font-weight: 700;
                text-align: center;
                text-decoration: none;
            }

            .mobile-actions a.active,
            .mobile-actions a:hover {
                color: var(--ink);
                background: var(--gold);
            }
        }

        @media (max-width: 560px) {
            main {
                width: calc(100% - 32px);
                padding: 56px 0 100px;
            }

            .ticket-card {
                grid-template-columns: 1fr;
                padding: 22px;
            }

            .qr-box {
                justify-items: start;
                text-align: left;
            }
        }

        @media (max-width: 760px) {
            .nav {
                width: calc(100% - 20px);
                margin-top: 10px;
                padding: 12px;
                gap: 8px;
                border: 1px solid rgba(245, 200, 115, .22);
                border-radius: 18px;
            }

            .brand {
                padding-top: 2px;
                font-size: .78rem;
            }

            .links {
                display: flex;
                justify-content: flex-start;
                gap: 6px;
                width: 100%;
                padding: 4px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .links::-webkit-scrollbar {
                display: none;
            }

            .links a {
                flex: 0 0 auto;
                min-height: 38px;
                padding: 9px 13px;
                border: 1px solid rgba(245, 200, 115, .18);
                border-radius: 10px;
                font-size: .62rem;
                white-space: nowrap;
            }

            .links .active {
                color: var(--ink);
                background: var(--gold);
                border-color: var(--gold);
            }

            .login-status {
                align-self: flex-end;
            }
        }
    </style>
</head>

<body>
    <nav class="nav" aria-label="Navigasi utama">
        <a class="brand" href="home.php">Ramayana</a>
        <div class="links">
            <a href="home.php">Imah</a>
            <a href="sinopsis.php">Ringkesan</a>
            <a href="tokoh.php">Palaku</a>
            <a href="beli_tiket.php">Tiket</a>
            <a class="desktop-nav-link" href="divisi.php">Divisi</a>
            <a class="desktop-nav-link active" href="riwayat.php">Riwayat</a>
            <a class="desktop-nav-link" href="akun.php">Akun</a>
            <a class="desktop-nav-link admin-link" href="admin_scan.php">Pangurus</a>
        </div>
        <div class="login-status" title="Login sebagai <?php echo escape($userEmail); ?>" aria-label="Status login aktif">Online</div>
    </nav>
    <main>
        <div class="eyebrow">Pesenan tiket</div>
        <h1>Riwayat pesenan.</h1>
        <p class="lead">Barcode pesenan anjeun disimpen di dieu. Tunjukkeun QR ieu ka pangurus nalika sumping ka pintonan.</p>
        <?php if (!$userTickets): ?>
            <div class="empty">Can aya riwayat pesenan. Mangga pesen tiket heula.</div>
        <?php else: ?>
            <div class="history-list">
                <?php foreach (array_reverse($userTickets) as $index => $ticket): ?>
                    <?php $qrId = 'qrcode-' . $index; ?>
                    <article class="ticket-card">
                        <div>
                            <h2>E-Tiket Ramayana</h2>
                            <div class="ticket-data">
                                <div><strong>Kode:</strong> <?php echo escape((string) ($ticket['token'] ?? '')); ?></div>
                                <div><strong>Nama:</strong> <?php echo escape((string) ($ticket['nama'] ?? '')); ?></div>
                                <div><strong>Kelas:</strong> <?php echo escape((string) ($ticket['kelas'] ?? '')); ?></div>
                                <div><strong>Sesi:</strong> <?php echo escape((string) ($ticket['waktu_reservasi'] ?? '')); ?></div>
                                <div><strong>Dibuat:</strong> <?php echo escape((string) ($ticket['dibuat_pada'] ?? '')); ?></div>
                            </div>
                            <div class="status">Status: <?php echo escape((string) ($ticket['status'] ?? 'belum digunakan')); ?></div>
                        </div>
                        <div class="qr-box">
                            <div id="<?php echo escape($qrId); ?>"></div>
                            <span>Scan barcode tiket</span>
                        </div>
                        <script>
                            new QRCode(document.getElementById(<?php echo json_encode($qrId); ?>), {
                                text: <?php echo json_encode((string) ($ticket['token'] ?? '')); ?>,
                                width: 220,
                                height: 220,
                                colorDark: '#24120d',
                                colorLight: '#ffffff'
                            });
                        </script>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <nav class="mobile-actions" aria-label="Menu bawah">
        <a href="divisi.php">Divisi</a>
        <a class="active" href="riwayat.php">Riwayat</a>
        <a href="akun.php">Akun</a>
        <a href="admin_scan.php">Pangurus</a>
    </nav>
    <footer class="footer">RAMAYANA: RUHAK DINAGARA ALENGKA &middot; Seni tradisi, cerita yang abadi</footer>
</body>

</html>