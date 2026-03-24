<?php
// admin/login.php
session_start();
require_once __DIR__ . '/../db.php';

if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user && $pass) {
        $stmt = db()->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$user]);
        $row = $stmt->fetch();
        if ($row && password_verify($pass, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $row['id'];
            header('Location: index.php');
            exit;
        }
    }
    $err = 'Credenziali non valide.';
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100svh;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .login-box h1 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .field label {
            font-size: .85rem;
            font-weight: 600;
            color: var(--muted);
        }

        .field input {
            padding: .7rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            font-size: .95rem;
            font-family: inherit;
            transition: border-color .2s;
        }

        .field input:focus {
            outline: none;
            border-color: var(--border-a);
        }

        .err {
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .25);
            color: #f87171;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-size: .85rem;
        }
    </style>
</head>

<body>
    <form class="login-box" method="post">
        <h1>🔒 Admin</h1>
        <?php if ($err): ?>
            <div class="err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" autocomplete="username" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn primary">Accedi</button>
    </form>
    <script src="../../js/main.js"></script>
</body>

</html>