<style>
    /* ── Default Dark Theme (Cookie නැති විට හෝ user_theme = Dark) ── */
    body {
        background: #070b15 !important;
        transition: background 0.4s ease;
    }

    /* Starfield canvas */
    #starfield {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        opacity: 0.7;
        transition: opacity 0.4s ease;
    }

    /* ── Light Theme Styles ── */
    body.light-theme {
        background: #f0f2f5 !important;
    }

    body.light-theme #starfield {
        opacity: 0.25;
    }

    /* Main container sits above stars */
    .container {
        position: relative;
        z-index: 1;
        max-width: 1300px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profile-wrapper {
        max-width: 860px;
        margin: 0 auto;
        padding: 40px 20px 80px;
        position: relative;
        z-index: 1;
    }

    .pw-wrapper {
        max-width: 520px;
        margin: 0 auto;
        padding: 40px 20px 80px;
        position: relative;
        z-index: 1;
    }
</style>

<canvas id="starfield"></canvas>

<script>
    // ── Cookie Helper ──
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // ── Theme Detection (user_settings cookie) ──
    function getCurrentTheme() {
        const userSettings = getCookie('user_settings');
        if (!userSettings) return 'dark'; // default to dark

        try {
            const parsed = JSON.parse(userSettings);
            if (parsed.user_theme) {
                return parsed.user_theme.toLowerCase();
            }
        } catch (e) {
            // plain text like "Dark" or "Light"
            if (userSettings.toLowerCase().includes('dark')) return 'dark';
            if (userSettings.toLowerCase().includes('light')) return 'light';
        }
        return 'dark';
    }

    // Apply theme class to body
    const theme = getCurrentTheme();
    if (theme === 'light') {
        document.body.classList.add('light-theme');
    }

    // ── Starfield Animation (Theme Colors) ──
    const canvas = document.getElementById('starfield');
    const ctx = canvas.getContext('2d');
    let w, h;
    const stars = [];
    const maxStars = 120;

    function initStars() {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
        stars.length = 0;
        for (let i = 0; i < maxStars; i++) {
            stars.push({
                x: Math.random() * w,
                y: Math.random() * h,
                radius: Math.random() * 2 + 0.5,
                speed: Math.random() * 0.3 + 0.1,
                opacity: Math.random(),
                direction: Math.random() > 0.5 ? 1 : -1
            });
        }
    }

    function drawStars() {
        ctx.clearRect(0, 0, w, h);

        // Current theme check (allows live class changes)
        const isLight = document.body.classList.contains('light-theme');

        const starColor = isLight
            ? (opacity) => `rgba(71, 85, 105, ${opacity})`   // Slate gray
            : (opacity) => `rgba(167, 139, 250, ${opacity})`; // Purple

        const shadowColor = isLight
            ? 'rgba(100, 116, 139, 0.3)'
            : 'rgba(124, 92, 252, 0.7)';

        for (let star of stars) {
            star.y += star.speed * star.direction;
            if (star.y < -10) star.y = h + 10;
            if (star.y > h + 10) star.y = -10;

            ctx.beginPath();
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fillStyle = starColor(star.opacity);
            ctx.shadowBlur = 6;
            ctx.shadowColor = shadowColor;
            ctx.fill();
        }
        ctx.shadowBlur = 0;
        requestAnimationFrame(drawStars);
    }

    window.addEventListener('resize', initStars);
    initStars();
    drawStars();
</script>