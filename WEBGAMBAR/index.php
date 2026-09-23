<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAMAYANA: RUHAK DINAGARA ALENGKA</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --espresso: #24120d;
            --brown: #522716;
            --terracotta: #9a4222;
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
            background: var(--espresso);
            font-family: "Manrope", Arial, sans-serif;
            overflow-x: hidden;
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
            animation: grain-drift 18s linear infinite;
        }

        .hero {
            position: relative;
            min-height: 690px;
            display: flex;
            align-items: center;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(36, 18, 13, .98) 25%, rgba(36, 18, 13, .7) 68%, rgba(36, 18, 13, .42)),
                radial-gradient(circle at 88% 18%, rgba(245, 200, 115, .2), transparent 18%),
                radial-gradient(circle at 72% 78%, rgba(165, 75, 36, .42), transparent 28%),
                radial-gradient(circle at 76% 48%, rgba(226, 135, 56, .55), transparent 24%),
                linear-gradient(135deg, #29140d, #7c321b);
            border-bottom: 1px solid var(--line);
            isolation: isolate;
        }

        .nav-brand {
            display: none;
        }

        .nav-links {
            display: none;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .12;
            pointer-events: none;
            background: repeating-linear-gradient(128deg, transparent 0 26px, rgba(245, 200, 115, .32) 27px 28px, transparent 29px 54px);
            animation: batik-slide 22s linear infinite;
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 430px;
            height: 430px;
            right: 8%;
            bottom: -210px;
            border: 1px solid rgba(245, 200, 115, .45);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgba(245, 200, 115, .06), 0 0 0 56px rgba(245, 200, 115, .05);
            animation: mandala-pulse 7s ease-in-out infinite;
        }

        .hero-inner,
        .section-inner {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            padding-top: 28px;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            color: var(--gold);
            font: 700 .75rem Arial, sans-serif;
            letter-spacing: .2em;
            text-transform: uppercase;
            animation: reveal-up .8s both;
        }

        .eyebrow::before {
            content: "✦";
            color: var(--orange);
            font-size: 1.1rem;
        }

        h1 {
            max-width: 760px;
            margin: 0;
            color: var(--cream);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(3.6rem, 8vw, 7.8rem);
            font-weight: 400;
            line-height: .9;
            letter-spacing: -.03em;
            text-transform: uppercase;
            animation: reveal-up 1s .12s both;
        }

        .hero-copy {
            max-width: 560px;
            margin: 30px 0 0;
            color: var(--muted);
            font-size: 1.1rem;
            line-height: 1.75;
            animation: reveal-up .9s .28s both;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 36px;
            animation: reveal-up .9s .42s both;
        }

        .button {
            display: inline-block;
            padding: 15px 23px;
            color: var(--espresso);
            background: var(--orange);
            font: 700 .78rem Arial, sans-serif;
            letter-spacing: .1em;
            text-decoration: none;
            text-transform: uppercase;
            transition: transform .2s, background .2s;
            position: relative;
            overflow: hidden;
        }

        .button::after {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 70%;
            height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255, 248, 233, .45), transparent);
            transform: skewX(-18deg);
            transition: left .55s ease;
        }

        .button:hover::after {
            left: 140%;
        }

        .button:hover {
            background: var(--gold);
            transform: translateY(-3px);
        }

        .button.secondary {
            color: var(--gold);
            background: transparent;
            border: 1px solid var(--line);
        }

        .button.secondary:hover {
            color: var(--espresso);
            background: var(--gold);
        }

        .puppet-decoration {
            position: absolute;
            right: 16%;
            bottom: 0;
            width: 245px;
            height: 570px;
            opacity: .72;
            transform: rotate(-7deg);
            transform-origin: 50% 100%;
            animation: puppet-sway 5s ease-in-out infinite;
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

        .puppet-head {
            position: absolute;
            top: 65px;
            left: 97px;
            width: 74px;
            height: 98px;
            border: 3px solid var(--gold);
            border-radius: 58% 45% 50% 42%;
            transform: rotate(12deg);
        }

        .puppet-head::before {
            content: "";
            position: absolute;
            top: -28px;
            left: 12px;
            width: 80px;
            height: 35px;
            border-top: 3px solid var(--gold);
            border-radius: 50%;
            transform: rotate(-22deg);
        }

        .puppet-head::after {
            content: "";
            position: absolute;
            top: 38px;
            left: -45px;
            width: 42px;
            height: 3px;
            background: var(--gold);
            transform: rotate(16deg);
            box-shadow: 8px 12px 0 -1px var(--gold);
        }

        .puppet-body {
            position: absolute;
            top: 155px;
            left: 85px;
            width: 100px;
            height: 250px;
            border: 3px solid var(--gold);
            border-top: 0;
            border-radius: 30% 70% 20% 20%;
            transform: skew(-8deg);
        }

        .puppet-arm {
            position: absolute;
            top: 194px;
            width: 118px;
            height: 3px;
            background: var(--gold);
        }

        .puppet-arm.left {
            left: 6px;
            transform: rotate(31deg);
        }

        .puppet-arm.right {
            left: 133px;
            transform: rotate(-22deg);
        }

        .puppet-staff {
            position: absolute;
            top: 135px;
            left: 216px;
            width: 3px;
            height: 430px;
            background: var(--gold);
        }

        .section {
            padding: 100px 0;
            position: relative;
        }

        .section.alt {
            background: #32180f;
            border-block: 1px solid var(--line);
        }

        .notice-section {
            background: #42200f;
            border-bottom: 1px solid var(--line);
        }

        .notice-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
        }

        .event-card,
        .notice-card {
            padding: 30px;
            border: 1px solid var(--line);
            background: rgba(36, 18, 13, .5);
            animation: reveal-up .8s both;
        }

        .event-card h3,
        .notice-card h3 {
            margin: 0 0 22px;
            color: var(--gold);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 2rem;
            font-weight: 600;
        }

        .event-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .event-detail {
            padding-top: 12px;
            border-top: 1px solid var(--line);
        }

        .event-detail span {
            display: block;
            margin-bottom: 6px;
            color: var(--orange);
            font: 700 .68rem Arial, sans-serif;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .event-detail strong {
            color: var(--cream);
            font: 500 1.05rem "Cormorant Garamond", Georgia, serif;
        }

        .notice-card ul {
            display: grid;
            gap: 14px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .notice-card li {
            position: relative;
            padding-left: 23px;
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.6;
        }

        .notice-card li::before {
            content: "✦";
            position: absolute;
            left: 0;
            color: var(--orange);
        }

        .section-heading {
            max-width: 680px;
            margin-bottom: 52px;
            animation: reveal-up .9s both;
        }

        .section-heading h2 {
            margin: 0 0 16px;
            color: var(--cream);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(2.3rem, 5vw, 4.5rem);
            font-weight: 400;
            line-height: 1;
        }

        .section-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 1.03rem;
            line-height: 1.8;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1.3fr .7fr;
            gap: 28px;
            align-items: stretch;
        }

        .story-main,
        .quote {
            padding: 34px;
            border: 1px solid var(--line);
            background: rgba(82, 39, 22, .42);
            animation: reveal-up .9s .16s both;
        }

        .story-main p {
            margin: 0;
            color: #f0d6b5;
            font-size: 1.08rem;
            line-height: 1.9;
        }

        .quote {
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-color: var(--orange);
            animation-delay: .28s;
        }

        .quote-mark {
            color: var(--orange);
            font-size: 4rem;
            line-height: .6;
        }

        .quote p {
            margin: 24px 0 0;
            color: var(--gold);
            font-size: 1.45rem;
            line-height: 1.4;
            font-family: "Cormorant Garamond", Georgia, serif;
        }

        .characters {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .character {
            min-height: 180px;
            padding: 24px 20px;
            border-top: 3px solid var(--orange);
            background: #42200f;
            transition: transform .35s ease, background .35s ease, border-color .35s ease;
            animation: reveal-up .8s both;
        }

        .character:nth-child(2) {
            animation-delay: .1s;
        }

        .character:nth-child(3) {
            animation-delay: .2s;
        }

        .character:nth-child(4) {
            animation-delay: .3s;
        }

        .character:hover {
            transform: translateY(-10px);
            background: #5a2a15;
            border-color: var(--gold);
        }

        .character-number {
            color: var(--orange);
            font: 700 .72rem Arial, sans-serif;
            letter-spacing: .15em;
        }

        .character h3 {
            margin: 24px 0 8px;
            color: var(--cream);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 1.45rem;
            font-weight: 400;
        }

        .character p {
            margin: 0;
            color: var(--muted);
            font: .85rem/1.6 Arial, sans-serif;
        }

        .closing {
            text-align: center;
            background: linear-gradient(135deg, #6b2d18, #a54b24);
        }

        .closing .section-inner {
            max-width: 700px;
        }

        .closing h2 {
            margin: 0 0 18px;
            font-size: clamp(2.4rem, 6vw, 5rem);
            font-weight: 400;
            font-family: "Cormorant Garamond", Georgia, serif;
            animation: reveal-up .9s both;
        }

        .closing p {
            margin: 0 auto 30px;
            color: #f3d4ad;
            line-height: 1.7;
        }

        footer {
            padding: 22px;
            color: #b88762;
            background: #1b0d08;
            font: .72rem Arial, sans-serif;
            letter-spacing: .12em;
            text-align: center;
            text-transform: uppercase;
        }

        .mobile-actions {
            display: none;
        }

        @keyframes reveal-up {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes puppet-sway {

            0%,
            100% {
                transform: rotate(-7deg) translateY(0);
            }

            50% {
                transform: rotate(-3deg) translateY(-10px);
            }
        }

        @keyframes mandala-pulse {

            0%,
            100% {
                opacity: .65;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.04);
            }
        }

        @keyframes batik-slide {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(54px);
            }
        }

        @keyframes grain-drift {
            from {
                transform: translate(0, 0);
            }

            to {
                transform: translate(17px, 17px);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }

        @media (max-width: 800px) {
            .hero {
                min-height: 670px;
            }

            .puppet-decoration {
                right: -55px;
                opacity: .22;
            }

            .puppet-image {
                right: -40px;
                width: min(68vw, 360px);
                max-height: 58%;
                opacity: .7;
            }

            .story-grid {
                grid-template-columns: 1fr;
            }

            .notice-grid {
                grid-template-columns: 1fr;
            }

            .characters {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 520px) {

            .hero-inner,
            .section-inner {
                width: min(100% - 32px, 1120px);
            }

            .hero {
                min-height: 720px;
            }

            .site-nav {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
                padding: 14px 0;
            }

            body {
                padding-bottom: 82px;
            }

            .mobile-actions {
                position: fixed;
                right: 12px;
                bottom: 12px;
                left: 12px;
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
                text-align: center;
                text-decoration: none;
            }

            .mobile-actions a.active,
            .mobile-actions a:hover {
                color: var(--espresso);
                background: var(--gold);
            }

            .nav-links {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                width: 100%;
                gap: 5px;
                padding: 5px;
                background: rgba(36, 18, 13, .55);
                border: 1px solid rgba(245, 200, 115, .22);
                border-radius: 12px;
            }

            .nav-links a {
                min-height: 40px;
                display: grid;
                place-items: center;
                padding: 6px 2px;
                border-radius: 8px;
                font-size: .62rem;
                text-align: center;
            }

            .nav-links a:hover,
            .nav-links a.active {
                background: rgba(245, 200, 115, .12);
            }

            .hero-copy {
                font-size: 1rem;
            }

            .section {
                padding: 72px 0;
            }

            .story-main,
            .quote {
                padding: 24px;
            }

            .characters {
                grid-template-columns: 1fr;
            }

            .event-details {
                grid-template-columns: 1fr;
            }

            .event-card,
            .notice-card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>
    <header class="hero" id="home">
        <div class="hero-inner">
            <div class="eyebrow">Hiji pintonan wayang</div>
            <h1>Ramayana:<br>Ruhak Dinagara Alengka</h1>
            <p class="hero-copy">Carita ngeunaan kasatiaan, kawani, jeung lalampahan panjang pikeun meunangkeun deui bebeneran tina kalangkang sarakah.</p>
            <div class="hero-actions">
                <a class="button" href="sinopsis.php">Baca Ringkesan</a>
                <a class="button secondary" href="tokoh.php">Kenal Palaku</a>
            </div>
        </div>
        <?php if (is_file(__DIR__ . '/assets/wayang.png')): ?>
            <img class="puppet-image" src="assets/wayang.png?v=<?php echo filemtime(__DIR__ . '/assets/wayang.png'); ?>" alt="Wayang Ramayana">
        <?php else: ?>
            <div class="puppet-decoration" aria-hidden="true">
                <div class="puppet-head"></div>
                <div class="puppet-body"></div>
                <div class="puppet-arm left"></div>
                <div class="puppet-arm right"></div>
                <div class="puppet-staff"></div>
            </div>
        <?php endif; ?>
    </header>
    <main></main>

    <footer>RAMAYANA: RUHAK DINAGARA ALENGKA &middot; Seni tradisi, cerita yang abadi</footer>
</body>

</html>