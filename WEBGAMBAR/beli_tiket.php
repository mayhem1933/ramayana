<?php
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['user_email'])) {
    header('Location: Login.php');
    exit;
}

$userEmail = (string) $_SESSION['user_email'];

$errors = [];
$ticket = null;
$emailSent = false;
$emailError = '';
$classGroups = [
    '10' => range('A', 'J'),
    '11' => range('A', 'L'),
    '12' => range('A', 'J'),
];
$ticketQuota = getTicketQuota();
$ticketsRemaining = max(0, (int) $ticketQuota['max_tickets'] - (int) $ticketQuota['sold_count']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $tingkatKelas = trim($_POST['tingkat_kelas'] ?? '');
    $kelasRombel = trim($_POST['kelas'] ?? '');
    $kelas = $tingkatKelas . ' ' . $kelasRombel;
    $jumlahTiket = (int) ($_POST['jumlah_tiket'] ?? 0);
    $waktuReservasi = trim($_POST['waktu_reservasi'] ?? '');
    $nomorHandphone = trim($_POST['nomor_handphone'] ?? '');
    $email = $userEmail;

    $validClass = isset($classGroups[$tingkatKelas]) && in_array($kelasRombel, $classGroups[$tingkatKelas], true);
    if ($nama === '' || !$validClass || $jumlahTiket !== 1 || $waktuReservasi === '' || $nomorHandphone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Lengkepan sadaya data sareng paké alamat email anu leres.';
    }

    if (!$errors) {
        $existingTickets = readTickets();
        foreach ($existingTickets as $existingTicket) {
            if (($existingTicket['dibuat_pada'] ?? '') < ($ticketQuota['reset_at'] ?? '')) {
                continue;
            }

            if (strcasecmp((string) ($existingTicket['email'] ?? ''), $email) === 0) {
                $errors[] = 'Hiji akun ngan tiasa gaduh hiji tiket. Email ieu parantos gaduh tiket.';
                break;
            }
        }
    }

    if (!$errors && !reserveTicketSlot()) {
        $errors[] = 'Hapunten, sadaya tiket parantos béak.';
    }

    if (!$errors) {
        $ticket = [
            'token' => makeTicketToken(),
            'nama' => $nama,
            'kelas' => $kelas,
            'jumlah_tiket' => $jumlahTiket,
            'waktu_reservasi' => $waktuReservasi,
            'nomor_handphone' => $nomorHandphone,
            'email' => $email,
            'status' => 'belum digunakan',
            'dibuat_pada' => date('Y-m-d'),
        ];

        try {
            $tickets = readTickets();
            $tickets[] = $ticket;
            writeTickets($tickets);
        } catch (Throwable $exception) {
            releaseTicketSlot();
            throw $exception;
        }

        $subject = 'E-Tiket Ramayana: ' . $ticket['token'];
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . rawurlencode($ticket['token']);
        $qrCodeImage = false;
        if (function_exists('curl_init')) {
            $curl = curl_init($qrCodeUrl);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $qrCodeImage = curl_exec($curl);
            $qrCodeType = (string) curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
            $qrCodeStatus = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($qrCodeStatus !== 200 || stripos($qrCodeType, 'image/png') === false) {
                $qrCodeImage = false;
            }
        }

        $barcodeSource = $qrCodeImage !== false && $qrCodeImage !== '' ? 'cid:ticket-barcode' : $qrCodeUrl;
        $body = '<!doctype html><html lang="id"><body style="font-family:Arial,sans-serif;color:#24120d">' .
            '<h2>E-Tiket Ramayana</h2>' .
            '<p>Reservasi Anda berhasil.</p>' .
            '<p><strong>Nama:</strong> ' . escape($nama) . '<br>' .
            '<strong>Kelas:</strong> ' . escape($kelas) . '<br>' .
            '<strong>Jumlah tiket:</strong> ' . $jumlahTiket . '<br>' .
            '<strong>Waktu:</strong> ' . escape($waktuReservasi) . '<br>' .
            '<strong>Kode tiket:</strong> ' . escape($ticket['token']) . '</p>' .
            '<p><img src="' . escape($barcodeSource) . '" width="240" height="240" alt="Barcode tiket ' . escape($ticket['token']) . '"></p>' .
            '<p>Tunjukkeun barcode atanapi kode tiket ieu ka pangurus nalika sumping.</p>' .
            '</body></html>';
        $mailFrom = defined('SMTP_FROM') ? SMTP_FROM : MAIL_FROM;
        if ($qrCodeImage !== false && $qrCodeImage !== '') {
            $mixedBoundary = '=_mixed_' . bin2hex(random_bytes(12));
            $relatedBoundary = '=_related_' . bin2hex(random_bytes(12));
            $headers = "From: " . $mailFrom . "\r\nReply-To: " . $mailFrom . "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"{$mixedBoundary}\"\r\n";
            $relatedBody = "--{$relatedBoundary}\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n" .
                quoted_printable_encode($body) . "\r\n\r\n" .
                "--{$relatedBoundary}\r\n" .
                "Content-Type: image/png; name=barcode-tiket.png\r\nContent-Transfer-Encoding: base64\r\nContent-ID: <ticket-barcode>\r\nContent-Disposition: inline; filename=barcode-tiket.png\r\n\r\n" .
                chunk_split(base64_encode($qrCodeImage)) .
                "--{$relatedBoundary}--\r\n";
            $bodyWithAttachment = "--{$mixedBoundary}\r\n" .
                "Content-Type: multipart/related; type=\"text/html\"; boundary=\"{$relatedBoundary}\"\r\n\r\n" .
                $relatedBody .
                "\r\n--{$mixedBoundary}\r\n" .
                "Content-Type: image/png; name=barcode-tiket.png\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=barcode-tiket.png\r\n\r\n" .
                chunk_split(base64_encode($qrCodeImage)) .
                "--{$mixedBoundary}--\r\n";
        } else {
            $headers = "From: " . $mailFrom . "\r\nReply-To: " . $mailFrom . "\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            $bodyWithAttachment = $body;
        }

        $emailSent = sendEmailSmtp($email, $subject, $bodyWithAttachment ?? $body);
        if (!$emailSent) {
            $emailError = 'Email gagal dikirim. Periksa konfigurasi SMTP Gmail dan App Password di config.php.';
            error_log('Gagal mengirim e-tiket ke ' . $email . ' untuk token ' . $ticket['token']);
        }
    }
}
$ticketQuota = getTicketQuota();
$ticketsRemaining = max(0, (int) $ticketQuota['max_tickets'] - (int) $ticketQuota['sold_count']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesen Tiket | Ramayana</title>
    <link rel="stylesheet" href="assets/global-effects.css?v=11">
    <script src="assets/global-effects.js?v=2" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --ink: #24120d;
            --brown: #542816;
            --orange: #d86b2f;
            --gold: #f5c873;
            --cream: #fff4dc;
            --muted: #d9b996;
            --line: #8e4b2b;
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

        .wrap {
            width: min(960px, 100%);
            margin: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px 22px;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--line);
        }

        .brand {
            color: var(--gold);
            font: 700 .8rem Georgia, serif;
            letter-spacing: .14em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .back {
            color: var(--muted);
            font-size: .85rem;
            text-decoration: none;
        }

        .menu {
            display: flex;
            flex: 1 1 auto;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px 18px;
        }

        .menu a {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .menu a:hover,
        .menu .active {
            color: var(--gold);
        }

        .menu .logout {
            color: #efaa8d;
        }

        .menu .admin-link {
            margin-left: 8px;
            padding-left: 18px;
            border-left: 1px solid var(--line);
        }

        .back:hover {
            color: var(--gold);
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
            flex: 0 0 7px;
            border-radius: 50%;
            background: #8dd28d;
            box-shadow: 0 0 0 4px rgba(141, 210, 141, .15);
        }

        .account-link {
            color: var(--gold);
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none;
        }

        .notice-card {
            margin-bottom: 24px;
            padding: 24px 28px;
            background: rgba(52, 24, 15, .8);
            border: 1px solid var(--line);
            box-shadow: 8px 10px 0 rgba(20, 8, 4, .2);
        }

        .notice-card h2 {
            margin-bottom: 18px;
            color: var(--gold);
            font-size: 2rem;
        }

        .notice-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .notice-item {
            padding-top: 12px;
            border-top: 1px solid var(--line);
        }

        .notice-item span {
            display: block;
            margin-bottom: 6px;
            color: var(--orange);
            font: 700 .64rem Arial, sans-serif;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .notice-item strong {
            color: var(--cream);
            font-size: 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .panel {
            padding: 32px;
            background: rgba(84, 40, 22, .7);
            border: 1px solid var(--line);
            box-shadow: 12px 14px 0 rgba(20, 8, 4, .3);
        }

        h1,
        h2 {
            margin: 0;
            font-family: Georgia, serif;
            font-weight: 400;
        }

        h1 {
            margin-bottom: 10px;
            font-size: clamp(2.4rem, 6vw, 4.8rem);
            line-height: .92;
        }

        h2 {
            margin-bottom: 22px;
            font-size: 2rem;
            color: var(--gold);
        }

        .intro {
            color: var(--muted);
            line-height: 1.7;
        }

        form {
            display: grid;
            gap: 14px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        label {
            color: #f5dfba;
            font-size: .78rem;
            font-weight: 700;
        }

        input,
        select {
            width: 100%;
            padding: 13px;
            color: var(--cream);
            background: #2e150c;
            border: 1px solid #9b5a36;
            border-radius: 3px;
            font: inherit;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px #f5c87324;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        button {
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

        .error {
            margin-bottom: 16px;
            padding: 12px;
            color: #ffd0c8;
            background: #8f302633;
            border-left: 3px solid #fa775d;
        }

        .ticket {
            background: #fff4dc;
            color: var(--ink);
        }

        .ticket h2 {
            color: var(--brown);
        }

        .ticket-data {
            display: grid;
            gap: 8px;
            padding: 16px 0;
            border-block: 1px solid #d6b27c;
            font-size: .9rem;
        }

        .ticket-data strong {
            color: var(--brown);
        }

        #qrcode {
            display: grid;
            place-items: center;
            min-height: 180px;
            margin: 22px 0 14px;
        }

        .ticket-note {
            margin: 0;
            color: #73513d;
            font-size: .78rem;
            line-height: 1.5;
            text-align: center;
        }

        .mail-note {
            margin-top: 18px;
            color: #73513d;
            font-size: .78rem;
            line-height: 1.5;
        }

        @media (max-width:720px) {
            .notice-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 14px;
            }

            .menu {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                width: 100%;
                gap: 6px;
                padding: 6px;
                background: rgba(36, 18, 13, .45);
                border: 1px solid var(--line);
            }

            .menu a {
                min-height: 36px;
                display: grid;
                place-items: center;
                padding: 6px 3px;
                font-size: .58rem;
                text-align: center;
            }

            .menu .admin-link {
                margin-left: 0;
                padding-left: 3px;
                border-left: 0;
            }

            .login-status,
            .back {
                align-self: flex-end;
            }

            .back {
                width: 100%;
                padding: 12px;
                border: 1px solid var(--line);
                text-align: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .panel {
                padding: 24px;
            }
        }

        @media (max-width:420px) {
            .row {
                grid-template-columns: 1fr;
            }

        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="topbar">
            <a class="brand" href="home.php">Ramayana / E-Ticket</a>
            <div class="login-status" title="Login sebagai <?php echo escape($userEmail); ?>" aria-label="Status login aktif">Online</div>
            <a class="back" href="home.php">&larr; Balik ka Imah</a>
        </div>
        <section class="notice-card" aria-label="Informasi pementasan">
            <h2>Inpormasi pintonan</h2>
            <p class="intro">Sésa tiket: <?php echo $ticketsRemaining; ?> tina <?php echo (int) $ticketQuota['max_tickets']; ?></p>
            <div class="notice-grid">
                <div class="notice-item">
                    <span>Tanggal</span>
                    <strong>Sabtu, 24 Oktober 2026</strong>
                </div>
                <div class="notice-item">
                    <span>Waktos</span>
                    <strong>Sesi Siang 13.00 WIB &amp; Sesi Malam 19.00 WIB</strong>
                </div>
                <div class="notice-item">
                    <span>Tempat</span>
                    <strong>Kelas 12 F</strong>
                </div>
                <div class="notice-item">
                    <span>Catetan</span>
                    <strong>Hapunten, mangga sumping 30 menit saméméh sési dimimitian.</strong>
                </div>
            </div>
        </section>
        <div class="grid">
            <section class="panel">
                <h1>Pesen tiket pintonan.</h1>
                <p class="intro">Hiji akun atanapi alamat email ngan tiasa mesen hiji tiket.</p>
                <?php foreach ($errors as $error): ?><div class="error"><?php echo escape($error); ?></div><?php endforeach; ?>
                <form method="POST" action="beli_tiket.php">
                    <div class="field"><label for="nama">Nami lengkep</label><input id="nama" name="nama" required value="<?php echo escape($_POST['nama'] ?? ''); ?>"></div>
                    <div class="row">
                        <div class="field"><label for="tingkat_kelas">Tingkat kelas</label><select id="tingkat_kelas" name="tingkat_kelas" required>
                                <option value="">Pilih tingkat</option>
                                <?php foreach (array_keys($classGroups) as $grade): ?><option value="<?php echo escape($grade); ?>" <?php echo (($_POST['tingkat_kelas'] ?? '') === $grade) ? 'selected' : ''; ?>>Kelas <?php echo escape($grade); ?></option><?php endforeach; ?>
                            </select></div>
                        <div class="field"><label for="kelas">Rombel</label><select id="kelas" name="kelas" required disabled>
                                <option value="">Pilih tingkat heula</option>
                            </select></div>
                        <div class="field"><label for="jumlah_tiket">Jumlah tiket</label><input id="jumlah_tiket" name="jumlah_tiket" type="number" min="1" max="1" required value="1" readonly></div>
                    </div>
                    <div class="field"><label for="waktu_reservasi">Waktos pintonan</label><select id="waktu_reservasi" name="waktu_reservasi" required>
                            <option value="">Pilih sesi</option>
                            <option>Sesi Siang - 13.00 WIB</option>
                            <option>Sesi Malam - 19.00 WIB</option>
                        </select></div>
                    <div class="field"><label for="nomor_handphone">Nomor handphone</label><input id="nomor_handphone" name="nomor_handphone" type="tel" required value="<?php echo escape($_POST['nomor_handphone'] ?? ''); ?>"></div>
                    <div class="field"><label for="email">Email penerima e-tiket</label><input id="email" name="email" type="email" value="<?php echo escape($userEmail); ?>" readonly></div>
                    <button type="submit">Jieun sareng kirim e-tiket</button>
                </form>
            </section>
            <?php if ($ticket): ?>
                <section class="panel ticket">
                    <h2>E-Tiket Anda</h2>
                    <div class="ticket-data">
                        <div><strong>Kode:</strong> <?php echo escape($ticket['token']); ?></div>
                        <div><strong>Nama:</strong> <?php echo escape($ticket['nama']); ?></div>
                        <div><strong>Sesi:</strong> <?php echo escape($ticket['waktu_reservasi']); ?></div>
                        <div><strong>Jumlah:</strong> <?php echo $ticket['jumlah_tiket']; ?> tiket</div>
                    </div>
                    <div id="qrcode"></div>
                    <p class="ticket-note">Tunjukkeun QR ieu ka pangurus pikeun mariksa tiket. Status awal: can dipaké.</p>
                    <p class="mail-note"><?php echo $emailSent ? 'E-tiket dan barcode sudah dikirim ke email Anda.' : ($emailError !== '' ? escape($emailError) : 'Tiket tersimpan, tetapi email belum terkirim.'); ?></p>
                    <a class="back" href="riwayat.php">Tingali riwayat pesenan</a>
                </section>
                <script>
                    new QRCode(document.getElementById('qrcode'), {
                        text: <?php echo json_encode($ticket['token']); ?>,
                        width: 180,
                        height: 180,
                        colorDark: '#24120d',
                        colorLight: '#fff4dc'
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
    <script>
        const classGroups = <?php echo json_encode($classGroups, JSON_UNESCAPED_UNICODE); ?>;
        const gradeSelect = document.getElementById('tingkat_kelas');
        const classSelect = document.getElementById('kelas');
        const selectedClass = <?php echo json_encode($_POST['kelas'] ?? ''); ?>;

        function updateClassOptions() {
            const grade = gradeSelect.value;
            classSelect.replaceChildren();
            if (!grade || !classGroups[grade]) {
                classSelect.disabled = true;
                classSelect.add(new Option('Pilih tingkat dulu', ''));
                return;
            }

            classSelect.disabled = false;
            classSelect.add(new Option('Pilih rombel', ''));
            classGroups[grade].forEach((className) => {
                const option = new Option('Kelas ' + grade + ' ' + className, className);
                option.selected = className === selectedClass;
                classSelect.add(option);
            });
        }

        gradeSelect.addEventListener('change', updateClassOptions);
        updateClassOptions();
    </script>
</body>

</html>