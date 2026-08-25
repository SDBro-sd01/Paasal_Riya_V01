<?php
session_start();
include "includes/db_connection.php";

$Page_Name = "About Us and Contact Us Page Management";

$Session_Messages_Helper = [
    "success" => ["class" => "session-success", "icon" => "fa-check-circle"],
    "error"   => ["class" => "session-error",   "icon" => "fa-times-circle"],
    "info"    => ["class" => "session-info",     "icon" => "fa-info-circle"]
];

function setMessage($type, $text) {
    $_SESSION['flash_message'] = ['type' => $type, 'text' => $text];
}

function getMessageHTML() {
    global $Session_Messages_Helper;
    if (!empty($_SESSION['flash_message'])) {
        $msg   = $_SESSION['flash_message'];
        $type  = $msg['type'];
        $class = $Session_Messages_Helper[$type]['class'] ?? 'session-info';
        $icon  = $Session_Messages_Helper[$type]['icon']  ?? 'fa-info-circle';
        unset($_SESSION['flash_message']);
        return '<div class="flash-message '.$class.'"><i class="fas '.$icon.'"></i> '.htmlspecialchars($msg['text']).'</div>';
    }
    return '';
}

$icons = [
    'fas fa-star'              => 'Star',
    'fas fa-bullseye'          => 'Bullseye',
    'fas fa-eye'               => 'Eye',
    'fas fa-history'           => 'History',
    'fas fa-heart'             => 'Heart',
    'fas fa-rocket'            => 'Rocket',
    'fas fa-lightbulb'         => 'Lightbulb',
    'fas fa-handshake'         => 'Handshake',
    'fas fa-globe'             => 'Globe',
    'fas fa-phone-alt'         => 'Phone',
    'fas fa-envelope'          => 'Envelope',
    'fas fa-map-marker-alt'    => 'Map Marker',
    'fas fa-clock'             => 'Clock',
    'fas fa-share-alt'         => 'Share',
    'fas fa-link'              => 'Link',
    'fas fa-users'             => 'Users',
    'fas fa-cog'               => 'Settings',
    'fas fa-chart-line'        => 'Chart Line',
    'fas fa-home'              => 'Home',
    'fas fa-building'          => 'Building',
    'fas fa-school'            => 'School',
    'fas fa-university'        => 'University',
    'fas fa-book'              => 'Book',
    'fas fa-book-open'         => 'Open Book',
    'fas fa-graduation-cap'    => 'Graduation Cap',
    'fas fa-user'              => 'User',
    'fas fa-user-tie'          => 'Professional',
    'fas fa-user-friends'      => 'Friends',
    'fas fa-user-plus'         => 'User Plus',
    'fas fa-user-check'        => 'User Check',
    'fas fa-comments'          => 'Comments',
    'fas fa-comment-dots'      => 'Chat',
    'fas fa-comment-alt'       => 'Comment',
    'fas fa-bell'              => 'Bell',
    'fas fa-calendar'          => 'Calendar',
    'fas fa-calendar-check'    => 'Calendar Check',
    'fas fa-calendar-alt'      => 'Calendar Alt',
    'fas fa-camera'            => 'Camera',
    'fas fa-image'             => 'Image',
    'fas fa-video'             => 'Video',
    'fas fa-microphone'        => 'Microphone',
    'fas fa-music'             => 'Music',
    'fas fa-headphones'        => 'Headphones',
    'fas fa-play'              => 'Play',
    'fas fa-pause'             => 'Pause',
    'fas fa-stop'              => 'Stop',
    'fas fa-download'          => 'Download',
    'fas fa-upload'            => 'Upload',
    'fas fa-cloud'             => 'Cloud',
    'fas fa-cloud-upload-alt'  => 'Cloud Upload',
    'fas fa-cloud-download-alt'=> 'Cloud Download',
    'fas fa-database'          => 'Database',
    'fas fa-server'            => 'Server',
    'fas fa-code'              => 'Code',
    'fas fa-laptop-code'       => 'Laptop Code',
    'fas fa-desktop'           => 'Desktop',
    'fas fa-mobile-alt'        => 'Mobile',
    'fas fa-tablet-alt'        => 'Tablet',
    'fas fa-wifi'              => 'WiFi',
    'fas fa-lock'              => 'Lock',
    'fas fa-unlock'            => 'Unlock',
    'fas fa-shield-alt'        => 'Shield',
    'fas fa-key'               => 'Key',
    'fas fa-search'            => 'Search',
    'fas fa-filter'            => 'Filter',
    'fas fa-sort'              => 'Sort',
    'fas fa-list'              => 'List',
    'fas fa-th'                => 'Grid',
    'fas fa-bars'              => 'Menu',
    'fas fa-check'             => 'Check',
    'fas fa-times'             => 'Close',
    'fas fa-plus'              => 'Plus',
    'fas fa-minus'             => 'Minus',
    'fas fa-edit'              => 'Edit',
    'fas fa-trash'             => 'Trash',
    'fas fa-save'              => 'Save',
    'fas fa-print'             => 'Print',
    'fas fa-shopping-cart'     => 'Shopping Cart',
    'fas fa-store'             => 'Store',
    'fas fa-gift'              => 'Gift',
    'fas fa-credit-card'       => 'Credit Card',
    'fas fa-wallet'            => 'Wallet',
    'fas fa-money-bill'        => 'Money',
    'fas fa-coins'             => 'Coins',
    'fas fa-chart-bar'         => 'Chart Bar',
    'fas fa-chart-pie'         => 'Chart Pie',
    'fas fa-briefcase'         => 'Briefcase',
    'fas fa-trophy'            => 'Trophy',
    'fas fa-medal'             => 'Medal',
    'fas fa-flag'              => 'Flag',
    'fas fa-fire'              => 'Fire',
    'fas fa-leaf'              => 'Leaf',
    'fas fa-tree'              => 'Tree',
    'fas fa-seedling'          => 'Seedling',
    'fas fa-car'               => 'Car',
    'fas fa-bus'               => 'Bus',
    'fas fa-plane'             => 'Plane',
    'fas fa-ship'              => 'Ship',
    'fas fa-bicycle'           => 'Bicycle',
    'fas fa-walking'           => 'Walking',
    'fas fa-running'           => 'Running',
    'fas fa-dumbbell'          => 'Fitness',
    'fas fa-stethoscope'       => 'Health',
    'fas fa-hospital'          => 'Hospital',
    'fab fa-facebook-f' => 'Facebook',
    'fab fa-instagram'  => 'Instagram',
    'fab fa-threads'    => 'Threads',
    'fab fa-x-twitter'  => 'X',
    'fab fa-linkedin-in'=> 'LinkedIn',
    'fab fa-youtube'    => 'YouTube',
    'fab fa-tiktok'     => 'TikTok',
    'fab fa-whatsapp'   => 'WhatsApp',
    'fab fa-telegram'   => 'Telegram',
    'fab fa-discord'    => 'Discord',
    'fab fa-reddit'     => 'Reddit',
    'fab fa-pinterest'  => 'Pinterest',
    'fab fa-snapchat'   => 'Snapchat',
    'fab fa-github'     => 'GitHub',
    'fab fa-spotify'    => 'Spotify'

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_about'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $desc  = mysqli_real_escape_string($conn, $_POST['description']);
        mysqli_query($conn, "UPDATE about_content SET title='$title', description='$desc' WHERE id=1");
        setMessage('success', 'About Us updated!');
    }
    if (isset($_POST['save_about_section'])) {
        $id    = $_POST['section_id'] ?? '';
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $desc  = mysqli_real_escape_string($conn, $_POST['description']);
        $icon  = mysqli_real_escape_string($conn, $_POST['icon_class']);
        $sort  = intval($_POST['sort_order']);
        if (!empty($id)) {
            mysqli_query($conn, "UPDATE about_sections SET title='$title', description='$desc', icon_class='$icon', sort_order=$sort WHERE id=$id");
            setMessage('success', 'Section updated!');
        } else {
            mysqli_query($conn, "INSERT INTO about_sections (title, description, icon_class, sort_order) VALUES ('$title', '$desc', '$icon', $sort)");
            setMessage('success', 'Section added!');
        }
    }
    if (isset($_POST['update_contact'])) {
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $hours   = mysqli_real_escape_string($conn, $_POST['working_hours']);
        $map     = mysqli_real_escape_string($conn, $_POST['map_embed']);
        mysqli_query($conn, "UPDATE contact_content SET address='$address', working_hours='$hours', map_embed='$map' WHERE id=1");
        setMessage('success', 'Contact info updated!');
    }
    if (isset($_POST['save_phone'])) {
        $id   = $_POST['phone_id'] ?? '';
        $num  = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $icon = mysqli_real_escape_string($conn, $_POST['icon_class']);
        $sort = intval($_POST['sort_order']);
        if ($id) { mysqli_query($conn, "UPDATE contact_phones SET phone_number='$num', icon_class='$icon', sort_order=$sort WHERE id=$id"); setMessage('success', 'Phone updated!'); }
        else      { mysqli_query($conn, "INSERT INTO contact_phones (phone_number, icon_class, sort_order) VALUES ('$num', '$icon', $sort)"); setMessage('success', 'Phone added!'); }
    }
    if (isset($_POST['save_email'])) {
        $id    = $_POST['email_id'] ?? '';
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $icon  = mysqli_real_escape_string($conn, $_POST['icon_class']);
        $sort  = intval($_POST['sort_order']);
        if ($id) { mysqli_query($conn, "UPDATE contact_emails SET email='$email', icon_class='$icon', sort_order=$sort WHERE id=$id"); setMessage('success', 'Email updated!'); }
        else      { mysqli_query($conn, "INSERT INTO contact_emails (email, icon_class, sort_order) VALUES ('$email', '$icon', $sort)"); setMessage('success', 'Email added!'); }
    }
    if (isset($_POST['save_social'])) {
        $id       = $_POST['social_id'] ?? '';
        $platform = mysqli_real_escape_string($conn, $_POST['platform_name']);
        $url      = mysqli_real_escape_string($conn, $_POST['url']);
        $icon     = mysqli_real_escape_string($conn, $_POST['icon_class']);
        $sort     = intval($_POST['sort_order']);
        if ($id) { mysqli_query($conn, "UPDATE contact_social_links SET platform_name='$platform', url='$url', icon_class='$icon', sort_order=$sort WHERE id=$id"); setMessage('success', 'Social link updated!'); }
        else      { mysqli_query($conn, "INSERT INTO contact_social_links (platform_name, url, icon_class, sort_order) VALUES ('$platform', '$url', '$icon', $sort)"); setMessage('success', 'Social link added!'); }
    }
    if (isset($_POST['save_web'])) {
        $id    = $_POST['web_id'] ?? '';
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $url   = mysqli_real_escape_string($conn, $_POST['url']);
        $icon  = mysqli_real_escape_string($conn, $_POST['icon_class']);
        $sort  = intval($_POST['sort_order']);
        if ($id) { mysqli_query($conn, "UPDATE contact_website_links SET title='$title', url='$url', icon_class='$icon', sort_order=$sort WHERE id=$id"); setMessage('success', 'Website link updated!'); }
        else      { mysqli_query($conn, "INSERT INTO contact_website_links (title, url, icon_class, sort_order) VALUES ('$title', '$url', '$icon', $sort)"); setMessage('success', 'Website link added!'); }
    }
    if (isset($_POST['save_team'])) {
        $id       = $_POST['member_id'] ?? '';
        $name     = mysqli_real_escape_string($conn, $_POST['name']);
        $position = mysqli_real_escape_string($conn, $_POST['position']);
        $bio      = mysqli_real_escape_string($conn, $_POST['bio']);
        $sort     = intval($_POST['sort_order']);
        function createMemberFolder($name, $id = null) {
            $base = "uploads/Members/";
            if (!is_dir($base)) mkdir($base, 0755, true);
            $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
            $folder = $base . $safeName;
            if (is_dir($folder) && $id) $folder .= '_' . $id;
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            return $folder;
        }
        if ($id) {
            $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM team_members WHERE id=$id"));
            $folder = $old['folder'] ?? '';
            $newFolder = createMemberFolder($name, $id);
            if ($folder && $folder != $newFolder && is_dir($folder)) rename($folder, $newFolder);
            $folder = $newFolder ?: $folder;
        } else { $folder = createMemberFolder($name); }
        $photo_path = $old['photo'] ?? '';
        if (!empty($_FILES['photo']['name'])) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name) . '.' . $ext;
            $target = $folder . '/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                if ($photo_path && file_exists($photo_path) && $photo_path != $target) unlink($photo_path);
                $photo_path = $target;
            }
        }
        if ($id) { mysqli_query($conn, "UPDATE team_members SET name='$name', position='$position', bio='$bio', photo='$photo_path', folder='$folder', sort_order=$sort WHERE id=$id"); setMessage('success', 'Team member updated!'); }
        else      { mysqli_query($conn, "INSERT INTO team_members (name, position, bio, photo, folder, sort_order) VALUES ('$name', '$position', '$bio', '$photo_path', '$folder', $sort)"); setMessage('success', 'Team member added!'); }
    }
    if (isset($_POST['delete_about_section'])) { mysqli_query($conn, "DELETE FROM about_sections WHERE id=".intval($_POST['delete_about_section'])); setMessage('success', 'Section deleted.'); }
    if (isset($_POST['delete_phone']))          { mysqli_query($conn, "DELETE FROM contact_phones WHERE id=".intval($_POST['delete_phone'])); setMessage('success', 'Phone deleted.'); }
    if (isset($_POST['delete_email']))          { mysqli_query($conn, "DELETE FROM contact_emails WHERE id=".intval($_POST['delete_email'])); setMessage('success', 'Email deleted.'); }
    if (isset($_POST['delete_social']))         { mysqli_query($conn, "DELETE FROM contact_social_links WHERE id=".intval($_POST['delete_social'])); setMessage('success', 'Social link deleted.'); }
    if (isset($_POST['delete_web']))            { mysqli_query($conn, "DELETE FROM contact_website_links WHERE id=".intval($_POST['delete_web'])); setMessage('success', 'Website link deleted.'); }
    if (isset($_POST['delete_team'])) {
        $id = intval($_POST['delete_team']);
        $res = mysqli_query($conn, "SELECT photo, folder FROM team_members WHERE id=$id");
        if ($row = mysqli_fetch_assoc($res)) {
            if ($row['photo'] && file_exists($row['photo'])) unlink($row['photo']);
            if ($row['folder'] && is_dir($row['folder'])) @rmdir($row['folder']);
        }
        mysqli_query($conn, "DELETE FROM team_members WHERE id=$id");
        setMessage('success', 'Team member deleted.');
    }
    header("Location: contact_us_and_about_us_backend.php", true, 303);
    exit;
}

