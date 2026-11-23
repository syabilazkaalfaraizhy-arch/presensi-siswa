document.addEventListener('DOMContentLoaded', () => {
    loadLeaderboard();
    setupLiveClock();

    const navs = document.querySelectorAll('header nav');

    navs.forEach((nav) => {
        const links = nav.querySelectorAll('a');
        const toggle = nav.parentElement.querySelector('.nav-toggle');

        if (toggle) {
            toggle.addEventListener('click', () => {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!isOpen));
                toggle.setAttribute('aria-label', isOpen ? 'Buka menu navigasi' : 'Tutup menu navigasi');
                nav.classList.toggle('menu-open', !isOpen);
            });

            links.forEach((link) => {
                link.addEventListener('click', () => {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.setAttribute('aria-label', 'Buka menu navigasi');
                    nav.classList.remove('menu-open');
                });
            });
        }

        const moveUnderline = (link) => {
            const navRect = nav.getBoundingClientRect();
            const linkRect = link.getBoundingClientRect();

            nav.style.setProperty('--underline-left', `${linkRect.left - navRect.left}px`);
            nav.style.setProperty('--underline-top', `${linkRect.bottom - navRect.top - 2}px`);
            nav.style.setProperty('--underline-width', `${linkRect.width}px`);
            nav.classList.add('underline-visible');
        };

        links.forEach((link) => {
            link.addEventListener('pointerenter', () => moveUnderline(link));
            link.addEventListener('focus', () => moveUnderline(link));
        });

        nav.addEventListener('pointerleave', () => {
            nav.classList.remove('underline-visible');
        });

        nav.addEventListener('focusout', (event) => {
            if (!nav.contains(event.relatedTarget)) {
                nav.classList.remove('underline-visible');
            }
        });

        window.addEventListener('resize', () => {
            const hoveredLink = nav.querySelector('a:hover');
            const focusedLink = document.activeElement instanceof HTMLAnchorElement
                && nav.contains(document.activeElement)
                ? document.activeElement
                : null;

            if (hoveredLink) {
                moveUnderline(hoveredLink);
            } else if (focusedLink) {
                moveUnderline(focusedLink);
            } else {
                nav.classList.remove('underline-visible');
            }
        });
    });
});

function setupLiveClock() {
    const dateElement = document.querySelector('#live-date');
    const timeElement = document.querySelector('#live-time');
    if (!dateElement || !timeElement) return;

    const timezone = 'Asia/Makassar';
    const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: timezone,
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
    const timeFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: timezone,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hourCycle: 'h23',
    });

    const updateClock = () => {
        const now = new Date();
        dateElement.textContent = dateFormatter.format(now);
        timeElement.textContent = `${timeFormatter.format(now)} WITA`;
        timeElement.dateTime = now.toISOString();
    };

    updateClock();
    window.setInterval(updateClock, 1000);
}

async function loadLeaderboard() {
    const container = document.querySelector('#leaderboard-content');
    if (!container) return;

    try {
        const response = await fetch('pages/presensi/leaderboard.php', {
            headers: { Accept: 'application/json' },
        });
        if (!response.ok) throw new Error('Gagal memuat leaderboard');

        const classes = await response.json();
        if (!Array.isArray(classes) || classes.length === 0 || classes.every((item) => Number(item.total_presensi) === 0)) {
            container.replaceChildren(createLeaderboardMessage('Belum ada data presensi untuk ditampilkan.'));
            return;
        }

        const list = document.createElement('ol');
        list.className = 'leaderboard-list';

        classes.forEach((item, index) => {
            const row = document.createElement('li');
            row.className = 'leaderboard-item';

            const rank = document.createElement('span');
            rank.className = 'leaderboard-rank';
            rank.textContent = String(index + 1).padStart(2, '0');

            const details = document.createElement('div');
            details.className = 'leaderboard-details';

            const className = document.createElement('strong');
            className.className = 'leaderboard-class-name';
            className.textContent = item.nama_kelas;

            const contributor = document.createElement('span');
            contributor.className = 'leaderboard-student';
            contributor.textContent = item.nama_siswa_teratas
                ? `Terbanyak mencatat: ${item.nama_siswa_teratas} (${item.presensi_siswa_teratas} tap)`
                : 'Belum ada catatan presensi';

            const count = document.createElement('span');
            count.className = 'leaderboard-count';
            count.textContent = `${item.total_presensi} presensi`;

            details.append(className, contributor);
            row.append(rank, details, count);
            list.append(row);
        });

        container.replaceChildren(list);
    } catch (error) {
        container.replaceChildren(createLeaderboardMessage('Leaderboard belum bisa dimuat. Coba muat ulang halaman.'));
    }
}

function createLeaderboardMessage(message) {
    const paragraph = document.createElement('p');
    paragraph.className = 'leaderboard-status';
    paragraph.textContent = message;
    return paragraph;
}
