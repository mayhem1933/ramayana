<?php
session_start();
$userEmail = !empty($_SESSION['user_email']) ? (string) $_SESSION['user_email'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imah | RAMAYANA: RUHAK DINAGARA ALENGKA</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=12">
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

        html {
            scroll-behavior: smooth;
        }

        body {
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
            animation: drift 18s linear infinite;
        }

        .nav {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px 20px;
            width: min(1120px, calc(100% - 48px));
            margin: auto;
            padding: 24px 0;
            border-bottom: 1px solid var(--line);
        }

        .brand {
            color: var(--gold);
            font: 700 .78rem Arial, sans-serif;
            letter-spacing: .16em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            gap: 10px 22px;
        }

        .links a {
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

        .links .logout {
            color: #efaa8d;
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

        .hero {
            min-height: 700px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(90deg, rgba(36, 18, 13, .98) 25%, rgba(36, 18, 13, .66)), radial-gradient(circle at 78% 42%, rgba(226, 135, 56, .55), transparent 25%), linear-gradient(135deg, #29140d, #7c321b);
            border-bottom: 1px solid var(--line);
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 430px;
            height: 430px;
            right: 8%;
            bottom: -220px;
            border: 1px solid rgba(245, 200, 115, .5);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgba(245, 200, 115, .06), 0 0 0 56px rgba(245, 200, 115, .05);
            animation: pulse 7s ease-in-out infinite;
        }

        .puppet-stage {
            position: absolute;
            right: 4%;
            bottom: 0;
            z-index: 1;
            width: min(42vw, 500px);
            min-width: 330px;
            color: var(--gold);
            opacity: .78;
            pointer-events: none;
        }

        .puppet-image {
            position: absolute;
            right: 7%;
            bottom: 18px;
            z-index: 1;
            width: min(38vw, 480px);
            max-height: 88%;
            object-fit: contain;
            object-position: bottom center;
            filter: drop-shadow(0 24px 28px rgba(0, 0, 0, .3));
            scale: -1 1;
            animation: puppet-sway 5s ease-in-out infinite;
        }

        .puppet-stage::after {
            position: absolute;
            right: 4%;
            bottom: 18px;
            left: 4%;
            height: 1px;
            content: "";
            background: rgba(245, 200, 115, .55);
            box-shadow: 0 0 30px rgba(245, 200, 115, .55);
        }

        .puppet {
            transform-box: fill-box;
            transform-origin: center bottom;
        }

        .puppet-one {
            animation: puppet-sway 5s ease-in-out infinite;
        }

        .puppet-two {
            animation: puppet-sway 5s 1.4s ease-in-out infinite reverse;
        }

        .puppet-arm {
            transform-box: fill-box;
            transform-origin: top center;
            animation: puppet-arm 3.6s ease-in-out infinite;
        }

        .puppet-two .puppet-arm {
            animation-delay: 1s;
        }

        .inner {
            width: min(1120px, calc(100% - 48px));
            margin: auto;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
        }

        .eyebrow {
            margin-bottom: 22px;
            color: var(--gold);
            font: 700 .75rem Arial, sans-serif;
            letter-spacing: .2em;
            text-transform: uppercase;
            animation: up .8s both;
        }

        h1,
        h2,
        h3 {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-weight: 400;
        }

        h1 {
            margin: 0;
            font-size: clamp(3.8rem, 8vw, 7.8rem);
            line-height: .88;
            letter-spacing: -.03em;
            text-transform: uppercase;
            animation: up 1s .1s both;
        }

        .lead {
            max-width: 570px;
            margin: 30px 0 0;
            color: var(--muted);
            font-size: 1.08rem;
            line-height: 1.8;
            animation: up .9s .25s both;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 34px;
            animation: up .9s .4s both;
        }

        .button {
            display: inline-block;
            padding: 15px 22px;
            color: var(--ink);
            background: var(--orange);
            font: 700 .76rem Arial, sans-serif;
            letter-spacing: .1em;
            text-decoration: none;
            text-transform: uppercase;
            transition: transform .2s, background .2s;
        }

        .button:hover {
            color: var(--ink);
            background: var(--gold);
            transform: translateY(-3px);
        }

        .button.outline {
            color: var(--gold);
            background: transparent;
            border: 1px solid var(--line);
        }

        .button.outline:hover {
            color: var(--ink);
            background: var(--gold);
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
            display: none;
        }

        @keyframes up {
            from {
                opacity: 0;
                transform: translateY(24px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: .65;
                transform: scale(1)
            }

            50% {
                opacity: 1;
                transform: scale(1.04)
            }
        }

        @keyframes puppet-sway {

            0%,
            100% {
                transform: rotate(-1deg) translateY(0);
            }

            50% {
                transform: rotate(1.5deg) translateY(-8px);
            }
        }

        @keyframes puppet-arm {

            0%,
            100% {
                transform: rotate(-4deg);
            }

            50% {
                transform: rotate(7deg);
            }
        }

        @keyframes drift {
            from {
                transform: translate(0, 0)
            }

            to {
                transform: translate(17px, 17px)
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }

        @media (max-width:760px) {
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

            .links a::after {
                bottom: 2px;
                left: 12%;
                right: 12%;
            }

            .links .admin-link {
                margin-left: 0;
                padding-left: 3px;
                border-left: 0;
            }

            .login-status {
                align-self: flex-end;
            }

            .hero {
                min-height: 730px
            }

            .puppet-stage {
                right: -16%;
                width: 410px;
                opacity: .3;
            }

            .puppet-image {
                right: -40px;
                width: min(68vw, 360px);
                max-height: 58%;
                opacity: .7;
            }

        }

        @media (max-width:520px) {
            .inner {
                width: calc(100% - 32px)
            }

            .puppet-stage {
                right: -34%;
                bottom: 20px;
                min-width: 300px;
                opacity: .2;
            }

            .puppet-image {
                right: -70px;
                bottom: 20px;
                width: min(78vw, 330px);
                max-height: 54%;
                opacity: .62;
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

<body class="home-page">
    <nav class="nav" aria-label="Navigasi utama">
        <a class="brand" href="home.php">Ramayana</a>
        <div class="links">
            <a class="active" href="home.php">Imah</a>
            <a href="sinopsis.php">Ringkesan</a>
            <a href="tokoh.php">Palaku</a>
            <a href="beli_tiket.php">Tiket</a>
        </div>
        <?php if ($userEmail !== ''): ?>
            <div class="login-status" title="Login sebagai <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Status login aktif">Online</div>
        <?php endif; ?>
    </nav>
    <header class="hero">
        <?php if (is_file(__DIR__ . '/assets/wayang.png')): ?>
            <img class="puppet-image" src="assets/wayang.png?v=<?php echo filemtime(__DIR__ . '/assets/wayang.png'); ?>" alt="Wayang Ramayana">
        <?php else: ?>
            <div class="puppet-stage" aria-hidden="true">
                <svg viewBox="0 0 520 520" role="presentation">
                    <g class="puppet puppet-one" fill="currentColor">
                        <circle cx="188" cy="90" r="28"></circle>
                        <path d="M178 118c-10 34-18 70-15 117l-22 151h93l-29-151c4-47-4-83-16-117z"></path>
                        <path d="M150 147 73 208l8 11 89-43z"></path>
                        <path class="puppet-arm" d="M211 150 286 91l8 12-68 77z"></path>
                        <path d="m150 386-33 104h16l47-102zM205 386l31 104h16l-20-104z"></path>
                        <path d="M161 132 119 91l-8 10 43 54zM206 131l42-37 8 11-46 51z"></path>
                    </g>
                    <g class="puppet puppet-two" fill="currentColor" opacity=".62">
                        <circle cx="363" cy="134" r="23"></circle>
                        <path d="M355 157c-9 31-13 64-10 102l-19 125h76l-21-125c3-38-2-72-12-102z"></path>
                        <path d="m335 181-61 46 7 10 72-31z"></path>
                        <path class="puppet-arm" d="m383 181 64-47 7 11-58 63z"></path>
                        <path d="m337 384-25 93h14l40-91zM380 384l25 93h14l-17-93z"></path>
                        <path d="m342 169-33-35-8 9 35 47zM381 168l36-34 8 10-38 46z"></path>
                    </g>
                </svg>
            </div>
        <?php endif; ?>
        <div class="inner hero-content">
            <div class="eyebrow">Hiji pintonan wayang</div>
            <h1>Ramayana:<br>Ruhak Dinagara Alengka</h1>
            <p class="lead">Carita ngeunaan kasatiaan, kawani, jeung lalampahan panjang pikeun meunangkeun deui bebeneran tina kalangkang sarakah.</p>
            <div class="actions"><a class="button" href="riwayat.php">Riwayat tiket</a><a class="button outline" href="beli_tiket.php">Pesen tiket</a></div>
        </div>
    </header>
    <main>
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