$about         = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_content WHERE id=1"));
$contact       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM contact_content WHERE id=1"));
$aboutSections = mysqli_query($conn, "SELECT * FROM about_sections ORDER BY sort_order ASC");
$teamMembers   = mysqli_query($conn, "SELECT * FROM team_members ORDER BY sort_order ASC");
$phones        = mysqli_query($conn, "SELECT * FROM contact_phones ORDER BY sort_order ASC");
$emails        = mysqli_query($conn, "SELECT * FROM contact_emails ORDER BY sort_order ASC");
$socials       = mysqli_query($conn, "SELECT * FROM contact_social_links ORDER BY sort_order ASC");
$websites      = mysqli_query($conn, "SELECT * FROM contact_website_links ORDER BY sort_order ASC");

function iconSelectPreview($name, $selected, $iconsList) {
    $selectedLabel = $iconsList[$selected] ?? 'Select Icon';
    $html = '<div class="custom-icon-select" data-name="'.$name.'">';
    $html .= '<input type="hidden" name="'.$name.'" value="'.$selected.'">';
    $html .= '<button type="button" class="custom-select-btn">';
    $html .= '<i class="'.$selected.'"></i>';
    $html .= '<span class="cust-sel-label">'.$selectedLabel.'</span>';
    $html .= '<i class="fas fa-chevron-up caret-icon"></i>'; // changed chevron to up
    $html .= '</button>';
    $html .= '<div class="custom-select-options">';
    foreach ($iconsList as $class => $label) {
        $activeClass = ($class === $selected) ? ' active' : '';
        $html .= '<div class="custom-option'.$activeClass.'" data-value="'.$class.'" data-label="'.$label.'">';
        $html .= '<i class="'.$class.'"></i>';
        $html .= '<span>'.$label.'</span>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
?>
<?php include "side_bar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — About & Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --void:        #06060f;
            --deep:        #0d0d20;
            --surface:     rgba(255,255,255,0.03);
            --surface-2:   rgba(255,255,255,0.055);
            --edge:        rgba(255,255,255,0.08);
            --edge-strong: rgba(255,255,255,0.14);
            --neon-v:      #9b5de5;
            --neon-p:      #f72585;
            --neon-c:      #00f5ff;
            --neon-g:      #06ffa5;
            --neon-o:      #ff6b35;
            --white:       #ffffff;
            --w90:         rgba(255,255,255,0.90);
            --w70:         rgba(255,255,255,0.70);
            --radius-lg:   22px;
            --radius-md:   14px;
            --radius-sm:   9px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--void);
            color: var(--white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 5%  10%,  rgba(155,93,229,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 55% 45% at 95% 5%,   rgba(247,37,133,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 60% 40% at 50% 100%, rgba(0,245,255,0.08)  0%, transparent 60%);
            pointer-events: none; z-index: 0;
            animation: aurora 20s ease-in-out infinite alternate;
        }
        @keyframes aurora {
            0%   { opacity: 1; }
            100% { opacity: 0.6; transform: scale(1.05); }
        }

        .admin-wrapper {
            max-width: 1260px;
            margin: 0 auto;
            padding: 2.5rem 2rem 5rem;
            position: relative; z-index: 1;
        }

        .page-header {
            margin-bottom: 2.5rem;
            display: flex; align-items: center; gap: 1.2rem;
        }
        .page-header-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--neon-v), var(--neon-p));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 0 25px rgba(155,93,229,0.45);
        }
        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.7rem; font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, var(--neon-v) 70%, var(--neon-p));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .page-header p { font-size: 0.88rem; color: var(--w90); margin-top: 0.2rem; }

        .flash-message {
            padding: 1rem 1.4rem; border-radius: var(--radius-md);
            margin-bottom: 2rem; display: flex; align-items: center; gap: 0.8rem;
            font-weight: 600; font-size: 0.92rem; backdrop-filter: blur(20px);
            animation: flashIn 0.35s cubic-bezier(.16,1,.3,1);
        }
        .session-success { background: rgba(6,255,165,0.08); border: 1px solid rgba(6,255,165,0.35); color: #06ffa5; box-shadow: 0 0 25px rgba(6,255,165,0.12); }
        .session-error   { background: rgba(247,37,133,0.08); border: 1px solid rgba(247,37,133,0.35); color: #f72585; box-shadow: 0 0 25px rgba(247,37,133,0.12); }
        @keyframes flashIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }

        .card {
            background: var(--surface);
            border: 1px solid var(--edge);
            border-radius: var(--radius-lg);
            padding: 2.2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: visible !important;
            transition: border-color 0.35s, box-shadow 0.35s;
        }
        .card:hover {
            border-color: rgba(155,93,229,0.22);
            box-shadow: 0 0 50px rgba(155,93,229,0.08), 0 16px 50px rgba(0,0,0,0.35);
        }

        .card-header {
            display: flex; align-items: center; gap: 0.9rem;
            margin-bottom: 1.8rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid var(--edge);
        }
        .card-header-icon {
            width: 40px; height: 40px; min-width: 40px;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem;
        }
        .ci-violet { background: rgba(155,93,229,0.18); border: 1px solid rgba(155,93,229,0.30); color: var(--neon-v); }
        .ci-pink   { background: rgba(247,37,133,0.15); border: 1px solid rgba(247,37,133,0.28); color: var(--neon-p); }
        .ci-cyan   { background: rgba(0,245,255,0.10);  border: 1px solid rgba(0,245,255,0.25);  color: var(--neon-c); }
        .ci-green  { background: rgba(6,255,165,0.10);  border: 1px solid rgba(6,255,165,0.22);  color: var(--neon-g); }
        .ci-orange { background: rgba(255,107,53,0.12); border: 1px solid rgba(255,107,53,0.26); color: var(--neon-o); }

        .card-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem; font-weight: 700;
            color: var(--white);
        }
        .card-header span { font-size: 0.8rem; color: var(--w90); display: block; margin-top: 0.1rem; }

        .form-sub-heading {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-family: 'Syne', sans-serif;
            font-size: 0.82rem; font-weight: 700;
            letter-spacing: 0.10em; text-transform: uppercase;
            color: var(--neon-v);
            background: rgba(155,93,229,0.10);
            border: 1px solid rgba(155,93,229,0.22);
            padding: 0.35rem 0.9rem; border-radius: 50px;
            margin: 1.8rem 0 1.2rem;
        }

        label {
            display: block;
            font-size: 0.78rem; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
            color: var(--white);
            margin: 1rem 0 0.4rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--edge-strong);
            border-radius: var(--radius-sm);
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            outline: none;
        }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.30); }
        input:focus, textarea:focus {
            border-color: var(--neon-v);
            background: rgba(155,93,229,0.06);
            box-shadow: 0 0 0 3px rgba(155,93,229,0.14);
        }
        textarea { min-height: 95px; resize: vertical; }

        input[type="file"] {
            padding: 0.6rem 0.9rem;
            cursor: pointer;
            color: var(--w90);
        }
        input[type="file"]::-webkit-file-upload-button {
            background: rgba(155,93,229,0.20);
            border: 1px solid rgba(155,93,229,0.35);
            border-radius: 6px; padding: 0.3rem 0.8rem;
            color: var(--white); font-size: 0.82rem; cursor: pointer;
            transition: background 0.2s;
        }
        input[type="file"]::-webkit-file-upload-button:hover {
            background: rgba(155,93,229,0.35);
        }

        .btn {
            display: inline-flex; align-items: center; gap: 0.45rem;
            padding: 0.55rem 1.3rem;
            border: none; border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-size: 0.83rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            vertical-align: middle;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--neon-v), var(--neon-p));
            color: #fff;
            box-shadow: 0 3px 14px rgba(155,93,229,0.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(247,37,133,0.40); }
        .btn-edit {
            background: rgba(0,245,255,0.12);
            border: 1px solid rgba(0,245,255,0.28);
            color: var(--neon-c);
        }
        .btn-edit:hover { background: rgba(0,245,255,0.22); transform: translateY(-1px); }
        .btn-danger {
            background: rgba(247,37,133,0.12);
            border: 1px solid rgba(247,37,133,0.30);
            color: var(--neon-p);
        }
        .btn-danger:hover { background: rgba(247,37,133,0.25); transform: translateY(-1px); }
        .btn-cancel {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--edge-strong);
            color: var(--w90);
        }
        .btn-cancel:hover { background: rgba(255,255,255,0.12); }
        .btn-actions { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

        .inline-form { display: inline; margin: 0; padding: 0; }

        .form-actions { margin-top: 1.4rem; display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }

        .table-wrap {
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--edge);
            margin-bottom: 0.5rem;
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: rgba(155,93,229,0.14); }
        th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--white);
        }
        tbody tr {
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--edge);
            transition: background 0.2s;
        }
        tbody tr:hover { background: rgba(155,93,229,0.07); }
        td {
            padding: 0.7rem 1rem;
            font-size: 0.88rem;
            color: var(--white);
            vertical-align: middle;
        }
        .td-icon i { font-size: 1.1rem; color: var(--neon-v); }
        .empty-row td {
            text-align: center; padding: 1.2rem;
            color: var(--w90); font-style: italic; font-size: 0.85rem;
        }

        .team-thumb {
            width: 38px; height: 38px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid var(--neon-p);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 600px) {
            .form-grid-2 { grid-template-columns: 1fr; }
        }

        .neon-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-v), var(--neon-p), transparent);
            opacity: 0.20; margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .admin-wrapper { padding: 1.5rem 1rem 4rem; }
            .card { padding: 1.5rem; }
        }

        /* ── Modal Styles ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center; justify-content: center;
            z-index: 99999;
        }
        .modal-overlay.active { display: flex; }
        .modal-dialog {
            background: var(--deep);
            border: 1px solid var(--edge-strong);
            border-radius: var(--radius-lg);
            max-width: 600px; width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8), 0 0 0 1px rgba(155,93,229,0.2);
            position: relative;
            pointer-events: auto;
        }
        .modal-dialog.wide { max-width: 750px; }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--edge);
            background: rgba(155,93,229,0.06);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
        .modal-header h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem; font-weight: 700;
            color: var(--white);
        }
        .modal-close {
            background: none; border: none;
            color: var(--w90); font-size: 1.3rem; cursor: pointer;
            transition: color 0.2s;
        }
        .modal-close:hover { color: var(--neon-p); }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-body .form-actions { margin-top: 1.2rem; }

        /* ── Custom Icon Select Dropdown (opens UPWARD) ── */
        .custom-icon-select {
            position: relative;
            width: 100%;
        }
        .custom-select-btn {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--edge-strong);
            border-radius: var(--radius-sm);
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 0.7rem;
            cursor: pointer;
            font-size: 0.92rem;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        .custom-select-btn:hover {
            border-color: var(--neon-v);
        }
        .custom-select-btn .cust-sel-label {
            flex: 1;
            text-align: left;
        }
        .custom-select-btn .caret-icon {
            margin-left: auto;
            font-size: 0.75rem;
            color: var(--w90);
            transition: transform 0.2s;
        }
        /* Open state: rotate chevron-up to chevron-down? Since we use chevron-up, rotation not needed */
        .custom-icon-select.open .custom-select-btn .caret-icon {
            /* transform: rotate(180deg); // optional, but not needed for up arrow */
        }
        .custom-select-options {
            position: absolute;
            bottom: calc(100% + 4px);  /* opens upward */
            top: auto;
            left: 0;
            right: 0;
            max-height: 220px;
            overflow-y: auto;
            background: #0d0d20;
            border: 1px solid var(--edge-strong);
            border-radius: var(--radius-sm);
            box-shadow: 0 -8px 24px rgba(0,0,0,0.6); /* shadow on top */
            display: none;
            z-index: 1000;
        }
        .custom-icon-select.open .custom-select-options {
            display: block;
        }
        .custom-option {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.65rem 1rem;
            cursor: pointer;
            transition: background 0.15s;
            color: var(--w90);
            font-size: 0.88rem;
        }
        .custom-option i {
            width: 1.2rem;
            text-align: center;
            color: var(--neon-v);
        }
        .custom-option:hover {
            background: rgba(155,93,229,0.12);
        }
        .custom-option.active {
            background: rgba(155,93,229,0.25);
            color: #fff;
        }
        .modal-body .custom-select-options {
            z-index: 99999;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <div class="page-header">
        <div class="page-header-icon"><i class="fas fa-sliders-h"></i></div>
        <div>
            <h1>About & Contact Management</h1>
            <p>Edit all content that appears on the public About & Contact page.</p>
        </div>
    </div>

    <?= getMessageHTML() ?>

    <!-- ══════ About Us Main ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-violet"><i class="fas fa-pen-nib"></i></div>
            <div>
                <h2>About Us — Main Content</h2>
                <span>Hero title and main description paragraph</span>
            </div>
        </div>
        <form method="post">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($about['title']) ?>" required placeholder="About page headline…">
            <label>Description</label>
            <textarea name="description" placeholder="Short intro paragraph…"><?= htmlspecialchars($about['description']) ?></textarea>
            <div class="form-actions">
                <button type="submit" name="update_about" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>

    <!-- ══════ About Sections ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-cyan"><i class="fas fa-layer-group"></i></div>
            <div>
                <h2>About Sections</h2>
                <span>Feature/value cards shown below the hero</span>
            </div>
        </div>

        <?php if (mysqli_num_rows($aboutSections) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Icon</th><th>Title</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($sec = mysqli_fetch_assoc($aboutSections)): ?>
            <tr>
                <td class="td-icon"><i class="<?= $sec['icon_class'] ?>"></i></td>
                <td><?= htmlspecialchars($sec['title']) ?></td>
                <td><?= $sec['sort_order'] ?></td>
                <td>
                    <div class="btn-actions">
                        <button type="button" class="btn btn-edit edit-section-btn"
                                data-id="<?= $sec['id'] ?>"
                                data-title="<?= htmlspecialchars($sec['title']) ?>"
                                data-description="<?= htmlspecialchars($sec['description']) ?>"
                                data-icon_class="<?= $sec['icon_class'] ?>"
                                data-sort_order="<?= $sec['sort_order'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form class="inline-form" method="post" onsubmit="return confirm('Delete this section?')">
                            <input type="hidden" name="delete_about_section" value="<?= $sec['id'] ?>">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-plus"></i> Add New Section</div>
        <form method="post">
            <input type="hidden" name="section_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Title</label>
                    <input type="text" name="title" required placeholder="Section title…">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <label>Description</label>
            <textarea name="description" placeholder="Short description for this section…"></textarea>
            <label>Icon</label>
            <?= iconSelectPreview('icon_class', 'fas fa-star', $icons) ?>
            <div class="form-actions">
                <button type="submit" name="save_about_section" class="btn btn-primary"><i class="fas fa-plus"></i> Add Section</button>
            </div>
        </form>
    </div>

    <!-- ══════ Contact Info ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-pink"><i class="fas fa-address-card"></i></div>
            <div>
                <h2>Contact Info</h2>
                <span>Address, working hours, and map embed</span>
            </div>
        </div>
        <form method="post">
            <label>Address</label>
            <textarea name="address" placeholder="Physical address…"><?= htmlspecialchars($contact['address']) ?></textarea>
            <label>Working Hours</label>
            <input type="text" name="working_hours" value="<?= htmlspecialchars($contact['working_hours']) ?>" placeholder="e.g. Mon–Fri, 9 AM – 6 PM">
            <label>Google Map Embed Code</label>
            <textarea name="map_embed" placeholder='Paste the &lt;iframe&gt; code from Google Maps…'><?= htmlspecialchars($contact['map_embed']) ?></textarea>
            <div class="form-actions">
                <button type="submit" name="update_contact" class="btn btn-primary"><i class="fas fa-save"></i> Save Contact Info</button>
            </div>
        </form>
    </div>

    <!-- ══════ Phones ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-green"><i class="fas fa-phone-alt"></i></div>
            <div><h2>Phone Numbers</h2><span>Listed in the contact section</span></div>
        </div>

        <?php if (mysqli_num_rows($phones) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Icon</th><th>Phone Number</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($p = mysqli_fetch_assoc($phones)): ?>
            <tr>
                <td class="td-icon"><i class="<?= $p['icon_class'] ?>"></i></td>
                <td><?= htmlspecialchars($p['phone_number']) ?></td>
                <td><?= $p['sort_order'] ?></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn btn-edit edit-phone-btn"
                            data-id="<?= $p['id'] ?>"
                            data-phone_number="<?= htmlspecialchars($p['phone_number']) ?>"
                            data-icon_class="<?= $p['icon_class'] ?>"
                            data-sort_order="<?= $p['sort_order'] ?>">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form class="inline-form" method="post" onsubmit="return confirm('Delete?')">
                        <input type="hidden" name="delete_phone" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-plus"></i> Add Phone</div>
        <form method="post">
            <input type="hidden" name="phone_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" required placeholder="+94 77 123 4567">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <label>Icon</label>
            <?= iconSelectPreview('icon_class', 'fas fa-phone-alt', $icons) ?>
            <div class="form-actions">
                <button type="submit" name="save_phone" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </div>
        </form>
    </div>

    <!-- ══════ Emails ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-violet"><i class="fas fa-envelope"></i></div>
            <div><h2>Email Addresses</h2><span>Shown in the contact section</span></div>
        </div>

        <?php if (mysqli_num_rows($emails) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Icon</th><th>Email</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($e = mysqli_fetch_assoc($emails)): ?>
            <tr>
                <td class="td-icon"><i class="<?= $e['icon_class'] ?>"></i></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= $e['sort_order'] ?></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn btn-edit edit-email-btn"
                            data-id="<?= $e['id'] ?>"
                            data-email="<?= htmlspecialchars($e['email']) ?>"
                            data-icon_class="<?= $e['icon_class'] ?>"
                            data-sort_order="<?= $e['sort_order'] ?>">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form class="inline-form" method="post" onsubmit="return confirm('Delete?')">
                        <input type="hidden" name="delete_email" value="<?= $e['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-plus"></i> Add Email</div>
        <form method="post">
            <input type="hidden" name="email_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="info@example.com">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <label>Icon</label>
            <?= iconSelectPreview('icon_class', 'fas fa-envelope', $icons) ?>
            <div class="form-actions">
                <button type="submit" name="save_email" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </div>
        </form>
    </div>

    <!-- ══════ Social Links ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-pink"><i class="fas fa-share-alt"></i></div>
            <div><h2>Social Media Links</h2><span>Facebook, YouTube, Instagram, etc.</span></div>
        </div>

        <?php if (mysqli_num_rows($socials) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Icon</th><th>Platform</th><th>URL</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($s = mysqli_fetch_assoc($socials)): ?>
            <tr>
                <td class="td-icon"><i class="<?= $s['icon_class'] ?>"></i></td>
                <td><?= htmlspecialchars($s['platform_name']) ?></td>
                <td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($s['url']) ?></td>
                <td><?= $s['sort_order'] ?></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn btn-edit edit-social-btn"
                            data-id="<?= $s['id'] ?>"
                            data-platform_name="<?= htmlspecialchars($s['platform_name']) ?>"
                            data-url="<?= htmlspecialchars($s['url']) ?>"
                            data-icon_class="<?= $s['icon_class'] ?>"
                            data-sort_order="<?= $s['sort_order'] ?>">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form class="inline-form" method="post" onsubmit="return confirm('Delete?')">
                        <input type="hidden" name="delete_social" value="<?= $s['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-plus"></i> Add Social Link</div>
        <form method="post">
            <input type="hidden" name="social_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Platform Name</label>
                    <input type="text" name="platform_name" placeholder="Facebook, YouTube…">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <label>URL</label>
            <input type="text" name="url" required placeholder="https://…">
            <label>Icon</label>
            <?= iconSelectPreview('icon_class', 'fas fa-share-alt', $icons) ?>
            <div class="form-actions">
                <button type="submit" name="save_social" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </div>
        </form>
    </div>

    <!-- ══════ Website Links ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-cyan"><i class="fas fa-globe"></i></div>
            <div><h2>Other Website Links</h2><span>External URLs displayed in the contact section</span></div>
        </div>

        <?php if (mysqli_num_rows($websites) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Icon</th><th>Title</th><th>URL</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($w = mysqli_fetch_assoc($websites)): ?>
            <tr>
                <td class="td-icon"><i class="<?= $w['icon_class'] ?>"></i></td>
                <td><?= htmlspecialchars($w['title']) ?></td>
                <td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($w['url']) ?></td>
                <td><?= $w['sort_order'] ?></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn btn-edit edit-web-btn"
                            data-id="<?= $w['id'] ?>"
                            data-title="<?= htmlspecialchars($w['title']) ?>"
                            data-url="<?= htmlspecialchars($w['url']) ?>"
                            data-icon_class="<?= $w['icon_class'] ?>"
                            data-sort_order="<?= $w['sort_order'] ?>">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form class="inline-form" method="post" onsubmit="return confirm('Delete?')">
                        <input type="hidden" name="delete_web" value="<?= $w['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-plus"></i> Add Website Link</div>
        <form method="post">
            <input type="hidden" name="web_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Our Website">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <label>URL</label>
            <input type="text" name="url" required placeholder="https://…">
            <label>Icon</label>
            <?= iconSelectPreview('icon_class', 'fas fa-globe', $icons) ?>
            <div class="form-actions">
                <button type="submit" name="save_web" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
            </div>
        </form>
    </div>

    <!-- ══════ Team Members ══════ -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon ci-orange"><i class="fas fa-users"></i></div>
            <div><h2>Team Members</h2><span>Photos, roles, and bios for the Meet Our Team section</span></div>
        </div>

        <?php if (mysqli_num_rows($teamMembers) > 0): ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Photo</th><th>Name</th><th>Position</th><th>Sort</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($tm = mysqli_fetch_assoc($teamMembers)): ?>
            <tr>
                <td><img src="<?= $tm['photo'] ?: 'assets/default-avatar.png' ?>" class="team-thumb" alt="<?= htmlspecialchars($tm['name']) ?>"></td>
                <td><?= htmlspecialchars($tm['name']) ?></td>
                <td><?= htmlspecialchars($tm['position']) ?></td>
                <td><?= $tm['sort_order'] ?></td>
                <td><div class="btn-actions">
                    <button type="button" class="btn btn-edit edit-team-btn"
                            data-id="<?= $tm['id'] ?>"
                            data-name="<?= htmlspecialchars($tm['name']) ?>"
                            data-position="<?= htmlspecialchars($tm['position']) ?>"
                            data-bio="<?= htmlspecialchars($tm['bio']) ?>"
                            data-photo="<?= $tm['photo'] ?>"
                            data-sort_order="<?= $tm['sort_order'] ?>">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form class="inline-form" method="post" onsubmit="return confirm('Delete this team member?')">
                        <input type="hidden" name="delete_team" value="<?= $tm['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="neon-line"></div>
        <div class="form-sub-heading"><i class="fas fa-user-plus"></i> Add New Member</div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="member_id" value="">
            <div class="form-grid-2">
                <div>
                    <label>Full Name <span style="color:var(--neon-p)">*</span></label>
                    <input type="text" name="name" required placeholder="Full name…">
                </div>
                <div>
                    <label>Position / Role</label>
                    <input type="text" name="position" placeholder="Lead Developer…">
                </div>
            </div>
            <label>Bio</label>
            <textarea name="bio" placeholder="Short bio about this team member…"></textarea>
            <div class="form-grid-2">
                <div>
                    <label>Photo</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="text" name="sort_order" value="0">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_team" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Member</button>
            </div>
        </form>
    </div>

</div>

<!-- ═══════════════ EDIT MODALS ═══════════════ -->

<!-- Edit Section Modal -->
<div class="modal-overlay" id="editSectionModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit About Section</h3>
            <button type="button" class="modal-close" onclick="closeModal('editSectionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editSectionForm">
                <input type="hidden" name="section_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" required placeholder="Section title…">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <label>Description</label>
                <textarea name="description" placeholder="Short description…"></textarea>
                <label>Icon</label>
                <?= iconSelectPreview('icon_class', 'fas fa-star', $icons) ?>
                <div class="form-actions">
                    <button type="submit" name="save_about_section" class="btn btn-primary"><i class="fas fa-save"></i> Update Section</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editSectionModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Phone Modal -->
<div class="modal-overlay" id="editPhoneModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Phone Number</h3>
            <button type="button" class="modal-close" onclick="closeModal('editPhoneModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editPhoneForm">
                <input type="hidden" name="phone_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" required placeholder="+94 77 123 4567">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <label>Icon</label>
                <?= iconSelectPreview('icon_class', 'fas fa-phone-alt', $icons) ?>
                <div class="form-actions">
                    <button type="submit" name="save_phone" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editPhoneModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Email Modal -->
<div class="modal-overlay" id="editEmailModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Email Address</h3>
            <button type="button" class="modal-close" onclick="closeModal('editEmailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editEmailForm">
                <input type="hidden" name="email_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="info@example.com">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <label>Icon</label>
                <?= iconSelectPreview('icon_class', 'fas fa-envelope', $icons) ?>
                <div class="form-actions">
                    <button type="submit" name="save_email" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editEmailModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Social Modal -->
<div class="modal-overlay" id="editSocialModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Social Link</h3>
            <button type="button" class="modal-close" onclick="closeModal('editSocialModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editSocialForm">
                <input type="hidden" name="social_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Platform Name</label>
                        <input type="text" name="platform_name" placeholder="Facebook, YouTube…">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <label>URL</label>
                <input type="text" name="url" required placeholder="https://…">
                <label>Icon</label>
                <?= iconSelectPreview('icon_class', 'fas fa-share-alt', $icons) ?>
                <div class="form-actions">
                    <button type="submit" name="save_social" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editSocialModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Web Link Modal -->
<div class="modal-overlay" id="editWebModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Edit Website Link</h3>
            <button type="button" class="modal-close" onclick="closeModal('editWebModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" id="editWebForm">
                <input type="hidden" name="web_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Our Website">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <label>URL</label>
                <input type="text" name="url" required placeholder="https://…">
                <label>Icon</label>
                <?= iconSelectPreview('icon_class', 'fas fa-globe', $icons) ?>
                <div class="form-actions">
                    <button type="submit" name="save_web" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editWebModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Team Member Modal -->
<div class="modal-overlay" id="editTeamModal">
    <div class="modal-dialog wide">
        <div class="modal-header">
            <h3>Edit Team Member</h3>
            <button type="button" class="modal-close" onclick="closeModal('editTeamModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" enctype="multipart/form-data" id="editTeamForm">
                <input type="hidden" name="member_id" value="">
                <div class="form-grid-2">
                    <div>
                        <label>Full Name <span style="color:var(--neon-p)">*</span></label>
                        <input type="text" name="name" required placeholder="Full name…">
                    </div>
                    <div>
                        <label>Position / Role</label>
                        <input type="text" name="position" placeholder="Lead Developer…">
                    </div>
                </div>
                <label>Bio</label>
                <textarea name="bio" placeholder="Short bio…"></textarea>
                <div class="form-grid-2">
                    <div>
                        <label>Photo <span style="color:var(--w90);font-weight:400;text-transform:none;letter-spacing:0">(leave empty to keep current)</span></label>
                        <input type="file" name="photo" accept="image/*">
                        <div id="editTeamCurrentPhoto" style="margin-top:0.5rem; display:none;">
                            <img src="" style="width:50px; height:50px; border-radius:50%; object-fit:cover; border:2px solid var(--neon-p);" alt="Current photo">
                        </div>
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" value="0">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" name="save_team" class="btn btn-primary"><i class="fas fa-save"></i> Update Member</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editTeamModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ═══════════════ CUSTOM SELECT + MODALS JS ═══════════════
let modalStack = [];

function openModal(modalId) {
    const overlay = document.getElementById(modalId);
    if (!overlay) return;
    overlay.classList.add('active');
    if (!modalStack.includes(modalId)) modalStack.push(modalId);
    document.addEventListener('keydown', handleModalEscape, true);
}
function closeModal(modalId) {
    const overlay = document.getElementById(modalId);
    if (overlay) overlay.classList.remove('active');
    modalStack = modalStack.filter(id => id !== modalId);
    if (modalStack.length === 0) document.removeEventListener('keydown', handleModalEscape, true);
}
function closeTopModal() {
    if (modalStack.length > 0) closeModal(modalStack[modalStack.length - 1]);
}
function handleModalEscape(e) {
    if (e.key === 'Escape') { e.preventDefault(); closeTopModal(); }
}
document.addEventListener('click', function(e) {
    const overlay = e.target.closest('.modal-overlay');
    if (overlay && !e.target.closest('.modal-dialog')) {
        closeModal(overlay.id);
    }
});

// ── Custom Select Logic ──
document.addEventListener('click', function(e) {
    // Toggle custom select
    const btn = e.target.closest('.custom-select-btn');
    if (btn) {
        e.preventDefault();
        const wrapper = btn.closest('.custom-icon-select');
        if (!wrapper) return;
        document.querySelectorAll('.custom-icon-select.open').forEach(el => {
            if (el !== wrapper) el.classList.remove('open');
        });
        wrapper.classList.toggle('open');
        return;
    }
    // Select an option
    const option = e.target.closest('.custom-option');
    if (option) {
        e.preventDefault();
        const wrapper = option.closest('.custom-icon-select');
        if (!wrapper) return;
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const button = wrapper.querySelector('.custom-select-btn');
        const iconClass = option.dataset.value;
        const label = option.dataset.label;
        hiddenInput.value = iconClass;
        // Update button: icon + label + caret (keep upward chevron)
        button.innerHTML = '<i class="'+iconClass+'"></i><span class="cust-sel-label">'+label+'</span><i class="fas fa-chevron-up caret-icon"></i>';
        wrapper.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('active'));
        option.classList.add('active');
        wrapper.classList.remove('open');
        return;
    }
    // Click outside closes select
    if (!e.target.closest('.custom-icon-select')) {
        document.querySelectorAll('.custom-icon-select.open').forEach(el => el.classList.remove('open'));
    }
});

// ── Edit Button Handlers ──
document.addEventListener('click', function(e) {
    const target = e.target.closest('button');
    if (!target) return;

    if (target.classList.contains('edit-section-btn')) {
        const btn = target;
        const form = document.getElementById('editSectionForm');
        form.querySelector('input[name="section_id"]').value = btn.dataset.id;
        form.querySelector('input[name="title"]').value = btn.dataset.title;
        form.querySelector('textarea[name="description"]').value = btn.dataset.description;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        updateCustomSelect(form, 'icon_class', btn.dataset.icon_class);
        openModal('editSectionModal');
    }
    else if (target.classList.contains('edit-phone-btn')) {
        const btn = target;
        const form = document.getElementById('editPhoneForm');
        form.querySelector('input[name="phone_id"]').value = btn.dataset.id;
        form.querySelector('input[name="phone_number"]').value = btn.dataset.phone_number;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        updateCustomSelect(form, 'icon_class', btn.dataset.icon_class);
        openModal('editPhoneModal');
    }
    else if (target.classList.contains('edit-email-btn')) {
        const btn = target;
        const form = document.getElementById('editEmailForm');
        form.querySelector('input[name="email_id"]').value = btn.dataset.id;
        form.querySelector('input[name="email"]').value = btn.dataset.email;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        updateCustomSelect(form, 'icon_class', btn.dataset.icon_class);
        openModal('editEmailModal');
    }
    else if (target.classList.contains('edit-social-btn')) {
        const btn = target;
        const form = document.getElementById('editSocialForm');
        form.querySelector('input[name="social_id"]').value = btn.dataset.id;
        form.querySelector('input[name="platform_name"]').value = btn.dataset.platform_name;
        form.querySelector('input[name="url"]').value = btn.dataset.url;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        updateCustomSelect(form, 'icon_class', btn.dataset.icon_class);
        openModal('editSocialModal');
    }
    else if (target.classList.contains('edit-web-btn')) {
        const btn = target;
        const form = document.getElementById('editWebForm');
        form.querySelector('input[name="web_id"]').value = btn.dataset.id;
        form.querySelector('input[name="title"]').value = btn.dataset.title;
        form.querySelector('input[name="url"]').value = btn.dataset.url;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        updateCustomSelect(form, 'icon_class', btn.dataset.icon_class);
        openModal('editWebModal');
    }
    else if (target.classList.contains('edit-team-btn')) {
        const btn = target;
        const form = document.getElementById('editTeamForm');
        form.querySelector('input[name="member_id"]').value = btn.dataset.id;
        form.querySelector('input[name="name"]').value = btn.dataset.name;
        form.querySelector('input[name="position"]').value = btn.dataset.position;
        form.querySelector('textarea[name="bio"]').value = btn.dataset.bio;
        form.querySelector('input[name="sort_order"]').value = btn.dataset.sort_order;
        const photoDiv = document.getElementById('editTeamCurrentPhoto');
        if (btn.dataset.photo) {
            photoDiv.style.display = 'block';
            photoDiv.querySelector('img').src = btn.dataset.photo;
        } else {
            photoDiv.style.display = 'none';
        }
        openModal('editTeamModal');
    }
});

function updateCustomSelect(form, inputName, iconClass) {
    const wrapper = form.querySelector('.custom-icon-select[data-name="'+inputName+'"]');
    if (!wrapper) return;
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');
    const button = wrapper.querySelector('.custom-select-btn');
    const option = wrapper.querySelector('.custom-option[data-value="'+iconClass+'"]');
    const label = option ? option.dataset.label : 'Select Icon';
    hiddenInput.value = iconClass;
    button.innerHTML = '<i class="'+iconClass+'"></i><span class="cust-sel-label">'+label+'</span><i class="fas fa-chevron-up caret-icon"></i>';
    wrapper.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('active'));
    if (option) option.classList.add('active');
}
</script>
</body>
</html>