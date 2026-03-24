<?php
// admin/edit.php  — create or update a project
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$isEdit = $id !== null;
$proj = null;
$techs = [];
$photos = [];

if ($isEdit) {
    $proj = db()->prepare("SELECT * FROM projects WHERE id = ?");
    $proj->execute([$id]);
    $proj = $proj->fetch();
    if (!$proj) {
        header('Location: index.php');
        exit;
    }

    $t = db()->prepare("SELECT tech FROM project_techs WHERE project_id = ? ORDER BY id");
    $t->execute([$id]);
    $techs = $t->fetchAll(PDO::FETCH_COLUMN);

    $ph = db()->prepare("SELECT * FROM project_photos WHERE project_id = ? ORDER BY is_cover DESC, sort_order ASC");
    $ph->execute([$id]);
    $photos = $ph->fetchAll();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $tipo = in_array($_POST['tipo'] ?? '', ['scolastico', 'personale']) ? $_POST['tipo'] : 'personale';
    $modalita = in_array($_POST['modalita'] ?? '', ['singolo', 'gruppo']) ? $_POST['modalita'] : 'singolo';
    $periodo = trim($_POST['periodo'] ?? '') ?: null;
    $demo_url = trim($_POST['demo_url'] ?? '') ?: null;
    $github_url = trim($_POST['github_url'] ?? '') ?: null;
    $techInput = trim($_POST['techs'] ?? '');

    if (!$nome)
        $errors[] = 'Il nome è obbligatorio.';
    if (!$descrizione)
        $errors[] = 'La descrizione è obbligatoria.';

    // Handle file uploads
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0755, true);

    // Handle doc upload (PDF or MD)
    $doc_filename = $proj['doc_filename'] ?? null;
    $doc_type = $proj['doc_type'] ?? null;
    if (!empty($_FILES['doc']['tmp_name']) && $_FILES['doc']['error'] === UPLOAD_ERR_OK) {
        $allowedDoc = ['application/pdf' => 'pdf', 'text/plain' => 'md', 'text/markdown' => 'md', 'text/x-markdown' => 'md'];
        $docMime = mime_content_type($_FILES['doc']['tmp_name']);
        $docExt = strtolower(pathinfo($_FILES['doc']['name'], PATHINFO_EXTENSION));
        if (isset($allowedDoc[$docMime]) || in_array($docExt, ['pdf', 'md'])) {
            $detectedType = in_array($docExt, ['pdf', 'md']) ? $docExt : $allowedDoc[$docMime];
            $docFn = uniqid('doc_', true) . '.' . $detectedType;
            if (move_uploaded_file($_FILES['doc']['tmp_name'], $uploadDir . $docFn)) {
                // Delete old doc if exists
                if ($doc_filename)
                    @unlink($uploadDir . $doc_filename);
                $doc_filename = $docFn;
                $doc_type = $detectedType;
            }
        } else {
            $errors[] = 'Formato documentazione non supportato (usa PDF o .md).';
        }
    }
    // Delete doc if requested
    if (!empty($_POST['delete_doc']) && $doc_filename) {
        @unlink($uploadDir . $doc_filename);
        $doc_filename = null;
        $doc_type = null;
    }

    $newFiles = [];
    if (!empty($_FILES['photos']['name'][0])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        foreach ($_FILES['photos']['tmp_name'] as $k => $tmp) {
            if ($_FILES['photos']['error'][$k] !== UPLOAD_ERR_OK)
                continue;
            $mime = mime_content_type($tmp);
            if (!in_array($mime, $allowed)) {
                $errors[] = 'Tipo file non supportato: ' . htmlspecialchars($_FILES['photos']['name'][$k]);
                continue;
            }
            $ext = pathinfo($_FILES['photos']['name'][$k], PATHINFO_EXTENSION);
            $fn = uniqid('proj_', true) . '.' . strtolower($ext);
            if (move_uploaded_file($tmp, $uploadDir . $fn)) {
                $newFiles[] = $fn;
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            db()->prepare("UPDATE projects SET nome=?, descrizione=?, tipo=?, modalita=?, periodo=?, demo_url=?, github_url=?, doc_filename=?, doc_type=? WHERE id=?")
                ->execute([$nome, $descrizione, $tipo, $modalita, $periodo, $demo_url, $github_url, $doc_filename, $doc_type, $id]);
            db()->prepare("DELETE FROM project_techs WHERE project_id = ?")->execute([$id]);
        } else {
            db()->prepare("INSERT INTO projects (nome,descrizione,tipo,modalita,periodo,demo_url,github_url,doc_filename,doc_type) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$nome, $descrizione, $tipo, $modalita, $periodo, $demo_url, $github_url, $doc_filename, $doc_type]);
            $id = db()->lastInsertId();
        }

        // Techs
        if ($techInput) {
            foreach (array_filter(array_map('trim', explode(',', $techInput))) as $tech) {
                db()->prepare("INSERT INTO project_techs (project_id,tech) VALUES (?,?)")->execute([$id, $tech]);
            }
        }

        // New photos — first new photo becomes cover if no cover exists yet
        if ($newFiles) {
            $coverExists = db()->prepare("SELECT COUNT(*) FROM project_photos WHERE project_id=? AND is_cover=1")->execute([$id]);
            $coverExists = db()->prepare("SELECT COUNT(*) FROM project_photos WHERE project_id=? AND is_cover=1");
            $coverExists->execute([$id]);
            $hasCover = (int) $coverExists->fetchColumn() > 0;

            foreach ($newFiles as $i => $fn) {
                $isCover = (!$hasCover && $i === 0) ? 1 : 0;
                if ($isCover)
                    $hasCover = true;
                db()->prepare("INSERT INTO project_photos (project_id,filename,is_cover,sort_order) VALUES (?,?,?,?)")
                    ->execute([$id, $fn, $isCover, $i]);
            }
        }

        // Set cover from form
        if (!empty($_POST['cover_id'])) {
            $cid = (int) $_POST['cover_id'];
            db()->prepare("UPDATE project_photos SET is_cover=0 WHERE project_id=?")->execute([$id]);
            db()->prepare("UPDATE project_photos SET is_cover=1 WHERE id=? AND project_id=?")->execute([$cid, $id]);
        }

        // Delete individual photos
        if (!empty($_POST['delete_photos'])) {
            foreach ($_POST['delete_photos'] as $pid) {
                $pid = (int) $pid;
                $row = db()->prepare("SELECT filename FROM project_photos WHERE id=? AND project_id=?");
                $row->execute([$pid, $id]);
                $row = $row->fetch();
                if ($row) {
                    @unlink($uploadDir . $row['filename']);
                    db()->prepare("DELETE FROM project_photos WHERE id=?")->execute([$pid]);
                }
            }
        }

        $msg = $isEdit ? 'Progetto aggiornato!' : 'Progetto creato!';
        header('Location: index.php?ok=' . urlencode($msg));
        exit;
    }

    // Re-populate on error
    $proj = ['nome' => $nome, 'descrizione' => $descrizione, 'tipo' => $tipo, 'modalita' => $modalita, 'periodo' => $periodo, 'demo_url' => $demo_url, 'github_url' => $github_url, 'doc_filename' => $doc_filename, 'doc_type' => $doc_type];
    $techs = array_filter(array_map('trim', explode(',', $techInput)));
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Modifica' : 'Nuovo' ?> Progetto — Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .edit-wrap { max-width: 760px; margin: 0 auto; padding: 2rem 1.25rem 5rem; }
        .edit-header { padding: 1.5rem 0 2rem; }
        .edit-header h1 { font-size:1.8rem; font-weight:800; letter-spacing:-.03em; margin-top:.3rem; }
        .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; padding:2rem; display:flex; flex-direction:column; gap:1.5rem; margin-bottom:1.5rem; }
        .field { display:flex; flex-direction:column; gap:.4rem; }
        .field label { font-size:.85rem; font-weight:600; color:var(--muted); }
        .field input, .field textarea, .field select {
            padding:.7rem 1rem; border-radius:12px; border:1px solid var(--border);
            background:var(--surface); color:var(--text); font-size:.95rem; font-family:inherit;
            transition:border-color .2s;
        }
        .field input:focus, .field textarea:focus, .field select:focus { outline:none; border-color:var(--border-a); }
        .field textarea { resize:vertical; min-height:100px; }
        .field select option { background:var(--bg-card); }
        .row2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:500px){ .row2 { grid-template-columns:1fr; } }
        .errors { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); color:#f87171; border-radius:12px; padding:1rem 1.2rem; display:flex; flex-direction:column; gap:.25rem; font-size:.88rem; }
        .section-title { font-size:.95rem; font-weight:700; color:var(--accent-l); margin-bottom:-.5rem; }
        .hint { font-size:.78rem; color:var(--muted); margin-top:.25rem; }

        /* Photos grid */
        .photos-grid { display:flex; flex-wrap:wrap; gap:.75rem; }
        .photo-item { position:relative; }
        .photo-item img { width:110px; height:78px; object-fit:cover; border-radius:10px; border:2px solid var(--border); display:block; }
        .photo-item img.is-cover { border-color:var(--accent); }
        .photo-actions { display:flex; gap:.25rem; margin-top:.3rem; }
        .photo-actions button { font-size:.7rem; font-weight:600; padding:.2rem .55rem; border-radius:6px; border:1px solid var(--border); background:transparent; color:var(--muted); cursor:pointer; transition:all .2s; }
        .photo-actions .set-cover { border-color:var(--border-a); color:var(--accent-l); }
        .photo-actions .del-ph { }
        .photo-actions .del-ph:hover { border-color:rgba(239,68,68,.4); color:#f87171; }
        .cover-badge { position:absolute; top:4px; left:4px; background:var(--accent); color:#fff; font-size:.6rem; font-weight:700; padding:.1rem .4rem; border-radius:4px; }

        .form-actions { display:flex; gap:.75rem; flex-wrap:wrap; }
    </style>
</head>
<body>
<div id="scroll-progress"></div>
<button id="theme-toggle"><span class="icon-dark">🌙</span><span class="icon-light">☀️</span></button>

<div class="edit-wrap">
    <div class="edit-header">
        <a href="index.php" style="color:var(--muted);font-size:.88rem;">← Torna ai progetti</a>
        <span class="label" style="display:block;margin-top:.5rem;"><?= $isEdit ? 'Modifica' : 'Nuovo' ?></span>
        <h1><?= $isEdit ? htmlspecialchars($proj['nome']) : 'Nuovo Progetto' ?></h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="errors"><?php foreach ($errors as $e): ?><span>⚠ <?= htmlspecialchars($e) ?></span><?php endforeach; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <!-- Base info -->
        <div class="form-card">
            <p class="section-title">📋 Informazioni</p>

            <div class="field">
                <label>Nome progetto *</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($proj['nome'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Descrizione *</label>
                <textarea name="descrizione"><?= htmlspecialchars($proj['descrizione'] ?? '') ?></textarea>
            </div>

            <div class="row2">
                <div class="field">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="personale"  <?= ($proj['tipo'] ?? '') == 'personale' ? 'selected' : '' ?>>Personale</option>
                        <option value="scolastico" <?= ($proj['tipo'] ?? '') == 'scolastico' ? 'selected' : '' ?>>Scolastico</option>
                    </select>
                </div>
                <div class="field">
                    <label>Modalità</label>
                    <select name="modalita">
                        <option value="singolo" <?= ($proj['modalita'] ?? '') == 'singolo' ? 'selected' : '' ?>>Singolo</option>
                        <option value="gruppo"  <?= ($proj['modalita'] ?? '') == 'gruppo' ? 'selected' : '' ?>>Gruppo</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Periodo</label>
                <input type="text" name="periodo" value="<?= htmlspecialchars($proj['periodo'] ?? '') ?>" placeholder="es: Settembre 2024 – Gennaio 2025">
            </div>

            <div class="field">
                <label>Tecnologie utilizzate</label>
                <input type="text" name="techs" value="<?= htmlspecialchars(implode(', ', $techs)) ?>">
                <span class="hint">Separate da virgola — es: PHP, MySQL, JavaScript</span>
            </div>
        </div>

        <!-- Links -->
        <div class="form-card">
            <p class="section-title">🔗 Link (opzionali)</p>
            <div class="field">
                <label>Demo live</label>
                <input type="url" name="demo_url" value="<?= htmlspecialchars($proj['demo_url'] ?? '') ?>" placeholder="https://...">
            </div>
            <div class="field">
                <label>GitHub</label>
                <input type="url" name="github_url" value="<?= htmlspecialchars($proj['github_url'] ?? '') ?>" placeholder="https://github.com/...">
            </div>
        </div>

        <!-- Documentation -->
        <div class="form-card">
            <p class="section-title">📄 Documentazione (opzionale)</p>

            <?php if (!empty($proj['doc_filename'])): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;background:var(--surface);border:1px solid var(--border);border-radius:12px;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        <span style="font-size:1.4rem;"><?= $proj['doc_type'] === 'pdf' ? '📕' : '📝' ?></span>
                        <div>
                            <span style="font-size:.88rem;font-weight:600;"><?= strtoupper($proj['doc_type']) ?></span>
                            <span style="font-size:.78rem;color:var(--muted);display:block;"><?= htmlspecialchars($proj['doc_filename']) ?></span>
                        </div>
                    </div>
                    <label style="display:inline-flex;align-items:center;gap:.35rem;cursor:pointer;">
                        <input type="checkbox" name="delete_doc" value="1" style="accent-color:#ef4444;">
                        <span style="font-size:.78rem;font-weight:600;color:#f87171;">Rimuovi</span>
                    </label>
                </div>
            <?php endif; ?>

            <div class="field">
                <label><?= !empty($proj['doc_filename']) ? 'Sostituisci file' : 'Carica file' ?></label>
                <input type="file" name="doc" accept=".pdf,.md,application/pdf,text/markdown"
                       style="background:transparent;border:2px dashed var(--border);cursor:pointer;padding:1rem;">
                <span class="hint">PDF o Markdown (.md)</span>
            </div>
        </div>

        <!-- Photos -->
        <div class="form-card">
            <p class="section-title">🖼 Foto</p>

            <?php if (!empty($photos)): ?>
                <div class="photos-grid">
                    <?php foreach ($photos as $ph): ?>
                        <div class="photo-item">
                            <?php if ($ph['is_cover']): ?><span class="cover-badge">Cover</span><?php endif; ?>
                            <img src="../../uploads/<?= htmlspecialchars($ph['filename']) ?>"
                                 class="<?= $ph['is_cover'] ? 'is-cover' : '' ?>">
                            <div class="photo-actions">
                                <?php if (!$ph['is_cover']): ?>
                                    <button type="submit" name="cover_id" value="<?= $ph['id'] ?>" class="set-cover">⭐ Cover</button>
                                <?php endif; ?>
                                <label style="display:inline-flex;align-items:center;gap:.25rem;">
                                    <input type="checkbox" name="delete_photos[]" value="<?= $ph['id'] ?>" style="accent-color:#ef4444;">
                                    <span class="del-ph" style="font-size:.7rem;font-weight:600;color:var(--muted);">Elimina</span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="field" style="margin-top:<?= !empty($photos) ? '1rem' : '0' ?>">
                <label>Carica nuove foto</label>
                <input type="file" name="photos[]" multiple accept="image/*"
                       style="background:transparent;border:2px dashed var(--border);cursor:pointer;padding:1rem;">
                <span class="hint">JPG, PNG, WebP, GIF — puoi selezionarne più di una</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">💾 <?= $isEdit ? 'Salva modifiche' : 'Crea progetto' ?></button>
            <a href="index.php" class="btn ghost">Annulla</a>
        </div>
    </form>
</div>

<button id="btt">↑</button>
<script src="../../js/main.js"></script>
</body>
</html>