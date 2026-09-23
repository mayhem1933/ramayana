<?php
session_start();

if (empty($_SESSION['user_email'])) {
    header('Location: Login.php');
    exit;
}

$userEmail = (string) $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palaku | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #24120d;
            --brown: #522716;
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

        .links .admin-link {
            margin-left: 8px;
            padding-left: 18px;
            border-left: 1px solid var(--line);
        }

        .login-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            padding: 7px 10px;
            border: 1px solid rgba(141, 210, 141, .24);
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
            max-width: 760px;
            margin: 14px 0 18px;
            color: var(--cream);
            font: 400 clamp(3rem, 7vw, 6.2rem)/.95 "Cormorant Garamond", Georgia, serif;
        }

        .lead {
            max-width: 760px;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.85;
        }

        .character-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-top: 42px;
        }

        .character-card {
            padding: 32px;
            border: 1px solid var(--line);
            background: rgba(82, 39, 22, .58);
            box-shadow: 0 24px 50px rgba(17, 9, 6, .2);
        }

        .character-card h2 {
            margin: 0 0 14px;
            color: var(--gold);
            font: 600 2rem "Cormorant Garamond", Georgia, serif;
        }

        .character-card p {
            margin: 10px 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .character-card strong {
            color: var(--cream);
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

        .footer {
            padding: 26px 16px 46px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            text-align: center;
            letter-spacing: .08em;
            text-transform: uppercase;
            font: 700 .68rem Arial, sans-serif;
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
                border-radius: 16px 16px 0 0;
                background: rgba(36, 18, 13, .94);
                box-shadow: 0 14px 32px rgba(0, 0, 0, .28);
                backdrop-filter: blur(14px);
            }

            .brand {
                width: 100%;
                text-align: center;
                padding-top: 8px;
            }

            .login-status {
                align-self: flex-end;
                margin-left: auto;
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
                min-height: 40px;
                display: grid;
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
                gap: 8px;
                width: auto;
                margin: 0;
                padding: 14px 10px 18px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: rgba(36, 18, 13, .94);
                box-shadow: 0 14px 32px rgba(0, 0, 0, .28);
                backdrop-filter: blur(14px);
            }

            .mobile-actions a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 8px;
                border: 1px solid var(--line);
                color: var(--muted);
                text-decoration: none;
                font: 700 .62rem Arial, sans-serif;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .mobile-actions a.active {
                color: var(--gold);
                border-color: rgba(245, 200, 115, .4);
            }

            .character-grid {
                grid-template-columns: 1fr;
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
            <a class="active" href="tokoh.php">Palaku</a>
            <a href="beli_tiket.php">Tiket</a>
            <a class="desktop-nav-link" href="divisi.php">Divisi</a>
            <a class="desktop-nav-link" href="riwayat.php">Riwayat</a>
            <a class="desktop-nav-link" href="akun.php">Akun</a>
            <a class="desktop-nav-link admin-link" href="admin_scan.php">Pangurus</a>
        </div>
        <div class="login-status" title="Login sebagai <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Status login aktif">Online</div>
    </nav>

    <main>
        <div class="eyebrow">Para palaku</div>
        <h1>Rupa-rupa dina ieu carita.</h1>
        <p class="lead">Unggal palaku dina Ramayana mawa tatu, harepan, jeung kaputusan anu ngawangun jalan carita. Di balik kahormatan, dendam, jeung kasatiaan, aya sisi manusa anu hirup dina konflik anu teu kungsi leungit.</p>

        <div class="character-grid">
            <article class="character-card">
                <h2>1. Sang Sarpakinaka</h2>
                <p><strong>Peran:</strong> Raksasi dari Alengka, adik Prabu Rahwana.</p>
                <p><strong>Karakter:</strong> Mempunyai kekuatan gaib untuk berubah wujud, penuh hasrat, tidak menerima penolakan, serta sentimental.</p>
                <p><strong>Deskripsi:</strong> Awal dari semua musibah di Alengka. Rasa cinta yang ditolak oleh Rama berubah menjadi luka dan dendam yang menggiring tragedi besar.</p>
            </article>

            <article class="character-card">
                <h2>2. Prabu Rahwana</h2>
                <p><strong>Peran:</strong> Raja Kerajaan Alengka, kakak Sarpakinaka.</p>
                <p><strong>Karakter:</strong> Berwibawa, mudah marah, menjaga kehormatan diri dan bangsa, serta mudah tergoda.</p>
                <p><strong>Deskripsi:</strong> Ketika mendengar kisah Dewi Sinta, Rahwana membangun strategi jahat yang membawa Alengka ke dalam perang yang mengubah sejarah.</p>
            </article>

            <article class="character-card">
                <h2>3. Raden Rama</h2>
                <p><strong>Peran:</strong> Pangeran dan satria dari Ayodhya.</p>
                <p><strong>Karakter:</strong> Bijaksana, setia, gagah berani, dan sangat menjaga kehormatan istrinya.</p>
                <p><strong>Deskripsi:</strong> Rama berjalan dalam pengasingan bersama Sinta dan Laksmana, lalu memimpin pasukan untuk menuntut kebenaran dan menegakkan dharma.</p>
            </article>

            <article class="character-card">
                <h2>4. Dewi Sinta</h2>
                <p><strong>Peran:</strong> Putri Ayodhya dan istri Raden Rama.</p>
                <p><strong>Karakter:</strong> Lembut, perhatian, memiliki kecantikan luar biasa, dan setia.</p>
                <p><strong>Deskripsi:</strong> Karena sifatnya yang lembut dan penuh ketulusan, ia terjebak dalam tipu daya Rahwana yang menjadikannya pusat konflik besar.</p>
            </article>

            <article class="character-card">
                <h2>5. Raden Laksmana</h2>
                <p><strong>Peran:</strong> Adik Raden Rama.</p>
                <p><strong>Karakter:</strong> Tegas, berani, setia, dan waspada menjaga keamanan.</p>
                <p><strong>Deskripsi:</strong> Laksmana senantiasa menjaga Rama dan Sinta, serta menghadapi ancaman dari Sarpakinaka dengan sikap tegas dan penuh tanggung jawab.</p>
            </article>

            <article class="character-card">
                <h2>6. Resi Jatayu</h2>
                <p><strong>Peran:</strong> Burung sakti yang menjadi pendukung Rama.</p>
                <p><strong>Karakter:</strong> Setia, berani, dan penuh tanggung jawab.</p>
                <p><strong>Deskripsi:</strong> Jatayu mencoba menghentikan Rahwana saat menculik Sinta. Walau gugur, ia memberikan kabar penting kepada Rama dan menjadi simbol keberanian.</p>
            </article>

            <article class="character-card">
                <h2>7. Maricha</h2>
                <p><strong>Peran:</strong> Pengikut Rahwana dan pelaku penyamaran.</p>
                <p><strong>Karakter:</strong> Patuh dan ahli berkamuflase.</p>
                <p><strong>Deskripsi:</strong> Maricha berubah wujud menjadi kijang emas untuk memisahkan Rama dan Laksmana dari Sinta, memicu langkah besar menuju perang.</p>
            </article>
        </div>
    </main>

    <nav class="mobile-actions" aria-label="Menu bawah">
        <a href="divisi.php">Divisi</a>
        <a href="riwayat.php">Riwayat</a>
        <a href="akun.php">Akun</a>
        <a href="admin_scan.php">Pangurus</a>
    </nav>

    <footer class="footer">RAMAYANA: RUHAK DINAGARA ALENGKA &middot; Seni tradisi, cerita yang abadi</footer>
</body>

</html>