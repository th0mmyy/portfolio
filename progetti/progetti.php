<?php
require_once __DIR__ . '/db.php';

// ── Fetch all projects with cover photo and techs ──
$projects = db()->query("
    SELECT p.*,
           ph.filename AS cover
    FROM   projects p
    LEFT JOIN project_photos ph
           ON ph.project_id = p.id AND ph.is_cover = 1
    ORDER  BY p.created_at DESC
")->fetchAll();

foreach ($projects as &$proj) {
    $stmt = db()->prepare("SELECT tech FROM project_techs WHERE project_id = ? ORDER BY id");
    $stmt->execute([$proj['id']]);
    $proj['techs'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
unset($proj);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progetti — Thomas Manzoni</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ── Projects page extras ── */
        .page-hero {
            padding: 7rem 1.25rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-hero .orb {
            position: absolute;
        }

        /* Filters */
        .filter-row {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 2.5rem;
        }

        .filter-btn {
            padding: .4rem 1.1rem;
            border-radius: 50px;
            font-size: .85rem;
            font-weight: 600;
            border: 1px solid var(--border);
            color: var(--muted);
            background: transparent;
            cursor: pointer;
            transition: all .2s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(99, 102, 241, .12);
            border-color: var(--border-a);
            color: var(--accent-l);
        }

        /* Grid */
        .proj-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media(min-width:640px) {
            .proj-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(min-width:1024px) {
            .proj-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Card */
        .proj-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: border-color .25s, transform .25s, box-shadow .25s;
        }

        .proj-card:hover {
            border-color: var(--border-a);
            transform: translateY(-4px);
            box-shadow: 0 16px 40px var(--shadow);
        }

        /* Cover */
        .proj-thumb {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            display: block;
        }

        .proj-thumb-placeholder {
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        /* Body */
        .proj-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .proj-meta {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .tag {
            padding: .18rem .65rem;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .tag.scolastico {
            background: rgba(34, 197, 94, .12);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, .25);
        }

        .tag.personale {
            background: rgba(99, 102, 241, .12);
            color: var(--accent-l);
            border: 1px solid rgba(99, 102, 241, .25);
        }

        .tag.singolo {
            background: var(--surface);
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .tag.gruppo {
            background: rgba(236, 72, 153, .1);
            color: #ec4899;
            border: 1px solid rgba(236, 72, 153, .25);
        }

        .proj-title {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .proj-desc {
            font-size: .88rem;
            color: var(--muted);
            line-height: 1.6;
            flex: 1;
        }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
        }

        .tech-pill {
            padding: .2rem .6rem;
            border-radius: 6px;
            font-size: .72rem;
            font-weight: 600;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .proj-links {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin-top: auto;
            padding-top: .5rem;
            border-top: 1px solid var(--border);
        }

        .proj-links a {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .82rem;
            font-weight: 600;
            color: var(--muted);
            transition: color .2s;
        }

        .proj-links a:hover {
            color: var(--accent-l);
        }

        /* Lightbox */
        .lb-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .85);
            z-index: 9990;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .lb-overlay.open {
            display: flex;
        }

        .lb-box {
            position: relative;
            max-width: 900px;
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
        }

        .lb-box img {
            width: 100%;
            display: block;
            max-height: 80vh;
            object-fit: contain;
            background: #000;
        }

        .lb-close {
            position: absolute;
            top: .75rem;
            right: .75rem;
            width: 36px;
            height: 36px;
            background: rgba(0, 0, 0, .6);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
        }

        .lb-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, .5);
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
        }

        .lb-prev {
            left: .75rem;
        }

        .lb-next {
            right: .75rem;
        }

        /* Gallery thumbnails on card click */
        .gallery-strip {
            display: flex;
            gap: .4rem;
            padding: 0 1.25rem 1.25rem;
            overflow-x: auto;
        }

        .gallery-strip img {
            width: 64px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color .2s, transform .2s;
        }

        .gallery-strip img:hover {
            border-color: var(--border-a);
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 5rem 1rem;
            color: var(--muted);
        }

        .empty-state p {
            margin-top: .5rem;
        }
    </style>
</head>

<body>
    <div id="scroll-progress"></div>

    <button id="theme-toggle" aria-label="Cambia tema">
        <span class="icon-dark">🌙</span>
        <span class="icon-light">☀️</span>
    </button>

    <!-- Hero -->
    <div class="page-hero">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div style="position:relative;z-index:2;max-width:640px;margin:0 auto;">
            <span class="label">Portfolio</span>
            <h1 style="font-size:clamp(2.5rem,8vw,5rem);margin-top:.5rem;">Progetti</h1>
            <p class="sub">Una raccolta dei miei lavori — scolastici e personali.</p>
        </div>
    </div>

    <!-- Main -->
    <main class="section" style="padding-top:1rem;">

        <!-- Filters -->
        <div class="filter-row">
            <button class="filter-btn active" data-filter="all">Tutti</button>
            <button class="filter-btn" data-filter="scolastico">Scolastici</button>
            <button class="filter-btn" data-filter="personale">Personali</button>
            <button class="filter-btn" data-filter="singolo">Singolo</button>
            <button class="filter-btn" data-filter="gruppo">Gruppo</button>
        </div>

        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <span style="font-size:3rem">📂</span>
                <p>Nessun progetto ancora. <a href="admin/" style="color:var(--accent-l)">Aggiungine uno</a>.</p>
            </div>
        <?php else: ?>
            <div class="proj-grid">
                <?php foreach ($projects as $p):
                    $photos = db()->prepare("SELECT filename FROM project_photos WHERE project_id = ? ORDER BY is_cover DESC, sort_order ASC");
                    $photos->execute([$p['id']]);
                    $allPhotos = $photos->fetchAll(PDO::FETCH_COLUMN);
                    ?>
                    <article class="proj-card fade" data-tipo="<?= htmlspecialchars($p['tipo']) ?>"
                        data-modalita="<?= htmlspecialchars($p['modalita']) ?>">

                        <!-- Cover -->
                        <?php if ($p['cover']): ?>
                            <img class="proj-thumb" src="../uploads/<?= htmlspecialchars($p['cover']) ?>"
                                alt="<?= htmlspecialchars($p['nome']) ?>"
                                style="cursor:<?= count($allPhotos) > 1 ? 'pointer' : 'default' ?>" <?php if (count($allPhotos) > 1): ?>onclick="openLb(<?= $p['id'] ?>, 0)" <?php endif ?>>
                        <?php else: ?>
                            <div class="proj-thumb-placeholder">🚀</div>
                        <?php endif; ?>

                        <!-- Body -->
                        <div class="proj-body">
                            <div class="proj-meta">
                                <span class="tag <?= $p['tipo'] ?>"><?= ucfirst($p['tipo']) ?></span>
                                <span class="tag <?= $p['modalita'] ?>"><?= ucfirst($p['modalita']) ?></span>
                            </div>

                            <h2 class="proj-title"><?= htmlspecialchars($p['nome']) ?></h2>
                            <?php if ($p['periodo']): ?>
                                <p style="font-size:.8rem;color:var(--accent-l);font-weight:600;">📅
                                    <?= htmlspecialchars($p['periodo']) ?></p>
                            <?php endif; ?>
                            <p class="proj-desc"><?= nl2br(htmlspecialchars($p['descrizione'])) ?></p>

                            <?php if (!empty($p['techs'])): ?>
                                <div class="tech-list">
                                    <?php foreach ($p['techs'] as $t): ?>
                                        <span class="tech-pill"><?= htmlspecialchars($t) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($p['demo_url'] || $p['github_url'] || $p['doc_filename']): ?>
                                <div class="proj-links">
                                    <?php if ($p['demo_url']): ?>
                                        <a href="<?= htmlspecialchars($p['demo_url']) ?>" target="_blank" rel="noopener">
                                            🔗 Demo live
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($p['github_url']): ?>
                                        <a href="<?= htmlspecialchars($p['github_url']) ?>" target="_blank" rel="noopener">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82a7.65 7.65 0 012-.27c.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                                            </svg>
                                            GitHub
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($p['doc_filename']): ?>
                                        <a href="../uploads/<?= htmlspecialchars($p['doc_filename']) ?>" download>
                                            <?= $p['doc_type'] === 'pdf' ? '📕' : '📝' ?> Documentazione
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Thumbnail strip (if multiple photos) -->
                        <?php if (count($allPhotos) > 1): ?>
                            <div class="gallery-strip" id="strip-<?= $p['id'] ?>">
                                <?php foreach ($allPhotos as $i => $fn): ?>
                                    <img src="../uploads/<?= htmlspecialchars($fn) ?>" alt="Foto <?= $i + 1 ?>"
                                        onclick="openLb(<?= $p['id'] ?>, <?= $i ?>)">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Hidden photo list for lightbox -->
                        <script>
                            window._photos = window._photos || {};
                            window._photos[<?= $p['id'] ?>] = <?= json_encode($allPhotos) ?>;
                        </script>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Lightbox -->
    <div class="lb-overlay" id="lb" onclick="if(event.target===this)closeLb()">
        <div class="lb-box">
            <button class="lb-close" onclick="closeLb()">✕</button>
            <button class="lb-nav lb-prev" onclick="lbNav(-1)">‹</button>
            <img id="lb-img" src="" alt="">
            <button class="lb-nav lb-next" onclick="lbNav(1)">›</button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-links">
            <a href="../index.html">← Home</a>
        </div>
        <p>© <?= date('Y') ?> Thomas Manzoni</p>
    </footer>

    <button id="btt" aria-label="Torna su">↑</button>

    <script src="../js/main.js"></script>
    <script>
        /* ── Filters ── */
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const f = btn.dataset.filter;
                document.querySelectorAll('.proj-card').forEach(card => {
                    const show = f === 'all' || card.dataset.tipo === f || card.dataset.modalita === f;
                    card.style.display = show ? '' : 'none';
                });
            });
        });

        /* ── Lightbox ── */
        let _curProj = null, _curIdx = 0;

        function openLb(projId, idx) {
            _curProj = projId; _curIdx = idx;
            document.getElementById('lb-img').src = '../uploads/' + window._photos[projId][idx];
            document.getElementById('lb').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeLb() {
            document.getElementById('lb').classList.remove('open');
            document.body.style.overflow = '';
        }
        function lbNav(dir) {
            const photos = window._photos[_curProj];
            _curIdx = (_curIdx + dir + photos.length) % photos.length;
            document.getElementById('lb-img').src = '../uploads/' + photos[_curIdx];
        }
        document.addEventListener('keydown', e => {
            if (!document.getElementById('lb').classList.contains('open')) return;
            if (e.key === 'Escape') closeLb();
            if (e.key === 'ArrowRight') lbNav(1);
            if (e.key === 'ArrowLeft') lbNav(-1);
        });
    </script>
</body>

</html>