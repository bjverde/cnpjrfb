<?php
$baseDir = __DIR__;
$directories = [];

if (is_dir($baseDir)) {
    $items = scandir($baseDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || str_starts_with($item, '.')) {
            continue;
        }
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $directories[] = [
                'name' => $item,
                'mtime' => filemtime($fullPath),
            ];
        }
    }
}

$phpVersion = phpversion();
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache';
$pdoDrivers = class_exists('PDO') ? PDO::getAvailableDrivers() : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormDocker — Debian 13 &amp; PHP <?= htmlspecialchars($phpVersion) ?></title>
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface-hover: #26354d;
            --border: #334155;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #38bdf8;
            --primary-dark: #0284c7;
            --accent: #818cf8;
            --success: #34d399;
            --warning: #fbbf24;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at top right, #1e293b 0%, var(--bg) 65%);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
            padding: 2.5rem 1.25rem;
        }

        .container {
            max-width: 1080px;
            margin: 0 auto;
        }

        /* Header */
        header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1.75rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.25);
        }

        .brand h1 {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand p {
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: var(--primary);
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Grid Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(129, 140, 248, 0.15);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 600;
        }

        .card-desc {
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-bottom: 1.25rem;
        }

        .credentials-box {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }

        .credentials-box .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 0.2rem 0;
        }

        .credentials-box code {
            color: var(--warning);
            background: rgba(251, 191, 36, 0.12);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: Consolas, monospace;
        }

        .credentials-note {
            margin-top: 0.55rem;
            padding-top: 0.55rem;
            border-top: 1px dashed var(--border);
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.7rem 1.1rem;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-dark), var(--accent));
            color: #fff;
            transition: opacity 0.2s ease;
        }

        .btn:hover {
            opacity: 0.92;
        }

        /* Directories Section */
        .section-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .count-pill {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.8rem;
            padding: 0.15rem 0.65rem;
            border-radius: 999px;
        }

        .search-input {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.55rem 0.95rem;
            border-radius: 10px;
            font-size: 0.9rem;
            min-width: 240px;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        .dir-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .dir-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.15rem;
            text-decoration: none;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .dir-item:hover {
            background: var(--surface-hover);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .dir-folder-icon {
            font-size: 1.5rem;
        }

        .dir-info {
            overflow: hidden;
        }

        .dir-name {
            font-weight: 600;
            font-size: 0.96rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dir-meta {
            color: var(--text-muted);
            font-size: 0.78rem;
        }

        .empty-state {
            background: var(--surface);
            border: 1px dashed var(--border);
            border-radius: 14px;
            padding: 2.25rem;
            text-align: center;
            color: var(--text-muted);
        }

        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                <div class="brand-icon">🐳</div>
                <div>
                    <h1>FormDocker — Ambiente de Desenvolvimento</h1>
                    <p>Debian 13 &bull; <?= htmlspecialchars($serverSoftware) ?> &bull; PostgreSQL</p>
                </div>
            </div>
            <div class="badges">
                <span class="badge">PHP <?= htmlspecialchars($phpVersion) ?></span>
                <?php foreach ($pdoDrivers as $driver): ?>
                    <span class="badge">PDO: <?= htmlspecialchars($driver) ?></span>
                <?php endforeach; ?>
            </div>
        </header>

        <section class="cards-grid">
            <!-- Card info.php -->
            <div class="card">
                <div>
                    <div class="card-header">
                        <div class="card-icon">🐘</div>
                        <div class="card-title">Informações do PHP (phpinfo)</div>
                    </div>
                    <p class="card-desc">
                        Consulte todas as configurações ativas do PHP <?= htmlspecialchars($phpVersion) ?>, diretivas do <code>php.ini</code>, módulos do Apache e extensões instaladas.
                    </p>
                </div>
                <a class="btn" href="info.php" target="_blank" rel="noopener">
                    Abrir info.php &rarr;
                </a>
            </div>

            <!-- Card pgAdmin -->
            <div class="card">
                <div>
                    <div class="card-header">
                        <div class="card-icon">🐘</div>
                        <div class="card-title">pgAdmin 4 &amp; PostgreSQL</div>
                    </div>
                    <p class="card-desc">
                        Gerenciador web para administrar o banco de dados PostgreSQL do container.
                    </p>
                    <div class="credentials-box">
                        <div class="cred-row">
                            <span>Banco — Usuário / Senha:</span>
                            <span><code>postgres</code> / <code>postgres</code></span>
                        </div>
                        <div class="cred-row">
                            <span>Host interno (container):</span>
                            <span><code>database</code> (porta <code>5432</code>)</span>
                        </div>
                        <div class="cred-row">
                            <span>Login Web pgAdmin:</span>
                            <span><code>user@domain.com</code> / <code>123456</code></span>
                        </div>
                        <div class="credentials-note">
                            💡 O usuário e a senha do banco são <strong>postgres</strong> por padrão, porém podem ser alterados no arquivo <code>docker-compose.yml</code>.
                        </div>
                    </div>
                </div>
                <a class="btn" href="http://localhost:8080" target="_blank" rel="noopener">
                    Acessar pgAdmin (localhost:8080) &rarr;
                </a>
            </div>
        </section>

        <!-- Diretórios em www -->
        <section>
            <div class="section-header">
                <div class="section-title">
                    <span>📂 Projetos / Diretórios em <code>www/</code></span>
                    <span class="count-pill"><?= count($directories) ?> pasta(s)</span>
                </div>
                <?php if (count($directories) > 0): ?>
                    <input
                        type="search"
                        id="dirFilter"
                        class="search-input"
                        placeholder="Filtrar pastas..."
                        aria-label="Filtrar pastas"
                    >
                <?php endif; ?>
            </div>

            <?php if (count($directories) === 0): ?>
                <div class="empty-state">
                    Nenhuma subpasta encontrada em <code>/var/www/html</code> (pasta <code>www/</code>).<br>
                    Crie ou copie as pastas dos seus projetos para dentro de <code>www/</code> e atualize esta página.
                </div>
            <?php else: ?>
                <div class="dir-grid" id="dirGrid">
                    <?php foreach ($directories as $dir): ?>
                        <a class="dir-item" href="<?= htmlspecialchars(rawurlencode($dir['name'])) ?>/" data-name="<?= htmlspecialchars(strtolower($dir['name'])) ?>">
                            <span class="dir-folder-icon">📁</span>
                            <div class="dir-info">
                                <div class="dir-name"><?= htmlspecialchars($dir['name']) ?></div>
                                <div class="dir-meta">Modificado em <?= date('d/m/Y H:i', $dir['mtime']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer>
            FormDocker &mdash; Ambiente configurado para desenvolvimento local
        </footer>
    </div>

    <script>
        const filterInput = document.getElementById('dirFilter');
        if (filterInput) {
            filterInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('#dirGrid .dir-item');
                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    item.style.display = name.includes(term) ? 'flex' : 'none';
                });
            });
        }
    </script>
</body>
</html>

