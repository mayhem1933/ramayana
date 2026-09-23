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
    <title>Ringkesan | Ramayana</title>
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
            max-width: 760px;
            margin: 14px 0 26px;
            color: var(--cream);
            font: 400 clamp(3rem, 7vw, 6.2rem)/.95 "Cormorant Garamond", Georgia, serif;
        }

        .lead {
            max-width: 760px;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.85;
        }

        .story {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 22px;
            margin-top: 42px;
        }

        article {
            padding: 32px;
            border: 1px solid var(--line);
            background: rgba(82, 39, 22, .58);
            box-shadow: 0 24px 50px rgba(17, 9, 6, .2);
        }

        h2 {
            margin: 0 0 18px;
            color: var(--gold);
            font: 600 2.2rem "Cormorant Garamond", Georgia, serif;
        }

        article p {
            color: var(--muted);
            line-height: 1.8;
        }

        .beats {
            display: grid;
            gap: 14px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .beats li {
            padding: 14px 16px;
            border-left: 2px solid var(--orange);
            color: var(--cream);
            line-height: 1.6;
            background: rgba(32, 16, 11, .3);
        }

        .content-section {
            margin-top: 42px;
        }

        .content-section>h2 {
            margin-bottom: 20px;
        }

        .character-list {
            display: grid;
            gap: 14px;
        }

        .character-card {
            padding: 22px;
            border-left: 3px solid var(--orange);
            background: rgba(82, 39, 22, .45);
        }

        .character-card h3 {
            margin: 0 0 8px;
            color: var(--gold);
            font: 600 1.55rem "Cormorant Garamond", Georgia, serif;
        }

        .character-card p {
            margin: 7px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .character-card strong {
            color: var(--cream);
        }

        .lesson-list {
            display: grid;
            gap: 14px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .lesson-list li {
            padding: 18px 20px;
            border: 1px solid var(--line);
            color: var(--muted);
            line-height: 1.75;
            background: rgba(32, 16, 11, .3);
        }

        .lesson-list strong {
            display: block;
            margin-bottom: 6px;
            color: var(--gold);
        }

        .social-box {
            padding: 24px;
            border: 1px solid var(--orange);
            background: rgba(154, 66, 34, .24);
        }

        .social-box p {
            margin: 0 0 14px;
            color: var(--muted);
            line-height: 1.7;
        }

        .social-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .social-links a {
            display: inline-block;
            padding: 11px 14px;
            color: var(--ink);
            background: var(--gold);
            font-size: .75rem;
            font-weight: 700;
            text-decoration: none;
        }

        .social-links a:hover {
            background: var(--cream);
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
                text-align: center;
                padding-top: 8px;
            }

            body {
                padding-top: 0;
                padding-bottom: 0;
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
                min-height: 44px;
                display: grid;
                place-items: center;
                padding: 5px;
                border-radius: 10px;
                color: var(--muted);
                font-size: .62rem;
                font-weight: 700;
                text-decoration: none;
            }

            .mobile-actions a.active,
            .mobile-actions a:hover {
                color: var(--ink);
                background: var(--gold);
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

            .links .admin-link {
                margin-left: 0;
                padding-left: 3px;
                border-left: 0;
            }

            .login-status {
                align-self: flex-end;
            }

            .story {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            main {
                width: calc(100% - 32px);
                padding: 56px 0 80px;
            }

            article {
                padding: 24px;
            }

            .character-card,
            .social-box {
                padding: 18px;
            }

            .social-links {
                display: grid;
            }

            .social-links a {
                text-align: center;
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
            <a href="home.php">Imah</a><a class="active" href="sinopsis.php">Ringkesan</a><a href="tokoh.php">Palaku</a><a href="beli_tiket.php">Tiket</a><a class="desktop-nav-link" href="divisi.php">Divisi</a><a class="desktop-nav-link" href="riwayat.php">Riwayat</a><a class="desktop-nav-link" href="akun.php">Akun</a><a class="desktop-nav-link admin-link" href="admin_scan.php">Pangurus</a>
        </div>
        <div class="login-status" title="Login sebagai <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Status login aktif">Online</div>
    </nav>
    <main>
        <div class="eyebrow">Sinopsis pertunjukan</div>
        <h1>Ruhak di Nagara Alengka.</h1>
        <p class="lead">Ruhak di Nagara Alengka ngaguar kisah Sarpakinaka, adi Prabu Rahwana. Ti rasa cinta anu ditolak tur tatu anu disanghareupan di Leuweung Dandaka, anjeunna melak winih amarah jeung dendam anu ngahuru Rahwana pikeun nyulik Dewi Sinta, pemicu pecahna perang ageung anu ngaruntuhkeun Karajaan Alengka.</p>
        <div class="story">
            <article>
                <h2>Gagasan utama</h2>
                <p>Kisah ieu dimimitian ku rasa cinta Sarpakinaka anu ditolak ku Rama. Tatu dina raga jeung manahna robah jadi amarah, tuluy nyurung Rahwana nyieun siasat pikeun nyulik Dewi Sinta.</p>
                <p>Penculikan éta jadi awal perang gedé antara Rama jeung pasukan Alengka. Di tengah konflik, kasatiaan, kawaspadaan, jeung kawani diuji ku akibat tina amarah jeung dendam.</p>
            </article>
            <article>
                <h2>Runtuyan kajadian</h2>
                <ol class="beats">
                    <li><strong>Leuweung Dandaka</strong><br>Sarpakinaka nepungan Rama sarta nyanghareupan panolakan.</li>
                    <li><strong>Winih dendam</strong><br>Rahwana kahuru ku carita ngeunaan kageulisan Sinta.</li>
                    <li><strong>Siasat Rahwana</strong><br>Maricha nyamar jadi kijang emas pikeun ngajauhkeun Rama jeung Laksmana.</li>
                    <li><strong>Perang Alengka</strong><br>Rama mingpin pasukan pikeun nyalametkeun Sinta.</li>
                </ol>
            </article>
        </div>
        <section class="content-section">
            <h2>Pangwanoh Palaku</h2>
            <div class="character-list">
                <article class="character-card">
                    <h3>1. Sang Sarpakinaka (Shurpanakha)</h3>
                    <p><strong>Peran:</strong> Raksasi ti Alengka, adi Prabu Rahwana.</p>
                    <p><strong>Karakter:</strong> Miboga kakuatan gaib pikeun ngarobah wujud, pinuh ku kahayang, teu tiasa nampi panolakan, tur sentimental.</p>
                    <p><strong>Deskripsi:</strong> Awal tina sagala musibah di Alengka. Rasa cinta anu ditolak ku Rama sarta tatu dina raga jeung manahna ngarobah kanyaahna jadi seuneu amarah jeung dendam.</p>
                </article>
                <article class="character-card">
                    <h3>2. Prabu Rahwana</h3>
                    <p><strong>Peran:</strong> Raja Karaton Alengka, lanceuk Sarpakinaka.</p>
                    <p><strong>Karakter:</strong> Wibawa, gampang kahuru amarah, resep ngajaga kahormatan diri jeung bangsa, sarta gampang kagoda.</p>
                    <p><strong>Deskripsi:</strong> Ngadéngé adu domba jeung carita ngeunaan kageulisan Dewi Sinta, Rahwana nyieun siasat jahat anu mawa Alengka kana perang gedé.</p>
                </article>
                <article class="character-card">
                    <h3>3. Raden Rama</h3>
                    <p><strong>Peran:</strong> Satria putra Prabu Dasarata ti Ayodya.</p>
                    <p><strong>Karakter:</strong> Bijaksana, satia, gagah berani, tur pohara ngajaga kahormatan bojona.</p>
                    <p><strong>Deskripsi:</strong> Nuju pangasingan di Leuweung Dandaka dipirig ku Sinta jeung Laksmana. Anjeunna nolak cinta Sarpakinaka kalayan hormat, tuluy mingpin pasukan ka Alengka pikeun nyalametkeun Sinta.</p>
                </article>
                <article class="character-card">
                    <h3>4. Dewi Sinta</h3>
                    <p><strong>Peran:</strong> Putri ti Ayodya, bojo Raden Rama.</p>
                    <p><strong>Karakter:</strong> Lembut, perhatian, miboga kageulisan luar biasa, tur satia.</p>
                    <p><strong>Deskripsi:</strong> Kusabab rasa karunya jeung kapolosanana, anjeunna kakeunaan ku jebakan Rahwana anu nyamar jadi aki-aki sepuh.</p>
                </article>
                <article class="character-card">
                    <h3>5. Raden Laksmana</h3>
                    <p><strong>Peran:</strong> Satria, adi Raden Rama.</p>
                    <p><strong>Karakter:</strong> Tegas, wani, satia, sarta waspada ngajaga kaamanan.</p>
                    <p><strong>Deskripsi:</strong> Anjeunna ngajaga Rama jeung Sinta, motong irung jeung ceuli Sarpakinaka nalika ngancam, sarta nyieun lingkaran gaib panyalindungan.</p>
                </article>
                <article class="character-card">
                    <h3>6. Resi Jatayu</h3>
                    <p><strong>Peran:</strong> Manuk sakti, mitra Raden Rama.</p>
                    <p><strong>Karakter:</strong> Setia, berani, tur pinuh ku tanggung jawab.</p>
                    <p><strong>Deskripsi:</strong> Nyoba ngahalangan Rahwana nalika nyulik Sinta. Sanajan gugur, anjeunna sempet ngadugikeun warta éta ka Rama.</p>
                </article>
                <article class="character-card">
                    <h3>7. Maricha (Kijang Emas)</h3>
                    <p><strong>Peran:</strong> Abdi jeung pengikut Rahwana.</p>
                    <p><strong>Karakter:</strong> Penurut, ahli nyamar.</p>
                    <p><strong>Deskripsi:</strong> Nyamar jadi kijang emas pikeun ngajauhkeun Rama jeung Laksmana ti Dewi Sinta.</p>
                </article>
            </div>
        </section>
        <section class="content-section">
            <h2>Amanat jeung Piwulang</h2>
            <ul class="lesson-list">
                <li><strong>Ulah ngalakonkeun amarah jeung dendam</strong>Tatu dina haté jeung rasa teu narima kana panolakan bisa robah jadi seuneu dendam. Dendam moal ngabéréskeun masalah, tapi kalah ngaduruk kahirupan sorangan jeung jalma di sakuriling urang.</li>
                <li><strong>Kaseimbangan dina nyiptakeun kaputusan</strong>Ulah gampang kahuru ku adu domba. Rahwana gampang amarah sabab ukur ngadéngékeun carita ti hiji pihak tanpa merhatikeun akibatna.</li>
                <li><strong>Kagagahan jeung kasatiaan kana kabeneran</strong>Rama, Laksmana, jeung Jatayu némbongkeun yén kasatiaan, kawaspadaan, jeung kahayang ngajaga batur kudu dijanggelekkeun sanajan nyanghareupan taruhan nyawa.</li>
            </ul>
        </section>
        <section class="content-section social-box">
            <h2>Patepang salajengna di média sosial!</h2>
            <p>Ulah dugi ka kantun warta ngeunaan prosés latihan, potrét di tukangeun panggung, jeung inpormasi tikét pementasan salajengna. Hayu tuturkeun jeung pantau akun média sosial resmi urang.</p>
            <div class="social-links"><a href="https://www.instagram.com/xii.frontier/" target="_blank" rel="noopener">Instagram</a><a href="https://www.tiktok.com/@11.frontier" target="_blank" rel="noopener">TikTok</a></div>
        </section>
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