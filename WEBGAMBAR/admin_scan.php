<?php
session_start();
require_once __DIR__ . '/config.php';

$authenticated = !empty($_SESSION['admin_authenticated']);
$adminEmail = (string) ($_SESSION['user_email'] ?? '');
$scanResult = isset($_GET['quota_reset']) ? 'Kuota tiket parantos di-reset janten 0.' : null;
$scanType = $scanResult !== null ? 'success' : '';
$ticketQuota = getTicketQuota();

if ($adminEmail === '') {
    header('Location: Login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    if (authenticateAdmin((string) ($_POST['admin_password'] ?? ''))) {
        $_SESSION['admin_authenticated'] = true;
        $authenticated = true;
    } else {
        $scanResult = 'Sandi pangurus lepat.';
        $scanType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();
    $authenticated = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $authenticated && isset($_POST['action']) && $_POST['action'] === 'scan') {
    $token = trim($_POST['token']);
    $tickets = readTickets();
    $found = false;

    foreach ($tickets as &$ticket) {
        if ($ticket['token'] !== $token) {
            continue;
        }

        $found = true;
        if ($ticket['status'] === 'sudah digunakan') {
            $scanResult = 'Tiket ieu parantos kungsi dipaké.';
            $scanType = 'error';
        } else {
            $ticket['status'] = 'sudah digunakan';
            $ticket['dipindai_pada'] = date('Y-m-d');
            $scanResult = 'Tiket sah. Aksés pintonan ditarima.';
            $scanType = 'success';
        }
        break;
    }
    unset($ticket);

    if (!$found) {
        $scanResult = 'Kode tiket teu kapanggih.';
        $scanType = 'error';
    }

    if ($found && $scanType === 'success') {
        writeTickets($tickets);
    }
}

$scannedTickets = array_values(array_filter(readTickets(), static function (array $ticket): bool {
    return ($ticket['status'] ?? '') === 'sudah digunakan';
}));
usort($scannedTickets, static function (array $first, array $second): int {
    return strcmp($second['dipindai_pada'] ?? '', $first['dipindai_pada'] ?? '');
});
?>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pangurus | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript" onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js';"></script>
    <style>
        :root {
            --ink: #24120d;
            --brown: #542816;
            --orange: #d86b2f;
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
            padding: 32px 18px;
            color: var(--cream);
            background:
                radial-gradient(circle at 12% 8%, rgba(226, 135, 56, .22), transparent 28%),
                radial-gradient(circle at 88% 22%, rgba(245, 200, 115, .14), transparent 24%),
                linear-gradient(135deg, #24120d 0%, #4f2315 48%, #24120d 100%);
            font-family: Arial, sans-serif;
        }

        .scanner-page::before {
            background: none !important;
            opacity: 0 !important;
            filter: none !important;
        }

        .wrap {
            width: min(680px, 100%);
            margin: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px 20px;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(245, 200, 115, .28);
        }

        .brand {
            color: var(--gold);
            font: 700 .8rem Georgia, serif;
            letter-spacing: .14em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
            margin-left: auto;
        }

        .nav-links a {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .nav-links a:hover,
        .nav-links .active {
            color: var(--gold);
        }

        .nav-links .admin-link {
            margin-left: 8px;
            padding-left: 18px;
            border-left: 1px solid rgba(245, 200, 115, .28);
        }

        a {
            color: var(--muted);
            font-size: .85rem;
            text-decoration: none;
        }

        a:hover {
            color: var(--gold);
        }

        .panel {
            padding: 32px;
            background: rgba(84, 40, 22, .72);
            border: 1px solid #8e4b2b;
            box-shadow: 12px 14px 0 rgba(20, 8, 4, .3);
        }

        h1 {
            margin: 0 0 10px;
            font: 400 clamp(2.5rem, 7vw, 4.8rem) Georgia, serif;
            line-height: .9;
        }

        .intro {
            color: var(--muted);
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 20px 0 7px;
            color: #f5dfba;
            font-size: .78rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 14px;
            color: var(--cream);
            background: #2e150c;
            border: 1px solid #9b5a36;
            border-radius: 3px;
            font: inherit;
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 14px;
            color: var(--ink);
            background: var(--orange);
            border: 0;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: var(--gold);
        }

        #reader {
            width: min(100%, 420px);
            min-height: 280px;
            margin: 18px auto 0;
            overflow: hidden;
            background: #fff4dc;
            border: 4px solid var(--gold);
            border-radius: 12px;
        }

        #reader video {
            transform: scaleX(-1) !important;
        }

        .camera-title {
            margin: 28px 0 8px;
            color: var(--gold);
            font: 700 1rem Georgia, serif;
            text-align: center;
        }

        .camera-status {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: .8rem;
            text-align: center;
        }

        .camera-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: min(100%, 420px);
            margin: 14px auto 0;
        }

        .camera-actions button {
            margin: 0;
        }

        .camera-actions button.secondary {
            color: var(--cream);
            background: transparent;
            border: 1px solid #9b5a36;
        }

        .camera-actions button.secondary:hover {
            color: var(--ink);
            background: var(--gold);
        }

        .status {
            margin-top: 22px;
            padding: 16px;
            line-height: 1.5;
            border-left: 4px solid;
        }

        .success {
            color: #c9f5c5;
            background: #287a5526;
            border-color: #8dd28d;
        }

        .error {
            color: #ffd0c8;
            background: #8f302633;
            border-color: #fa775d;
        }

        .hint {
            color: var(--muted);
            font-size: .8rem;
            line-height: 1.5;
        }

        .admin-bar,
        .history-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .admin-bar {
            padding-bottom: 16px;
            color: var(--gold);
            border-bottom: 1px solid #8e4b2b;
            font-weight: 700;
        }

        .admin-bar form {
            margin: 0;
        }

        .quota-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 18px;
            padding-top: 16px;
            color: var(--muted);
            border-top: 1px solid #8e4b2b;
            font-size: .82rem;
        }

        .quota-bar form {
            margin: 0;
        }

        button.reset-quota {
            width: auto;
            margin: 0;
            padding: 8px 13px;
            color: var(--cream);
            background: transparent;
            border: 1px solid #9b5a36;
        }

        button.reset-quota:hover {
            color: var(--ink);
            background: var(--gold);
        }

        button.logout {
            width: auto;
            margin: 0;
            padding: 8px 13px;
            color: var(--cream);
            background: transparent;
            border: 1px solid #9b5a36;
        }

        .history-panel {
            margin-top: 24px;
        }

        .history-panel h2 {
            margin: 0 0 6px;
            color: var(--gold);
            font: 400 2rem Georgia, serif;
        }

        .count {
            color: var(--orange);
            white-space: nowrap;
        }

        .empty {
            color: var(--muted);
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 18px;
        }

        table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            font-size: .78rem;
        }

        th {
            padding: 12px 10px;
            color: var(--gold);
            background: #2e150c;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 13px 10px;
            color: #f0d6b5;
            border-bottom: 1px solid #8e4b2b;
            vertical-align: top;
        }

        td small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
        }

        .used {
            color: #bde5ae;
        }

        @media (max-width: 560px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .nav-links {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                width: 100%;
                gap: 5px;
                padding: 5px;
                margin-left: 0;
                background: rgba(36, 18, 13, .45);
                border: 1px solid rgba(245, 200, 115, .28);
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
            .nav-links .active {
                background: rgba(245, 200, 115, .12);
            }

            .nav-links .admin-link {
                margin-left: 0;
                padding-left: 3px;
                border-left: 0;
            }

            .history-heading {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="scanner-page">
    <div class="wrap">
        <div class="topbar">
            <div class="brand">Ramayana / Pangurus</div>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="home.php">Imah</a>
                <a href="sinopsis.php">Ringkesan</a>
                <a href="beli_tiket.php">Tiket</a>
                <a href="akun.php">Akun</a>
            </nav>
            <a href="index.php">&larr; Landing page</a>
        </div>
        <section class="panel">
            <h1>Pariksa tiket.</h1>
            <?php if (!$authenticated): ?>
                <p class="intro">Anjeun parantos login nganggo email <strong><?php echo escape($adminEmail); ?></strong>. Lebetkeun sandi pangurus pikeun muka kaméra sareng ningali data tiket.</p>
                <form method="POST" action="admin_scan.php">
                    <input type="hidden" name="action" value="login">
                    <label for="admin_password">Sandi pangurus</label>
                    <input id="admin_password" name="admin_password" type="password" autocomplete="current-password" required>
                    <button type="submit">Asup salaku pangurus</button>
                </form>
                <p class="hint">Sandi ieu dipaké babarengan ku pangurus anu meunang idin.</p>
            <?php else: ?>
                <div class="admin-bar"><span>Pangurus aktip: <?php echo escape($adminEmail); ?></span>
                    <form method="POST" action="admin_scan.php"><input type="hidden" name="action" value="logout"><button class="logout" type="submit">Kaluar</button></form>
                </div>
                <div class="quota-bar">
                    <span>Kuota: <?php echo (int) $ticketQuota['sold_count']; ?>/<?php echo (int) $ticketQuota['max_tickets']; ?> tiket</span>
                    <form method="POST" action="reset_kuota.php" onsubmit="return confirm('Reset kuota tanpa menghapus riwayat tiket?');">
                        <button class="reset-quota" type="submit">Reset kuota</button>
                    </form>
                </div>
                <p class="intro">Arahkeun kaméra kana QR e-tiket nu meuli. Unggal tiket ngan tiasa dipaké sakali.</p>
                <form method="POST" action="admin_scan.php">
                    <input type="hidden" name="action" value="scan">
                    <label for="token">Kode tiket (opsional jika kamera tidak tersedia)</label>
                    <input id="token" name="token" placeholder="Contoh: RMA-1A2B3C4D5E">
                    <button type="submit">Validasi kode tiket</button>
                </form>
                <div class="camera-title">Scan QR ku kaméra</div>
                <div id="reader"></div>
                <div class="camera-actions">
                    <button id="start-camera" type="button">Buka Kamera</button>
                    <button id="stop-camera" class="secondary" type="button">Tutup Kamera</button>
                </div>
                <div id="camera-status" class="camera-status">Kaméra can dibuka.</div>
                <p class="hint">QR yang terbaca akan masuk ke kolom kode. Tekan validasi untuk mencatat tiket.</p>
            <?php endif; ?>
            <?php if ($scanResult): ?><div class="status <?php echo $scanType; ?>"><?php echo escape($scanResult); ?></div><?php endif; ?>
        </section>

        <?php if ($authenticated): ?>
            <section class="panel history-panel">
                <div class="history-heading">
                    <div>
                        <h2>Riwayat tiket nu dipariksa</h2>
                        <p class="hint">Data ieu asalna tina tiket anu parantos hasil divalidasi.</p>
                    </div><strong class="count"><?php echo count($scannedTickets); ?> tiket</strong>
                </div>
                <?php if (!$scannedTickets): ?>
                    <p class="empty">Can aya tiket anu dipariksa.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nu meuli</th>
                                    <th>Jumlah</th>
                                    <th>Sesi</th>
                                    <th>Dipariksa dina</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scannedTickets as $scannedTicket): ?>
                                    <tr>
                                        <td><?php echo escape($scannedTicket['token'] ?? '-'); ?></td>
                                        <td><?php echo escape($scannedTicket['nama'] ?? '-'); ?><small><?php echo escape($scannedTicket['email'] ?? ''); ?></small></td>
                                        <td><?php echo (int) ($scannedTicket['jumlah_tiket'] ?? 0); ?></td>
                                        <td><?php echo escape($scannedTicket['waktu_reservasi'] ?? '-'); ?></td>
                                        <td><?php echo escape($scannedTicket['dipindai_pada'] ?? '-'); ?></td>
                                        <td><span class="used">Sudah digunakan</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

    </div>
    <?php if ($authenticated): ?><script>
            const reader = new Html5Qrcode('reader');
            const tokenInput = document.getElementById('token');
            const cameraStatus = document.getElementById('camera-status');
            let cameraRunning = false;

            function onScanSuccess(decodedText) {
                tokenInput.value = decodedText;
                cameraStatus.textContent = 'QR terbaca. Tekan Validasi kode tiket untuk memeriksa.';
                stopCamera();
            }

            function startCamera() {
                if (cameraRunning) return;
                if (!window.isSecureContext || !navigator.mediaDevices) {
                    cameraStatus.textContent = 'Kamera diblokir browser. Gunakan http://localhost/WEB%20PHP/admin_scan.php di komputer ini, atau aktifkan HTTPS untuk akses dari HP.';
                    return;
                }
                if (typeof Html5Qrcode === 'undefined') {
                    cameraStatus.textContent = 'Library kamera gagal dimuat. Periksa koneksi internet lalu muat ulang halaman.';
                    return;
                }
                cameraStatus.textContent = 'Meminta izin kamera...';
                reader.start({
                    facingMode: 'environment'
                }, {
                    fps: 10,
                    qrbox: 220
                }, onScanSuccess, () => {}).then(() => {
                    cameraRunning = true;
                    cameraStatus.textContent = 'Kamera aktif. Arahkan QR ke kotak scan.';
                }).catch(error => {
                    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                        cameraStatus.textContent = 'Izin kamera ditolak. Klik ikon gembok di alamat browser, izinkan Kamera, lalu muat ulang halaman.';
                    } else if (error.name === 'NotReadableError') {
                        cameraStatus.textContent = 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi tersebut lalu coba lagi.';
                    } else {
                        cameraStatus.textContent = 'Kamera tidak ditemukan atau tidak dapat digunakan. Periksa izin kamera, HTTPS, dan koneksi perangkat.';
                    }
                });
            }

            function stopCamera() {
                if (!cameraRunning) return;
                reader.stop().then(() => {
                    cameraRunning = false;
                    cameraStatus.textContent = 'Kamera ditutup.';
                }).catch(() => {});
            }

            document.getElementById('start-camera').addEventListener('click', startCamera);
            document.getElementById('stop-camera').addEventListener('click', stopCamera);
        </script><?php endif; ?>
</body>

</html>