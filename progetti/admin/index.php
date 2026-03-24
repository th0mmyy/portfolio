<?php
// admin/index.php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../db.php';

$projects = db()->query("
    SELECT p.*, ph.filename AS cover,
           (SELECT GROUP_CONCAT(tech ORDER BY id SEPARATOR ', ')
            FROM project_techs WHERE project_id = p.id) AS techs
    FROM projects p
    LEFT JOIN project_photos ph ON ph.project_id = p.id AND ph.is_cover = 1
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Progetti</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .admin-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 5rem;
        }

        .admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 0 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .admin-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .admin-header-actions {
            display: flex;
            gap: .5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: .9rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--surface);
        }

        .td-cover {
            width: 80px;
        }

        .td-cover img {
            width: 72px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .td-cover-placeholder {
            width: 72px;
            height: 48px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .actions {
            display: flex;
            gap: .4rem;
        }

        .actions a,
        .actions button {
            font-size: .78rem;
            font-weight: 600;
            padding: .3rem .75rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--muted);
            background: transparent;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
        }

        .actions a:hover {
            border-color: var(--border-a);
            color: var(--accent-l);
        }

        .actions .del-btn:hover {
            border-color: rgba(239, 68, 68, .4);
            color: #f87171;
            background: rgba(239, 68, 68, .08);
        }

        .tag {
            padding: .18rem .6rem;
            border-radius: 50px;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
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

        .toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(34, 197, 94, .9);
            color: #fff;
            padding: .75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: .9rem;
            opacity: 0;
            transition: opacity .3s;
            pointer-events: none;
            z-index: 9999;
        }

        .toast.show {
            opacity: 1;
        }

        .empty {
            text-align: center;
            padding: 4rem;
            color: var(--muted);
        }
    </style>
</head>

<body>
    <div id="scroll-progress"></div>
    <button id="theme-toggle" aria-label="Cambia tema"><span class="icon-dark">🌙</span><span
            class="icon-light">☀️</span></button>

    <div class="admin-wrap">
        <div class="admin-header">
            <div>
                <span class="label">Pannello Admin</span>
                <h1>Progetti</h1>
            </div>
            <div class="admin-header-actions">
                <a class="btn ghost" href="../progetti.php">← Sito</a>
                <a class="btn primary" href="edit.php">＋ Nuovo</a>
                <a class="btn ghost" href="logout.php">Logout</a>
            </div>
        </div>

        <?php if (empty($projects)): ?>
            <div class="empty">
                <p style="font-size:3rem">📂</p>
                <p>Nessun progetto. <a href="edit.php" style="color:var(--accent-l)">Crea il primo!</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th class="td-cover">Cover</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Modalità</th>
                        <th>Tecnologie</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td class="td-cover">
                                <?php if ($p['cover']): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($p['cover']) ?>" alt="">
                                <?php else: ?>
                                    <div class="td-cover-placeholder">🚀</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                            <td><span class="tag <?= $p['tipo'] ?>"><?= ucfirst($p['tipo']) ?></span></td>
                            <td><span class="tag <?= $p['modalita'] ?>"><?= ucfirst($p['modalita']) ?></span></td>
                            <td style="color:var(--muted);font-size:.82rem;"><?= htmlspecialchars($p['techs'] ?? '—') ?></td>
                            <td style="color:var(--muted);font-size:.82rem;"><?= date('d/m/Y', strtotime($p['created_at'])) ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?= $p['id'] ?>">✏️ Modifica</a>
                                    <button class="del-btn" data-id="<?= $p['id'] ?>"
                                        data-name="<?= htmlspecialchars($p['nome']) ?>">🗑 Elimina</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Delete confirm dialog (custom, no alert()) -->
    <div id="del-dialog"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9990;align-items:center;justify-content:center;">
        <div
            style="background:var(--bg-card);border:1px solid var(--border);border-radius:20px;padding:2rem;max-width:380px;width:90%;text-align:center;">
            <p style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem;">Eliminare il progetto?</p>
            <p id="del-name" style="color:var(--muted);font-size:.9rem;margin-bottom:1.5rem;"></p>
            <div style="display:flex;gap:.5rem;justify-content:center;">
                <button class="btn ghost"
                    onclick="document.getElementById('del-dialog').style.display='none'">Annulla</button>
                <form method="post" action="delete.php" id="del-form" style="display:inline">
                    <input type="hidden" name="id" id="del-id">
                    <button type="submit" class="btn primary"
                        style="background:linear-gradient(135deg,#ef4444,#dc2626);">Elimina</button>
                </form>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <button id="btt" aria-label="Torna su">↑</button>

    <script src="../../js/main.js"></script>
    <script>
        document.querySelectorAll('.del-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('del-id').value = btn.dataset.id;
                document.getElementById('del-name').textContent = btn.dataset.name;
                document.getElementById('del-dialog').style.display = 'flex';
            });
        });

        <?php if (!empty($_GET['ok'])): ?>
                (function () {
                    const t = document.getElementById('toast');
                    t.textContent = '<?= addslashes(htmlspecialchars($_GET['ok'])) ?>';
                    t.classList.add('show');
                    setTimeout(() => t.classList.remove('show'), 3000);
                })();
        <?php endif; ?>
    </script>
</body>

</html>