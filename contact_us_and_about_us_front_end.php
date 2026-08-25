<?php
session_start();
include "includes/db_connection.php";

$Page_Name = "About & Contact";

$Session_Messages_Helper = [
    "success" => ["class" => "session-success", "icon" => "fa-check-circle"],
    "error"   => ["class" => "session-error", "icon" => "fa-times-circle"],
    "info"    => ["class" => "session-info", "icon" => "fa-info-circle"]
];

function setMessage($type, $text) {
    $_SESSION['flash_message'] = ['type' => $type, 'text' => $text];
}

function getMessageHTML() {
    global $Session_Messages_Helper;
    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $type = $msg['type'];
        $class = $Session_Messages_Helper[$type]['class'] ?? 'session-info';
        $icon  = $Session_Messages_Helper[$type]['icon'] ?? 'fa-info-circle';
        unset($_SESSION['flash_message']);
        return '<div class="flash-message ' . $class . '"><i class="fas ' . $icon . '"></i> ' . htmlspecialchars($msg['text']) . '</div>';
    }
    return '';
}

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$aboutQ = mysqli_query($conn, "SELECT * FROM about_content WHERE id=1");
$about = mysqli_fetch_assoc($aboutQ);
$aboutSections = mysqli_query($conn, "SELECT * FROM about_sections ORDER BY sort_order ASC");
$contactQ = mysqli_query($conn, "SELECT * FROM contact_content WHERE id=1");
$contact = mysqli_fetch_assoc($contactQ);
$phones  = mysqli_query($conn, "SELECT * FROM contact_phones ORDER BY sort_order ASC");
$emails  = mysqli_query($conn, "SELECT * FROM contact_emails ORDER BY sort_order ASC");
$socials = mysqli_query($conn, "SELECT * FROM contact_social_links ORDER BY sort_order ASC");
$websites = mysqli_query($conn, "SELECT * FROM contact_website_links ORDER BY sort_order ASC");
$teamQ = mysqli_query($conn, "SELECT * FROM team_members ORDER BY sort_order ASC");

