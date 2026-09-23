<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/config.php';

$user = $_SESSION['user'] ?? null;

if ($user === null || ($user['role'] ?? '') !== 'admin') {
    header('Location: Akun.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root { --ink: #25150f; --paper: #fff4dc; --muted: #b99676; --orange: #e28738; --gold: #f5c873; --line: rgba(245, 200, 115, .28); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; color: var(--paper); font-family: Georgia, "Times New Roman", serif; background: radial-gradient(circle at 85% 15%, #98441f 0, transparent 30%), linear-gradient(135deg, #24120d, #6e2f1b); }
        header { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 24px min(7vw, 90px); border-bottom: 1px solid var(--line); }
        .brand { color: var(--gold); font: 700 .8rem Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; }
        .logout { padding: 10px 14px; color: var(--gold); border: 1px solid var(--line); font: 700 .72rem Arial, sans-serif; letter-spacing: .08em; text-decoration: none; text-transform: uppercase; }
        .logout:hover { color: var(--ink); background: var(--gold); }
        main { width: min(920px, calc(100% - 40px)); margin: 0 auto; padding: 86px 0; }
        .eyebrow { color: var(--gold); font: 700 .75rem Arial, sans-serif; letter-spacing: .18em; text-transform: uppercase; }
        h1 { margin: 18px 0 12px; font-size: clamp(3rem, 8vw, 6.5rem); line-height: .9; font-weight: 400; }
        .intro { color: var(--muted); font-size: 1.1rem; line-height: 1.7; }
        .status { margin-top: 44px; padding: 24px; border-left: 3px solid var(--orange); background: rgba(36, 18, 13, .7); }
        .status strong { color: var(--gold); }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; margin-top: 18px; background: var(--line); }
        .item { padding: 24px; background: rgba(36, 18, 13, .75); }
        .item small { display: block; margin-bottom: 10px; color: var(--muted); font: 700 .68rem Arial, sans-serif; letter-spacing: .1em; text-transform: uppercase; }
        .item span { font-size: 1.15rem; }
        @media (max-width: 620px) { header { align-items: flex-start; flex-direction: column; } main { padding: 56px 0; } .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="brand">Panel Admin / Alengka</div>
        <a class="logout" href="logout.php">Keluar</a>
    </header>
    <main>
        <div class="eyebrow">Status akun</div>
        <h1>Anda sudah login.</h1>
        <p class="intro">Selamat datang di area admin. Halaman ini masih berupa tampilan awal untuk pengelolaan pertunjukan.</p>
        <div class="status">Akun aktif: <strong><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
        <div class="grid">
            <div class="item"><small>Username</small><span><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="item"><small>Peran</small><span><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="item"><small>Status</small><span>Aktif</span></div>
        </div>
    </main>
</body>
</html>