if (isset($_POST['send_message'])) {
    $senderName  = $_POST['sender_name'] ?? '';
    $senderEmail = $_POST['sender_email'] ?? '';
    $userMessage = $_POST['message'] ?? '';
    $adminEmail = $contact['email'] ?? 'info@yourcompany.lk';
    mail($adminEmail, "New Contact Message from $senderName", "Name: $senderName\nEmail: $senderEmail\n\n$userMessage", "From: $senderEmail");
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls';
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;
        $mail->setFrom($_ENV['SMTP_USER'], 'Sisu Seriya');
        $mail->addAddress($senderEmail, $senderName);
        $mail->isHTML(true);
        $mail->Subject = "We received your message";
        $mail->Body = "<p>Hello <b>" . htmlspecialchars($senderName) . "</b>,</p>
                       <p>We have received your message and will send you our response soon.</p>
                       <hr><p><strong>Your message:</strong><br>" . nl2br(htmlspecialchars($userMessage)) . "</p>";
        $mail->send();
        setMessage('success', 'Message sent! Check your email for confirmation.');
    } catch (Exception $e) {
        setMessage('error', 'Message sent but confirmation email failed. We will still contact you.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<?php include "side_bar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About & Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --void:       #06060f;
            --deep:       #0d0d20;
            --glass:      rgba(255,255,255,0.04);
            --glass-edge: rgba(255,255,255,0.10);
            --neon-v:     #9b5de5;
            --neon-p:     #f72585;
            --neon-c:     #00f5ff;
            --neon-g:     #06ffa5;
            --white:      #ffffff;
            --white-90:   rgba(255,255,255,0.90);
            --white-70:   rgba(255,255,255,0.70);
            --radius-lg:  24px;
            --radius-md:  16px;
            --radius-sm:  10px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--void);
            color: var(--white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Aurora Background ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 10% 0%,   rgba(155,93,229,0.25) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 10%,  rgba(247,37,133,0.18) 0%, transparent 55%),
                radial-gradient(ellipse 70% 40% at 50% 100%, rgba(0,245,255,0.12)  0%, transparent 60%),
                radial-gradient(ellipse 50% 60% at 80% 80%,  rgba(6,255,165,0.08)  0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
            animation: auroraShift 18s ease-in-out infinite alternate;
        }
        @keyframes auroraShift {
            0%   { opacity: 1; transform: scale(1); }
            100% { opacity: 0.7; transform: scale(1.08) rotate(2deg); }
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2.5rem 2rem 4rem;
            position: relative;
            z-index: 1;
        }

        /* ── Flash Messages ── */
        .flash-message {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 600;
            font-size: 0.95rem;
            backdrop-filter: blur(20px);
            animation: flashIn 0.4s cubic-bezier(.16,1,.3,1);
        }
        .session-success {
            background: rgba(6,255,165,0.10);
            border: 1px solid rgba(6,255,165,0.40);
            color: #06ffa5;
            box-shadow: 0 0 30px rgba(6,255,165,0.15);
        }
        .session-error {
            background: rgba(247,37,133,0.10);
            border: 1px solid rgba(247,37,133,0.40);
            color: #f72585;
            box-shadow: 0 0 30px rgba(247,37,133,0.15);
        }
        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.97); }
            to   { opacity: 1; transform: none; }
        }

        /* ── Glass Card ── */
        .card {
            background: var(--glass);
            border: 1px solid var(--glass-edge);
            border-radius: var(--radius-lg);
            padding: 3rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            position: relative;
            overflow: hidden;
            transition: border-color 0.4s, box-shadow 0.4s;
        }
        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--radius-lg);
            padding: 1px;
            background: linear-gradient(135deg, rgba(155,93,229,0.4), rgba(247,37,133,0.2), rgba(0,245,255,0.2));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s;
        }
        .card:hover::before { opacity: 1; }
        .card:hover {
            border-color: rgba(155,93,229,0.25);
            box-shadow: 0 0 60px rgba(155,93,229,0.12), 0 20px 60px rgba(0,0,0,0.4);
        }

        /* ── Section Labels ── */
        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--neon-c);
            background: rgba(0,245,255,0.08);
            border: 1px solid rgba(0,245,255,0.25);
            padding: 0.3rem 0.9rem;
            border-radius: 50px;
            margin-bottom: 1.2rem;
        }
        .section-eyebrow::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--neon-c);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--neon-c);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── Hero ── */
        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.4rem, 5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-bottom: 1.2rem;
            background: linear-gradient(135deg, #fff 30%, var(--neon-v) 65%, var(--neon-p) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 1.15rem;
            font-weight: 400;
            color: var(--white-90);
            max-width: 660px;
            line-height: 1.75;
        }

        /* ── About Sections Grid ── */
        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
            margin-top: 2.5rem;
        }
        .section-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: var(--radius-md);
            padding: 1.8rem;
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(.16,1,.3,1);
            cursor: default;
        }
        .section-item::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-v), var(--neon-p), var(--neon-c));
            transform: scaleX(0);
            transition: transform 0.35s ease;
        }
        .section-item:hover::after { transform: scaleX(1); }
        .section-item:hover {
            background: rgba(155,93,229,0.08);
            border-color: rgba(155,93,229,0.30);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 30px rgba(155,93,229,0.10);
        }
        .section-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(155,93,229,0.20), rgba(247,37,133,0.15));
            border: 1px solid rgba(155,93,229,0.30);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.2rem;
            transition: box-shadow 0.3s;
        }
        .section-item:hover .section-icon-wrap {
            box-shadow: 0 0 20px rgba(155,93,229,0.40);
        }
        .section-icon-wrap i { font-size: 1.35rem; color: var(--neon-v); }
        .section-item h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.6rem;
        }
        .section-item p {
            font-size: 0.92rem;
            color: var(--white-90);
            line-height: 1.65;
        }

        /* ── Team ── */
        .block-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 20%, var(--neon-p) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        .block-sub {
            color: var(--white-90);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .team-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: var(--radius-md);
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.35s cubic-bezier(.16,1,.3,1);
            position: relative;
            overflow: hidden;
        }
        .team-card::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at center, rgba(247,37,133,0.06) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .team-card:hover::before { opacity: 1; }
        .team-card:hover {
            transform: translateY(-8px);
            border-color: rgba(247,37,133,0.35);
            box-shadow: 0 20px 50px rgba(0,0,0,0.35), 0 0 35px rgba(247,37,133,0.10);
        }
        .team-avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 1.2rem;
        }
        .team-avatar-wrap::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-v), var(--neon-p));
            z-index: 0;
            animation: spinRing 6s linear infinite;
        }
        @keyframes spinRing {
            to { transform: rotate(360deg); }
        }
        .team-card img {
            width: 88px; height: 88px;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 1;
            border: 3px solid var(--deep);
        }
        .team-card h4 {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.25rem;
        }
        .team-role {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--neon-p);
            margin-bottom: 0.7rem;
        }
        .team-bio {
            font-size: 0.875rem;
            color: var(--white-90);
            line-height: 1.6;
        }

        /* ── Contact Info Grid ── */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: var(--radius-sm);
            padding: 1.1rem 1.3rem;
            transition: all 0.3s;
        }
        .info-box:hover {
            background: rgba(0,245,255,0.05);
            border-color: rgba(0,245,255,0.25);
        }
        .info-icon {
            width: 38px; height: 38px; min-width: 38px;
            border-radius: 10px;
            background: rgba(0,245,255,0.10);
            border: 1px solid rgba(0,245,255,0.20);
            display: flex; align-items: center; justify-content: center;
        }
        .info-icon i { font-size: 1rem; color: var(--neon-c); }
        .info-text {
            font-size: 0.9rem;
            color: var(--white);
            line-height: 1.5;
            padding-top: 0.1rem;
        }
        .info-text a {
            color: var(--neon-c);
            text-decoration: none;
            transition: color 0.2s;
        }
        .info-text a:hover { color: var(--white); text-decoration: underline; }

        /* ── Map ── */
        .map-container {
            margin-top: 2rem;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid rgba(155,93,229,0.20);
            box-shadow: 0 0 40px rgba(155,93,229,0.10);
        }

        /* ── Contact Form ── */
        .form-section { margin-top: 2.5rem; }
        .form-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.3rem;
        }
        .form-sub {
            font-size: 0.9rem;
            color: var(--white-90);
            margin-bottom: 1.8rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-bottom: 1rem;
        }
        .field-group label {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--white);
        }
        .field-group input,
        .field-group textarea {
            width: 100%;
            padding: 0.9rem 1.1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: var(--radius-sm);
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            outline: none;
        }
        .field-group input::placeholder,
        .field-group textarea::placeholder { color: rgba(255,255,255,0.35); }
        .field-group input:focus,
        .field-group textarea:focus {
            border-color: var(--neon-v);
            background: rgba(155,93,229,0.06);
            box-shadow: 0 0 0 3px rgba(155,93,229,0.15), 0 0 20px rgba(155,93,229,0.10);
        }
        .field-group textarea { min-height: 130px; resize: vertical; }

        .btn-send {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.9rem 2.4rem;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, var(--neon-v) 0%, var(--neon-p) 100%);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 4px 20px rgba(155,93,229,0.35);
        }
        .btn-send::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.25s;
        }
        .btn-send:hover::before { opacity: 1; }
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(247,37,133,0.45), 0 0 50px rgba(155,93,229,0.25);
        }
        .btn-send:active { transform: translateY(0); }

        /* ── Divider ── */
        .neon-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-v), var(--neon-p), transparent);
            margin: 2.5rem 0;
            opacity: 0.3;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .container { padding: 1.5rem 1rem 3rem; }
            .card { padding: 2rem 1.5rem; }
            .hero-title { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <?= getMessageHTML() ?>

    <!-- ── About Card ── -->
    <div class="card">
        <div class="section-eyebrow">Our Story</div>
        <h1 class="hero-title"><?= htmlspecialchars($about['title'] ?? 'About Us') ?></h1>
        <p class="hero-desc"><?= nl2br(htmlspecialchars($about['description'] ?? '')) ?></p>

        <?php if (mysqli_num_rows($aboutSections) > 0): ?>
        <div class="sections-grid">
            <?php while ($sec = mysqli_fetch_assoc($aboutSections)): ?>
            <div class="section-item">
                <div class="section-icon-wrap">
                    <i class="<?= htmlspecialchars($sec['icon_class'] ?? 'fas fa-star') ?>"></i>
                </div>
                <h3><?= htmlspecialchars($sec['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($sec['description'])) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Team Card ── -->
    <?php if (mysqli_num_rows($teamQ) > 0): ?>
    <div class="card">
        <div class="section-eyebrow">The People</div>
        <h2 class="block-title">Meet Our Team</h2>
        <p class="block-sub">The talented minds behind everything we do.</p>
        <div class="team-grid">
            <?php while ($member = mysqli_fetch_assoc($teamQ)): ?>
            <div class="team-card">
                <div class="team-avatar-wrap">
                    <img src="<?= ($member['photo'] && file_exists($member['photo'])) ? $member['photo'] : 'assets/default-avatar.png' ?>"
                         alt="<?= htmlspecialchars($member['name']) ?>">
                </div>
                <h4><?= htmlspecialchars($member['name']) ?></h4>
                <div class="team-role"><?= htmlspecialchars($member['position']) ?></div>
                <div class="team-bio"><?= htmlspecialchars($member['bio']) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Contact Card ── -->
    <div class="card">
        <div class="section-eyebrow">Get In Touch</div>
        <h2 class="block-title">Contact Us</h2>
        <p class="block-sub">We'd love to hear from you. Reach out any time.</p>

        <div class="contact-grid">
            <?php if (!empty($contact['address'])): ?>
            <div class="info-box">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text"><?= htmlspecialchars($contact['address']) ?></div>
            </div>
            <?php endif; ?>

            <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
            <div class="info-box">
                <div class="info-icon"><i class="<?= htmlspecialchars($phone['icon_class'] ?? 'fas fa-phone-alt') ?>"></i></div>
                <div class="info-text"><?= htmlspecialchars($phone['phone_number']) ?></div>
            </div>
            <?php endwhile; ?>

            <?php while ($email = mysqli_fetch_assoc($emails)): ?>
            <div class="info-box">
                <div class="info-icon"><i class="<?= htmlspecialchars($email['icon_class'] ?? 'fas fa-envelope') ?>"></i></div>
                <div class="info-text"><?= htmlspecialchars($email['email']) ?></div>
            </div>
            <?php endwhile; ?>

            <?php if (!empty($contact['working_hours'])): ?>
            <div class="info-box">
                <div class="info-icon"><i class="fas fa-clock"></i></div>
                <div class="info-text"><?= htmlspecialchars($contact['working_hours']) ?></div>
            </div>
            <?php endif; ?>

            <?php while ($social = mysqli_fetch_assoc($socials)): ?>
            <div class="info-box">
                <div class="info-icon"><i class="<?= htmlspecialchars($social['icon_class'] ?? 'fas fa-share-alt') ?>"></i></div>
                <div class="info-text">
                    <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank">
                        <?= htmlspecialchars($social['platform_name'] ?: $social['url']) ?>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>

            <?php while ($web = mysqli_fetch_assoc($websites)): ?>
            <div class="info-box">
                <div class="info-icon"><i class="<?= htmlspecialchars($web['icon_class'] ?? 'fas fa-globe') ?>"></i></div>
                <div class="info-text">
                    <a href="<?= htmlspecialchars($web['url']) ?>" target="_blank">
                        <?= htmlspecialchars($web['title'] ?: $web['url']) ?>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if (!empty($contact['map_embed'])): ?>
        <div class="map-container"><?= $contact['map_embed'] ?></div>
        <?php endif; ?>

        <div class="neon-divider"></div>

        <div class="form-section">
            <p class="form-title">Send a Message</p>
            <p class="form-sub">Fill in the form below and we'll get back to you shortly.</p>
            <form method="post">
                <div class="form-row">
                    <div class="field-group">
                        <label for="sender_name">Your Name</label>
                        <input type="text" id="sender_name" name="sender_name" placeholder="John Doe" required>
                    </div>
                    <div class="field-group">
                        <label for="sender_email">Your Email</label>
                        <input type="email" id="sender_email" name="sender_email" placeholder="john@example.com" required>
                    </div>
                </div>
                <div class="field-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" placeholder="Tell us how we can help you…" required></textarea>
                </div>
                <button type="submit" name="send_message" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

</div>
</body>
</html>